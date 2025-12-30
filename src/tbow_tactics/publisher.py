"""
TBOW Tactics Publisher

Handles publishing to Stocktwits with rate limiting,
de-duplication, and draft mode.
"""

from __future__ import annotations

import hashlib
import json
import logging
import time
from dataclasses import dataclass, field
from datetime import datetime, timezone, timedelta
from pathlib import Path
from typing import Any, Optional
from enum import Enum
import urllib.request
import urllib.error

from .models import Signal, TradePlan, PostType
from .plan_composer import ComposedPost, PlanComposer
from .config import (
    LEDGER_DIR,
    MAX_POSTS_PER_HOUR,
    MAX_POSTS_PER_DAY,
    POST_COOLDOWN_SECONDS,
    DEDUPE_WINDOW_HOURS,
)

logger = logging.getLogger(__name__)


# ═══════════════════════════════════════════════════════════════════════════
# RATE LIMITER
# ═══════════════════════════════════════════════════════════════════════════

@dataclass
class RateLimitState:
    """Tracks rate limit state."""
    posts_this_hour: int = 0
    posts_today: int = 0
    last_post_time: Optional[datetime] = None
    hour_start: Optional[datetime] = None
    day_start: Optional[datetime] = None
    
    def to_dict(self) -> dict[str, Any]:
        return {
            "posts_this_hour": self.posts_this_hour,
            "posts_today": self.posts_today,
            "last_post_time": self.last_post_time.isoformat() if self.last_post_time else None,
            "hour_start": self.hour_start.isoformat() if self.hour_start else None,
            "day_start": self.day_start.isoformat() if self.day_start else None,
        }
    
    @classmethod
    def from_dict(cls, d: dict[str, Any]) -> "RateLimitState":
        return cls(
            posts_this_hour=int(d.get("posts_this_hour", 0)),
            posts_today=int(d.get("posts_today", 0)),
            last_post_time=datetime.fromisoformat(d["last_post_time"]) if d.get("last_post_time") else None,
            hour_start=datetime.fromisoformat(d["hour_start"]) if d.get("hour_start") else None,
            day_start=datetime.fromisoformat(d["day_start"]) if d.get("day_start") else None,
        )


class RateLimiter:
    """
    Rate limiter for Stocktwits publishing.
    
    Enforces:
    - Max posts per hour
    - Max posts per day
    - Cooldown between posts
    """
    
    def __init__(
        self,
        max_per_hour: int = MAX_POSTS_PER_HOUR,
        max_per_day: int = MAX_POSTS_PER_DAY,
        cooldown_seconds: int = POST_COOLDOWN_SECONDS,
        state_file: Optional[Path] = None,
    ):
        self.max_per_hour = max_per_hour
        self.max_per_day = max_per_day
        self.cooldown_seconds = cooldown_seconds
        self.state_file = state_file or (LEDGER_DIR / "rate_limit_state.json")
        self.state = self._load_state()
    
    def _load_state(self) -> RateLimitState:
        """Load state from file or return fresh state."""
        if self.state_file.exists():
            try:
                with open(self.state_file, "r") as f:
                    return RateLimitState.from_dict(json.load(f))
            except (json.JSONDecodeError, KeyError):
                pass
        return RateLimitState()
    
    def _save_state(self) -> None:
        """Save state to file."""
        self.state_file.parent.mkdir(parents=True, exist_ok=True)
        with open(self.state_file, "w") as f:
            json.dump(self.state.to_dict(), f, indent=2)
    
    def _reset_if_needed(self) -> None:
        """Reset counters if hour/day has rolled over."""
        now = datetime.now(timezone.utc)
        
        # Reset hourly counter
        if self.state.hour_start:
            if now - self.state.hour_start >= timedelta(hours=1):
                self.state.posts_this_hour = 0
                self.state.hour_start = now
        else:
            self.state.hour_start = now
        
        # Reset daily counter
        if self.state.day_start:
            if now.date() > self.state.day_start.date():
                self.state.posts_today = 0
                self.state.day_start = now
        else:
            self.state.day_start = now
    
    def can_post(self) -> tuple[bool, Optional[str]]:
        """
        Check if we can post now.
        
        Returns (can_post, reason_if_not)
        """
        self._reset_if_needed()
        now = datetime.now(timezone.utc)
        
        # Check hourly limit
        if self.state.posts_this_hour >= self.max_per_hour:
            return False, f"Hourly limit reached ({self.max_per_hour})"
        
        # Check daily limit
        if self.state.posts_today >= self.max_per_day:
            return False, f"Daily limit reached ({self.max_per_day})"
        
        # Check cooldown
        if self.state.last_post_time:
            elapsed = (now - self.state.last_post_time).total_seconds()
            if elapsed < self.cooldown_seconds:
                remaining = self.cooldown_seconds - elapsed
                return False, f"Cooldown: {remaining:.0f}s remaining"
        
        return True, None
    
    def wait_until_can_post(self) -> None:
        """Block until we can post (respect rate limits)."""
        while True:
            can, reason = self.can_post()
            if can:
                return
            logger.info(f"Rate limited: {reason}. Waiting...")
            time.sleep(60)  # Check every minute
    
    def record_post(self) -> None:
        """Record that a post was made."""
        self._reset_if_needed()
        now = datetime.now(timezone.utc)
        
        self.state.posts_this_hour += 1
        self.state.posts_today += 1
        self.state.last_post_time = now
        
        self._save_state()
    
    def get_status(self) -> dict[str, Any]:
        """Get current rate limit status."""
        self._reset_if_needed()
        can, reason = self.can_post()
        
        return {
            "can_post": can,
            "reason": reason,
            "posts_this_hour": self.state.posts_this_hour,
            "max_per_hour": self.max_per_hour,
            "posts_today": self.state.posts_today,
            "max_per_day": self.max_per_day,
            "last_post": self.state.last_post_time.isoformat() if self.state.last_post_time else None,
        }


