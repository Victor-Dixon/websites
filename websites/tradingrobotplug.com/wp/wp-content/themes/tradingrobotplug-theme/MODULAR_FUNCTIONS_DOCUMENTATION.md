# Modular Functions.php Documentation
## TradingRobotPlug Professional Dark Theme

**Version:** 2.0.0  
**Created:** 2025-12-25  
**Author:** Agent-7 (Web Development Specialist)  
**Status:** ✅ Complete - V2 Compliant Modular Architecture

---

## Architecture Overview

The theme uses a **modular functions.php architecture** following V2 compliance principles:
- **Main loader:** `functions.php` (56 lines) - Clean, organized includes
- **Modular components:** All under `inc/` directory
- **V2 Compliance:** All files < 300 lines, all functions < 30 lines
- **Professional Dark Theme:** Complete CSS variable system

---

## File Structure

```
tradingrobotplug-theme/
├── functions.php                    # Main loader (56 lines)
├── variables.css                    # Dark theme CSS variables
├── inc/
│   ├── theme-setup.php             # WordPress theme supports
│   ├── asset-enqueue.php           # Styles & scripts
│   ├── rest-api.php                # Trading data endpoints
│   ├── analytics.php               # GA4/Pixel tracking
│   ├── forms.php                   # Form handlers
│   └── template-helpers.php        # Template loading fixes
├── admin/
│   ├── theme-options.php           # Theme options
│   └── shortcodes.php              # Custom shortcodes
└── assets/
    ├── css/
    │   └── custom.css              # Component styles
    └── js/
        └── main.js                 # Main JavaScript
```

---

## Module Descriptions

### 1. `functions.php` - Main Loader
**Purpose:** Central entry point that loads all modular components  
**Lines:** 56  
**Key Features:**
- Defines theme constants (version, paths)
- Loads all modules in logical order
- Includes existing admin files conditionally

### 2. `inc/theme-setup.php` - Theme Configuration
**Purpose:** WordPress theme supports and navigation menus  
**Lines:** 48  
**Key Features:**
- Title tag support
- Post thumbnails
- Custom logo with flexible dimensions
- HTML5 support
- Navigation menu registration (primary, footer)

### 3. `inc/asset-enqueue.php` - Asset Management
**Purpose:** Enqueue stylesheets and JavaScript  
**Lines:** 47  
**Key Features:**
- Main stylesheet (style.css)
- Custom CSS (assets/css/custom.css)
- Dark theme variables (variables.css)
- Main JavaScript with AJAX localization
- Cache busting via version numbers

### 4. `inc/rest-api.php` - Trading Data API
**Purpose:** REST API endpoints for trading data  
**Lines:** ~280  
**Key Features:**
- Alpha Vantage data endpoint
- Polygon data endpoint
- Real-time data endpoint
- Trading signals endpoint
- AI suggestions endpoint
- Stock data query endpoint
- Public access authentication for trading endpoints

### 4b. `inc/dashboard-api.php` - Dashboard REST API
**Purpose:** Dashboard endpoints for trading performance platform  
**Lines:** 250  
**Key Features:**
- Dashboard overview endpoint (`GET /dashboard/overview`)
- Strategy dashboard endpoint (`GET /dashboard/strategies/{strategy_id}`)
- Performance metrics endpoint (`GET /performance/{strategy_id}/metrics`)
- Performance history endpoint (`GET /performance/{strategy_id}/history`)
- Trades endpoint (`GET /trades`)
- Trade details endpoint (`GET /trades/{trade_id}`)

### 5. `inc/analytics.php` - Analytics Integration
**Purpose:** GA4 and Facebook Pixel tracking  
**Lines:** 89  
**Key Features:**
- GA4 gtag.js integration
- Facebook Pixel code
- CTA click tracking
- Form submission tracking
- Combined event tracking for both platforms

### 6. `inc/forms.php` - Form Handlers
**Purpose:** Waitlist and contact form processing  
**Lines:** 60  
**Key Features:**
- Waitlist signup handler (admin-post.php)
- Contact form handler (Tier 1 Quick Win WEB-04)
- Nonce verification for security
- Email sanitization
- Redirect to thank-you page

