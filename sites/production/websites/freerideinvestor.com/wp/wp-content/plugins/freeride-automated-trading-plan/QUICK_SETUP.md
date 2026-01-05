# Quick Setup Guide - See It On Your Site

## Step 1: Activate the Plugin

1. Go to WordPress Admin → Plugins
2. Find "FreeRide Automated Trading Plan"
3. Click "Activate"

## Step 2: Configure Basic Settings

1. Go to **Trading Plans → Settings**
2. Enter your API keys (Alpha Vantage or Finnhub)
3. Set stock symbols (e.g., "TSLA")
4. Click "Save Settings"

## Step 3: Create Pages (Auto-Created on Activation)

The plugin automatically creates these pages on activation:
- `/premium-signup` - Premium signup page
- `/trading-plans` - Plans listing page

If they don't exist, create them manually:

### Premium Signup Page
- Create new page: "Premium Signup"
- Slug: `premium-signup`
- Add shortcode: `[fratp_premium_signup]`
- Publish

### Trading Plans Page
- Create new page: "Trading Plans"
- Slug: `trading-plans`
- Add shortcode: `[fratp_plans_list]`
- Publish

## Step 4: Generate Your First Plan

### Option A: Manual Generation (Quick Test)
1. Go to **Trading Plans → Settings**
2. Scroll to "Manual Plan Generation"
3. Enter symbol: `TSLA`
4. Check "Create TBOW Post"
5. Click "Generate Plan"

### Option B: Wait for Automatic Generation
- Plans generate daily at 9:30 AM EST
- Or trigger manually via cron

## Step 5: View on Frontend

### Test Premium Signup Page
Visit: `https://yoursite.com/premium-signup`

You should see:
- Premium pricing
- Features list
- Sign up button

### Test Trading Plans Page
Visit: `https://yoursite.com/trading-plans`

You should see:
- List of generated plans (if any)
- Or upgrade prompt if not premium

### Test Individual Plan
Create a test page with: `[fratp_daily_plan symbol="TSLA"]`

## Step 6: Test Access Control

1. **Not Logged In**: Should see "Access Denied" with signup prompt
2. **Free Member**: Should see upgrade prompt
3. **Premium Member**: Should see full plan

## Quick Test Checklist

- [ ] Plugin activated
- [ ] API keys configured
- [ ] Stock symbols set
- [ ] Premium signup page created/visible
- [ ] Trading plans page created/visible
- [ ] First plan generated (manual or automatic)
- [ ] Can see premium signup page on frontend
- [ ] Access control working (try viewing plan while logged out)

## Troubleshooting

### Can't see pages?
- Check if pages were created: WordPress Admin → Pages
- If not, create them manually with the shortcodes above

### Plans not generating?
- Check API keys are valid
- Check stock symbol is correct
- Check error logs: WordPress Admin → Tools → Site Health

### Can't see plans?
- Make sure you're logged in
- Check if you have premium access (or test as admin)
- Verify plan was generated successfully

## View URLs

After setup, you can view:
- Premium Signup: `/premium-signup`
- Trading Plans: `/trading-plans`
- Individual Plan: Create page with `[fratp_daily_plan symbol="TSLA"]`

## Need Help?

Check the main README.md for detailed documentation.

