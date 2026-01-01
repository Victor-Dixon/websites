#!/usr/bin/env python3
"""Read and analyze index.php to understand why content isn't displaying."""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    deployer.connect()
    
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    index_php = f"{remote_path}/wp-content/themes/{theme_name}/index.php"
    
    content = deployer.execute_command(f"cat {index_php}")
    print("=" * 70)
    print("INDEX.PHP CONTENT")
    print("=" * 70)
    print(content)
    print("=" * 70)
    print(f"Length: {len(content)} bytes")
    print(f"Has have_posts: {'have_posts' in content}")
    print(f"Has the_post: {'the_post' in content}")
    print(f"Has WP_Query: {'WP_Query' in content}")
    
    deployer.disconnect()


if __name__ == "__main__":
    main()


