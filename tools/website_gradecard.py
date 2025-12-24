import json
import os
import re
from pathlib import Path
from datetime import datetime

REPO_ROOT = Path(__file__).resolve().parents[1]
SITES_REGISTRY = REPO_ROOT / "configs" / "sites_registry.json"
GRADECARD_FILE = REPO_ROOT / "global_website_gradecard.json"

def load_domains():
    if not SITES_REGISTRY.exists():
        return []
    with open(SITES_REGISTRY, 'r') as f:
        data = json.load(f)
    return sorted(data.keys())

def find_best_site_location(domain):
    candidates = [
        REPO_ROOT / domain,
        REPO_ROOT / "sites" / domain,
        REPO_ROOT / "websites" / domain,
    ]
    
    # Priority 1: Has wp-content or wp/wp-content
    for p in candidates:
        if p.exists() and p.is_dir():
            if (p / "wp-content").exists() or (p / "wp" / "wp-content").exists() or (p / "wp").exists():
                return p
    
    # Priority 2: Exists (fallback)
    for p in candidates:
        if p.exists() and p.is_dir():
            return p
            
    return None

def get_theme_path(site_root):
    if not site_root:
        return None
        
    # Try to find the active theme
    themes_dirs = [
        site_root / "wp" / "wp-content" / "themes",
        site_root / "wp-content" / "themes",
        site_root / "wordpress" / "wp-content" / "themes"
    ]
    
    themes_dir = None
    for d in themes_dirs:
        if d.exists():
            themes_dir = d
            break
            
    if not themes_dir:
        return None

    # List themes
    themes = [d for d in themes_dir.iterdir() if d.is_dir()]
    if not themes:
        return None
    
    # Heuristic: pick the one that looks most like a custom theme
    # 1. Matches domain name part
    domain_part = site_root.name.split('.')[0]
    for t in themes:
        if domain_part in t.name:
            return t
    
    # 2. Return the first one
    return themes[0]

def check_ux_design(theme_path):
    score = 0
    max_score = 3
    details = []

    if not theme_path:
        return 0, ["No theme found"]

    # Check 1: CSS organization
    css_dir = theme_path / "css"
    style_css = theme_path / "style.css"
    
    if css_dir.exists() or style_css.exists():
        score += 1
        details.append("CSS present")
    else:
        details.append("Missing CSS")

    # Check 2: Responsiveness (Media Queries)
    has_media_queries = False
    css_files = list(theme_path.rglob("*.css"))
    for css_file in css_files:
        try:
            content = css_file.read_text(errors='ignore')
            if "@media" in content:
                has_media_queries = True
                break
        except:
            pass
    
    if has_media_queries:
        score += 1
        details.append("Responsive (Media Queries found)")
    else:
        details.append("Not Responsive (No Media Queries)")

    # Check 3: Modern CSS (Variables)
    has_vars = False
    for css_file in css_files:
        try:
            content = css_file.read_text(errors='ignore')
            if "--" in content and "var(" in content:
                has_vars = True
                break
        except:
            pass

    if has_vars:
        score += 1
        details.append("Modern CSS (Variables used)")
    else:
        details.append("Legacy CSS (No Variables)")

    return (score / max_score) * 100, details

def check_seo(theme_path, site_root):
    score = 0
    max_score = 3
    details = []

    if not theme_path:
        return 0, ["No theme found"]

    php_files = list(theme_path.rglob("*.php"))
    
    # Check 1: Meta Description
    has_meta_desc = False
    for php in php_files:
        try:
            content = php.read_text(errors='ignore')
            if 'name="description"' in content or "name='description'" in content:
                has_meta_desc = True
                break
        except:
            pass
    
    if has_meta_desc:
        score += 1
        details.append("Meta Description present")
    else:
        details.append("Missing Meta Description tag")

    # Check 2: Open Graph
    has_og = False
    for php in php_files:
        try:
            content = php.read_text(errors='ignore')
            if 'property="og:' in content or "property='og:" in content:
                has_og = True
                break
        except:
            pass
    
    if has_og:
        score += 1
        details.append("Open Graph tags present")
    else:
        details.append("Missing Open Graph tags")

    # Check 3: Sitemap or Robots (in site root)
    has_robots = (site_root / "robots.txt").exists()
    has_sitemap = (site_root / "sitemap.xml").exists()
    
    if has_robots or has_sitemap:
        score += 1
        details.append("Robots/Sitemap found")
    else:
        details.append("Missing Robots.txt/Sitemap.xml")

    return (score / max_score) * 100, details