### 7. `inc/template-helpers.php` - Template Loading
**Purpose:** Template loading fixes and 404 handling  
**Lines:** 106  
**Key Features:**
- Custom page template mapping
- 404 fallback handling
- Page slug detection from URL
- Cache clearing on theme activation
- LiteSpeed Cache support

### 8. `variables.css` - Dark Theme System
**Purpose:** Professional dark theme CSS variables  
**Lines:** 173  
**Key Features:**
- Complete dark color palette (GitHub-style)
- Trading platform accent colors (cyan, purple, green, red)
- Typography system
- Spacing system (8px base)
- Border radius system
- Shadow system (dark theme optimized)
- Chart colors for trading data visualization
- Status colors for trading signals

---

## V2 Compliance Checklist

✅ **Main functions.php:** 56 lines (< 300 limit)  
✅ **All modules:** < 300 lines each  
✅ **All functions:** < 30 lines each  
✅ **Modular structure:** Clear separation of concerns  
✅ **No circular dependencies:** Linear loading order  
✅ **Single responsibility:** Each module has one purpose  
✅ **Professional documentation:** Clear comments and structure  

---

## Dark Theme Color System

### Primary Backgrounds
- `--color-bg-primary: #0d1117` - Main background (GitHub-style dark)
- `--color-bg-secondary: #161b22` - Elevated surfaces
- `--color-bg-tertiary: #21262d` - Hover states, borders
- `--color-bg-elevated: #1c2128` - Cards, modals

### Accent Colors
- `--color-accent-primary: #00e5ff` - Cyan (Primary CTA)
- `--color-accent-secondary: #7c4dff` - Purple (Secondary actions)
- `--color-accent-success: #00e676` - Green (Gains, positive)
- `--color-accent-danger: #ff5252` - Red (Losses, alerts)

### Text Colors
- `--color-text-primary: #f0f6fc` - Primary text
- `--color-text-secondary: #c9d1d9` - Secondary text
- `--color-text-muted: #8b949e` - Muted/disabled

### Trading-Specific
- Chart colors for candlestick visualization
- Status colors for buy/sell/hold signals
- Profit/loss color system

---

## Integration Points

### REST API Endpoints
All endpoints under `/wp-json/tradingrobotplug/v1/`:
- `/fetchdata` - Alpha Vantage data
- `/fetchpolygondata` - Polygon data
- `/fetchrealtime` - Real-time data
- `/fetchsignals` - Trading signals (authenticated)
- `/fetchaisuggestions` - AI suggestions (authenticated)
- `/querystockdata` - Stock data query

### Form Actions
- `handle_waitlist_signup` - Waitlist form
- `handle_contact_form` - Contact form (WEB-04)

### JavaScript Localization
- `trpData.ajaxUrl` - AJAX endpoint
- `trpData.restUrl` - REST API base URL
- `trpData.nonce` - Security nonce

---

## Usage Examples

### Accessing REST API from JavaScript

```javascript
fetch(trpData.restUrl + 'fetchdata')
    .then(response => response.json())
    .then(data => console.log(data));
```

### Using Dark Theme Variables in CSS

```css
.custom-component {
    background-color: var(--color-bg-secondary);
    color: var(--color-text-primary);
    border: 1px solid var(--color-border-primary);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-md);
}
```

### Using Trading Status Colors

```css
.profit {
    color: var(--color-status-buy);
}

.loss {
    color: var(--color-status-sell);
}
```

---

## Next Steps

1. **Integrate with Agent-2 Architecture:** Align with automated trading tools platform design
2. **Connect to Agent-1 Integration:** Trading robot data pipeline integration
3. **Connect to Agent-5 Analytics:** Performance tracking implementation
4. **Connect to Agent-3 Infrastructure:** Deployment automation setup

---

## Maintenance Notes

- **Cache Busting:** Theme version (`TRP_THEME_VERSION`) automatically busts cache
- **Module Updates:** Each module can be updated independently
- **V2 Compliance:** Maintain file/function line limits
- **Dark Theme:** Extend variables.css for new components

---

**Status:** ✅ Production Ready  
**Compliance:** ✅ V2 Standards  
**Theme:** ✅ Professional Dark Theme  
**Documentation:** ✅ Complete

