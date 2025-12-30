"""
TBOW Bot WordPress Publisher

Publishes reports to tradingrobotplug.com via WP REST API.
"""

from __future__ import annotations

import logging
import base64
from dataclasses import dataclass
from datetime import date
from typing import Optional, Any
import urllib.request
import urllib.error
import json

from .config import Config, WP_BASE, WP_USER, WP_APP_PASSWORD
from .db import record_post, was_posted

logger = logging.getLogger("tbow_bot.post_wp")


# ═══════════════════════════════════════════════════════════════════════════
# WORDPRESS CLIENT
# ═══════════════════════════════════════════════════════════════════════════

@dataclass
class PostResult:
    """Result of a WordPress post operation."""
    success: bool
    post_id: Optional[int] = None
    post_url: Optional[str] = None
    error: Optional[str] = None


class WordPressClient:
    """
    WordPress REST API client.
    
    Uses Application Passwords for authentication.
    
    To create an Application Password:
    1. Go to WP Admin → Users → Your Profile
    2. Scroll to "Application Passwords"
    3. Enter a name and click "Add New"
    4. Copy the generated password
    """
    
    def __init__(
        self,
        base_url: str = "",
        username: str = "",
        app_password: str = "",
    ):
        self.base_url = base_url or WP_BASE
        self.username = username or WP_USER
        self.app_password = app_password or WP_APP_PASSWORD
        
        # Ensure base URL doesn't have trailing slash
        self.base_url = self.base_url.rstrip("/")
    
    def _get_auth_header(self) -> str:
        """Generate Basic Auth header."""
        credentials = f"{self.username}:{self.app_password}"
        encoded = base64.b64encode(credentials.encode()).decode()
        return f"Basic {encoded}"
    
    def _make_request(
        self,
        endpoint: str,
        method: str = "GET",
        data: Optional[dict] = None,
    ) -> tuple[bool, Any]:
        """
        Make an authenticated request to WP REST API.
        
        Returns (success, response_or_error)
        """
        url = f"{self.base_url}/wp-json/wp/v2/{endpoint}"
        
        headers = {
            "Authorization": self._get_auth_header(),
            "Content-Type": "application/json",
        }
        
        try:
            if data:
                encoded_data = json.dumps(data).encode("utf-8")
            else:
                encoded_data = None
            
            request = urllib.request.Request(
                url,
                data=encoded_data,
                method=method,
                headers=headers,
            )
            
            with urllib.request.urlopen(request, timeout=30) as response:
                response_data = json.loads(response.read().decode("utf-8"))
                return True, response_data
                
        except urllib.error.HTTPError as e:
            error_body = e.read().decode("utf-8") if e.fp else ""
            logger.error(f"WP API error: HTTP {e.code} - {error_body}")
            return False, f"HTTP {e.code}: {error_body}"
            
        except Exception as e:
            logger.exception(f"WP API request failed: {e}")
            return False, str(e)
    
    def test_connection(self) -> bool:
        """Test if the WordPress connection is working."""
        success, response = self._make_request("users/me")
        
        if success:
            logger.info(f"Connected to WordPress as: {response.get('name', 'unknown')}")
            return True
        else:
            logger.error(f"WordPress connection failed: {response}")
            return False
    
    def create_post(
        self,
        title: str,
        content: str,
        status: str = "draft",
        categories: Optional[list[int]] = None,
        tags: Optional[list[int]] = None,
        featured_media: Optional[int] = None,
    ) -> PostResult:
        """
        Create a new WordPress post.
        
        Args:
            title: Post title
            content: HTML content
            status: "draft", "publish", "pending", "private"
            categories: List of category IDs
            tags: List of tag IDs
            featured_media: Featured image attachment ID
        
        Returns:
            PostResult with success status and post details
        """
        if not self.username or not self.app_password:
            return PostResult(
                success=False,
                error="WordPress credentials not configured",
            )
        
        data = {
            "title": title,
            "content": content,
            "status": status,
        }
        
        if categories:
            data["categories"] = categories
        if tags:
            data["tags"] = tags
        if featured_media:
            data["featured_media"] = featured_media
        
        success, response = self._make_request("posts", "POST", data)
        
        if success:
            post_id = response.get("id")
            post_url = response.get("link")
            
            logger.info(f"Created post #{post_id}: {post_url}")
            
            return PostResult(
                success=True,
                post_id=post_id,
                post_url=post_url,
            )
        else:
            return PostResult(
                success=False,
                error=str(response),
            )
    
    def update_post(
        self,
        post_id: int,
        title: Optional[str] = None,
        content: Optional[str] = None,
        status: Optional[str] = None,
    ) -> PostResult:
        """Update an existing post."""
        data = {}
        if title:
            data["title"] = title
        if content:
            data["content"] = content
        if status:
            data["status"] = status
        
        success, response = self._make_request(f"posts/{post_id}", "POST", data)
        
        if success:
            return PostResult(
                success=True,
                post_id=response.get("id"),
                post_url=response.get("link"),
            )
        else:
            return PostResult(
                success=False,
                error=str(response),
            )
    
    def get_categories(self) -> list[dict]:
        """Get all categories."""
        success, response = self._make_request("categories?per_page=100")
        if success:
            return response
        return []
    
    def get_tags(self) -> list[dict]:
        """Get all tags."""
        success, response = self._make_request("tags?per_page=100")
        if success:
            return response
        return []


