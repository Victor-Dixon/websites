#!/usr/bin/env python3
"""
Verify freerideinvestor.com Content Visibility
==============================================

Comprehensive verification of content visibility issues:
- CSS opacity checks (main content vs animations)
- WordPress posts existence
- JavaScript loading
- Content visibility testing

Agent-8: SSOT & System Integration Specialist
Task: Verify and fix freerideinvestor.com empty content area (CRITICAL)
"""

import json
import sys
import re
from pathlib import Path
from datetime import datetime

# Add deployment tools to path
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

try:
    from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    DEPLOYER_AVAILABLE = True
except ImportError:
    DEPLOYER_AVAILABLE = False
    print("❌ SimpleWordPressDeployer not available")
    sys.exit(1)

def check_css_main_content_opacity(deployer, theme_name, wp_path):
    """Check if CSS is hiding main content with opacity: 0."""
    print("🔍 Checking CSS for main content hiding...")
    
    css_files = ["style.css", "custom.css"]
    main_content_selectors = [
        "#main-content",
        ".site-main",
        "main",
        ".container",
        ".hero-section",
        "#dev-log-section",
        "#tbow-tactics-section"
    ]
    
    issues = []
    
    for css_file in css_files:
        css_path = f"{wp_path}/wp-content/themes/{theme_name}/{css_file}"
        check_result = deployer.execute_command(f"test -f {css_path} && echo 'EXISTS' || echo 'MISSING'")
        
        if "EXISTS" in check_result:
            css_content = deployer.execute_command(f"cat {css_path}")
            if css_content:
                # Check for opacity: 0 on main content selectors
                for selector in main_content_selectors:
                    # Look for selector with opacity: 0 (not in animation keyframes)
                    pattern = rf"{re.escape(selector)}\s*{{[^}}]*opacity\s*:\s*0[^}}]*}}"
                    matches = re.findall(pattern, css_content, re.IGNORECASE | re.DOTALL)
                    
                    if matches:
                        # Check if it's in an animation keyframe
                        for match in matches:
                            # Check context - if it's in @keyframes, it's OK
                            match_index = css_content.find(match)
                            before_context = css_content[max(0, match_index-100):match_index]
                            
                            if "@keyframes" not in before_context and "from {" not in before_context and "to {" not in before_context:
                                issues.append({
                                    "file": css_file,
                                    "selector": selector,
                                    "issue": "opacity: 0 found (not in animation)",
                                    "severity": "HIGH"
                                })
                                print(f"  ⚠️  {selector} has opacity: 0 in {css_file} (not animation)")
    
    # Also check imported CSS files
    css_dir = f"{wp_path}/wp-content/themes/{theme_name}/css"
    if deployer.execute_command(f"test -d {css_dir} && echo 'EXISTS' || echo 'MISSING'") == "EXISTS":
        css_files_list = deployer.execute_command(f"find {css_dir} -name '*.css' -type f 2>/dev/null | head -10")
        if css_files_list:
            for css_file_path in css_files_list.strip().split('\n'):
                if css_file_path:
                    css_content = deployer.execute_command(f"cat {css_file_path}")
                    if css_content:
                        for selector in main_content_selectors:
                            pattern = rf"{re.escape(selector)}\s*{{[^}}]*opacity\s*:\s*0[^}}]*}}"
                            matches = re.findall(pattern, css_content, re.IGNORECASE | re.DOTALL)
                            
                            if matches:
                                for match in matches:
                                    match_index = css_content.find(match)
                                    before_context = css_content[max(0, match_index-100):match_index]
                                    
                                    if "@keyframes" not in before_context:
                                        issues.append({
                                            "file": css_file_path.split('/')[-1],
                                            "selector": selector,
                                            "issue": "opacity: 0 found (not in animation)",
                                            "severity": "HIGH"
                                        })
                                        print(f"  ⚠️  {selector} has opacity: 0 in {css_file_path.split('/')[-1]}")
    
    if not issues:
        print("  ✅ No CSS hiding main content found")
    
    return issues

def check_wordpress_posts(deployer, wp_path):
    """Check if WordPress has posts to display."""
    print("🔍 Checking WordPress posts...")
    
    # Get post count
    result = deployer.execute_command(f"wp post list --format=count --path={wp_path}")
    post_count = 0
    
    if result and result.strip().isdigit():
        post_count = int(result.strip())
    
    # Get published posts
    published_result = deployer.execute_command(f"wp post list --post_status=publish --format=count --path={wp_path}")
    published_count = 0
    
    if published_result and published_result.strip().isdigit():
        published_count = int(published_result.strip())
    
    # Get recent posts
    recent_posts = deployer.execute_command(f"wp post list --posts_per_page=5 --format=table --path={wp_path}")
    
    print(f"  📊 Total posts: {post_count}")
    print(f"  📊 Published posts: {published_count}")
    
    if published_count == 0:
        print("  ⚠️  No published posts found - homepage will be empty")
        return {
            "has_posts": False,
            "total": post_count,
            "published": published_count,
            "issue": "No published posts to display"
        }
    else:
        print(f"  ✅ {published_count} published posts available")
        if recent_posts:
            print(f"  📝 Recent posts preview:")
            lines = recent_posts.strip().split('\n')[:6]  # Header + 5 posts
            for line in lines:
                print(f"     {line[:80]}")
    
    return {
        "has_posts": published_count > 0,
        "total": post_count,
        "published": published_count,
        "recent_posts": recent_posts[:500] if recent_posts else None
    }

