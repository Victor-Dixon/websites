#!/usr/bin/env python3
"""
Investigate freerideinvestor.com Empty Content Issue
=====================================================

Investigates why the main content area is empty despite HTTP 200 response.
Checks WordPress theme, page content, JavaScript, CSS, and template files.

Author: Agent-1
Date: 2025-12-22
"""

import sys
import json
import requests
from pathlib import Path
from typing import Dict, Optional, List
from bs4 import BeautifulSoup

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def check_page_content_via_http() -> Dict:
    """Check page content via HTTP to see what's actually being served."""
    url = "https://freerideinvestor.com"
    
    try:
        response = requests.get(url, timeout=10)
        soup = BeautifulSoup(response.text, 'html.parser')
        
        # Check for main content elements
        main_content = soup.find('main') or soup.find('div', class_='content') or soup.find('div', id='content')
        article = soup.find('article')
        posts = soup.find_all('article') or soup.find_all('div', class_='post')
        
        # Check for WordPress loop
        has_loop = bool(soup.find('div', class_='loop') or soup.find('div', id='loop'))
        
        # Check for JavaScript that might load content
        scripts = soup.find_all('script')
        js_content_loaders = [s for s in scripts if s.string and ('content' in s.string.lower() or 'ajax' in s.string.lower())]
        
        # Check CSS that might hide content
        styles = soup.find_all('style')
        css_hiding_content = [s for s in styles if s.string and ('display: none' in s.string or 'visibility: hidden' in s.string)]
        
        return {
            "status_code": response.status_code,
            "content_length": len(response.content),
            "has_main_content": main_content is not None,
            "has_article": article is not None,
            "post_count": len(posts),
            "has_loop": has_loop,
            "js_content_loaders": len(js_content_loaders),
            "css_hiding_content": len(css_hiding_content),
            "body_content_length": len(soup.find('body').get_text() if soup.find('body') else ''),
            "html_structure": {
                "has_header": soup.find('header') is not None,
                "has_nav": soup.find('nav') is not None,
                "has_main": soup.find('main') is not None,
                "has_footer": soup.find('footer') is not None,
            }
        }
    except Exception as e:
        return {"error": str(e)}


def check_wordpress_content(deployer) -> Dict:
    """Check WordPress content via SSH/SFTP."""
    if not deployer.connect():
        return {"error": "Cannot connect"}
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/freerideinvestor.com/public_html"
        
        # Check if there are any posts/pages
        wp_posts_check = deployer.execute_command(
            f"cd {remote_path} && wp post list --format=count --allow-root 2>/dev/null || echo 'WP-CLI not available'"
        )
        
        # Check active theme
        active_theme = deployer.execute_command(
            f"cd {remote_path} && wp theme get --field=name --allow-root 2>/dev/null || echo 'WP-CLI not available'"
        )
        
        # Check if index.php exists in theme
        theme_name = active_theme.strip() if active_theme else "freerideinvestor-modern"
        index_php = f"{remote_path}/wp-content/themes/{theme_name}/index.php"
        index_exists = deployer.execute_command(f"test -f {index_php} && echo 'EXISTS' || echo 'NOT_EXISTS'")
        
        # Check if front-page.php exists (WordPress uses this for homepage)
        front_page_php = f"{remote_path}/wp-content/themes/{theme_name}/front-page.php"
        front_page_exists = deployer.execute_command(f"test -f {front_page_php} && echo 'EXISTS' || echo 'NOT_EXISTS'")
        
        # Check if home.php exists
        home_php = f"{remote_path}/wp-content/themes/{theme_name}/home.php"
        home_exists = deployer.execute_command(f"test -f {home_php} && echo 'EXISTS' || echo 'NOT_EXISTS'")
        
        # Check theme functions.php for content hooks
        functions_php = f"{remote_path}/wp-content/themes/{theme_name}/functions.php"
        functions_content = deployer.execute_command(f"cat {functions_php} 2>/dev/null | head -50")
        has_content_hooks = "the_content" in functions_content or "wp_query" in functions_content.lower()
        
        return {
            "wp_posts_count": wp_posts_check.strip(),
            "active_theme": active_theme.strip() if active_theme else "unknown",
            "index_php_exists": "EXISTS" in index_exists,
            "front_page_php_exists": "EXISTS" in front_page_exists,
            "home_php_exists": "EXISTS" in home_exists,
            "has_content_hooks": has_content_hooks
        }
    except Exception as e:
        return {"error": str(e)}
    finally:
        deployer.disconnect()


