# 🔒 WordPress Security Patterns - SSOT Guide

**Created By:** Agent-8 (SSOT & System Integration Specialist)  
**Mission:** WP-SEC-003  
**Date:** 2025-10-17  
**For:** Agent-2, Agent-7, and all WordPress developers

---

## 🎯 **Purpose**

This document provides Single Source of Truth (SSOT) security patterns for FreeRide Investor WordPress site. All agents working on security fixes should use these patterns to ensure consistent security across all plugins.

---

## 📚 **Security Utilities Location**

**File:** `includes/security-utilities.php`

**Include in your plugin:**
```php
require_once get_template_directory() . '/includes/security-utilities.php';
```

Or in theme functions.php:
```php
require_once __DIR__ . '/includes/security-utilities.php';
```

---

## 🔐 **Common Security Patterns**

### **Pattern 1: Input Sanitization**

**Problem:** Unsanitized user input

**SSOT Solution:**
```php
// OLD (UNSAFE):
$user_input = $_POST['field_name'];

// NEW (SAFE):
$user_input = fri_sanitize_input($_POST['field_name'], 'text');

// Type-specific sanitization:
$email = fri_sanitize_input($_POST['email'], 'email');
$url = fri_sanitize_input($_POST['website'], 'url');
$number = fri_sanitize_input($_POST['count'], 'int');
$textarea = fri_sanitize_input($_POST['description'], 'textarea');
$html = fri_sanitize_input($_POST['content'], 'html');
```

**Available Types:**
- `text` - Default text sanitization
- `email` - Email addresses
- `url` - URLs
- `textarea` - Textarea content
- `html` - HTML content (allowed tags only)
- `int` - Integers
- `float` - Floating point numbers
- `boolean` - Boolean values
- `slug` - URL slugs
- `key` - Array keys
- `filename` - File names
- `user` - Usernames

---

### **Pattern 2: Output Escaping**

**Problem:** Unescaped output leading to XSS

**SSOT Solution:**
```php
// OLD (UNSAFE):
echo $user_input;
echo "<div id='$attr'>$content</div>";

// NEW (SAFE):
echo fri_escape_output($user_input, 'html');
echo '<div id="' . fri_escape_output($attr, 'attr') . '">' . fri_escape_output($content, 'html') . '</div>';
```

**Available Contexts:**
- `html` - HTML content
- `attr` - HTML attributes
- `url` - URLs
- `js` - JavaScript
- `textarea` - Textarea content
- `sql` - SQL (use prepare() instead!)

---

### **Pattern 3: SQL Injection Prevention**

**Problem:** Direct variable insertion in SQL queries

**SSOT Solution:**
```php
// OLD (UNSAFE):
$query = "SELECT * FROM $wpdb->posts WHERE ID = $_GET['id']";
$results = $wpdb->get_results($query);

// NEW (SAFE):
$id = fri_sanitize_input($_GET['id'], 'int');
$query = fri_prepare_query(
    "SELECT * FROM $wpdb->posts WHERE ID = %d",
    $id
);
$results = $wpdb->get_results($query);

// Multiple parameters:
$query = fri_prepare_query(
    "SELECT * FROM $wpdb->posts WHERE post_type = %s AND post_status = %s",
    'post',
    'publish'
);
```

**Rules:**
- ALWAYS use `fri_prepare_query()` for dynamic values
- Use `%s` for strings, `%d` for integers, `%f` for floats
- Sanitize input BEFORE passing to prepare()

---

### **Pattern 4: Nonce Verification**

**Problem:** CSRF vulnerabilities

**SSOT Solution:**

**Regular Forms:**
```php
// In form HTML:
<?php wp_nonce_field('my_action_name', 'my_nonce_name'); ?>

// In processing code:
fri_verify_nonce('my_nonce_name', 'my_action_name');
// Automatically dies if invalid

// Or check without dying:
if (!fri_verify_nonce('my_nonce_name', 'my_action_name', false)) {
    // Handle error
}
```

**AJAX Requests:**
```php
// In JavaScript:
$.ajax({
    url: ajaxurl,
    data: {
        action: 'my_ajax_action',
        nonce: '<?php echo wp_create_nonce('my_ajax_action'); ?>',
        // other data...
    }
});

// In PHP AJAX handler:
fri_verify_ajax_nonce('nonce', 'my_ajax_action');
// Dies with JSON error if invalid
```

---

