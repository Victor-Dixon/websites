import json
import os
from pathlib import Path
from datetime import datetime

REPO_ROOT = Path(__file__).resolve().parents[1]
SITES_REGISTRY = REPO_ROOT / "configs" / "sites_registry.json"
AUDIT_FILE = REPO_ROOT / "global_plugin_audit_results.json"

def load_domains():
    if not SITES_REGISTRY.exists():
        return []
    with open(SITES_REGISTRY, 'r') as f:
        data = json.load(f)
    return sorted(data.keys())

def find_site_locations(domain):
    locations = []
    # Common patterns in this repo
    candidates = [
        REPO_ROOT / domain,
        REPO_ROOT / "sites" / domain,
        REPO_ROOT / "websites" / domain,
    ]
    for p in candidates:
        if p.exists() and p.is_dir():
            locations.append(p)
    return locations

def find_plugins(site_root):
    # Check for various WP structures
    possible_paths = [
        site_root / "wp-content" / "plugins",
        site_root / "wp" / "wp-content" / "plugins",
        site_root / "wordpress" / "wp-content" / "plugins",
        site_root / "public_html" / "wp-content" / "plugins"
    ]
    
    plugins_dir = None
    for p in possible_paths:
        if p.exists() and p.is_dir():
            plugins_dir = p
            break
            
    if not plugins_dir:
        return []

    plugins = []
    for item in plugins_dir.iterdir():
        if item.is_dir():
            # Check if it looks like a plugin (has PHP files)
            has_php = any(item.glob("*.php"))
            if has_php:
                plugins.append(item.name)
            # Also check if it's a single file plugin (less common but possible in subdirs? No, standard is dir or file)
    
    # Also check for single file plugins in the plugins root
    for item in plugins_dir.glob("*.php"):
        if item.is_file() and item.name != "index.php" and item.name != "hello.php":
             plugins.append(item.stem)

    return sorted(list(set(plugins)))

def main():
    domains = load_domains()
    results = {
        "audit_date": datetime.now().isoformat(),
        "websites": {},
        "summary": {}
    }

    total_websites = 0
    websites_audited = 0
    total_plugins = 0

    for domain in domains:
        total_websites += 1
        locs = find_site_locations(domain)
        
        site_info = {
            "status": "skipped",
            "reason": "Path not found",
            "plugins": [],
            "plugin_count": 0,
            "site_path": None
        }

        if locs:
            # Find the best location (one that has plugins)
            best_plugins = []
            best_path = locs[0]
            found_plugins = False

            for loc in locs:
                plugins = find_plugins(loc)
                if plugins:
                    best_plugins = plugins
                    best_path = loc
                    found_plugins = True
                    break # Found plugins, stop searching
            
            # If no plugins found in any location, stick with the first one or the one with most WP-like structure
            # For now, just use the one we found plugins in, or the first one.
            
            site_info["site_path"] = str(best_path.relative_to(REPO_ROOT))
            site_info["plugins"] = best_plugins
            site_info["plugin_count"] = len(best_plugins)
            
            if found_plugins:
                site_info["status"] = "audited"
                site_info["reason"] = "Plugins found"
                websites_audited += 1
                total_plugins += len(best_plugins)
            elif locs:
                 # We found the site but no plugins
                 site_info["status"] = "audited_empty"
                 site_info["reason"] = "Site directory found but no plugins detected"
                 websites_audited += 1 # We did audit it, just found nothing
        
        results["websites"][domain] = site_info

    results["summary"] = {
        "total_websites": total_websites,
        "websites_audited": websites_audited,
        "total_plugins": total_plugins
    }

    with open(AUDIT_FILE, 'w') as f:
        json.dump(results, f, indent=2)
    
    print(f"Audit complete. Results saved to {AUDIT_FILE}")
    print(json.dumps(results["summary"], indent=2))

if __name__ == "__main__":
    main()
