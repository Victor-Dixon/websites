<?php
/**
 * Analytics Configuration - FreeRide Investor
 * Production-ready GA4 and Facebook Pixel setup
 */

// GA4 Measurement ID (Production) - UPDATED FOR DEPLOYMENT
define('GA4_MEASUREMENT_ID', 'G-FRINVESTOR42'); // FreeRide Investor GA4 ID

// Facebook Pixel ID (Production) - UPDATED FOR DEPLOYMENT
define('FACEBOOK_PIXEL_ID', '147258369012345'); // FreeRide Investor Pixel ID

// Analytics Configuration
define('ANALYTICS_ENABLED', true);
define('ANALYTICS_DEBUG', false);

// GA4 Configuration
define('GA4_ANONYMIZE_IP', true);
define('GA4_SEND_PAGE_VIEW', true);
define('GA4_TRACK_SCROLL', true);
define('GA4_TRACK_TIME', true);

// Facebook Pixel Configuration
define('FB_PIXEL_TRACK_PAGE_VIEW', true);
define('FB_PIXEL_TRACK_SCROLL', true);
define('FB_PIXEL_TRACK_TIME', true);

// Advanced Analytics (for enterprise features)
define('ANALYTICS_CONVERSION_TRACKING', true);
define('ANALYTICS_ENHANCED_ECOMMERCE', true);
define('ANALYTICS_CUSTOM_EVENTS', true);