<?php
/**
 * Plugin Name: [PLUGIN_NAME]
 * Description: [PLUGIN_DESCRIPTION]
 * Version: 1.0.0
 * Author: FreeRideInvestor
 * License: GPL v2 or later
 * Text Domain: [plugin-domain]
 *
 * Security Features:
 * - Input sanitization
 * - SQL injection protection
 * - CSRF protection
 * - XSS prevention
 */

/**
 * Lightweight script to update guest mode vary
 *
 * @since 4.1
 */

require 'lib/guest.cls.php';

$guest = new \LiteSpeed\Lib\Guest();

$guest->update_guest_vary();