# ═══════════════════════════════════════════════════════════════════════════
# DEDUPLICATION
# ═══════════════════════════════════════════════════════════════════════════

class DeduplicationStore:
    """
    Tracks posted signals to prevent duplicates.
    
    Uses content hashing to detect duplicates.
    """
    
    def __init__(
        self,
        window_hours: int = DEDUPE_WINDOW_HOURS,
        store_file: Optional[Path] = None,
    ):
        self.window_hours = window_hours
        self.store_file = store_file or (LEDGER_DIR / "dedupe_store.json")
        self.hashes: dict[str, datetime] = self._load()
    
    def _load(self) -> dict[str, datetime]:
        """Load hash store from file."""
        if self.store_file.exists():
            try:
                with open(self.store_file, "r") as f:
                    data = json.load(f)
                return {
                    k: datetime.fromisoformat(v) 
                    for k, v in data.items()
                }
            except (json.JSONDecodeError, ValueError):
                pass
        return {}
    
    def _save(self) -> None:
        """Save hash store to file."""
        self.store_file.parent.mkdir(parents=True, exist_ok=True)
        data = {k: v.isoformat() for k, v in self.hashes.items()}
        with open(self.store_file, "w") as f:
            json.dump(data, f, indent=2)
    
    def _cleanup_old(self) -> None:
        """Remove hashes older than window."""
        now = datetime.now(timezone.utc)
        cutoff = now - timedelta(hours=self.window_hours)
        self.hashes = {
            k: v for k, v in self.hashes.items()
            if v > cutoff
        }
    
    def is_duplicate(self, signal: Signal) -> bool:
        """Check if signal is a duplicate."""
        self._cleanup_old()
        hash_key = signal.dedupe_hash()
        return hash_key in self.hashes
    
    def is_content_duplicate(self, content: str) -> bool:
        """Check if content is a duplicate."""
        self._cleanup_old()
        hash_key = hashlib.sha256(content.encode()).hexdigest()
        return hash_key in self.hashes
    
    def record(self, signal: Signal) -> None:
        """Record a signal as posted."""
        self._cleanup_old()
        self.hashes[signal.dedupe_hash()] = datetime.now(timezone.utc)
        self._save()
    
    def record_content(self, content: str) -> None:
        """Record content as posted."""
        self._cleanup_old()
        hash_key = hashlib.sha256(content.encode()).hexdigest()
        self.hashes[hash_key] = datetime.now(timezone.utc)
        self._save()


# ═══════════════════════════════════════════════════════════════════════════
# STOCKTWITS CLIENT
# ═══════════════════════════════════════════════════════════════════════════

class PublishResult(str, Enum):
    """Result of a publish attempt."""
    SUCCESS = "success"
    RATE_LIMITED = "rate_limited"
    DUPLICATE = "duplicate"
    API_ERROR = "api_error"
    DRAFT_MODE = "draft_mode"
    VALIDATION_ERROR = "validation_error"


