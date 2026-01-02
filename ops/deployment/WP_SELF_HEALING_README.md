# 🚑 WordPress Self-Healing System

**Location:** `ops/deployment/`  
**Purpose:** Intelligent WordPress error monitoring and automatic healing

## 🎯 What It Does

The WordPress Self-Healing System is an intelligent monitoring and repair system that:

- ✅ **Monitors WP_DEBUG logs** in real-time across all sites
- ✅ **Automatically detects and categorizes** WordPress errors
- ✅ **Applies intelligent fixes** for known error patterns
- ✅ **Tests fixes and rolls back** if they fail
- ✅ **Integrates with deployment pipeline** for seamless operation
- ✅ **Provides comprehensive notifications** and reporting

## 🏗️ System Architecture

### Core Components

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Error Monitor │───▶│  Self-Healing   │───▶│  Notification   │
│   Daemon        │    │  Engine         │    │  System         │
└─────────────────┘    └──────────────────┘    └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│  WP_DEBUG Logs  │    │  Fix Patterns    │    │  Slack/Email/   │
│  Real-time      │    │  Database        │    │  Discord        │
└─────────────────┘    └──────────────────┘    └─────────────────┘
```

### Key Files

- **`wp_debug_self_healing.py`** - Core self-healing engine
- **`wp_error_monitor.py`** - Real-time monitoring daemon
- **`wp_monitor_config.json`** - Configuration file
- **`start_wp_self_healing.sh`** - Startup script

## 🚀 Quick Start

### 1. Enable Self-Healing

```bash
# Start the complete self-healing system
./ops/start_wp_self_healing.sh

# Or test first (dry run)
./ops/start_wp_self_healing.sh --dry-run
```

### 2. Check System Status

```bash
# Show current status
./ops/start_wp_self_healing.sh --status

# View healing reports
python ops/deployment/wp_debug_self_healing.py --report
```

### 3. Manual Healing

```bash
# Run healing cycle on specific site
python ops/deployment/wp_debug_self_healing.py --site dadudekc.com

# Run healing on all sites
python ops/deployment/wp_debug_self_healing.py --sites freerideinvestor.com dadudekc.com
```

## 🔧 Supported Error Types

### PHP Errors
- **Syntax Errors**: Automatic PHP syntax correction
- **Undefined Functions**: Function existence checks and conditional loading
- **Memory Limit**: Automatic memory limit increases

### WordPress Errors
- **Plugin Activation Failures**: Automatic plugin deactivation
- **Theme Errors**: Theme rollback to stable version
- **Database Connection**: Connection diagnostics (manual intervention required)

### File System Errors
- **Permission Errors**: File permission corrections
- **Missing Files**: File existence checks and path corrections

## ⚙️ Configuration

### Monitor Configuration (`config/wp_monitor_config.json`)

```json
{
  "check_interval_seconds": 60,
  "error_threshold": 5,
  "critical_error_threshold": 1,
  "enable_auto_healing": true,
  "enable_notifications": true,

  "error_patterns": {
    "php_syntax_error": {
      "enabled": true,
      "auto_heal": true,
      "priority": "high"
    }
  },

  "sites": {
    "dadudekc.com": {
      "monitoring_enabled": true,
      "healing_enabled": true,
      "priority": "high"
    }
  }
}
```

### Notification Configuration

Configure notifications in the config file:
- **Email**: SMTP-based notifications
- **Slack**: Webhook notifications
- **Discord**: Webhook notifications
- **Console**: Local terminal output

## 📊 Monitoring & Reporting

### Real-Time Monitoring

```bash
# Start monitoring daemon
python ops/deployment/wp_error_monitor.py --start

# Check monitoring status
python ops/deployment/wp_error_monitor.py --status

# Stop monitoring
python ops/deployment/wp_error_monitor.py --stop
```

### Healing Reports

```bash
# View latest healing report
python ops/deployment/wp_debug_self_healing.py --report

# Enable debug logging for specific site
python ops/deployment/wp_error_monitor.py --enable-debug dadudekc.com
```

## 🔄 Integration with Deployment Pipeline

The self-healing system integrates seamlessly with the deployment pipeline:

1. **Pre-deployment**: WP_DEBUG enabled automatically
2. **Post-deployment**: Self-healing verification runs
3. **Error Detection**: Real-time monitoring catches deployment issues
4. **Auto-Healing**: Failed deployments trigger immediate healing attempts
5. **Notifications**: Deployment status integrated with healing reports

### Pipeline Integration

```python
# In deployment pipeline
from wp_debug_self_healing import WPSelfHealingSystem

