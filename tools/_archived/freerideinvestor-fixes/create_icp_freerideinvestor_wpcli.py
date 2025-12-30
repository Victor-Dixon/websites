#!/usr/bin/env python3
"""Create ICP definition via WP-CLI for freerideinvestor.com"""

import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / 'ops' / 'deployment'))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

site_configs = load_site_configs()
deployer = SimpleWordPressDeployer('freerideinvestor.com', site_configs)

icp_content = {
    'title': 'FreeRide Investor Ideal Customer Profile',
    'content': 'For active traders (day/swing traders, $10K-$500K accounts) struggling with inconsistent results, we eliminate guesswork and provide proven trading strategies. Your outcome: consistent edge, reduced losses, trading confidence.',
    'target_demographic': 'Active traders (day/swing traders, $10K-$500K accounts)',
    'pain_points': 'inconsistent results, guesswork',
    'desired_outcomes': 'consistent edge, reduced losses, trading confidence'
}

if deployer.connect():
    print("Creating ICP definition via WP-CLI...")
    
    # Escape content for shell
    title_escaped = icp_content['title'].replace("'", "'\\''")
    content_escaped = icp_content['content'].replace("'", "'\\''")
    target_demo = icp_content['target_demographic']
    pain_pts = icp_content['pain_points']
    desired = icp_content['desired_outcomes']
    
    # Create post via WP-CLI
    cmd = (
        f"cd domains/freerideinvestor.com/public_html && "
        f"wp post create "
        f"--post_type=icp_definition "
        f"--post_title='{title_escaped}' "
        f"--post_content='{content_escaped}' "
        f"--post_status=publish "
        f"--meta_input='target_demographic={target_demo}' "
        f"--meta_input='pain_points={pain_pts}' "
        f"--meta_input='desired_outcomes={desired}' "
        f"--meta_input='site_assignment=freerideinvestor.com' "
        f"--allow-root "
        f"--porcelain 2>&1"
    )
    
    result = deployer.execute_command(cmd)
    print(result)
    
    if result and result.strip().isdigit():
        post_id = result.strip()
        print(f"\n✅ ICP definition created successfully! Post ID: {post_id}")
    else:
        print(f"\n❌ Failed to create ICP: {result}")
    
    deployer.disconnect()

