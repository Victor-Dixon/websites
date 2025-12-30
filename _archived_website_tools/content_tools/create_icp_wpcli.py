#!/usr/bin/env python3
"""Create ICP definition via WP-CLI for dadudekc.com"""

import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / 'ops' / 'deployment'))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
import json

site_configs = load_site_configs()
deployer = SimpleWordPressDeployer('dadudekc.com', site_configs)

icp_content = {
    'title': 'DadudeKC Ideal Customer Profile',
    'content': 'For small business owners and entrepreneurs who struggle with manual workflows and time-consuming tasks, we eliminate operational bottlenecks through automation and systems. Your outcome: more time for growth, reduced operational stress, scalable processes.',
    'target_demographic': 'Small business owners and entrepreneurs',
    'pain_points': 'manual workflows, time-consuming tasks, operational bottlenecks',
    'desired_outcomes': 'more time for growth, reduced operational stress, scalable processes'
}

if deployer.connect():
    print("Creating ICP definition via WP-CLI...")
    
    # Escape content for shell
    title_escaped = icp_content['title'].replace("'", "'\\''")
    content_escaped = icp_content['content'].replace("'", "'\\''")
    
    # Create post via WP-CLI
    target_demo = icp_content['target_demographic']
    pain_pts = icp_content['pain_points']
    desired = icp_content['desired_outcomes']
    
    cmd = (
        f"cd domains/dadudekc.com/public_html && "
        f"wp post create "
        f"--post_type=icp_definition "
        f"--post_title='{title_escaped}' "
        f"--post_content='{content_escaped}' "
        f"--post_status=publish "
        f"--meta_input='target_demographic={target_demo}' "
        f"--meta_input='pain_points={pain_pts}' "
        f"--meta_input='desired_outcomes={desired}' "
        f"--meta_input='site_assignment=dadudekc.com' "
        f"--allow-root "
        f"--porcelain 2>&1"
    )
    
    result = deployer.execute_command(cmd)
    print(result)
    
    if result and result.strip().isdigit():
        post_id = result.strip()
        print(f"\n✅ ICP definition created successfully! Post ID: {post_id}")
        
        # Get URL
        url_cmd = f"cd domains/dadudekc.com/public_html && wp post get {post_id} --field=url --allow-root 2>&1"
        url_result = deployer.execute_command(url_cmd)
        print(f"URL: {url_result.strip() if url_result else 'N/A'}")
    else:
        print(f"\n❌ Failed to create ICP: {result}")
    
    deployer.disconnect()