# ═══════════════════════════════════════════════════════════════════════════
# PUBLISHER
# ═══════════════════════════════════════════════════════════════════════════

class WordPressPublisher:
    """
    High-level publisher for TBOW reports.
    
    Handles:
    - Duplicate detection
    - Post tracking
    - Category/tag management
    """
    
    def __init__(
        self,
        config: Optional[Config] = None,
        client: Optional[WordPressClient] = None,
    ):
        self.config = config or Config.from_env()
        self.client = client or WordPressClient(
            base_url=self.config.wp_base,
            username=self.config.wp_user,
            app_password=self.config.wp_app_password,
        )
    
    def publish_daily_report(
        self,
        title: str,
        html: str,
        report_date: date,
        status: Optional[str] = None,
        skip_if_exists: bool = True,
    ) -> PostResult:
        """
        Publish a daily report to WordPress.
        
        Args:
            title: Post title
            html: HTML content
            report_date: Date of the report
            status: Override default status ("draft" or "publish")
            skip_if_exists: Skip if already posted for this date
        
        Returns:
            PostResult
        """
        date_str = report_date.strftime("%Y-%m-%d")
        
        # Check for duplicate
        if skip_if_exists and was_posted(date_str):
            logger.info(f"Report for {date_str} already posted, skipping")
            return PostResult(
                success=True,
                error="Already posted",
            )
        
        # Determine status
        post_status = status or self.config.post_status
        
        # Create post
        result = self.client.create_post(
            title=title,
            content=html,
            status=post_status,
        )
        
        # Record in database
        if result.success:
            record_post(
                post_date=date_str,
                wp_post_id=result.post_id,
                wp_post_url=result.post_url,
                status="published" if post_status == "publish" else "draft",
            )
        
        return result
    
    def test_connection(self) -> bool:
        """Test WordPress connection."""
        return self.client.test_connection()


# ═══════════════════════════════════════════════════════════════════════════
# CONVENIENCE FUNCTIONS
# ═══════════════════════════════════════════════════════════════════════════

def publish_report(
    title: str,
    html: str,
    status: str = "draft",
) -> PostResult:
    """
    Publish a report to WordPress.
    
    Simple wrapper for quick publishing.
    """
    client = WordPressClient()
    return client.create_post(title, html, status)


def create_post(title: str, html: str, status: str = "publish") -> dict:
    """
    Legacy function for backwards compatibility.
    
    Returns dict with post info or raises exception.
    """
    client = WordPressClient()
    result = client.create_post(title, html, status)
    
    if result.success:
        return {
            "id": result.post_id,
            "link": result.post_url,
        }
    else:
        raise Exception(result.error)
