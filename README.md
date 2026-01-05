# Websites Repository - Organized Structure

This repository contains the complete infrastructure for managing multiple websites, with a focus on automated content generation, deployment, and maintenance.

## 🏗️ Directory Structure

### 📁 Root Level
```
websites/
├── scripts/           # All automation scripts
├── sites/            # Website files and assets
├── config/           # Configuration files
├── content/          # Content management
├── docs/             # Documentation
├── tools/            # Utility tools
├── ops/              # Operations scripts
├── src/              # Source code
├── tests/            # Test files
├── archive/          # Archived content
├── temp/             # Temporary files
├── assets/           # Shared assets
├── backup/           # Backup files
└── config/paths.py   # Path management system
```

### 📂 Scripts Directory (`scripts/`)
Organized automation scripts by function:

```
scripts/
├── audit/            # Website auditing scripts
├── deploy/           # Deployment and publishing scripts
├── check/            # Health checking and monitoring scripts
├── debug/            # Debugging and diagnostic scripts
├── test/             # Testing scripts
└── services/         # Content and service management scripts
```

### 🌐 Sites Directory (`sites/`)
Website organization by environment:

```
sites/
├── production/       # Live production websites
├── staging/          # Staging/test environments
├── development/      # Development versions
├── wordpress-plugins/# WordPress plugins
├── website_design/   # Design assets
└── *.php             # Utility PHP files
```

### ⚙️ Configuration (`config/`)
Centralized configuration management:

```
config/
├── paths.py          # Path management system
├── *.yaml            # Site configurations
├── *.json            # Runtime data
├── diagnostics/      # Diagnostic reports
├── runtime/          # Runtime state files
└── message_queue/    # Message queue data
```

## 🚀 Key Features

### 🔧 Portable Path Management
- **No hardcoded paths**: All scripts use the centralized `config/paths.py` system
- **Environment agnostic**: Works across different machines and setups
- **Scalable**: Easy to add new websites or environments

### 📊 Automated Content Pipeline
- **Episode generation**: Automated content creation from conversation data
- **Canon declaration**: Identifies and declares canonical elements
- **Multi-platform publishing**: Automated deployment to multiple platforms

### 🌐 Multi-Site Management
- **Production sites**: Live websites under `sites/production/`
- **Environment separation**: Clear staging/production/development separation
- **Shared assets**: Common resources in `assets/` directory

## 🛠️ Usage

### Running Scripts
All scripts are now organized by function. Use the path management system:

```python
from config.paths import paths

# Get path to a website
site_path = paths.get_website_path("digitaldreamscape.site")

# Get path to scripts
deploy_script = paths.get_script_path("deploy_system_scripts.py", "deploy")
```

### Adding New Websites
1. Create directory in appropriate environment: `sites/production/new-site/`
2. Add configuration in `config/`
3. Update path management if needed

### Deployment
```bash
# Deploy system scripts
python scripts/deploy/deploy_system_scripts.py site-name

# Run canon declaration
python scripts/services/run_canon_declaration.py
```

## 📋 Organization Benefits

### ✅ Before (Chaotic)
- Scripts scattered across root
- Hardcoded paths everywhere
- Mixed content types
- Difficult maintenance

### ✅ After (Organized)
- **Clear separation**: Each directory has a specific purpose
- **Portable**: No hardcoded paths, works anywhere
- **Scalable**: Easy to add new sites, scripts, or environments
- **Maintainable**: Logical organization makes finding things easy

## 🔍 Finding Things

| What | Where |
|------|-------|
| Website files | `sites/production/website-name/` |
| Deployment scripts | `scripts/deploy/` |
| Configuration | `config/` |
| Content templates | `content/` |
| Documentation | `docs/` |
| Utility tools | `tools/` |

## 🚨 Important Notes

- **Path Management**: Always use `config/paths.py` for path resolution
- **Environment Variables**: Scripts respect standard environment variables
- **Backups**: Regular backups are stored in `backup/`
- **Archives**: Old content moved to `archive/` to reduce clutter

## 🤝 Contributing

1. Follow the directory structure
2. Use the path management system
3. Add documentation for new scripts
4. Test across environments

---

**Status**: 🏗️ Repository successfully reorganized with portable, scalable structure.