@dataclass
class PublishResponse:
    """Response from a publish attempt."""
    result: PublishResult
    post_id: Optional[str] = None
    message: str = ""
    content: str = ""


class StocktwitsClient:
    """
    Stocktwits API client.
    
    Note: You'll need to configure this with your actual
    Stocktwits access token and adjust the API calls
    based on Stocktwits' current API documentation.
    """
    
    API_BASE = "https://api.stocktwits.com/api/2"
    
    def __init__(self, access_token: str = ""):
        self.access_token = access_token
    
    def post_message(
        self,
        content: str,
        sentiment: Optional[str] = None,  # "bullish" or "bearish"
        chart_url: Optional[str] = None,
    ) -> PublishResponse:
        """
        Post a message to Stocktwits.
        
        Returns PublishResponse with result status.
        """
        if not self.access_token:
            return PublishResponse(
                result=PublishResult.API_ERROR,
                message="No access token configured",
            )
        
        # Validate content length
        if len(content) > 1000:
            return PublishResponse(
                result=PublishResult.VALIDATION_ERROR,
                message=f"Content too long: {len(content)} > 1000 chars",
                content=content,
            )
        
        # Build request
        url = f"{self.API_BASE}/messages/create.json"
        
        data = {
            "access_token": self.access_token,
            "body": content,
        }
        
        if sentiment and sentiment in ("bullish", "bearish"):
            data["sentiment"] = sentiment
        
        if chart_url:
            data["chart"] = chart_url
        
        try:
            encoded_data = json.dumps(data).encode("utf-8")
            request = urllib.request.Request(
                url,
                data=encoded_data,
                method="POST",
                headers={"Content-Type": "application/json"},
            )
            
            with urllib.request.urlopen(request, timeout=30) as response:
                response_data = json.loads(response.read().decode("utf-8"))
                
                if response_data.get("response", {}).get("status") == 200:
                    message_id = response_data.get("message", {}).get("id")
                    return PublishResponse(
                        result=PublishResult.SUCCESS,
                        post_id=str(message_id) if message_id else None,
                        message="Posted successfully",
                        content=content,
                    )
                else:
                    return PublishResponse(
                        result=PublishResult.API_ERROR,
                        message=str(response_data),
                        content=content,
                    )
                    
        except urllib.error.HTTPError as e:
            error_body = e.read().decode("utf-8") if e.fp else ""
            
            if e.code == 429:
                return PublishResponse(
                    result=PublishResult.RATE_LIMITED,
                    message=f"Rate limited by Stocktwits: {error_body}",
                    content=content,
                )
            
            return PublishResponse(
                result=PublishResult.API_ERROR,
                message=f"HTTP {e.code}: {error_body}",
                content=content,
            )
            
        except Exception as e:
            return PublishResponse(
                result=PublishResult.API_ERROR,
                message=str(e),
                content=content,
            )


# ═══════════════════════════════════════════════════════════════════════════
# PUBLISHER (MAIN INTERFACE)
# ═══════════════════════════════════════════════════════════════════════════

