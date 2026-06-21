# 🚀 Comprehensive Website Deployment System

**Location:** `ops/deployment/`  
**Purpose:** Automated deployment pipeline for all WordPress websites with validation, rollback, and monitoring

## 🎯 Key Features

- ✅ **Automated Deployment** - Triggers on every git commit/push
- ✅ **PHP Syntax Validation** - Prevents broken deployments
- ✅ **Parallel Processing** - Deploys to multiple sites simultaneously
- ✅ **Rollback Capability** - Automatic rollback on failures
- ✅ **Health Monitoring** - Post-deployment verification
- ✅ **Multi-Channel Notifications** - Email, Slack, Discord
- ✅ **Comprehensive Logging** - Full deployment history and reporting
- ✅ **WP_DEBUG Self-Healing** - Intelligent error monitoring and auto-fixing

## 🏗️ Architecture

### Core Components

- **`deployment_pipeline.py`** ⭐ - Complete deployment pipeline (RECOMMENDED)
- **`deployment_automation.py`** - Automated deployment with parallel processing
- **`deploy_on_push.py`** - CI/CD and push-triggered deployments
- **`deployment_monitor.py`** - Notifications and monitoring system
- **`deployment_rollback.py`** - Backup and rollback management
- **`auto_deploy_hook.py`** - Git hook integration

### Self-Healing System

- **`wp_debug_self_healing.py`** - Intelligent error detection and auto-fixing
- **`wp_error_monitor.py`** - Real-time WordPress error monitoring daemon
- **`wp_monitor_config.json`** - Self-healing configuration and patterns
- **`start_wp_self_healing.sh`** - Complete self-healing system startup

### Supporting Tools

- **`unified_deployer.py`** - Legacy unified deployment tool
- **`deploy_and_activate_themes.py`** - Theme deployment and activation
- **Git Hooks** - Pre-commit, post-commit, post-merge automation

## 🚀 Quick Start

### Full Automated Pipeline (Recommended)

```bash
# Deploy all websites with full validation and monitoring
python ops/deployment/deployment_pipeline.py --full

# Deploy specific site
python ops/deployment/deployment_pipeline.py --site dadudekc.com

# CI/CD mode (auto-detects changes)
python ops/deployment/deployment_pipeline.py --ci-cd

# Validation only (no deployment)
python ops/deployment/deployment_pipeline.py --validate-only

# Health checks only
python ops/deployment/deployment_pipeline.py --health-check
```

### weareswarm.online (static Command Center)

```powershell
cd D:\websites
.\ops\deployment\deploy_weareswarm.ps1              # prompt for SFTP password if needed
.\ops\deployment\deploy_weareswarm.ps1 -SaveCreds   # save to .env.deploy.local (gitignored)
.\ops\deployment\deploy_weareswarm.ps1 -SyncFirst   # sync planner JSON then deploy
```

Copy `.env.deploy.example` → `.env.deploy.local` for non-interactive deploys. See `websites/weareswarm.online/focus/DEPLOY.md`.

### Legacy Tools (Still Supported)

```bash
# Deploy single site
python ops/deployment/unified_deployer.py --site prismblossom.online

# Deploy all sites
python ops/deployment/unified_deployer.py --all

# Test without deploying
python ops/deployment/unified_deployer.py --site prismblossom.online --dry-run
```

## 🔄 Automated Deployment

### Git Hook Integration

The system automatically runs on:
- **Pre-commit**: PHP syntax validation
- **Post-commit**: Automated deployment
- **Post-merge**: Full deployment after branch merges
- **Push to main/master**: CI/CD pipeline execution

### Configuration

Edit `config/deployment_config.json` to customize:
- Site-specific settings
- Notification channels
- Rollback policies
- Validation rules

## 📊 Monitoring & Notifications

### Real-time Monitoring

```bash
# View deployment status
python ops/deployment/deployment_monitor.py --report

# Send test notification
python ops/deployment/deployment_monitor.py --notify dadudekc.com success "Test deployment completed"
```

### Notification Channels

