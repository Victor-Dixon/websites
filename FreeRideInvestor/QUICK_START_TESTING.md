# 🚀 Quick Start - WordPress Plugin Testing

**For:** FreeRideInvestor  
**Time to Setup:** 5 minutes  
**Purpose:** Test plugins with real data

---

## ⚡ **SUPER QUICK START (3 Steps)**

### **Step 1: Start Environment (1 min)**

```bash
# Navigate to project
cd D:\websites\FreeRideInvestor

# Start Docker containers
docker-compose up -d
```

### **Step 2: Access WordPress (1 min)**

**Open browser:**
- WordPress: http://localhost:8080
- Admin: http://localhost:8080/wp-admin
- Login: `admin` / `test_admin_123`

### **Step 3: Test a Plugin (3 min)**

```bash
# Quick test any plugin
.\quick-plugin-test.ps1 -PluginName "your-plugin-slug"
```

**Done!** 🎉

---

## 📋 **MANUAL TESTING (15 min per plugin)**

### **1. Activate Plugin**
- Go to Plugins → Find your plugin → Activate
- Check for errors

### **2. Configure Plugin**
- Find settings page
- Configure options
- Save and verify

### **3. Test Frontend**
- Visit site: http://localhost:8080
- Check if plugin works
- Open F12 console (check for errors)

### **4. Test Backend**
- Use plugin features in admin
- Create content
- Verify database writes

### **5. Test with Real Data**
```bash
# Import your real data
docker-compose exec -T db mysql -u wordpress -pwordpress_password freerider_test < your-backup.sql
```

### **6. Document Results**
- Use checklist in PLUGIN_TESTING_MANUAL.md
- Update PLUGIN_TESTING_SUMMARY.md

---

## 🛠️ **USEFUL COMMANDS**

### **Plugin Management:**
```bash
# List all plugins
docker-compose exec wpcli plugin list

# Activate plugin
docker-compose exec wpcli plugin activate PLUGIN_NAME

# Deactivate plugin
docker-compose exec wpcli plugin deactivate PLUGIN_NAME

# Delete plugin
docker-compose exec wpcli plugin delete PLUGIN_NAME
```

### **Database:**
```bash
# Backup database
docker-compose exec db mysqldump -u wordpress -pwordpress_password freerider_test > backup.sql

# Restore database
docker-compose exec -T db mysql -u wordpress -pwordpress_password freerider_test < backup.sql

# Access PHPMyAdmin
# Open: http://localhost:8081
```

### **Logs:**
```bash
# WordPress logs
docker-compose logs -f wordpress

# All logs
docker-compose logs -f

# Just errors
docker-compose logs wordpress | grep -i error
```

---

## 🔄 **ENVIRONMENT MANAGEMENT**

### **Start:**
```bash
docker-compose up -d
```

### **Stop:**
```bash
docker-compose down
```

### **Reset (fresh start):**
```bash
docker-compose down -v  # Deletes database!
docker-compose up -d
# Then run setup again
```

### **Restart:**
```bash
docker-compose restart
```

---

## 📊 **TESTING CHECKLIST (Quick)**

**For each plugin:**
- [ ] Activates without errors ✅
- [ ] Settings page works ✅
- [ ] Frontend output works ✅
- [ ] Backend features work ✅
- [ ] Real data processes correctly ✅
- [ ] No JavaScript errors ✅
- [ ] Performance acceptable ✅

**Rating:**
- ⭐⭐⭐⭐⭐ Production Ready
- ⭐⭐⭐⭐ Minor fixes needed
- ⭐⭐⭐ Major work needed
- ⭐⭐ Significant issues
- ⭐ Do not use

---

## 🎯 **COMMON ISSUES & FIXES**

### **Issue: Plugin won't activate**
```bash
# Check PHP errors
docker-compose logs wordpress | grep -i error

# Check plugin code for syntax errors
docker-compose exec wpcli plugin verify-checksums PLUGIN_NAME
```

### **Issue: White screen**
```bash
# Enable debugging
# Add to wp-config.php:
# define('WP_DEBUG', true);
# define('WP_DEBUG_LOG', true);

# Check debug log
docker-compose exec wordpress cat /var/www/html/wp-content/debug.log
```

### **Issue: Database connection error**
```bash
# Restart database
docker-compose restart db

# Check database is running
docker-compose ps
```

---

## 📝 **DAILY WORKFLOW**

### **Morning (5 min):**
1. `docker-compose up -d`
2. Check logs: `docker-compose logs -f wordpress`
3. Open http://localhost:8080/wp-admin

### **Testing (2-3 hours):**
1. Test 2-3 plugins
2. Document results
3. Fix issues or log them

### **Evening (2 min):**
1. Update PLUGIN_TESTING_SUMMARY.md
2. `docker-compose down`

---

## 🐝 **NEED HELP?**

**Check:**
- PLUGIN_TESTING_MANUAL.md (detailed guide)
- docker-compose.yml (environment config)
- test-environment-setup.sh (automation script)

**Or ask agents for help!**

---

**Happy Testing!** 🚀


