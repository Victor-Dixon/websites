#!/usr/bin/env python3
"""
Test script for DaDudeKC Website functionality
Tests the basic website features and improvements
"""

import os
import sys
import unittest
from pathlib import Path

# Add project root to path
project_root = Path(__file__).parent.parent
sys.path.append(str(project_root))

class TestDaDudeKCWebsite(unittest.TestCase):
    """Test cases for DaDudeKC Website functionality"""
    
    def setUp(self):
        """Set up test environment"""
        self.website_dir = Path(__file__).parent.parent / "dadudekc website"
        self.common_dir = self.website_dir / "common"
        self.home_dir = self.website_dir / "home"
        
    def test_required_files_exist(self):
        """Test that all required files exist"""
        required_files = [
            self.website_dir / "Index.html",
            self.common_dir / "css" / "common.css",
            self.common_dir / "js" / "utils.js",
            self.home_dir / "home.css",
            self.home_dir / "home.js",
            self.home_dir / "interactive-elements.js",
            self.common_dir / "js" / "animations.js"
        ]
        
        for file_path in required_files:
            with self.subTest(file_path=file_path):
                self.assertTrue(file_path.exists(), f"Required file {file_path} does not exist")
    
    def test_html_structure(self):
        """Test that HTML file has proper structure"""
        html_file = self.website_dir / "Index.html"
        
        with open(html_file, 'r', encoding='utf-8') as f:
            content = f.read()
            
        # Check for required elements
        self.assertIn('<!DOCTYPE html>', content)
        self.assertIn('<title>DaDudeKC Home</title>', content)
        self.assertIn('class="main-nav"', content)
        self.assertIn('class="hero-section"', content)
        
        # Check for CSS and JS references
        self.assertIn('common/css/common.css', content)
        self.assertIn('common/js/utils.js', content)
        self.assertIn('home/home.css', content)
        self.assertIn('home/home.js', content)
    
    def test_css_files_content(self):
        """Test that CSS files contain expected content"""
        # Test common.css
        common_css = self.common_dir / "css" / "common.css"
        with open(common_css, 'r', encoding='utf-8') as f:
            content = f.read()
            
        self.assertIn(':root', content)
        self.assertIn('--primary-color', content)
        self.assertIn('.main-nav', content)
        self.assertIn('.hero-section', content)
        
        # Test home.css
        home_css = self.home_dir / "home.css"
        with open(home_css, 'r', encoding='utf-8') as f:
            content = f.read()
            
        self.assertIn('.service-grid', content)
        self.assertIn('.blog-grid', content)
        self.assertIn('.contact-form', content)
    
    def test_js_files_content(self):
        """Test that JavaScript files contain expected content"""
        # Test utils.js
        utils_js = self.common_dir / "js" / "utils.js"
        with open(utils_js, 'r', encoding='utf-8') as f:
            content = f.read()
            
        self.assertIn('class WebsiteUtils', content)
        self.assertIn('setupSmoothScrolling', content)
        self.assertIn('setupMobileMenu', content)
        
        # Test home.js
        home_js = self.home_dir / "home.js"
        with open(home_js, 'r', encoding='utf-8') as f:
            content = f.read()
            
        self.assertIn('class HomePage', content)
        self.assertIn('setupServiceGrid', content)
        self.assertIn('setupBlogGrid', content)
        
        # Test animations.js
        animations_js = self.common_dir / "js" / "animations.js"
        with open(animations_js, 'r', encoding='utf-8') as f:
            content = f.read()
            
        self.assertIn('class AnimationManager', content)
        self.assertIn('setupParallaxEffects', content)
        self.assertIn('setupTypingEffect', content)
    
    def test_directory_structure(self):
        """Test that directory structure is correct"""
        # Check common directory structure
        self.assertTrue((self.common_dir / "css").exists())
        self.assertTrue((self.common_dir / "js").exists())
        
        # Check home directory structure
        self.assertTrue(self.home_dir.exists())
        
        # Check that all referenced directories exist
        expected_dirs = [
            self.website_dir / "ai",
            self.website_dir / "community",
            self.website_dir / "config"
        ]
        
        for dir_path in expected_dirs:
            with self.subTest(dir_path=dir_path):
                self.assertTrue(dir_path.exists(), f"Expected directory {dir_path} does not exist")
    
    def test_ai_components(self):
        """Test that AI components exist and are functional"""
        ai_dir = self.website_dir / "ai"
        
        # Check for AI service files
        chatbot_service = ai_dir / "ChatbotService.py"
        content_recommender = ai_dir / "ContentRecommender.py"
        
        self.assertTrue(chatbot_service.exists())
        self.assertTrue(content_recommender.exists())
        
        # Test chatbot service content
        with open(chatbot_service, 'r', encoding='utf-8') as f:
            content = f.read()
            
        self.assertIn('class ChatbotService', content)
        self.assertIn('chatterbot', content)
        self.assertIn('get_response', content)

class TestWebsiteIntegration(unittest.TestCase):
    """Test website integration and functionality"""
    
    def test_css_variables_consistency(self):
        """Test that CSS variables are consistent across files"""
        website_dir = Path(__file__).parent.parent / "dadudekc website"
        common_css = website_dir / "common" / "css" / "common.css"
        home_css = website_dir / "home" / "home.css"
        
        # Read CSS files
        with open(common_css, 'r', encoding='utf-8') as f:
            common_content = f.read()
            
        with open(home_css, 'r', encoding='utf-8') as f:
            home_content = f.read()
            
        # Check that CSS variables are referenced in home.css
        self.assertIn('var(--primary-color)', home_content)
        self.assertIn('var(--text-dark)', home_content)
        self.assertIn('var(--shadow)', home_content)
    
    def test_js_class_integration(self):
        """Test that JavaScript classes integrate properly"""
        website_dir = Path(__file__).parent.parent / "dadudekc website"
        utils_js = website_dir / "common" / "js" / "utils.js"
        home_js = website_dir / "home" / "home.js"
        
        # Read JS files
        with open(utils_js, 'r', encoding='utf-8') as f:
            utils_content = f.read()
            
        with open(home_js, 'r', encoding='utf-8') as f:
            home_content = f.read()
            
        # Check that home.js uses utils
        self.assertIn('window.websiteUtils', home_content)
        self.assertIn('showLoading', home_content)
        self.assertIn('hideLoading', home_content)

if __name__ == '__main__':
    # Create test suite
    test_suite = unittest.TestSuite()
    
    # Add test cases
    test_suite.addTest(unittest.makeSuite(TestDaDudeKCWebsite))
    test_suite.addTest(unittest.makeSuite(TestWebsiteIntegration))
    
    # Run tests
    runner = unittest.TextTestRunner(verbosity=2)
    result = runner.run(test_suite)
    
    # Exit with appropriate code
    sys.exit(not result.wasSuccessful())

