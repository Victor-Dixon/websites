"""
TBOW Bot FastAPI Webhook Receiver

Receives TradingView alerts and stores them in SQLite.

Endpoint: POST /tv-webhook
Expected payload:
{
  "secret": "YOUR_LONG_SECRET",
  "strategy": "TBOW_v5",
  "symbol": "{{ticker}}",
  "tf": "{{interval}}",
  "event": "{{alert_name}}",
  "price": {{close}},
  "time": "{{time}}"
}
"""

from __future__ import annotations

import json
import logging
from datetime import datetime, timezone
from typing import Any, Optional

from fastapi import FastAPI, HTTPException, Request
from fastapi.responses import JSONResponse
from pydantic import BaseModel

from .config import Config
from .db import insert_signal, init_db

# ═══════════════════════════════════════════════════════════════════════════
# LOGGING
# ═══════════════════════════════════════════════════════════════════════════

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(name)s: %(message)s",
)
logger = logging.getLogger("tbow_bot.app")

# ═══════════════════════════════════════════════════════════════════════════
# APP SETUP
# ═══════════════════════════════════════════════════════════════════════════

app = FastAPI(
    title="TBOW Bot Webhook",
    description="Receives TradingView alerts for TBOW strategy",
    version="1.0.0",
)

config = Config.from_env()


# ═══════════════════════════════════════════════════════════════════════════
# REQUEST MODELS
# ═══════════════════════════════════════════════════════════════════════════

class WebhookPayload(BaseModel):
    """Expected webhook payload from TradingView."""
    secret: str
    strategy: Optional[str] = "TBOW"
    symbol: str
    tf: Optional[str] = "UNKNOWN"
    event: str
    price: float
    time: Optional[str] = None
    
    class Config:
        extra = "allow"  # Allow extra fields


# ═══════════════════════════════════════════════════════════════════════════
# MIDDLEWARE
# ═══════════════════════════════════════════════════════════════════════════

@app.middleware("http")
async def log_requests(request: Request, call_next):
    """Log all incoming requests."""
    logger.info(f"{request.method} {request.url.path}")
    response = await call_next(request)
    return response


# ═══════════════════════════════════════════════════════════════════════════
# ROUTES
# ═══════════════════════════════════════════════════════════════════════════

@app.get("/")
async def root():
    """Health check endpoint."""
    return {
        "status": "ok",
        "service": "tbow-bot",
        "version": "1.0.0",
    }


@app.get("/health")
async def health():
    """Health check for monitoring."""
    return {"status": "healthy"}


@app.post("/tv-webhook")
async def tv_webhook(request: Request):
    """
    Receive TradingView webhook alerts.
    
    Validates secret, parses payload, and stores signal.
    """
    try:
        # Parse JSON body
        try:
            payload = await request.json()
        except json.JSONDecodeError:
            raise HTTPException(status_code=400, detail="Invalid JSON")
        
        # Validate secret
        secret = payload.get("secret", "")
        if not config.webhook_secret:
            logger.warning("No webhook secret configured - accepting all requests")
        elif secret != config.webhook_secret:
            logger.warning(f"Invalid secret from {request.client.host}")
            raise HTTPException(status_code=401, detail="Invalid secret")
        
        # Extract fields
        ts = payload.get("time") or datetime.now(timezone.utc).isoformat()
        symbol = payload.get("symbol", "UNKNOWN").upper()
        tf = payload.get("tf", "UNKNOWN")
        event = payload.get("event", "UNKNOWN")
        
        try:
            price = float(payload.get("price", 0))
        except (ValueError, TypeError):
            price = 0.0
        
        # Store signal
        signal_id = insert_signal(
            ts=ts,
            symbol=symbol,
            tf=tf,
            event=event,
            price=price,
            raw_json=json.dumps(payload),
        )
        
        logger.info(
            f"Signal #{signal_id}: {symbol} {event} @ {price:.2f} ({tf})"
        )
        
        return {
            "ok": True,
            "signal_id": signal_id,
            "event": event,
            "symbol": symbol,
        }
        
    except HTTPException:
        raise
    except Exception as e:
        logger.exception(f"Error processing webhook: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/tv-webhook-raw")
async def tv_webhook_raw(request: Request):
    """
    Raw webhook endpoint for debugging.
    
    Accepts any payload and logs it.
    """
    try:
        body = await request.body()
        logger.info(f"Raw webhook body: {body.decode('utf-8', errors='replace')}")
        
        try:
            payload = json.loads(body)
        except json.JSONDecodeError:
            payload = {"raw": body.decode("utf-8", errors="replace")}
        
        return {"ok": True, "received": payload}
        
    except Exception as e:
        logger.exception(f"Error in raw webhook: {e}")
        return {"ok": False, "error": str(e)}


# ═══════════════════════════════════════════════════════════════════════════
# STARTUP
# ═══════════════════════════════════════════════════════════════════════════

@app.on_event("startup")
async def startup():
    """Initialize on startup."""
    init_db()
    logger.info("TBOW Bot webhook server started")
    logger.info(f"Webhook secret configured: {bool(config.webhook_secret)}")


# ═══════════════════════════════════════════════════════════════════════════
# RUN (for development)
# ═══════════════════════════════════════════════════════════════════════════

def run_server():
    """Run the server (development mode)."""
    import uvicorn
    uvicorn.run(
        "tbow_bot.app:app",
        host=config.webhook_host,
        port=config.webhook_port,
        reload=True,
    )


if __name__ == "__main__":
    run_server()