# Enable debug and healing
healing = WPSelfHealingSystem()
healing.enable_wp_debug(site)
errors = healing.monitor_debug_logs(site)
healing_actions = healing.apply_self_healing(site, errors)
```

## 🛡️ Safety & Rollback

### Automatic Safety Measures

- **Backup Creation**: All fixes create backups before modification
- **Rollback Capability**: Failed fixes automatically roll back
- **Testing**: Fixes are tested before being considered successful
- **Cooldown Periods**: Prevents healing spam after failures
- **Escalation Policies**: Critical errors trigger manual intervention

### Manual Rollback

```bash
# List available backups
python ops/deployment/deployment_rollback.py --list-backups dadudekc.com

# Rollback to specific version
python ops/deployment/deployment_rollback.py --rollback dadudekc.com --version 20260101_120000

# Cleanup old backups
python ops/deployment/deployment_rollback.py --cleanup dadudekc.com
```

## 📈 Performance & Scaling

### Monitoring Metrics

- **Error Detection Rate**: Errors caught per minute
- **Healing Success Rate**: Percentage of successful auto-fixes
- **Response Time**: Time from error detection to fix application
- **Uptime Impact**: System performance monitoring

### Scaling Considerations

- **Concurrent Healing**: Configurable maximum concurrent healing operations
- **Site Prioritization**: High-priority sites healed first
- **Resource Limits**: Memory and CPU usage monitoring
- **Queue Management**: Healing queues for high-traffic periods

## 🚨 Error Escalation

### Escalation Levels

1. **Level 1**: Auto-healing successful - logged only
2. **Level 2**: Auto-healing failed - notification sent
3. **Level 3**: Critical error threshold reached - immediate alert
4. **Level 4**: Multiple failures - manual intervention required
5. **Level 5**: System-wide issues - emergency shutdown

### Manual Intervention Triggers

- 5+ failed healing attempts in 1 hour
- Critical database or security errors
- File system corruption detected
- Memory exhaustion preventing healing

## 🔧 Troubleshooting

### Common Issues

```bash
# Check if monitoring daemon is running
ps aux | grep wp_error_monitor

# View recent errors
tail -f wp_monitor.log

# Test healing on specific error
python ops/deployment/wp_debug_self_healing.py --test-fix php_syntax_error

# Restart monitoring daemon
./ops/start_wp_self_healing.sh --stop
./ops/start_wp_self_healing.sh
```

### Debug Mode

```bash
# Enable verbose logging
export WP_HEALING_DEBUG=1

# Test specific components
python ops/deployment/wp_debug_self_healing.py --test
python ops/deployment/wp_error_monitor.py --check-now
```

## 📋 Best Practices

### Configuration
1. **Start Small**: Enable healing for one site first
2. **Monitor Closely**: Review healing reports daily initially
3. **Gradual Rollout**: Increase automation as confidence grows
4. **Regular Backups**: Ensure backup systems are working

### Maintenance
1. **Update Patterns**: Regularly update error pattern database
2. **Review Logs**: Weekly review of healing activities
3. **Performance Monitoring**: Track system impact
4. **Backup Verification**: Test backup/rollback functionality

### Security
1. **Access Control**: Limit who can modify healing configurations
2. **Audit Logging**: All healing actions are logged
3. **Rollback Security**: Backups are encrypted and access-controlled
4. **Notification Security**: Secure webhook URLs and credentials

## 🎯 Success Metrics

### Key Performance Indicators

- **Mean Time to Detection**: How quickly errors are caught
- **Mean Time to Repair**: How quickly errors are fixed
- **Healing Success Rate**: Percentage of errors auto-fixed
- **False Positive Rate**: Legitimate errors incorrectly flagged
- **System Uptime**: WordPress site availability
- **User Impact**: Reduction in user-reported issues

### Expected Outcomes

- **80%+** of common WordPress errors auto-fixed
- **<5 minutes** average time from error to fix
- **99.9%** system uptime maintained
- **90%+** reduction in manual intervention required

---

## 🚀 Getting Started Checklist

- [ ] Review and customize `config/wp_monitor_config.json`
- [ ] Configure notification channels (Slack/Discord/Email)
- [ ] Test system with `./ops/start_wp_self_healing.sh --dry-run`
- [ ] Start monitoring with `./ops/start_wp_self_healing.sh`
- [ ] Monitor initial healing reports
- [ ] Gradually enable more sites and error types
- [ ] Set up regular report reviews

**🎉 Your WordPress sites now have intelligent self-healing capabilities!**