def check_javascript_loading(deployer, theme_name, wp_path):
    """Check if JavaScript files exist and are loading."""
    print("🔍 Checking JavaScript files...")
    
    js_dir = f"{wp_path}/wp-content/themes/{theme_name}/js"
    js_files = []
    
    # Check if js directory exists
    check_result = deployer.execute_command(f"test -d {js_dir} && echo 'EXISTS' || echo 'MISSING'")
    
    if "EXISTS" in check_result:
        js_list = deployer.execute_command(f"find {js_dir} -name '*.js' -type f 2>/dev/null | head -20")
        if js_list:
            js_files = [f.strip() for f in js_list.strip().split('\n') if f.strip()]
            print(f"  ✅ Found {len(js_files)} JavaScript files")
            for js_file in js_files[:5]:  # Show first 5
                print(f"     - {js_file.split('/')[-1]}")
        else:
            print("  ⚠️  No JavaScript files found in js/ directory")
    else:
        print("  ⚠️  js/ directory not found")
    
    # Check functions.php for enqueued scripts
    functions_path = f"{wp_path}/wp-content/themes/{theme_name}/functions.php"
    check_result = deployer.execute_command(f"test -f {functions_path} && echo 'EXISTS' || echo 'MISSING'")
    
    if "EXISTS" in check_result:
        functions_content = deployer.execute_command(f"cat {functions_path}")
        if functions_content:
            # Check for wp_enqueue_script
            script_enqueues = re.findall(r"wp_enqueue_script\s*\([^)]+\)", functions_content, re.IGNORECASE)
            if script_enqueues:
                print(f"  ✅ Found {len(script_enqueues)} script enqueues in functions.php")
            else:
                print("  ⚠️  No wp_enqueue_script found in functions.php")
    
    return {
        "js_files": js_files,
        "js_dir_exists": "EXISTS" in check_result,
        "has_script_enqueues": len(script_enqueues) > 0 if 'script_enqueues' in locals() else False
    }

def check_content_visibility(deployer, theme_name, wp_path):
    """Check if content elements are visible in HTML output."""
    print("🔍 Checking content visibility in template files...")
    
    templates_to_check = ["index.php", "front-page.php"]
    visibility_issues = []
    
    for template in templates_to_check:
        template_path = f"{wp_path}/wp-content/themes/{theme_name}/{template}"
        check_result = deployer.execute_command(f"test -f {template_path} && echo 'EXISTS' || echo 'MISSING'")
        
        if "EXISTS" in check_result:
            template_content = deployer.execute_command(f"cat {template_path}")
            if template_content:
                # Check for main content elements
                has_main = "main" in template_content.lower() or "#main-content" in template_content or ".site-main" in template_content
                has_content = "the_content" in template_content or "the_excerpt" in template_content or "WP_Query" in template_content
                has_sections = "section" in template_content.lower() or "hero" in template_content.lower()
                
                if not has_main:
                    visibility_issues.append({
                        "template": template,
                        "issue": "No main content container found"
                    })
                    print(f"  ⚠️  {template}: No main content container")
                elif not has_content:
                    visibility_issues.append({
                        "template": template,
                        "issue": "No content rendering functions found"
                    })
                    print(f"  ⚠️  {template}: No content rendering functions")
                else:
                    print(f"  ✅ {template}: Has main content structure")
    
    return visibility_issues

def fix_css_opacity_issues(deployer, theme_name, wp_path, css_issues):
    """Fix CSS opacity issues that are hiding content."""
    if not css_issues:
        return False
    
    print("🔧 Fixing CSS opacity issues...")
    
    fixed = False
    for issue in css_issues:
        css_file = issue.get("file")
        selector = issue.get("selector")
        
        css_path = f"{wp_path}/wp-content/themes/{theme_name}/{css_file}"
        
        # Read CSS file
        css_content = deployer.execute_command(f"cat {css_path}")
        if not css_content:
            continue
        
        # Find and fix opacity: 0 for main content selectors
        # Replace opacity: 0 with opacity: 1 for main content
        pattern = rf"({re.escape(selector)}\s*{{[^}}]*?)opacity\s*:\s*0([^}}]*?}})"
        replacement = r"\1opacity: 1\2"
        
        new_content = re.sub(pattern, replacement, css_content, flags=re.IGNORECASE | re.DOTALL)
        
        if new_content != css_content:
            # Write fixed CSS
            # Note: This would require write_file method - for now, just report
            print(f"  ⚠️  Would fix {selector} in {css_file} (opacity: 0 → opacity: 1)")
            print(f"     Note: Manual fix required or implement write_file method")
            fixed = True
    
    return fixed

