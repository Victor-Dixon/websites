#!/usr/bin/env python3
"""
Upload Music File to AriaJet Site
=================================

Uploads an MP3 file to WordPress media library and returns the URL
"""

import sys
from pathlib import Path

project_root = Path(__file__).parent.parent
sys.path.insert(0, str(project_root))

try:
    from ops.deployment.simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
except ImportError:
    try:
        sys.path.insert(0, str(project_root / "ops" / "deployment"))
        from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    except ImportError:
        print("❌ Could not import SimpleWordPressDeployer")
        sys.exit(1)

def upload_music_file(local_file_path: str, site_domain: str = "ariajet.site"):
    """Upload MP3 file to WordPress media library"""
    
    local_file = Path(local_file_path)
    if not local_file.exists():
        print(f"❌ File not found: {local_file_path}")
        sys.exit(1)
    
    print(f"🎵 Uploading music file to {site_domain}")
    print("=" * 60)
    print(f"📁 Local file: {local_file}")
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(site_domain, site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        sys.exit(1)
    
    wp_path = f"/home/u996867598/domains/{site_domain}/public_html"
    uploads_dir = f"{wp_path}/wp-content/uploads"
    
    # Create music directory if it doesn't exist
    print(f"\n📂 Creating music directory...")
    deployer.execute_command(f"mkdir -p {uploads_dir}/music 2>&1")
    
    # Upload file via SFTP
    remote_filename = local_file.name
    remote_path = f"{uploads_dir}/music/{remote_filename}"
    
    print(f"\n📤 Uploading {local_file.name}...")
    if deployer.deploy_file(local_file, remote_path):
        print(f"   ✅ File uploaded successfully")
        
        # Get the URL
        file_url = f"https://{site_domain}/wp-content/uploads/music/{remote_filename}"
        print(f"\n🔗 File URL: {file_url}")
        
        # Also add to WordPress media library via WP-CLI
        print(f"\n📚 Adding to WordPress media library...")
        media_cmd = f"cd {wp_path} && wp media import {remote_path} --title='{local_file.stem}' --porcelain 2>&1"
        media_result = deployer.execute_command(media_cmd)
        
        if media_result and media_result.strip().isdigit():
            media_id = media_result.strip()
            print(f"   ✅ Added to media library (ID: {media_id})")
            
            # Get attachment URL
            url_cmd = f"cd {wp_path} && wp post meta get {media_id} _wp_attached_file 2>&1"
            attached_file = deployer.execute_command(url_cmd)
            if attached_file:
                media_url = f"https://{site_domain}/wp-content/uploads/{attached_file.strip()}"
                print(f"   🔗 Media URL: {media_url}")
                deployer.disconnect()
                return media_url
        
        deployer.disconnect()
        return file_url
    else:
        print(f"   ❌ Failed to upload file")
        deployer.disconnect()
        sys.exit(1)

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python upload_music_to_ariajet.py <path_to_mp3_file>")
        sys.exit(1)
    
    file_path = sys.argv[1]
    url = upload_music_file(file_path)
    print(f"\n✨ Upload complete!")
    print(f"   Use this URL in your music page: {url}")

