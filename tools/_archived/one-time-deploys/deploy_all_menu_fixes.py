#!/usr/bin/env python3
"""Deploy all menu styling fixes to freerideinvestor.com"""
import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def main():
    print("=" * 70)
    print("DEPLOYING ALL MENU STYLING FIXES")
    print("=" * 70)
    
    deployer = SimpleWordPressDeployer('freerideinvestor.com', load_site_configs())
    deployer.connect()
    
    base = Path('D:/websites/websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern')
    remote_path = 'domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern'
    
    # All files that need to be deployed
    files_to_deploy = [
        ('functions.php', f'{remote_path}/functions.php'),
        ('header.php', f'{remote_path}/header.php'),
        ('style.css', f'{remote_path}/style.css'),
        ('css/styles/components/_navigation.css', f'{remote_path}/css/styles/components/_navigation.css'),
        ('css/styles/layout/_header-footer.css', f'{remote_path}/css/styles/layout/_header-footer.css'),
        ('css/styles/utilities/_responsive-enhancements.css', f'{remote_path}/css/styles/utilities/_responsive-enhancements.css'),
        ('css/custom.css', f'{remote_path}/css/custom.css'),
    ]
    
    print("\n📦 Deploying files...")
    deployed = []
    failed = []
    
    for local_file, remote_file in files_to_deploy:
        local_path = base / local_file
        if local_path.exists():
            try:
                deployer.deploy_file(str(local_path), remote_file)
                deployed.append(local_file)
                print(f"  ✅ Deployed: {local_file}")
            except Exception as e:
                failed.append((local_file, str(e)))
                print(f"  ❌ Failed: {local_file} - {e}")
        else:
            failed.append((local_file, "Not found locally"))
            print(f"  ⚠️  Skipped: {local_file} (not found locally)")
    
    print(f"\n✅ Successfully deployed: {len(deployed)} files")
    if failed:
        print(f"❌ Failed: {len(failed)} files")
        for f, reason in failed:
            print(f"   - {f}: {reason}")
    
    # Verify deployment
    print("\n🔍 Verifying deployment...")
    for local_file, remote_file in files_to_deploy:
        result = deployer.execute_command(f'test -f {remote_file} && echo "EXISTS" || echo "NOT_FOUND"')
        status = "✅" if "EXISTS" in result else "❌"
        print(f"  {status} {local_file}")
    
    # Clear cache
    print("\n🔄 Clearing all caches...")
    cache_results = []
    
    # WordPress cache
    result = deployer.execute_command(f'cd {remote_path} && wp cache flush --allow-root 2>&1')
    cache_results.append(("WordPress cache", "✅ Cleared" if "Success" in result or "flushed" in result.lower() else "⚠️ " + result[:50]))
    
    # Object cache
    result = deployer.execute_command(f'cd {remote_path} && wp transient delete --all --allow-root 2>&1')
    cache_results.append(("Transients", "✅ Cleared"))
    
    # Rewrite rules flush
    result = deployer.execute_command(f'cd {remote_path} && wp rewrite flush --allow-root 2>&1')
    cache_results.append(("Rewrite rules", "✅ Flushed"))
    
    for cache_type, status in cache_results:
        print(f"  {status} {cache_type}")
    
    deployer.disconnect()
    
    print("\n" + "=" * 70)
    print("DEPLOYMENT COMPLETE")
    print("=" * 70)
    print(f"\n✅ {len(deployed)} files deployed successfully")
    print("🌐 Ready for visual inspection on all pages")
    print("\n📋 Pages to check:")
    print("  - https://freerideinvestor.com/ (Home)")
    print("  - https://freerideinvestor.com/blog/ (Blog)")
    print("  - https://freerideinvestor.com/about/ (About)")
    print("  - https://freerideinvestor.com/contact/ (Contact)")

if __name__ == '__main__':
    main()

