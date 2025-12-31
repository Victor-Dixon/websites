# Publishing Notes (TradingRobotPlug)

- Publishing attempts from this environment failed due to proxy restrictions (CONNECT tunnel 403) when calling:
  - https://tradingrobotplug.com/wp-json/wp/v2/categories
  - https://tradingrobotplug.com/wp-json/wp/v2/tags
  - https://tradingrobotplug.com/wp-json/wp/v2/posts
- Use credentials from `configs/site_configs.json` under `tradingrobotplug.com` → `rest_api`.
- Recommended local publish flow:
  1. Create/verify categories: Execution, Algorithmic Trading.
  2. Create/verify tags: TSLA, VWAP, MACD curl, Bollinger Bands, RSI, tight stops, TradingView indicator, scalping, options.
  3. POST the HTML content from `content/blog_posts/tradingrobotplug/2025-12-31--tbow-permission-macd-curl-v0-1.html` to `/wp-json/wp/v2/posts` with status `draft`.
  4. Confirm `status=draft`, capture `post_id` and `link`.
