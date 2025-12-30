# Trading Robot API Documentation

**Author:** Agent-2 (Architecture & Design Specialist)  
**Date:** 2025-12-27  
**Status:** ACTIVE - API Documentation  
**Purpose:** Complete API documentation for Trading Robot REST and WebSocket endpoints

<!-- SSOT Domain: documentation -->

---

## Executive Summary

This document provides comprehensive API documentation for the Trading Robot system, including all REST endpoints, WebSocket endpoints, request/response formats, usage examples, and error handling.

**Base URL:** `http://localhost:8000` (default)  
**API Version:** 1.0.0  
**Protocol:** REST API (HTTP/HTTPS) + WebSocket  
**Format:** JSON  
**Framework:** FastAPI

---

## Table of Contents

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [REST API Endpoints](#rest-api-endpoints)
4. [WebSocket API](#websocket-api)
5. [Data Models](#data-models)
6. [Error Handling](#error-handling)
7. [Usage Examples](#usage-examples)
8. [Rate Limiting](#rate-limiting)
9. [OpenAPI/Swagger Documentation](#openapiswagger-documentation)

---

## Overview

### Base URL

The Trading Robot API is accessible at:

- **Development:** `http://localhost:8000`
- **Production:** Configure via `WEB_HOST` and `WEB_PORT` environment variables

### API Endpoints

All API endpoints are prefixed with `/api/`:

- REST endpoints: `GET`, `POST`, `PUT`, `DELETE` HTTP methods
- WebSocket endpoints: `ws://` protocol

### Response Format

All responses are returned in JSON format:

```json
{
  "status": "success|error",
  "data": { ... },
  "message": "Optional message",
  "timestamp": "2025-12-27T12:00:00Z"
}
```

---

## Authentication

**Current Status:** Authentication not implemented (local development)

**Future Implementation:**
- API key authentication
- OAuth 2.0
- JWT tokens
- Session-based authentication

**Security Note:** For production deployment, implement authentication before exposing API publicly.

---

## REST API Endpoints

### 1. Get System Status

Get current trading system status including market state, portfolio value, and positions.

**Endpoint:** `GET /api/status`

**Request:**
```http
GET /api/status HTTP/1.1
Host: localhost:8000
```

**Response (200 OK):**
```json
{
  "timestamp": "2025-12-27T12:00:00.000000",
  "market_open": true,
  "portfolio_value": 100000.00,
  "cash_balance": 50000.00,
  "positions": [
    {
      "symbol": "AAPL",
      "quantity": 10,
      "avg_entry_price": 150.00,
      "current_price": 155.00,
      "unrealized_pnl": 50.00
    }
  ],
  "total_positions": 1
}
```

**Response Fields:**
- `timestamp` (string): Current timestamp in ISO format
- `market_open` (boolean): Whether market is currently open
- `portfolio_value` (number): Total portfolio value in USD
- `cash_balance` (number): Available cash balance in USD
- `positions` (array): List of current positions
- `total_positions` (integer): Total number of open positions

**Error Response (500):**
```json
{
  "error": "Error message describing the issue"
}
```

**Usage Example (curl):**
```bash
curl -X GET http://localhost:8000/api/status
```

**Usage Example (Python):**
```python
import requests

response = requests.get('http://localhost:8000/api/status')
status = response.json()
print(f"Portfolio Value: ${status['portfolio_value']:.2f}")
print(f"Market Open: {status['market_open']}")
```

---

### 2. Get Portfolio Information

Get detailed portfolio information including positions and account details.

**Endpoint:** `GET /api/portfolio`

**Request:**
```http
GET /api/portfolio HTTP/1.1
Host: localhost:8000
```

**Response (200 OK):**
```json
{
  "positions": [
    {
      "symbol": "AAPL",
      "quantity": 10,
      "avg_entry_price": 150.00,
      "current_price": 155.00,
      "unrealized_pnl": 50.00,
      "market_value": 1550.00
    },
    {
      "symbol": "TSLA",
      "quantity": 5,
      "avg_entry_price": 200.00,
      "current_price": 210.00,
      "unrealized_pnl": 50.00,
      "market_value": 1050.00
    }
  ],
  "account": {
    "cash": 50000.00,
    "portfolio_value": 52100.00,
    "buying_power": 50000.00,
    "equity": 52100.00
  },
  "total_positions": 2
}
```

**Response Fields:**
- `positions` (array): List of all open positions
  - `symbol` (string): Stock symbol
  - `quantity` (integer): Number of shares
  - `avg_entry_price` (number): Average entry price per share
  - `current_price` (number): Current market price per share
  - `unrealized_pnl` (number): Unrealized profit/loss in USD
  - `market_value` (number): Current market value of position
- `account` (object): Account information
  - `cash` (number): Available cash balance
  - `portfolio_value` (number): Total portfolio value
  - `buying_power` (number): Available buying power
  - `equity` (number): Account equity
- `total_positions` (integer): Total number of open positions

**Error Response (500):**
```json
{
  "error": "Error message describing the issue"
}
```

**Usage Example (curl):**
```bash
curl -X GET http://localhost:8000/api/portfolio
```

**Usage Example (Python):**
```python
import requests

response = requests.get('http://localhost:8000/api/portfolio')
portfolio = response.json()

print(f"Total Positions: {portfolio['total_positions']}")
print(f"Portfolio Value: ${portfolio['account']['portfolio_value']:.2f}")

for position in portfolio['positions']:
    print(f"{position['symbol']}: {position['quantity']} shares @ ${position['current_price']:.2f}")
```

---

### 3. Get Market Data

Get market data (OHLCV) for a specific symbol.

**Endpoint:** `GET /api/market_data/{symbol}`

**Path Parameters:**
- `symbol` (string, required): Stock symbol (e.g., "AAPL", "TSLA")

**Query Parameters:**
- `timeframe` (string, optional): Timeframe for market data (default: "1Min")
  - Options: "1Min", "5Min", "15Min", "1Hour", "1Day"
- `limit` (integer, optional): Number of data points to return (default: 100, max: 1000)

**Request:**
```http
GET /api/market_data/AAPL?timeframe=5Min&limit=50 HTTP/1.1
Host: localhost:8000
```

**Response (200 OK):**
```json
{
  "symbol": "AAPL",
  "timeframe": "5Min",
  "data": [
    {
      "timestamp": "2025-12-27T09:30:00Z",
      "open": 150.00,
      "high": 151.00,
      "low": 149.50,
      "close": 150.50,
      "volume": 1000000
    },
    {
      "timestamp": "2025-12-27T09:35:00Z",
      "open": 150.50,
      "high": 152.00,
      "low": 150.00,
      "close": 151.50,
      "volume": 1200000
    }
  ]
}
```

**Response Fields:**
- `symbol` (string): Stock symbol
- `timeframe` (string): Timeframe used for data
- `data` (array): Array of OHLCV data points
  - `timestamp` (string): Timestamp in ISO format
  - `open` (number): Opening price
  - `high` (number): High price
  - `low` (number): Low price
  - `close` (number): Closing price
  - `volume` (integer): Trading volume

**Error Response (500):**
```json
{
  "error": "Error message describing the issue"
}
```

**Usage Example (curl):**
```bash
curl -X GET "http://localhost:8000/api/market_data/AAPL?timeframe=5Min&limit=50"
```

**Usage Example (Python):**
```python
import requests

params = {
    "timeframe": "5Min",
    "limit": 50
}
response = requests.get('http://localhost:8000/api/market_data/AAPL', params=params)
market_data = response.json()

print(f"Symbol: {market_data['symbol']}")
print(f"Data Points: {len(market_data['data'])}")
for bar in market_data['data'][:5]:  # Print first 5 bars
    print(f"{bar['timestamp']}: ${bar['close']:.2f} (Volume: {bar['volume']})")
```

---

### 4. Execute Trade

Execute a trade (buy or sell) for a specific symbol.

**Endpoint:** `POST /api/trade/{symbol}/{side}`

**Path Parameters:**
- `symbol` (string, required): Stock symbol (e.g., "AAPL", "TSLA")
- `side` (string, required): Trade side - "buy" or "sell"

**Query Parameters:**
- `quantity` (integer, optional): Number of shares to trade (default: 1)

**Request:**
```http
POST /api/trade/AAPL/buy?quantity=10 HTTP/1.1
Host: localhost:8000
Content-Type: application/json
```

**Response (200 OK - Success):**
```json
{
  "status": "success",
  "order": {
    "id": "order-12345",
    "symbol": "AAPL",
    "side": "buy",
    "quantity": 10,
    "order_type": "market",
    "status": "filled",
    "filled_price": 150.50,
    "filled_at": "2025-12-27T12:00:00Z"
  }
}
```

**Response (200 OK - Error):**
```json
{
  "status": "error",
  "message": "Error message describing why the trade failed"
}
```

**Response Fields (Success):**
- `status` (string): "success" or "error"
- `order` (object): Order details
  - `id` (string): Order ID
  - `symbol` (string): Stock symbol
  - `side` (string): "buy" or "sell"
  - `quantity` (integer): Number of shares
  - `order_type` (string): Order type (e.g., "market", "limit")
  - `status` (string): Order status (e.g., "filled", "pending", "cancelled")
  - `filled_price` (number): Price at which order was filled
  - `filled_at` (string): Timestamp when order was filled

**Error Responses:**
- `400 Bad Request`: Invalid parameters (symbol, side, quantity)
- `403 Forbidden`: Risk limits exceeded, trading not allowed
- `500 Internal Server Error`: System error

**Usage Example (curl):**
```bash
# Buy 10 shares of AAPL
curl -X POST "http://localhost:8000/api/trade/AAPL/buy?quantity=10"

# Sell 5 shares of TSLA
curl -X POST "http://localhost:8000/api/trade/TSLA/sell?quantity=5"
```

**Usage Example (Python):**
```python
import requests

# Buy trade
response = requests.post(
    'http://localhost:8000/api/trade/AAPL/buy',
    params={'quantity': 10}
)
result = response.json()

if result['status'] == 'success':
    order = result['order']
    print(f"Order {order['id']}: {order['side'].upper()} {order['quantity']} {order['symbol']} @ ${order['filled_price']:.2f}")
else:
    print(f"Trade failed: {result['message']}")
```

---

### 5. Emergency Stop Trading

Immediately stop all trading activity (emergency stop).

**Endpoint:** `GET /api/stop_trading`

**Request:**
```http
GET /api/stop_trading HTTP/1.1
Host: localhost:8000
```

**Response (200 OK - Success):**
```json
{
  "status": "success",
  "message": "Trading stopped"
}
```

**Response (200 OK - Error):**
```json
{
  "status": "error",
  "message": "Error message describing why stop failed"
}
```

**Response Fields:**
- `status` (string): "success" or "error"
- `message` (string): Status message

**Actions Performed:**
- Stops accepting new trades
- Cancels pending orders (if possible)
- Stops trading engine
- Logs emergency stop event

**Usage Example (curl):**
```bash
curl -X GET http://localhost:8000/api/stop_trading
```

**Usage Example (Python):**
```python
import requests

response = requests.get('http://localhost:8000/api/stop_trading')
result = response.json()

if result['status'] == 'success':
    print("Trading stopped successfully")
else:
    print(f"Stop failed: {result['message']}")
```

---

## WebSocket API

### Real-Time Updates

Connect to WebSocket endpoint to receive real-time trading updates.

**Endpoint:** `ws://localhost:8000/ws/updates`

**Connection:**
```javascript
const ws = new WebSocket('ws://localhost:8000/ws/updates');

ws.onopen = () => {
    console.log('WebSocket connected');
};

ws.onmessage = (event) => {
    const data = JSON.parse(event.data);
    console.log('Update received:', data);
};

ws.onerror = (error) => {
    console.error('WebSocket error:', error);
};

ws.onclose = () => {
    console.log('WebSocket disconnected');
};
```

**Message Format:**
Messages are sent every 5 seconds with current system status:

```json
{
  "timestamp": "2025-12-27T12:00:00.000000",
  "market_open": true,
  "portfolio_value": 100000.00,
  "cash_balance": 50000.00,
  "positions": [
    {
      "symbol": "AAPL",
      "quantity": 10,
      "avg_entry_price": 150.00,
      "current_price": 155.00,
      "unrealized_pnl": 50.00
    }
  ],
  "total_positions": 1
}
```

**Message Fields:**
Same as `/api/status` endpoint response (see above).

**Usage Example (Python):**
```python
import asyncio
import websockets
import json

async def listen_updates():
    uri = "ws://localhost:8000/ws/updates"
    async with websockets.connect(uri) as websocket:
        while True:
            message = await websocket.recv()
            data = json.loads(message)
            print(f"Portfolio Value: ${data['portfolio_value']:.2f}")
            print(f"Positions: {data['total_positions']}")

asyncio.run(listen_updates())
```

---

## Data Models

### Position

```json
{
  "symbol": "AAPL",
  "quantity": 10,
  "avg_entry_price": 150.00,
  "current_price": 155.00,
  "unrealized_pnl": 50.00,
  "market_value": 1550.00
}
```

### Account

```json
{
  "cash": 50000.00,
  "portfolio_value": 100000.00,
  "buying_power": 50000.00,
  "equity": 100000.00
}
```

### Market Data Bar

```json
{
  "timestamp": "2025-12-27T09:30:00Z",
  "open": 150.00,
  "high": 151.00,
  "low": 149.50,
  "close": 150.50,
  "volume": 1000000
}
```

### Order

```json
{
  "id": "order-12345",
  "symbol": "AAPL",
  "side": "buy",
  "quantity": 10,
  "order_type": "market",
  "status": "filled",
  "filled_price": 150.50,
  "filled_at": "2025-12-27T12:00:00Z"
}
```

---

## Error Handling

### Error Response Format

All errors follow a consistent format:

```json
{
  "error": "Error message describing what went wrong",
  "code": "ERROR_CODE",
  "timestamp": "2025-12-27T12:00:00Z"
}
```

### HTTP Status Codes

- `200 OK`: Request successful
- `400 Bad Request`: Invalid request parameters
- `401 Unauthorized`: Authentication required (future)
- `403 Forbidden`: Operation not allowed (risk limits, etc.)
- `404 Not Found`: Endpoint or resource not found
- `500 Internal Server Error`: Server error

### Common Error Messages

- `"Invalid symbol"`: Symbol parameter is invalid or not found
- `"Invalid side"`: Trade side must be "buy" or "sell"
- `"Invalid quantity"`: Quantity must be positive integer
- `"Risk limit exceeded"`: Trade would exceed risk limits
- `"Market closed"`: Market is not currently open
- `"Insufficient funds"`: Not enough cash to execute trade
- `"Trading stopped"`: Trading has been stopped (emergency stop)

---

## Usage Examples

### Complete Trading Flow Example

```python
import requests
import time

BASE_URL = "http://localhost:8000"

# 1. Check system status
status = requests.get(f"{BASE_URL}/api/status").json()
print(f"Market Open: {status['market_open']}")
print(f"Portfolio Value: ${status['portfolio_value']:.2f}")

# 2. Get market data
market_data = requests.get(
    f"{BASE_URL}/api/market_data/AAPL",
    params={"timeframe": "5Min", "limit": 10}
).json()
print(f"Latest AAPL Price: ${market_data['data'][-1]['close']:.2f}")

# 3. Execute buy trade
trade = requests.post(
    f"{BASE_URL}/api/trade/AAPL/buy",
    params={"quantity": 10}
).json()

if trade['status'] == 'success':
    print(f"Trade executed: {trade['order']['id']}")
    
    # 4. Check portfolio after trade
    portfolio = requests.get(f"{BASE_URL}/api/portfolio").json()
    print(f"Total Positions: {portfolio['total_positions']}")
    
    # 5. Monitor via WebSocket (would require websocket library)
    # ... WebSocket connection code ...
```

### Real-Time Monitoring Example

```python
import asyncio
import websockets
import json
import requests

async def monitor_trading():
    # Connect to WebSocket for real-time updates
    uri = "ws://localhost:8000/ws/updates"
    
    async with websockets.connect(uri) as websocket:
        print("Connected to real-time updates")
        
        while True:
            try:
                message = await websocket.recv()
                data = json.loads(message)
                
                print(f"\n[{data['timestamp']}]")
                print(f"Portfolio: ${data['portfolio_value']:,.2f}")
                print(f"Cash: ${data['cash_balance']:,.2f}")
                print(f"Positions: {data['total_positions']}")
                
                for position in data['positions']:
                    pnl_sign = "+" if position['unrealized_pnl'] >= 0 else ""
                    print(f"  {position['symbol']}: {position['quantity']} @ ${position['current_price']:.2f} ({pnl_sign}${position['unrealized_pnl']:.2f})")
                    
            except websockets.exceptions.ConnectionClosed:
                print("WebSocket connection closed")
                break
            except Exception as e:
                print(f"Error: {e}")
                break

# Run monitoring
asyncio.run(monitor_trading())
```

---

## Rate Limiting

**Current Status:** Rate limiting not implemented

**Future Implementation:**
- Per-endpoint rate limits
- Per-IP rate limits
- Rate limit headers in responses
- 429 Too Many Requests status code

**Recommendations:**
- Implement rate limiting for production
- Consider broker API rate limits
- Use exponential backoff for retries

---

## OpenAPI/Swagger Documentation

### Accessing Swagger UI

When the Trading Robot dashboard is running, access interactive API documentation at:

- **Swagger UI:** `http://localhost:8000/docs`
- **ReDoc:** `http://localhost:8000/redoc`
- **OpenAPI JSON:** `http://localhost:8000/openapi.json`

### FastAPI Auto-Generated Documentation

FastAPI automatically generates OpenAPI/Swagger documentation from route definitions. The documentation includes:

- All available endpoints
- Request/response schemas
- Parameter descriptions
- Try-it-out functionality
- Authentication requirements (when implemented)

---

## API Versioning

**Current Version:** 1.0.0

**Versioning Strategy:**
- Future versions will use URL versioning: `/api/v2/...`
- Current version accessible at `/api/...` (default)
- Backward compatibility maintained where possible

---

## Related Documents

- **[Trading Robot Operations Runbook](OPERATIONS_RUNBOOK.md)** - Operations procedures, troubleshooting, and incident response
- **[Trading Robot Deployment Guide](DEPLOYMENT_GUIDE.md)** - Deployment procedures and configuration
- **[Trading Robot Security Audit Report](SECURITY_AUDIT_REPORT.md)** - Security audit findings (includes API security recommendations)

## References

- **Operations Runbook:** `docs/trading_robot/OPERATIONS_RUNBOOK.md`
- **Deployment Guide:** `docs/trading_robot/DEPLOYMENT_GUIDE.md`
- **Security Audit Report:** `docs/trading_robot/SECURITY_AUDIT_REPORT.md`
- **Main README:** `README.md`
- **FastAPI Documentation:** https://fastapi.tiangolo.com/
- **WebSocket Documentation:** https://developer.mozilla.org/en-US/docs/Web/API/WebSocket

---

**Last Updated:** 2025-12-27 by Agent-2  
**Status:** ✅ ACTIVE - API Documentation Complete  
**Next Review:** After API authentication implementation or new endpoint additions

