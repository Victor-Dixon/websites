# Dreamscape Episode Blog Generator Debug Report

## Issue Summary
The D:\websites blog generator for dreamscape episodes is not posting to the live server due to multiple configuration and setup issues.

## Root Cause Analysis

### 1. **Missing WordPress Authentication Environment Variables** 🚨 CRITICAL
**Status:** Environment variables not configured
**Impact:** Cannot authenticate with WordPress REST API
**Evidence:**
- `DREAM_WP_URL`: NOT SET
- `DREAM_WP_USER`: NOT SET
- `DREAM_WP_APP_PASS`: NOT SET

**Required Action:** Set environment variables in `.env` file:
```
DREAM_WP_URL=https://digitaldreamscape.site
DREAM_WP_USER=your_wordpress_username
DREAM_WP_APP_PASS=your_wordpress_app_password
```

### 2. **Incomplete Site Configuration** 🚨 CRITICAL
**Status:** Missing calendar file and incomplete backlog setup
**Impact:** Autoblogger cannot select posts to generate
**Evidence:**
- Calendar file missing: `content/calendars/dream.yaml` ❌
- Backlog items missing `status: ready` field ❌
- No autoblogger state file exists (never run) ❌

**Required Action:** Create calendar file and update backlog items.

### 3. **Backlog Item Status Configuration** ⚠️ HIGH PRIORITY
**Status:** Backlog items exist but lack status field
**Evidence:**
```yaml
items:
  - title: "Building in Public: The Ultimate Guide for 2025"
    audience: indie_hackers
    pillar: build_in_public
    angle: "comprehensive guide"
    keywords: ["build in public", "indie hacker guide", "transparency marketing"]
    cta: "Share your journey"
    # MISSING: status: ready
```

**Required Action:** Add `status: ready` to all backlog items.

## System Architecture Overview

### Autoblogger Flow
1. **Configuration Loading** → `sites/dream.yaml`
2. **Content Selection** → Calendar or backlog with status "ready"
3. **AI Generation** → LLM creates blog post from templates
4. **WordPress Publishing** → REST API authentication and posting
5. **State Tracking** → Updates used items and history

### Authentication Flow
```
Environment Variables → WordPress REST API → Authentication → Post Creation
                      ❌ BROKEN HERE (Missing env vars)
```

## Required Fixes

### Immediate Actions (Priority Order)

#### 1. **Create Environment Configuration** 🔧
```bash
# Create .env file in websites root
echo "DREAM_WP_URL=https://digitaldreamscape.site" > .env
echo "DREAM_WP_USER=your_username" >> .env
echo "DREAM_WP_APP_PASS=your_app_password" >> .env
```

#### 2. **Create Calendar File** 📅
Create `content/calendars/dream.yaml`:
```yaml
schedule:
  "2026-01-04": "building-in-public-guide"
  "2026-01-05": "stream-monetization-guide"
```

#### 3. **Update Backlog Items** 📝
Modify `content/backlogs/dream.yaml`:
```yaml
items:
  - title: "Building in Public: The Ultimate Guide for 2025"
    audience: indie_hackers
    pillar: build_in_public
    angle: "comprehensive guide"
    keywords: ["build in public", "indie hacker guide", "transparency marketing"]
    cta: "Share your journey"
    status: ready  # ← ADD THIS

  - title: "How to Monetize Your Stream Without Selling Out"
    audience: streamers
    pillar: creator_economy
    angle: "ethical monetization"
    keywords: ["stream monetization", "creator economy tips", "ethical marketing"]
    cta: "Join our creator community"
    status: ready  # ← ADD THIS
```

#### 4. **Test Configuration** 🧪
```bash
# Test environment variables
python -c "import os; print('WP URL:', os.environ.get('DREAM_WP_URL', 'MISSING'))"

# Test autoblogger dry run
python -m src.autoblogger.run_daily --site dream --dry-run
```

## Diagnostic Commands

### Check Current Status
```bash
# Environment variables
python -c "import os; [print(f'{k}: {os.environ.get(k, \"NOT SET\")}') for k in ['DREAM_WP_URL', 'DREAM_WP_USER', 'DREAM_WP_APP_PASS']]"

# Backlog status
python -c "import yaml; data=yaml.safe_load(open('content/backlogs/dream.yaml')); print(f'Items: {len(data.get(\"items\", []))}')"

# Calendar status
ls content/calendars/dream.yaml 2>/dev/null && echo "Calendar exists" || echo "Calendar missing"
```

### Run Autoblogger Test
```bash
# Dry run (generates prompt but doesn't publish)
python -m src.autoblogger.run_daily --site dream --dry-run

# Full run with publishing (when env vars are set)
python -m src.autoblogger.run_daily --site dream --auto-publish --wp-status publish
```

## Expected Resolution Timeline

1. **Immediate (5 mins)**: Set environment variables
2. **Quick (10 mins)**: Create calendar file and update backlog
3. **Test (5 mins)**: Verify autoblogger can run
4. **Deploy (2 mins)**: Enable auto-publishing

## Monitoring & Verification

### Success Indicators
- ✅ Environment variables loaded without errors
- ✅ Autoblogger selects posts from backlog/calendar
- ✅ WordPress REST API authentication succeeds
- ✅ Posts appear on digitaldreamscape.site
- ✅ Autoblogger state file created with history

### Error Prevention
- Regular environment variable validation
- Backlog status monitoring
- WordPress API health checks
- Automated retry mechanisms

## Recommendations

1. **Environment Management**: Use centralized `.env` file management
2. **Configuration Validation**: Add startup checks for required configs
3. **Monitoring Dashboard**: Create web interface for autoblogger status
4. **Backup Systems**: Implement post backup and recovery mechanisms
5. **Documentation**: Update setup guides with environment configuration steps

---

**Status:** 🔍 **ISSUES IDENTIFIED** - Multiple configuration problems preventing blog generation and publishing to live server. Fixes outlined above will resolve the issues.