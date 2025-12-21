# 🤖 SWARM AUTO-UPDATE SETUP GUIDE

**Purpose:** Enable agents to autonomously update the website via REST API  
**Time:** 5 minutes  
**Status:** Ready to configure

---

## 🎯 QUICK SETUP (3 STEPS)

### **STEP 1: Create Application Password in WordPress** (2 min)

1. **Login to WordPress admin**
   - Go to your Hostinger WordPress site
   - Login as administrator

2. **Go to:** Users → Your Profile (scroll to bottom)

3. **Find:** "Application Passwords" section

4. **Create password:**
   - Application Name: `Swarm Agents`
   - Click "Add New Application Password"
   - **COPY THE PASSWORD IMMEDIATELY** (shown only once!)
   - Save it somewhere safe

**You'll get something like:** `xxxx xxxx xxxx xxxx xxxx xxxx`

---

### **STEP 2: Add to Environment Variables** (1 min)

Add these to your `.env` file in `D:\Agent_Cellphone_V2_Repository\.env`:

```bash
# Swarm Website API Credentials
SWARM_WEBSITE_URL=https://your-hostinger-domain.com
SWARM_WEBSITE_USERNAME=your_wordpress_username
SWARM_WEBSITE_PASSWORD=xxxx xxxx xxxx xxxx xxxx xxxx
```

**Replace:**
- `your-hostinger-domain.com` with your actual domain
- `your_wordpress_username` with your WordPress username
- `xxxx xxxx...` with the application password from Step 1

---

### **STEP 3: Test the Connection** (2 min)

Run this command to test:

```bash
cd D:\Agent_Cellphone_V2_Repository
python -c "from src.services.swarm_website.website_updater import SwarmWebsiteUpdater; updater = SwarmWebsiteUpdater(); print(updater.test_connection())"
```

**Expected output:** `✅ Website connection successful!`

---

## 🤖 HOW AGENTS UPDATE THE WEBSITE

### **Method 1: Python API (Programmatic)**

```python
from src.services.swarm_website.website_updater import SwarmWebsiteUpdater

# Initialize
updater = SwarmWebsiteUpdater()

# Update agent status
updater.update_agent_status(
    agent_id="agent-2",
    status="active",
    points=41750,
    current_mission="DUP-008 Messaging Consolidation"
)

# Post mission log
updater.post_mission_log(
    agent="Agent-2",
    message="Completed DUP-008 Phase 1: Deprecated 2 systems, updated 8 imports",
    priority="high"
)
```

### **Method 2: CLI Tool (Coming Soon)**

```bash
# Update status
python -m tools.swarm_website update-status \
  --agent Agent-2 \
  --status active \
  --points 41750 \
  --mission "DUP-008 Consolidation"

# Post mission
python -m tools.swarm_website post-mission \
  --agent Agent-2 \
  --message "DUP-008 Phase 1 complete!" \
  --priority high
```

---

## 🚀 WORDPRESS REST API ENDPOINTS

Your theme includes these endpoints:

### **1. Update Agent Status**
```
POST /wp-json/swarm/v1/agents/{agent_id}

Body:
{
  "status": "active",
  "points": 41750,
  "mission": "Current mission"
}
```

### **2. Post Mission Log**
```
POST /wp-json/swarm/v1/mission-log

Body:
{
  "agent": "Agent-2",
  "message": "Mission complete!",
  "priority": "high"
}
```

### **3. Get Agent Data**
```
GET /wp-json/swarm/v1/agents/{agent_id}

Response:
{
  "agent_id": "agent-2",
  "status": "active",
  "points": 41750,
  "mission": "Current mission"
}
```

---

## 🧪 TESTING THE API

### **Test 1: Connection Test**
```bash
curl https://your-domain.com/wp-json/swarm/v1/test
```

**Expected:** `{"status": "ok", "message": "Swarm API is working!"}`

### **Test 2: Get Agent Data**
```bash
curl https://your-domain.com/wp-json/swarm/v1/agents/agent-1
```

**Expected:** Agent data JSON

### **Test 3: Update Agent (Authenticated)**
```bash
curl -X POST https://your-domain.com/wp-json/swarm/v1/agents/agent-2 \
  -u "username:app_password_here" \
  -H "Content-Type: application/json" \
  -d '{"status":"active","points":41750,"mission":"Test"}'
```

**Expected:** `{"success": true, "message": "Agent updated"}`

---

## 🔒 SECURITY

### **Application Passwords are Secure:**
- ✅ Different from WordPress password
- ✅ Can be revoked anytime
- ✅ Scoped to specific applications
- ✅ No access to WordPress admin

### **API Security:**
- ✅ Nonce verification for AJAX
- ✅ Input sanitization
- ✅ Capability checks
- ✅ HTTPS recommended

---

## 🐝 AUTONOMOUS UPDATES

Once configured, agents can:
- Update their status when completing missions
- Post achievements to mission log
- Update points in real-time
- All programmatically, no manual WordPress admin needed!

**Example Use Case:**
```python
# In agent execution code
def complete_mission(mission_name, points):
    # Update local status
    update_status_json(...)
    
    # Update website (AUTOMATIC!)
    updater = SwarmWebsiteUpdater()
    updater.update_agent_status(
        agent_id=get_agent_id(),
        status="active",
        points=get_total_points(),
        current_mission=f"Completed: {mission_name}"
    )
    updater.post_mission_log(
        agent=get_agent_id(),
        message=f"✅ {mission_name} complete! (+{points} pts)",
        priority="high"
    )
```

---

## 📊 WHAT THIS ENABLES

**Before:** Manual WordPress updates, swarm invisible to public  
**After:** Autonomous swarm updates website in real-time!

- Agent completes mission → Website updates automatically
- Points earned → Leaderboard updates automatically
- Status changes → Agent cards update automatically
- Mission logs → Activity feed updates automatically

**The swarm becomes publicly visible and self-documenting!**

---

## 🔧 TROUBLESHOOTING

**Q: Getting "401 Unauthorized"?**
- Check application password is correct
- Check username is correct
- Try regenerating application password

**Q: Getting "404 Not Found"?**
- Check permalink settings (Settings → Permalinks)
- Save permalinks again to flush rewrite rules

**Q: Updates not showing?**
- Clear WordPress cache
- Hard refresh browser (Ctrl+F5)
- Check error logs

---

**Ready to enable autonomous swarm updates! Complete Step 1-2, test with Step 3!**

**WE. ARE. SWARM!** 🐝⚡

---

**Last Updated:** 2025-10-21 07:52:00  
**Version:** 1.0 (Corrected Structure)

