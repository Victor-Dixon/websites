---
config:
  layout: fixed
---
flowchart TD
    User(("User")) -- Selects stock, requests data --> Views["Views (SwiftUI)"]
    Views -- User action triggers VM calls --> ViewModels["ViewModels (StockResearch, HistoricalData, Alerts, TradePlan)"]
    ViewModels -- Fetch stock data & insights --> Services["Services (NetworkManager, OpenAIService, NotificationManager)"]
    Services -- GET request --> AlphaVantage["Alpha Vantage API"]
    AlphaVantage -- JSON response --> Services
    Services -- Analyze data & prepare results --> ViewModels
    ViewModels -- Generate trade plans --> Services
    Services -- POST request --> OpenAI["OpenAI GPT API"]
    OpenAI -- AI response --> Services
    Services -- Processed data --> ViewModels
    ViewModels -- Update UI with results --> Views
    Views -- Present charts, trade plans, alerts --> DataDisplay["Data Visualization & Alerts"]
    ViewModels -- Set alert thresholds --> Services
    Services -- Schedule alerts --> Notifications["Local Notifications"]
    Notifications -- Notify user when conditions met --> User