class StocktwitsPublisher:
    """
    Main publisher interface.
    
    Handles:
    - Rate limiting
    - De-duplication
    - Draft mode
    - Post composition
    """
    
    def __init__(
        self,
        access_token: str = "",
        draft_mode: bool = True,
        rate_limiter: Optional[RateLimiter] = None,
        dedupe_store: Optional[DeduplicationStore] = None,
        composer: Optional[PlanComposer] = None,
        draft_dir: Optional[Path] = None,
    ):
        self.draft_mode = draft_mode
        self.client = StocktwitsClient(access_token)
        self.rate_limiter = rate_limiter or RateLimiter()
        self.dedupe = dedupe_store or DeduplicationStore()
        self.composer = composer or PlanComposer()
        self.draft_dir = draft_dir or (LEDGER_DIR / "drafts")
        self.draft_dir.mkdir(parents=True, exist_ok=True)
    
    def publish_signal(
        self,
        signal: Signal,
        force: bool = False,
    ) -> PublishResponse:
        """
        Publish a signal as a trade plan post.
        
        Args:
            signal: The signal to publish
            force: Bypass rate limit and dedupe checks
        
        Returns:
            PublishResponse with result status
        """
        # Check for duplicate
        if not force and self.dedupe.is_duplicate(signal):
            logger.info(f"Duplicate signal detected: {signal.plan_id}")
            return PublishResponse(
                result=PublishResult.DUPLICATE,
                message="Signal already posted in dedupe window",
            )
        
        # Compose post
        post = self.composer.compose_plan(signal)
        
        return self._publish_post(post, signal, force)
    
    def publish_plan(
        self,
        plan: TradePlan,
        post_type: Optional[PostType] = None,
        force: bool = False,
    ) -> PublishResponse:
        """
        Publish from a TradePlan (auto-determines post type).
        """
        post = self.composer.compose_from_plan(plan, post_type)
        return self._publish_post(post, plan.signal, force)
    
    def publish_raw(
        self,
        content: str,
        ticker: str = "",
        force: bool = False,
    ) -> PublishResponse:
        """
        Publish raw content directly.
        
        Use this for custom posts like weekly recaps.
        """
        if not force and self.dedupe.is_content_duplicate(content):
            return PublishResponse(
                result=PublishResult.DUPLICATE,
                message="Content already posted in dedupe window",
            )
        
        post = ComposedPost(
            content=content,
            post_type=PostType.RECAP,
            ticker=ticker,
            plan_id="",
            character_count=len(content),
            includes_disclosure=False,
        )
        
        return self._publish_post(post, None, force)
    
    def _publish_post(
        self,
        post: ComposedPost,
        signal: Optional[Signal],
        force: bool,
    ) -> PublishResponse:
        """Internal publish method."""
        
        # Check content length
        if not post.is_valid_length:
            content = post.truncate_if_needed()
            logger.warning(f"Content truncated from {post.character_count} chars")
        else:
            content = post.content
        
        # Draft mode - save to file instead of posting
        if self.draft_mode:
            draft_file = self._save_draft(post, signal)
            logger.info(f"Draft saved: {draft_file}")
            return PublishResponse(
                result=PublishResult.DRAFT_MODE,
                message=f"Draft saved to {draft_file}",
                content=content,
            )
        
        # Check rate limit
        if not force:
            can_post, reason = self.rate_limiter.can_post()
            if not can_post:
                logger.warning(f"Rate limited: {reason}")
                return PublishResponse(
                    result=PublishResult.RATE_LIMITED,
                    message=reason or "Rate limited",
                    content=content,
                )
        
        # Determine sentiment
        sentiment = None
        if signal:
            if signal.bias.value == "bullish":
                sentiment = "bullish"
            elif signal.bias.value == "bearish":
                sentiment = "bearish"
        
        # Post to Stocktwits
        response = self.client.post_message(content, sentiment)
        
        # Record if successful
        if response.result == PublishResult.SUCCESS:
            self.rate_limiter.record_post()
            if signal:
                self.dedupe.record(signal)
            else:
                self.dedupe.record_content(content)
        
        return response
    
    def _save_draft(
        self,
        post: ComposedPost,
        signal: Optional[Signal],
    ) -> Path:
        """Save a draft to file for review."""
        now = datetime.now(timezone.utc)
        timestamp = now.strftime("%Y%m%d_%H%M%S")
        
        if signal:
            filename = f"{timestamp}_{signal.ticker}_{post.post_type.value}.txt"
        else:
            filename = f"{timestamp}_recap_{post.post_type.value}.txt"
        
        draft_file = self.draft_dir / filename
        
        content = f"""# TBOW Tactics Draft
# Type: {post.post_type.value}
# Ticker: {post.ticker}
# Plan ID: {post.plan_id}
# Characters: {post.character_count}/1000
# Generated: {now.isoformat()}
# Status: READY FOR REVIEW

---

{post.content}

---

# To publish: Remove draft_mode or use publish_raw()
"""
        
        with open(draft_file, "w") as f:
            f.write(content)
        
        # Also save signal JSON if available
        if signal:
            json_file = self.draft_dir / f"{timestamp}_{signal.ticker}_signal.json"
            with open(json_file, "w") as f:
                f.write(signal.to_json())
        
        return draft_file
    
    def get_pending_drafts(self) -> list[Path]:
        """Get list of pending draft files."""
        return sorted(self.draft_dir.glob("*.txt"))
    
    def get_status(self) -> dict[str, Any]:
        """Get publisher status."""
        return {
            "draft_mode": self.draft_mode,
            "rate_limit": self.rate_limiter.get_status(),
            "pending_drafts": len(self.get_pending_drafts()),
            "draft_dir": str(self.draft_dir),
        }
