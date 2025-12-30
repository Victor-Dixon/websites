#!/usr/bin/env python3
"""Deploy BUILD-IN-PUBLIC Phase 0 using working deployment pattern"""

import sys
from pathlib import Path

# Add deployment directory to path
deployment_dir = Path(__file__).parent.parent / "ops" / "deployment"
sys.path.insert(0, str(deployment_dir))

from deploy_and_activate_themes import deploy_and_activate_theme

# Theme configs matching the expected format
THEME_CONFIGS = {
    "dadudekc.com": {
        "site_key": "dadudekc.com",
        "theme_name": "dadudekc",
        "theme_path": "sites/dadudekc.com/wp/theme/dadudekc",
        "remote_path": "wp-content/themes/dadudekc"
    },
    "weareswarm.online": {
        "site_key": "weareswarm.online", 
        "theme_name": "swarm",
        "theme_path": "sites/weareswarm.online/wp/theme/swarm",
        "remote_path": "wp-content/themes/swarm"
    }
}

print("=" * 60)
print("BUILD-IN-PUBLIC Phase 0 Deployment")
print("=" * 60)

# Deploy dadudekc.com
print("\n1. Deploying dadudekc.com theme...")
upload_success, activate_success = deploy_and_activate_theme("dadudekc.com", THEME_CONFIGS["dadudekc.com"], activate=False)
print(f"   Upload: {'✅' if upload_success else '❌'}, Activate: {'✅' if activate_success else 'N/A'}")

# Deploy weareswarm.online
print("\n2. Deploying weareswarm.online theme...")
upload_success, activate_success = deploy_and_activate_theme("weareswarm.online", THEME_CONFIGS["weareswarm.online"], activate=False)
print(f"   Upload: {'✅' if upload_success else '❌'}, Activate: {'✅' if activate_success else 'N/A'}")

print("\n" + "=" * 60)
print("Deployment complete!")


