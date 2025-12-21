# 🚀 Quick Start Guide

Get the Trading Replay Journal running in 5 minutes.

---

## **Prerequisites**

- Python 3.11+
- Node.js 18+
- npm or yarn

---

## **Setup Backend**

```bash
cd trader_replay/backend
pip install -r requirements.txt
python main.py
```

Backend runs on `http://localhost:8000`

---

## **Setup Frontend**

```bash
cd trader_replay/frontend
npm install
npm start
```

Frontend runs on `http://localhost:3000`

---

## **Create Your First Session**

Use the API to create a test session:

```python
import requests

# Sample candles data
candles = [
    {
        "timestamp": "2024-01-01T09:30:00",
        "open": 150.0,
        "high": 151.0,
        "low": 149.5,
        "close": 150.5,
        "volume": 1000
    },
    {
        "timestamp": "2024-01-01T09:31:00",
        "open": 150.5,
        "high": 151.5,
        "low": 150.0,
        "close": 151.0,
        "volume": 1200
    },
    # Add more candles...
]

# Create session
response = requests.post("http://localhost:8000/sessions", json={
    "symbol": "AAPL",
    "session_date": "2024-01-01",
    "timeframe": "1m",
    "candles": candles
})

session_id = response.json()["session_id"]
print(f"Session created: {session_id}")
```

---

## **Use the App**

1. Open `http://localhost:3000`
2. Select your session
3. Click "Start Replay"
4. Use controls to step through candles
5. Add journal entries and paper trades as you go!

---

## **Next Steps**

- Connect to real market data provider
- Add more candles for full trading day
- Implement P&L calculation
- Add behavioral scoring

---

**That's it! You're ready to train.** 🎯



