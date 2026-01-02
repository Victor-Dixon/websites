# Websites Management System - main.py

A comprehensive command-line interface for managing all websites in the portfolio.

## Overview

`main.py` serves as the central entry point for website deployment, monitoring, and maintenance operations. Built by The Swarm AI system, it provides a unified interface to manage multiple websites efficiently.

## Quick Start

```bash
# Deploy all websites
python main.py deploy all

# Deploy a specific website
python main.py deploy site --site dadudekc.com

# Check status of all websites
python main.py status

# Dry run (test without deploying)
python main.py deploy all --dry-run
```

## Available Commands

### Deploy Command
```bash
python main.py deploy all                    # Deploy all websites
python main.py deploy site --site <domain>  # Deploy specific website
python main.py deploy all --dry-run        # Test deployment without changes
```

### Status Command
```bash
python main.py status                       # Check all website status
```

### Monitor Command
```bash
python main.py monitor                      # Monitor website health and performance
```

### Cache Management
```bash
python main.py cache clear                  # Clear all website caches
```

### WordPress Management
```bash
python main.py wordpress check              # Check WordPress versions across all sites
```

### Backup Command
```bash
python main.py backup                       # Create backups of all websites
```

## Features

- **Unified Deployment**: Deploy all websites with a single command
- **Dry Run Support**: Test deployments without making changes
- **Multi-Site Support**: Automatically detects websites from various configuration sources
- **Health Monitoring**: Check website status and WordPress versions
- **Cache Management**: Clear caches across all sites
- **Error Handling**: Robust error handling with detailed reporting
- **Progress Tracking**: Real-time deployment progress and summaries

## Configuration

The system automatically detects websites from:
- `config/site_configs.json` - Deployment configurations
- `config/sites_registry.json` - Site registry
- `websites/` directory - Website files
- `sites/` directory - Site configuration files

## Dependencies

Required Python packages:
- paramiko (for SFTP deployment)
- python-dotenv (for environment variables)

Install with:
```bash
pip install paramiko python-dotenv
```

## Environment Variables

Set deployment credentials in a `.env` file or environment:
- `DADUDEKC_WP_URL` - WordPress REST API URL
- `DADUDEKC_WP_USER` - WordPress username
- `DADUDEKC_WP_APP_PASS` - WordPress application password
- Similar variables for other sites

## Examples

### Full Deployment Workflow
```bash
# 1. Check current status
python main.py status

# 2. Dry run to preview changes
python main.py deploy all --dry-run

# 3. Deploy all websites
python main.py deploy all

# 4. Clear caches
python main.py cache clear

# 5. Verify deployment
python main.py status
```

### Individual Site Deployment
```bash
# Deploy only dadudekc.com
python main.py deploy site --site dadudekc.com

# Check its status
python main.py status | grep dadudekc.com
```

## Architecture

The system is built with a modular architecture:
- `WebsiteManager` class handles core functionality
- Integration with existing deployment tools
- Graceful degradation when tools are unavailable
- Comprehensive logging and error reporting

## Built by The Swarm

This tool is part of The Swarm's multi-agent AI system for web development and automation. It coordinates specialized agents for WordPress development, deployment, and maintenance.

For more information about The Swarm: https://weareswarm.site