### **Pattern 5: Capability Checks**

**Problem:** Unauthorized access to admin functions

**SSOT Solution:**

**Regular Checks:**
```php
// OLD (INCOMPLETE):
if (!is_admin()) {
    return;
}

// NEW (COMPLETE):
fri_check_capability('manage_options');
// Dies if user lacks capability

// Or check without dying:
if (!fri_check_capability('manage_options', false)) {
    // Handle error
}
```

**AJAX Checks:**
```php
fri_check_ajax_capability('manage_options');
// Dies with JSON error if unauthorized
```

**Common Capabilities:**
- `manage_options` - Administrators
- `edit_posts` - Editors and above
- `publish_posts` - Authors and above
- `edit_pages` - Page editors
- `upload_files` - Media uploaders

---

### **Pattern 6: Complete Security Check**

**Problem:** Need to check both nonce and capability

**SSOT Solution:**

**Regular Forms:**
```php
// One-line complete check:
fri_security_check('my_nonce_name', 'my_action_name', 'manage_options');
```

**AJAX Requests:**
```php
// One-line AJAX security:
fri_ajax_security_check('nonce', 'my_ajax_action', 'manage_options');
```

---

### **Pattern 7: File Upload Validation**

**Problem:** Unsafe file uploads

**SSOT Solution:**
```php
// Validate uploaded file:
$file = fri_validate_file_upload(
    $_FILES['my_file'],
    array('image/jpeg', 'image/png'),  // Allowed types
    5242880  // Max size (5MB)
);

if (is_wp_error($file)) {
    // Handle error:
    wp_die($file->get_error_message());
}

// File is safe, process it:
$upload = wp_handle_upload($file, array('test_form' => false));
```

---

### **Pattern 8: URL/POST Parameter Safety**

**Problem:** Unsafe parameter access

**SSOT Solution:**

**GET Parameters:**
```php
// OLD (UNSAFE):
$id = $_GET['id'];
$name = $_GET['name'];

// NEW (SAFE):
$id = fri_get_param('id', 'int', 0);  // Default to 0
$name = fri_get_param('name', 'text', '');  // Default to empty
$email = fri_get_param('email', 'email', '');
```

**POST Fields:**
```php
// OLD (UNSAFE):
$title = $_POST['title'];
$content = $_POST['content'];

// NEW (SAFE):
$title = fri_get_post_field('title', 'text', '');
$content = fri_get_post_field('content', 'textarea', '');
```

---

### **Pattern 9: Array Sanitization**

**Problem:** Sanitizing array data

**SSOT Solution:**
```php
// OLD (UNSAFE):
$data = $_POST['bulk_data'];  // Array

// NEW (SAFE):
$data = fri_sanitize_array($_POST['bulk_data'], 'text');

// All values in array are sanitized recursively
```

---

### **Pattern 10: Security Audit Logging**

**Problem:** No audit trail for security events

**SSOT Solution:**
```php
// Log security events:
fri_log_security_event('login_attempt', 'Failed login attempt', array(
    'username' => $username,
    'reason' => 'invalid_password'
));

fri_log_security_event('data_modification', 'User updated plugin settings', array(
    'plugin' => 'my-plugin',
    'settings' => $new_settings
));
```

---

## 🎯 **Quick Reference Checklist**

Use this checklist when fixing security issues:

### **Input Processing:**
- [ ] All `$_GET` variables sanitized with `fri_get_param()`
- [ ] All `$_POST` variables sanitized with `fri_get_post_field()` or `fri_sanitize_input()`
- [ ] All `$_REQUEST` variables sanitized
- [ ] All `$_COOKIE` variables sanitized
- [ ] File uploads validated with `fri_validate_file_upload()`

### **Output Display:**
- [ ] All dynamic output escaped with `fri_escape_output()`
- [ ] HTML attributes escaped with context 'attr'
- [ ] URLs escaped with context 'url'
- [ ] JavaScript escaped with context 'js'

### **Database Queries:**
- [ ] All queries with dynamic data use `fri_prepare_query()`
- [ ] Correct placeholders used (%s, %d, %f)
- [ ] No direct variable insertion in queries

### **Authorization:**
- [ ] Nonce verification on all forms with `fri_verify_nonce()`
- [ ] Capability checks on all admin actions with `fri_check_capability()`
- [ ] AJAX requests use `fri_ajax_security_check()`

### **Security Audit:**
- [ ] Critical actions logged with `fri_log_security_event()`

