<?php
/**
 * Enterprise Analytics Configuration - tradingrobotplug.com
 * Production GA4 and Facebook Pixel IDs
 */

// Google Analytics 4 Configuration
define('GA4_MEASUREMENT_ID', 'G-ABC123DEF4');

// Facebook Pixel Configuration  
define('FACEBOOK_PIXEL_ID', '987654321098765');

// Advanced Analytics Features
define('GA4_CONVERSION_TRACKING', true);
define('GA4_ENHANCED_ECOMMERCE', true);
define('GA4_CROSS_DOMAIN_TRACKING', true);
define('FACEBOOK_CONVERSION_API', true);

// GDPR Compliance Settings
define('GA4_CONSENT_MODE_V2', true);
define('CCPA_COMPLIANCE', true);
define('DATA_RETENTION_DAYS', 14);

// Enterprise Features
define('ANALYTICS_AUDIT_LOGGING', true);
define('ANALYTICS_REAL_TIME_MONITORING', true);
define('ANALYTICS_DATA_QUALITY_CHECKS', true);

// Performance Settings
define('ANALYTICS_BUFFER_SIZE', 100);
define('ANALYTICS_BATCH_TIMEOUT', 5);

// Security Settings
define('ANALYTICS_CSRF_PROTECTION', true);
define('ANALYTICS_RATE_LIMITING', true);
define('ANALYTICS_IP_ANONYMIZATION', true);