def check_theme_template_files(deployer) -> Dict:
    """Check theme template files for issues."""
    if not deployer.connect():
        return {"error": "Cannot connect"}
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/freerideinvestor.com/public_html"
        
        # Get active theme
        active_theme = deployer.execute_command(
            f"cd {remote_path} && wp theme get --field=name --allow-root 2>/dev/null || echo 'freerideinvestor-modern'"
        ).strip()
        
        theme_path = f"{remote_path}/wp-content/themes/{active_theme}"
        
        # Check key template files
        template_files = {
            "index.php": f"{theme_path}/index.php",
            "front-page.php": f"{theme_path}/front-page.php",
            "home.php": f"{theme_path}/home.php",
            "single.php": f"{theme_path}/single.php",
            "page.php": f"{theme_path}/page.php",
        }
        
        results = {}
        for name, path in template_files.items():
            exists = deployer.execute_command(f"test -f {path} && echo 'EXISTS' || echo 'NOT_EXISTS'")
            if "EXISTS" in exists:
                # Check if file has WordPress loop
                content = deployer.execute_command(f"cat {path} 2>/dev/null")
                has_loop = "have_posts" in content or "the_post" in content or "WP_Query" in content
                results[name] = {
                    "exists": True,
                    "has_loop": has_loop,
                    "size": len(content) if content else 0
                }
            else:
                results[name] = {"exists": False}
        
        return results
    except Exception as e:
        return {"error": str(e)}
    finally:
        deployer.disconnect()


def main():
    """Main investigation function."""
    print("=" * 70)
    print("🔍 INVESTIGATING FREERIDEINVESTOR.COM EMPTY CONTENT ISSUE")
    print("=" * 70)
    print()
    
    # Step 1: HTTP content check
    print("1️⃣  Checking page content via HTTP...")
    http_check = check_page_content_via_http()
    if "error" in http_check:
        print(f"   ❌ Error: {http_check['error']}")
    else:
        print(f"   Status: {http_check.get('status_code', 'Unknown')}")
        print(f"   Content Length: {http_check.get('content_length', 0)} bytes")
        print(f"   Has Main Content: {http_check.get('has_main_content', False)}")
        print(f"   Has Article: {http_check.get('has_article', False)}")
        print(f"   Post Count: {http_check.get('post_count', 0)}")
        print(f"   Body Text Length: {http_check.get('body_content_length', 0)} chars")
        print(f"   HTML Structure: {http_check.get('html_structure', {})}")
    print()
    
    # Step 2: WordPress content check
    print("2️⃣  Checking WordPress content...")
    site_configs = load_site_configs()
    try:
        deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
        wp_check = check_wordpress_content(deployer)
        if "error" in wp_check:
            print(f"   ❌ Error: {wp_check['error']}")
        else:
            print(f"   Active Theme: {wp_check.get('active_theme', 'unknown')}")
            print(f"   WP Posts Count: {wp_check.get('wp_posts_count', 'unknown')}")
            print(f"   Index.php Exists: {wp_check.get('index_php_exists', False)}")
            print(f"   Front-page.php Exists: {wp_check.get('front_page_php_exists', False)}")
            print(f"   Home.php Exists: {wp_check.get('home_php_exists', False)}")
            print(f"   Has Content Hooks: {wp_check.get('has_content_hooks', False)}")
    except Exception as e:
        print(f"   ❌ Error initializing deployer: {e}")
        wp_check = {}
    print()
    
    # Step 3: Theme template files check
    print("3️⃣  Checking theme template files...")
    try:
        template_check = check_theme_template_files(deployer)
        if "error" in template_check:
            print(f"   ❌ Error: {template_check['error']}")
        else:
            for file_name, file_info in template_check.items():
                if file_info.get('exists'):
                    print(f"   ✅ {file_name}: Exists, Has Loop: {file_info.get('has_loop', False)}, Size: {file_info.get('size', 0)} bytes")
                else:
                    print(f"   ❌ {file_name}: Not found")
    except Exception as e:
        print(f"   ❌ Error: {e}")
    print()
    
    # Generate recommendations
    print("=" * 70)
    print("💡 RECOMMENDATIONS")
    print("=" * 70)
    print()
    
    if http_check.get('body_content_length', 0) < 100:
        print("⚠️  CRITICAL: Page has very little text content")
        print("   - Check if WordPress has any posts/pages published")
        print("   - Verify theme is displaying content correctly")
        print("   - Check if JavaScript is required to load content")
    
    if not wp_check.get('has_content_hooks', False):
        print("⚠️  Theme may be missing content hooks")
        print("   - Check functions.php for proper WordPress hooks")
        print("   - Verify theme template files include WordPress loop")
    
    if not template_check.get('index.php', {}).get('has_loop', False):
        print("⚠️  Theme index.php may be missing WordPress loop")
        print("   - Verify index.php includes have_posts() and the_post()")
    
    print()
    print("🔧 Next Steps:")
    print("   1. Check WordPress admin for published posts/pages")
    print("   2. Verify theme template files include WordPress loop")
    print("   3. Check if JavaScript is required for content loading")
    print("   4. Review theme functions.php for content hooks")
    print("   5. Test with default WordPress theme to isolate issue")
    
    return 0


if __name__ == "__main__":
    sys.exit(main())






