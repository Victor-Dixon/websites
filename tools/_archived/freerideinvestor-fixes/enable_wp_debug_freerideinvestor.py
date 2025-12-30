#!/usr/bin/env python3
"""Enable WP_DEBUG for freerideinvestor.com"""

import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / 'ops' / 'deployment'))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

site_configs = load_site_configs()
deployer = SimpleWordPressDeployer('freerideinvestor.com', site_configs)

if deployer.connect():
    # Read wp-config.php
    result = deployer.execute_command('cd domains/freerideinvestor.com/public_html && cat wp-config.php')
    
    # Enable WP_DEBUG if not already enabled
    if 'define(\'WP_DEBUG\', true)' not in result and 'define("WP_DEBUG", true)' not in result:
        # Add WP_DEBUG lines before "That's all, stop editing!"
        lines = result.split('\n')
        new_lines = []
        added = False
        for line in lines:
            if 'stop editing' in line.lower() and not added:
                new_lines.append("define('WP_DEBUG', true);")
                new_lines.append("define('WP_DEBUG_LOG', true);")
                new_lines.append("define('WP_DEBUG_DISPLAY', false);")
                new_lines.append("@ini_set('display_errors', 0);")
                new_lines.append("")
                added = True
            new_lines.append(line)
        
        # Write back
        config_content = '\n'.join(new_lines)
        # Upload via SFTP
        import tempfile
        import os
        with tempfile.NamedTemporaryFile(mode='w', delete=False, suffix='.php') as f:
            f.write(config_content)
            temp_path = f.name
        
        from pathlib import Path as P
        deployer.deploy_file(P(temp_path), 'wp-config.php')
        os.unlink(temp_path)
        print("✅ WP_DEBUG enabled")
    else:
        print("WP_DEBUG already enabled")
    
    deployer.disconnect()