def main():
    """Main execution."""
    print("🔍 Verifying freerideinvestor.com content visibility...\n")
    
    # Load site config
    site_configs = load_site_configs()
    if "freerideinvestor.com" not in site_configs:
        print("❌ freerideinvestor.com not found in site configs")
        sys.exit(1)
    
    site_config = site_configs["freerideinvestor.com"]
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect to freerideinvestor.com")
        sys.exit(1)
    
    try:
        # Get WordPress path
        wp_path = deployer.remote_path
        if not wp_path:
            wp_path = f"domains/freerideinvestor.com/public_html"
        
        if not wp_path.startswith('/'):
            username = site_config.get('username') or site_config.get('sftp', {}).get('username', '')
            if username:
                wp_path = f"/home/{username}/{wp_path}"
        
        print(f"📁 WordPress path: {wp_path}\n")
        
        # Get active theme
        active_theme_result = deployer.execute_command(f"wp theme list --status=active --field=name --path={wp_path}")
        active_theme = active_theme_result.strip() if active_theme_result else None
        
        if not active_theme or "error" in active_theme.lower():
            print("❌ Could not determine active theme")
            sys.exit(1)
        
        print(f"🎨 Active theme: {active_theme}\n")
        
        # 1. Check CSS opacity issues
        css_issues = check_css_main_content_opacity(deployer, active_theme, wp_path)
        
        # 2. Check WordPress posts
        posts_info = check_wordpress_posts(deployer, wp_path)
        
        # 3. Check JavaScript
        js_info = check_javascript_loading(deployer, active_theme, wp_path)
        
        # 4. Check content visibility in templates
        visibility_issues = check_content_visibility(deployer, active_theme, wp_path)
        
        # Summary
        print(f"\n{'='*60}")
        print("Verification Summary")
        print("="*60)
        
        if css_issues:
            print(f"⚠️  CSS Issues: {len(css_issues)} issues found")
            for issue in css_issues:
                print(f"   - {issue['selector']} in {issue['file']}: {issue['issue']}")
        else:
            print("✅ CSS: No main content hiding issues")
        
        if not posts_info.get("has_posts"):
            print(f"⚠️  Posts: No published posts found (this is likely the main issue!)")
        else:
            print(f"✅ Posts: {posts_info.get('published')} published posts available")
        
        if not js_info.get("js_dir_exists") and not js_info.get("has_script_enqueues"):
            print("⚠️  JavaScript: No JS files or enqueues found")
        else:
            print("✅ JavaScript: Files/enqueues found")
        
        if visibility_issues:
            print(f"⚠️  Templates: {len(visibility_issues)} visibility issues")
        else:
            print("✅ Templates: Content structure looks good")
        
        # Recommendations
        print(f"\n{'='*60}")
        print("Recommendations")
        print("="*60)
        
        if not posts_info.get("has_posts"):
            print("🚨 PRIMARY ISSUE: No published posts found!")
            print("   → Create at least one published post to display on homepage")
            print("   → Or set homepage to a static page with content")
        
        if css_issues:
            print("⚠️  CSS: Fix opacity: 0 on main content selectors")
            print("   → Review CSS files and change opacity: 0 to opacity: 1 for main content")
        
        if visibility_issues:
            print("⚠️  Templates: Ensure content rendering functions are present")
        
        # Generate report
        report = {
            "timestamp": datetime.now().isoformat(),
            "site": "freerideinvestor.com",
            "theme": active_theme,
            "css_issues": css_issues,
            "posts_info": posts_info,
            "js_info": js_info,
            "visibility_issues": visibility_issues,
            "recommendations": []
        }
        
        if not posts_info.get("has_posts"):
            report["recommendations"].append("Create published posts or set static homepage")
        if css_issues:
            report["recommendations"].append("Fix CSS opacity: 0 on main content")
        if visibility_issues:
            report["recommendations"].append("Fix template content rendering")
        
        # Save report
        reports_dir = Path("D:/websites/docs/diagnostic_reports")
        reports_dir.mkdir(parents=True, exist_ok=True)
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        report_file = reports_dir / f"freerideinvestor_content_verification_{timestamp}.json"
        
        with open(report_file, 'w', encoding='utf-8') as f:
            json.dump(report, f, indent=2, ensure_ascii=False)
        
        print(f"\n📄 Report saved: {report_file}")
        
    finally:
        deployer.disconnect()

if __name__ == "__main__":
    main()