Configure in `config/deployment_config.json`:
- **Console**: Local terminal output
- **Email**: SMTP notifications
- **Slack**: Webhook notifications
- **Discord**: Webhook notifications

## 🔙 Rollback & Recovery

### Automatic Rollback

```bash
# Create backup before deployment
python ops/deployment/deployment_rollback.py --create-backup dadudekc.com

# Rollback to specific version
python ops/deployment/deployment_rollback.py --rollback dadudekc.com --version 20260101_120000

# List available backups
python ops/deployment/deployment_rollback.py --list-backups dadudekc.com

# Cleanup old backups
python ops/deployment/deployment_rollback.py --cleanup dadudekc.com --keep-count 5
```

## 📋 Workflow Examples

### Standard Development Workflow

1. **Make changes** to website files in `websites/<domain>/`
2. **Commit changes** - triggers PHP validation
3. **Post-commit hook** automatically deploys to affected sites
4. **Receive notifications** via configured channels
5. **Monitor health checks** and deployment status

### CI/CD Pipeline (GitHub Actions)

```yaml
# .github/workflows/deploy.yml (already configured)
on:
  push:
    branches: [ main, master ]
    paths:
      - 'websites/**'

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Deploy
        run: python ops/deployment/deployment_pipeline.py --ci-cd
```

### Manual Deployment

```bash
# Full pipeline with validation and rollback
python ops/deployment/deployment_pipeline.py --full

# Quick deploy specific site
python ops/deployment/deploy_on_push.py --site dadudekc.com

# Dry run to see what would deploy
python ops/deployment/deployment_automation.py --dry-run
```

## ⚙️ Configuration

### Sites Configuration

```json
// config/site_configs.json
{
  "dadudekc.com": {
    "sftp": {
      "host": "ftp.dadudekc.com",
      "remote_path": "/public_html"
    },
    "deployment_method": "sftp"
  }
}
```

### Pipeline Configuration

```json
// config/deployment_config.json
{
  "deployment": {
    "auto_deploy_on_commit": true,
    "parallel_deployments": 3,
    "rollback_on_failure": false
  },
  "sites": {
    "dadudekc.com": {
      "enabled": true,
      "backup_before_deploy": true,
      "verify_after_deploy": true
    }
  }
}
```

## 📈 Performance & Scaling

- **Parallel Deployment**: Up to 3 sites simultaneously
- **Smart File Detection**: Only deploys changed files
- **Caching**: Reduces deployment time for unchanged files
- **Health Checks**: Automatic post-deployment verification
- **Monitoring**: Real-time performance tracking

## 🔧 Troubleshooting

### Common Issues

```bash
# Check deployment logs
tail -f deployment.log

# View recent deployments
python ops/deployment/deployment_monitor.py --report

# Test deployment connectivity
python ops/deployment/deployment_automation.py --dry-run

# Validate configuration
python ops/deployment/deployment_pipeline.py --validate-only
```

### Debug Mode

```bash
# Enable verbose logging
export DEPLOYMENT_DEBUG=1
python ops/deployment/deployment_pipeline.py --full
```

## 🔒 Security

- Credentials stored securely (not in repo)
- SFTP with key-based authentication
- File permission validation
- Backup encryption (future feature)
- Audit logging for all operations

## 📚 Integration

### With Existing Tools

- **Compatible** with all existing deployment scripts
- **Gradual migration** path from legacy tools
- **Backward compatibility** maintained

### External Systems

- **GitHub Actions**: Pre-configured workflow
- **GitLab CI**: Pipeline templates available
- **Jenkins**: Plugin integration support
- **Monitoring**: Integration with external monitoring systems

## 🎯 Best Practices

1. **Always test locally** before pushing
2. **Use feature branches** for major changes
3. **Monitor deployments** via notifications
4. **Keep backups** for critical rollbacks
5. **Review deployment reports** regularly
6. **Update configurations** as sites change

---

**🎉 Your websites now deploy automatically on every push!**

## Migration Status

✅ **Migrated to ops/deployment/** (2025-12-20)
- All WordPress deployment tools now in canonical location
- Tools in `tools/` maintained for backward compatibility