---

## 🔍 **Common Vulnerabilities & Fixes**

### **SQL Injection**

**Vulnerable Code:**
```php
$query = "SELECT * FROM {$wpdb->prefix}options WHERE option_name = '{$_POST['name']}'";
$result = $wpdb->get_row($query);
```

**Fixed Code:**
```php
$name = fri_sanitize_input($_POST['name'], 'text');
$query = fri_prepare_query(
    "SELECT * FROM {$wpdb->prefix}options WHERE option_name = %s",
    $name
);
$result = $wpdb->get_row($query);
```

---

### **XSS (Cross-Site Scripting)**

**Vulnerable Code:**
```php
echo '<div>' . $_GET['message'] . '</div>';
```

**Fixed Code:**
```php
$message = fri_get_param('message', 'text', '');
echo '<div>' . fri_escape_output($message, 'html') . '</div>';
```

---

### **CSRF (Cross-Site Request Forgery)**

**Vulnerable Code:**
```php
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    delete_option('my_option');
}
```

**Fixed Code:**
```php
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    fri_security_check('my_nonce', 'delete_option_action', 'manage_options');
    delete_option('my_option');
}
```

---

### **Unauthorized Access**

**Vulnerable Code:**
```php
add_action('wp_ajax_update_settings', 'update_settings_callback');
function update_settings_callback() {
    update_option('my_settings', $_POST['settings']);
    wp_send_json_success();
}
```

**Fixed Code:**
```php
add_action('wp_ajax_update_settings', 'update_settings_callback');
function update_settings_callback() {
    fri_ajax_security_check('nonce', 'update_settings_action', 'manage_options');
    $settings = fri_sanitize_array($_POST['settings'], 'text');
    update_option('my_settings', $settings);
    wp_send_json_success();
}
```

---

## 📊 **Testing Security Fixes**

After applying security patterns, test:

1. **Functionality:** Feature still works correctly
2. **Authorization:** Only authorized users can access
3. **Input Validation:** Invalid input is rejected
4. **XSS Prevention:** Script tags don't execute
5. **SQL Injection:** Special characters don't break queries

---

## 🤝 **Coordination Guidelines**

### **For Agent-2 (nextend-facebook-connect):**
- Include security-utilities.php at top of files
- Replace all sanitize_* calls with fri_sanitize_input()
- Add nonce verification to all forms
- Add capability checks to all admin functions
- Use fri_prepare_query() for all database queries

### **For Agent-7 (freeride plugins):**
- Include security-utilities.php at top of files
- Use fri_escape_output() for all dynamic output
- Add fri_ajax_security_check() to all AJAX handlers
- Validate file uploads with fri_validate_file_upload()
- Log critical actions with fri_log_security_event()

---

## 💡 **Best Practices**

1. **Defense in Depth:** Apply multiple security layers
2. **Fail Securely:** Default to denying access
3. **Least Privilege:** Only grant necessary permissions
4. **Input Validation:** Validate all user input
5. **Output Encoding:** Escape all dynamic output
6. **Audit Logging:** Log security-relevant events

---

## 📝 **Security Review Checklist**

Before marking an issue as fixed:

- [ ] Code uses SSOT security utilities
- [ ] All inputs sanitized
- [ ] All outputs escaped
- [ ] Nonces verified
- [ ] Capabilities checked
- [ ] Queries use prepare()
- [ ] Tested functionality
- [ ] Tested security (try to break it!)
- [ ] Logged critical actions

---

## 🚀 **Quick Start for Agents**

**Step 1:** Include security utilities
```php
require_once get_template_directory() . '/includes/security-utilities.php';
```

**Step 2:** Identify vulnerability type (SQL, XSS, CSRF, Auth)

**Step 3:** Apply appropriate pattern from this guide

**Step 4:** Test functionality and security

**Step 5:** Mark issue as fixed

---

## 📞 **Questions?**

**Agent-8 (SSOT Coordinator)** is available for:
- Security pattern clarification
- Complex vulnerability fixes
- Code review
- Coordination between agents

**Let's make FreeRide Investor secure with consistent, SSOT-compliant security!** 🔒

---

**Version:** 1.0.0  
**Last Updated:** 2025-10-17  
**Created By:** Agent-8 (WP-SEC-003)  
**Status:** Active - Use for all security fixes

#SSOT #WordPress #Security #FreeRideInvestor

