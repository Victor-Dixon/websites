# TradingRobotPlug.com Homepage Stock Prices Fix

**Author:** Agent-2 (Architecture & Design Specialist)  
**Date:** 2025-12-27  
**Status:** ACTIVE - Issue Identified  
**Priority:** HIGH - Homepage showing outdated stock prices

<!-- SSOT Domain: documentation -->
<!-- SSOT Domain: trading_robot -->
<!-- SSOT Domain: fixes -->

---

## Issue Summary

Stock prices displayed on TradingRobotPlug.com homepage are **hardcoded** in the template file instead of fetching live data from the REST API endpoint.

**Location:** `websites/websites/tradingrobotplug.com/wp/wp-content/themes/tradingrobotplug-theme/front-page.php` (lines 31-59)

**Current Status:** ❌ Hardcoded prices (not updating)

---

## Root Cause

The homepage template contains hardcoded stock price values:

```php
<div class="market-item">
    <span class="market-symbol">SPY</span>
    <span class="market-price">$450.23</span>
    <span class="market-change positive">↑ +2.34%</span>
</div>
```

These values never update because they're static HTML in the template.

---

## Backend Infrastructure Status

✅ **REST API Endpoint Exists:**
- Endpoint: `/wp-json/tradingrobotplug/v1/stock-data`
- Function: `trp_get_stored_stock_data()` in `dashboard-api.php`
- Returns: Latest stock data for TSLA, QQQ, SPY, NVDA from database

✅ **Stock Data Collection:**
- Cron job: `trp_collect_stock_data` runs every 5 minutes
- Function: `trp_collect_stock_data_cron()` fetches from Yahoo Finance API
- Storage: `wp_trp_stock_data` database table
- Symbols: TSLA, QQQ, SPY, NVDA

✅ **Data Flow:**
1. Cron job fetches stock data from Yahoo Finance API every 5 minutes
2. Data is saved to `wp_trp_stock_data` table
3. REST API endpoint returns latest data from database

---

## Backend Bug Found

**Issue:** In `trp_get_stored_stock_data()` function (line 671 of `dashboard-api.php`):

```php
$results = $wpdb->get_results($query, ARRAY_A);
```

**Problem:** Variable `$query` is never defined. The data is already collected in the loop above, so this line should be removed.

**Fix:** Remove line 671 (it's redundant and causes an error).

---

## Solution

Replace hardcoded prices with JavaScript that fetches live data from the REST API endpoint.

### Option 1: JavaScript/AJAX Fetch (Recommended)

Replace the hardcoded HTML section (lines 31-59) with:

1. **HTML structure** with placeholder/loading state
2. **JavaScript function** that fetches from `/wp-json/tradingrobotplug/v1/stock-data`
3. **Update DOM** with fetched data
4. **Auto-refresh** every 30-60 seconds

### Option 2: PHP Template Function

Create a PHP function that fetches from the REST API or database directly, but this requires page reload to update.

---

## Implementation Details

### Required Changes

1. **Fix backend bug** (remove line 671 in `dashboard-api.php`)
2. **Update `front-page.php`** - Replace hardcoded section with dynamic content
3. **Add JavaScript** - Fetch and update stock prices
4. **Add CSS** - Handle loading/error states
5. **Test** - Verify prices update correctly

### JavaScript Implementation

```javascript
// Fetch stock data from REST API
async function fetchStockData() {
    try {
        const response = await fetch('/wp-json/tradingrobotplug/v1/stock-data');
        const data = await response.json();
        
        // Update DOM with fetched data
        if (data.stock_data && data.stock_data.length > 0) {
            data.stock_data.forEach(stock => {
                updateStockDisplay(stock.symbol, stock.price, stock.change_percent);
            });
        }
    } catch (error) {
        console.error('Error fetching stock data:', error);
        // Show error state or keep cached data
    }
}

function updateStockDisplay(symbol, price, changePercent) {
    const priceElement = document.querySelector(`[data-symbol="${symbol}"] .market-price`);
    const changeElement = document.querySelector(`[data-symbol="${symbol}"] .market-change`);
    
    if (priceElement) {
        priceElement.textContent = `$${price.toFixed(2)}`;
    }
    
    if (changeElement) {
        const isPositive = changePercent >= 0;
        changeElement.textContent = `${isPositive ? '↑' : '↓'} ${Math.abs(changePercent).toFixed(2)}%`;
        changeElement.className = `market-change ${isPositive ? 'positive' : 'negative'}`;
    }
}

// Initial fetch and auto-refresh every 30 seconds
fetchStockData();
setInterval(fetchStockData, 30000);
```

### HTML Structure Update

```html
<!-- Live Market Preview -->
<div class="market-preview">
    <h4>📈 Live Market Data</h4>
    <div class="market-items" id="market-data-container">
        <!-- Loading state -->
        <div class="market-loading">Loading market data...</div>
        <!-- Data will be populated by JavaScript -->
    </div>
    <p style="font-size: 12px; margin-top: 8px; opacity: 0.8;">
        Powered by Yahoo Finance | Updated every 5 minutes
    </p>
</div>
```

---

## Testing Checklist

- [ ] Backend bug fixed (line 671 removed)
- [ ] REST API endpoint returns correct data format
- [ ] JavaScript fetches data successfully
- [ ] Stock prices display correctly
- [ ] Change percentages display with correct positive/negative styling
- [ ] Auto-refresh works (updates every 30-60 seconds)
- [ ] Loading state displays correctly
- [ ] Error handling works (shows error or cached data)
- [ ] All 4 symbols (TSLA, QQQ, SPY, NVDA) display
- [ ] Prices update within 5 minutes after cron job runs

---

## Related Files

- **Template:** `websites/websites/tradingrobotplug.com/wp/wp-content/themes/tradingrobotplug-theme/front-page.php`
- **API:** `websites/websites/tradingrobotplug.com/wp/wp-content/themes/tradingrobotplug-theme/inc/dashboard-api.php`
- **REST Endpoint:** `/wp-json/tradingrobotplug/v1/stock-data`

---

## Assignment

**Agent-7** (Web Development) - Frontend implementation:
1. Fix backend bug (remove line 671)
2. Replace hardcoded HTML with dynamic JavaScript solution
3. Implement auto-refresh functionality
4. Test and verify prices update correctly

**Agent-2** (Architecture) - Validation:
1. Review implementation
2. Validate REST API integration
3. Verify architecture compliance

---

**Last Updated:** 2025-12-27 by Agent-2  
**Status:** 🟡 Issue Identified - Awaiting Agent-7 Implementation  
**Priority:** HIGH - Homepage functionality issue