def check_professionalism(theme_path, site_root):
    score = 0
    max_score = 3
    details = []

    if not theme_path:
        return 0, ["No theme found"]

    # Check 1: No Lorem Ipsum
    files_to_check = list(theme_path.rglob("*.php")) + list(theme_path.rglob("*.html"))
    has_lorem = False
    for f in files_to_check:
        try:
            content = f.read_text(errors='ignore')
            if "Lorem ipsum" in content or "lorem ipsum" in content:
                has_lorem = True
                break
        except:
            pass
    
    if not has_lorem:
        score += 1
        details.append("No Lorem Ipsum detected")
    else:
        details.append("Lorem Ipsum found (placeholder content)")

    # Check 2: No TODOs in Theme
    has_todo = False
    for f in files_to_check:
        try:
            content = f.read_text(errors='ignore')
            if "TODO" in content or "FIXME" in content:
                has_todo = True
                break
        except:
            pass
    
    if not has_todo:
        score += 1
        details.append("Clean Code (No TODOs)")
    else:
        details.append("TODOs/FIXMEs found in code")

    # Check 3: Favicon
    # Check in root or theme assets
    has_favicon = False
    if (site_root / "favicon.ico").exists():
        has_favicon = True
    else:
        # Check theme images
        for img in theme_path.rglob("*"):
            if "favicon" in img.name.lower():
                has_favicon = True
                break
    
    if has_favicon:
        score += 1
        details.append("Favicon present")
    else:
        details.append("Missing Favicon")

    return (score / max_score) * 100, details

def calculate_grade(total_score):
    if total_score >= 90: return "A"
    if total_score >= 80: return "B"
    if total_score >= 70: return "C"
    if total_score >= 60: return "D"
    return "F"

def main():
    domains = load_domains()
    report = {
        "date": datetime.now().isoformat(),
        "websites": {}
    }

    print(f"{'WEBSITE':<30} | {'GRADE':<5} | {'UX':<5} | {'SEO':<5} | {'PRO':<5}")
    print("-" * 65)

    for domain in domains:
        site_root = find_best_site_location(domain)
        if not site_root:
            print(f"{domain:<30} | {'N/A':<5} | {'-':<5} | {'-':<5} | {'-':<5}")
            continue

        theme_path = get_theme_path(site_root)
        
        ux_score, ux_details = check_ux_design(theme_path)
        seo_score, seo_details = check_seo(theme_path, site_root)
        pro_score, pro_details = check_professionalism(theme_path, site_root)

        total_score = (ux_score + seo_score + pro_score) / 3
        grade = calculate_grade(total_score)

        report["websites"][domain] = {
            "grade": grade,
            "total_score": round(total_score, 1),
            "scores": {
                "design_ux": round(ux_score, 1),
                "seo": round(seo_score, 1),
                "professionalism": round(pro_score, 1)
            },
            "details": {
                "design_ux": ux_details,
                "seo": seo_details,
                "professionalism": pro_details
            }
        }

        print(f"{domain:<30} | {grade:<5} | {int(ux_score):<5} | {int(seo_score):<5} | {int(pro_score):<5}")

    with open(GRADECARD_FILE, 'w') as f:
        json.dump(report, f, indent=2)
    
    print("-" * 65)
    print(f"Detailed gradecard saved to {GRADECARD_FILE}")

if __name__ == "__main__":
    main()
