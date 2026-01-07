#!/usr/bin/env python3
"""
Test WordPress Category Creation
"""

import os
import requests
from dotenv import load_dotenv

# Load environment
load_dotenv(dotenv_path='config/.env')

# Get credentials
base_url = os.environ.get('DREAM_WP_URL', '').replace('/wp-json/wp/v2', '')
username = os.environ.get('DREAM_WP_USER')
app_password = os.environ.get('DREAM_WP_APP_PASS')

print(f"Testing WordPress API at: {base_url}")
print(f"User: {username}")
print(f"Password set: {'Yes' if app_password else 'No'}")

if not all([base_url, username, app_password]):
    print("❌ Missing credentials")
    exit(1)

# Test basic API access
try:
    response = requests.get(f'{base_url}/wp-json/wp/v2/categories', auth=(username, app_password))
    print(f"API Status: {response.status_code}")

    if response.status_code == 200:
        categories = response.json()
        print(f"Found {len(categories)} categories")

        # Try to create a test category
        test_payload = {
            'name': 'Test Category - Digital Dreamscape',
            'slug': 'test-category-digital-dreamscape',
            'description': 'Test category for Digital Dreamscape activation'
        }

        create_response = requests.post(
            f'{base_url}/wp-json/wp/v2/categories',
            auth=(username, app_password),
            json=test_payload
        )

        print(f"Create category status: {create_response.status_code}")
        if create_response.status_code >= 400:
            print(f"Error: {create_response.text}")
        else:
            print("✅ Category creation successful!")
            category_data = create_response.json()
            print(f"Created category: {category_data.get('name')} (ID: {category_data.get('id')})")

    else:
        print(f"API Error: {response.text}")

except Exception as e:
    print(f"Request failed: {e}")