# Trading Robot API Documentation - Validation Notes

**Author:** Agent-2 (Architecture & Design Specialist)  
**Date:** 2025-12-27  
**Status:** ACTIVE - Validation Context for Agent-4  
**Purpose:** Context document for API documentation validation coordination

<!-- SSOT Domain: documentation -->

---

## Executive Summary

This document provides context and validation notes for Agent-4's review of the Trading Robot API documentation (`API_DOCUMENTATION.md`). It outlines the documented endpoints, identifies potential alignment points with TradingRobotPlug.com implementation, and provides coordination context.

---

## Documented API Endpoints

The `API_DOCUMENTATION.md` document covers the **Trading Robot Backend API** (Python/FastAPI):

### REST Endpoints Documented:

1. **GET /api/status** - System status (market state, portfolio value, positions)
2. **GET /api/portfolio** - Portfolio information (positions, account details)
3. **GET /api/market_data/{symbol}** - Market data (OHLCV) for symbol
4. **POST /api/trade/{symbol}/{side}** - Execute trade (buy/sell)
5. **GET /api/stop_trading** - Emergency stop trading

### WebSocket Endpoint:

- **ws://localhost:8000/ws/updates** - Real-time updates (5-second intervals)

---

## TradingRobotPlug.com WordPress REST API Endpoints

Based on codebase search, the TradingRobotPlug.com WordPress theme exposes these REST endpoints:

### Stock Data Endpoints:
- **GET /wp-json/tradingrobotplug/v1/stock-data** - Get all stored stock data
- **GET /wp-json/tradingrobotplug/v1/stock-data/{symbol}** - Get stock data for specific symbol

### Dashboard Endpoints:
- **GET /wp-json/tradingrobotplug/v1/dashboard/overview** - Dashboard overview
- **GET /wp-json/tradingrobotplug/v1/dashboard** - Dashboard data
- **GET /wp-json/tradingrobotplug/v1/dashboard/strategies/{strategy_id}** - Strategy dashboard

### Performance Endpoints:
- **GET /wp-json/tradingrobotplug/v1/performance** - Performance data
- **GET /wp-json/tradingrobotplug/v1/performance/{strategy_id}/metrics** - Strategy metrics
- **GET /wp-json/tradingrobotplug/v1/performance/{strategy_id}/history** - Strategy history

### Strategies Endpoints:
- **GET /wp-json/tradingrobotplug/v1/strategies** - List strategies

### Trades Endpoints:
- **GET /wp-json/tradingrobotplug/v1/trades** - List trades
- **GET /wp-json/tradingrobotplug/v1/trades/{trade_id}** - Get specific trade

### Charts Endpoints:
- **GET /wp-json/tradingrobotplug/v1/charts/performance/{strategy_id}** - Performance charts
- **GET /wp-json/tradingrobotplug/v1/charts/trades/{strategy_id}** - Trade charts

### Data Fetching Endpoints:
- **GET /wp-json/tradingrobotplug/v1/fetchdata** - Fetch data
- **GET /wp-json/tradingrobotplug/v1/fetchpolygondata** - Fetch Polygon data
- **GET /wp-json/tradingrobotplug/v1/fetchrealtime** - Fetch real-time data
- **GET /wp-json/tradingrobotplug/v1/fetchsignals** - Fetch signals
- **GET /wp-json/tradingrobotplug/v1/fetchaisuggestions** - Fetch AI suggestions
- **GET /wp-json/tradingrobotplug/v1/querystockdata** - Query stock data

### Other Endpoints:
- **POST /wp-json/tradingrobotplug/v1/waitlist** - Waitlist submission
- **POST /wp-json/tradingrobotplug/v1/contact** - Contact form submission

---

## Key Differences & Integration Points

### Architecture Separation:

1. **Trading Robot Backend API** (Python/FastAPI):
   - Located: `websites/TradingRobotPlugWeb/backend/`
   - Purpose: Core trading engine, strategy execution, order management
   - Base URL: `http://localhost:8000`
   - Framework: FastAPI

2. **TradingRobotPlug.com WordPress REST API**:
   - Located: `websites/tradingrobotplug.com/wp/wp-content/themes/tradingrobotplug-theme/inc/`
   - Purpose: WordPress frontend integration, stock data storage, dashboard data
   - Base URL: `https://tradingrobotplug.com/wp-json/tradingrobotplug/v1`
   - Framework: WordPress REST API

### Integration Scenarios:

1. **Stock Data Integration:**
   - WordPress stores stock data in `wp_trp_stock_data` table
   - WordPress provides REST endpoints for stock data
   - Trading Robot Backend may need to read from WordPress REST API or database
   - **Integration Point:** WordPress REST API → Trading Robot Backend

2. **Strategy Integration:**
   - Trading Robot Backend executes strategies
   - WordPress displays strategy performance
   - **Integration Point:** Trading Robot Backend → WordPress REST API (performance data)

3. **Trade Integration:**
   - Trading Robot Backend executes trades
   - WordPress displays trade history
   - **Integration Point:** Trading Robot Backend → WordPress REST API (trade data)

---

## Validation Checklist for Agent-4

### Documentation Completeness:
- [ ] Are all Trading Robot Backend API endpoints documented?
- [ ] Are request/response formats accurate?
- [ ] Are error responses documented?
- [ ] Are usage examples accurate and functional?

### Integration Alignment:
- [ ] Does API documentation align with TradingRobotPlug.com endpoints?
- [ ] Are integration points between backend and WordPress identified?
- [ ] Is stock data collection system (wp_trp_stock_data) integration documented?
- [ ] Are strategy and trade data integration points clear?

### Missing Documentation:
- [ ] Should WordPress REST API endpoints be documented separately?
- [ ] Should integration patterns be documented?
- [ ] Should data flow diagrams be included?
- [ ] Should authentication/authorization be documented?

### Plugin Integration:
- [ ] How do trading plugins (trading-robot-service, trp-paper-trading-stats) integrate?
- [ ] What API endpoints do plugins use?
- [ ] Are plugin integration points documented?

---

## Recommended Next Steps

1. **Agent-4 Review:**
   - Review `API_DOCUMENTATION.md` for completeness
   - Validate against TradingRobotPlug.com REST endpoints
   - Identify gaps and inconsistencies
   - Document integration requirements

2. **Agent-2 Updates:**
   - Update API documentation based on Agent-4's findings
   - Add missing endpoints or integration points
   - Clarify architecture separation (Backend vs WordPress)
   - Add integration patterns if needed

3. **Integration Roadmap (Agent-4):**
   - Document integration architecture
   - Create integration patterns document
   - Define data flow between systems
   - Create plugin integration guide

---

## Files Reference

- **API Documentation:** `websites/docs/trading_robot/API_DOCUMENTATION.md`
- **Trading Robot Backend:** `websites/TradingRobotPlugWeb/backend/`
- **WordPress Theme API:** `websites/tradingrobotplug.com/wp/wp-content/themes/tradingrobotplug-theme/inc/dashboard-api.php`
- **Stock Data Table:** `wp_trp_stock_data` (created by `trp_create_stock_data_table()`)

---

**Last Updated:** 2025-12-27 by Agent-2  
**Status:** ✅ ACTIVE - Validation Context Document  
**Purpose:** Coordination context for Agent-4 validation review

