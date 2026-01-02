# 🚀 Upgraded Websites Management System

## Overview

The Websites Management System has been **upgraded** with a powerful **Configuration Manager** that allows you to edit, deploy, and manage WordPress configurations directly through the command line.

## 🆕 New Features

### Configuration Management
- **Interactive wp-config.php Editor**: Edit database credentials and WordPress salts
- **Secure Salt Generation**: Automatically generate cryptographically secure WordPress salts
- **Configuration Deployment**: Deploy configuration changes to live sites
- **Backup System**: Automatic backups before configuration changes
- **Syntax Validation**: Validate configuration file syntax

## Quick Start

### Fix the HTTP 500 Errors

The two sites with 500 errors need their database configurations updated:

```bash
# 1. Edit freerideinvestor.com configuration
python main.py config edit --site freerideinvestor.com

# 2. Edit prismblossom.online configuration
python main.py config edit --site prismblossom.online

# 3. Deploy the configurations
python main.py config deploy --site freerideinvestor.com
python main.py config deploy --site prismblossom.online

# 4. Verify the sites are working
python audit_websites.py
```

## 📋 Available Commands

### Configuration Commands
```bash
python main.py config edit --site <domain>       # Interactive config editor
python main.py config deploy --site <domain>     # Deploy configuration
python main.py config backup --site <domain>     # Backup configuration
python main.py config validate --site <domain>   # Validate config syntax
```

### Standalone Config Manager
```bash
python config_manager.py list                    # List configured sites
python config_manager.py generate-salts          # Generate WordPress salts
python config_manager.py edit <domain>           # Edit specific site
python config_manager.py deploy <domain>         # Deploy specific site
```

## 🔧 How It Works

### Interactive Configuration Editor

When you run `config edit`, the system:

1. **Reads current configuration** from wp-config.php
2. **Prompts for database credentials** with smart defaults
3. **Generates new WordPress salts** for security
4. **Updates the configuration file** with your input
5. **Provides deployment instructions**

### Example Session

```
🔧 CONFIGURATION EDITOR: freerideinvestor.com
==================================================

📝 Enter database configuration:
Database name [freerideinvestor_db]: u123456789_freeride
Database user [freerideinvestor_user]: u123456789_user
Database password [CHANGE_THIS_PASSWORD]: your_real_password
Database host [localhost]: localhost

📋 Configuration for freerideinvestor.com:
   Database: u123456789_freeride
   User: u123456789_user
   Password: ***************
   Host: localhost

Save this configuration? (y/N): y

🔐 Generating new WordPress salts...
✅ Updated wp-config.php for freerideinvestor.com

💡 Next steps:
   1. Test the configuration locally
   2. Deploy to production: python main.py config deploy --site freerideinvestor.com
   3. Verify the site loads correctly
```

## 🛡️ Security Features

- **Automatic Salt Generation**: Uses Python's `secrets` module for cryptographically secure salts
- **Configuration Backups**: All changes are backed up with timestamps
- **Input Validation**: Sanitizes database credentials and configuration values
- **Syntax Validation**: Checks PHP syntax before deployment

## 📁 File Structure

```
websites/
├── freerideinvestor.com/
│   ├── wp-config.php          # WordPress configuration
│   ├── index.php             # WordPress bootstrap
│   └── .htaccess             # URL rewriting rules
├── prismblossom.online/
│   ├── wp-config.php         # WordPress configuration
│   ├── index.php             # WordPress bootstrap
│   └── .htaccess             # URL rewriting rules
└── ...

config/backups/               # Configuration backups
├── freerideinvestor.com_wp-config.php.20260101_120000
└── prismblossom.online_wp-config.php.20260101_120000
```

## 🔄 Deployment Workflow

### Complete Site Fix Workflow

```bash
# 1. Check current status
python audit_websites.py

# 2. Edit configuration for problematic sites
python main.py config edit --site freerideinvestor.com
python main.py config edit --site prismblossom.online

# 3. Validate configurations
python main.py config validate --site freerideinvestor.com
python main.py config validate --site prismblossom.online

# 4. Deploy configurations
python main.py config deploy --site freerideinvestor.com
python main.py config deploy --site prismblossom.online

# 5. Verify fixes
python audit_websites.py
```

## 🐛 Troubleshooting

### Common Issues

**"Configuration backed up" but no changes**
- Check that you answered "y" to save the configuration
- Verify the wp-config.php file was actually updated

**"Deployment failed"**
- Ensure deployment credentials are configured
- Check FTP/SFTP server connectivity
- Verify file permissions on the server

**Still getting 500 errors after deployment**
- Double-check database credentials with your hosting provider
- Verify the database exists and user has proper permissions
- Check PHP error logs on the server

### Recovery

If something goes wrong, you can restore from backup:

```bash
# List available backups
ls config/backups/

# Restore a specific backup
cp config/backups/freerideinvestor.com_wp-config.php.20260101_120000 \
   websites/freerideinvestor.com/wp-config.php
```

## 📊 Integration with Existing Tools

The Configuration Manager integrates seamlessly with:

- **audit_websites.py**: Website health monitoring
- **main.py**: Unified website management
- **deployment system**: File deployment infrastructure

## 🔐 Environment Variables

For deployment, set these in your `.env` file:

```bash
# freerideinvestor.com deployment
FREERIDEINVESTOR_FTP_HOST=your-ftp-host.com
FREERIDEINVESTOR_FTP_USER=your-ftp-user
FREERIDEINVESTOR_FTP_PASS=your-ftp-password

# prismblossom.online deployment
PRISMBLOSSOM_FTP_HOST=your-ftp-host.com
PRISMBLOSSOM_FTP_USER=your-ftp-user
PRISMBLOSSOM_FTP_PASS=your-ftp-password
```

## 🎯 Next Steps

1. **Fix the 500 errors** by configuring the database credentials
2. **Deploy all websites** with `python main.py deploy all`
3. **Set up monitoring** for ongoing site health
4. **Configure backups** for regular data protection

## 🆘 Support

If you encounter issues:

1. Check the configuration backups in `config/backups/`
2. Validate syntax with `python main.py config validate --site <domain>`
3. Review PHP error logs on your hosting server
4. Contact your hosting provider for database credential verification

---

**Built by The Swarm** 🐝 | **Date: 2026-01-01** | **Version: 2.0**