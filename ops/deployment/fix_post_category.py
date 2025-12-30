"""Fix post category for Christmas Eve blog post."""
import sys
sys.path.insert(0, '.')
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

site_configs = load_site_configs()
deployer = SimpleWordPressDeployer('digitaldreamscape.site', site_configs)
if deployer.connect():
    remote_path = 'domains/digitaldreamscape.site/public_html'
    
    # List categories
    print('Checking categories...')
    result = deployer.execute_command(f'cd {remote_path} && wp term list category --fields=term_id,name --allow-root 2>&1')
    print(result if result else 'None')
    
    # Create Build in Public category
    print('Creating Build in Public category...')
    result = deployer.execute_command(f"cd {remote_path} && wp term create category 'Build in Public' --slug=build-in-public --allow-root 2>&1")
    print(result if result else 'Done')
    
    # Set it on post 28
    print('Setting category on post 28...')
    result = deployer.execute_command(f"cd {remote_path} && wp post term set 28 category 'Build in Public' --allow-root 2>&1")
    print(result if result else 'Done')
    
    # Clear cache
    print('Clearing cache...')
    result = deployer.execute_command(f"cd {remote_path} && wp cache flush --allow-root 2>&1")
    print(result if result else 'Done')
    
    deployer.disconnect()
    print('Done!')

