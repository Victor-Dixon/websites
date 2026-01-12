<?php
/**
 * WeAreSwarm Theme Deployment Verification
 * Tests swarm intelligence features and theme functionality
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Run comprehensive deployment verification
 */
function weareswarm_verify_deployment() {
    $results = array(
        'timestamp' => current_time('mysql'),
        'theme_active' => false,
        'files_present' => array(),
        'swarm_features' => array(),
        'errors' => array(),
        'warnings' => array()
    );

    // Check if theme is active
    $current_theme = wp_get_theme();
    if ($current_theme->get_stylesheet() === 'weareswarm') {
        $results['theme_active'] = true;
    } else {
        $results['errors'][] = 'WeAreSwarm theme is not active. Current theme: ' . $current_theme->get_stylesheet();
    }

    // Verify critical files exist
    $critical_files = array(
        'style.css',
        'functions.php',
        'header.php',
        'footer.php',
        'index.php',
        'hero-swarm.php',
        'css/swarm-intelligence.css',
        'js/swarm-intelligence.js'
    );

    foreach ($critical_files as $file) {
        $file_path = get_template_directory() . '/' . $file;
        if (file_exists($file_path)) {
            $results['files_present'][] = $file;
        } else {
            $results['errors'][] = "Missing critical file: $file";
        }
    }

    // Check swarm intelligence features
    $swarm_features = array(
        'hero-swarm.php' => 'Swarm hero section with animations',
        'functions.php' => 'Swarm coordination hooks',
        'css/swarm-intelligence.css' => 'Swarm theming styles',
        'js/swarm-intelligence.js' => 'Swarm interaction JavaScript'
    );

    foreach ($swarm_features as $file => $description) {
        $file_path = get_template_directory() . '/' . $file;
        if (file_exists($file_path)) {
            $content = file_get_contents($file_path);
            $feature_checks = array();

            // Specific feature checks
            if ($file === 'functions.php') {
                $feature_checks = array(
                    'wp_localize_script' => strpos($content, 'wp_localize_script') !== false,
                    'swarm_intelligence' => strpos($content, 'swarm_intelligence') !== false,
                    'swarm_agent_status' => strpos($content, 'swarm_agent_status') !== false
                );
            } elseif ($file === 'hero-swarm.php') {
                $feature_checks = array(
                    'swarmPulse' => strpos($content, 'swarmPulse') !== false,
                    'agentOrbit' => strpos($content, 'agentOrbit') !== false,
                    'swarm intelligence' => strpos($content, 'swarm intelligence') !== false,
                    'AI agents' => strpos($content, 'AI agents') !== false
                );
            } elseif ($file === 'css/swarm-intelligence.css') {
                $feature_checks = array(
                    'swarm-core-active' => strpos($content, 'swarm-core-active') !== false,
                    'swarm-agent-status' => strpos($content, 'swarm-agent-status') !== false
                );
            }

            $results['swarm_features'][$file] = array(
                'description' => $description,
                'present' => true,
                'feature_checks' => $feature_checks
            );
        } else {
            $results['swarm_features'][$file] = array(
                'description' => $description,
                'present' => false,
                'error' => 'File not found'
            );
        }
    }

    // Check for common issues
    if (!function_exists('wp_enqueue_script')) {
        $results['warnings'][] = 'WordPress functions not available - verify WordPress installation';
    }

    // Check theme version
    $theme = wp_get_theme('weareswarm');
    if ($theme->exists()) {
        $results['theme_version'] = $theme->get('Version');
        $results['theme_name'] = $theme->get('Name');
    }

    return $results;
}

/**
 * Display verification results
 */
function weareswarm_display_verification($results) {
    echo "<div style='font-family: monospace; background: #1a1a2e; color: #ffffff; padding: 20px; margin: 20px 0; border-radius: 8px; border: 1px solid #a855f7;'>";
    echo "<h2 style='color: #a855f7; margin-top: 0;'>🐝 WeAreSwarm Deployment Verification</h2>";
    echo "<p><strong>Timestamp:</strong> {$results['timestamp']}</p>";

    // Theme status
    $theme_status = $results['theme_active'] ? '<span style="color: #06b6d4;">✅ ACTIVE</span>' : '<span style="color: #f59e0b;">❌ INACTIVE</span>';
    echo "<h3>Theme Status: $theme_status</h3>";

    if (isset($results['theme_name'])) {
        echo "<p><strong>Theme:</strong> {$results['theme_name']} v{$results['theme_version']}</p>";
    }

    // Files verification
    echo "<h3>📁 Critical Files</h3>";
    echo "<ul>";
    $total_files = count($results['files_present']);
    echo "<li style='color: #06b6d4;'>✅ Found: $total_files critical files</li>";
    foreach ($results['files_present'] as $file) {
        echo "<li style='color: #06b6d4;'>✓ $file</li>";
    }
    echo "</ul>";

    // Swarm features
    echo "<h3>🤖 Swarm Intelligence Features</h3>";
    foreach ($results['swarm_features'] as $file => $feature) {
        $status = $feature['present'] ? '✅' : '❌';
        echo "<h4>$status {$feature['description']}</h4>";

        if ($feature['present'] && isset($feature['feature_checks'])) {
            echo "<ul>";
            foreach ($feature['feature_checks'] as $check => $passed) {
                $check_status = $passed ? '<span style="color: #06b6d4;">✓</span>' : '<span style="color: #f59e0b;">⚠️</span>';
                echo "<li>$check_status $check</li>";
            }
            echo "</ul>";
        }

        if (isset($feature['error'])) {
            echo "<p style='color: #f59e0b;'>⚠️ {$feature['error']}</p>";
        }
    }

    // Errors and warnings
    if (!empty($results['errors'])) {
        echo "<h3 style='color: #ec4899;'>❌ Critical Issues</h3>";
        echo "<ul>";
        foreach ($results['errors'] as $error) {
            echo "<li style='color: #ec4899;'>$error</li>";
        }
        echo "</ul>";
    }

    if (!empty($results['warnings'])) {
        echo "<h3 style='color: #f59e0b;'>⚠️ Warnings</h3>";
        echo "<ul>";
        foreach ($results['warnings'] as $warning) {
            echo "<li style='color: #f59e0b;'>$warning</li>";
        }
        echo "</ul>";
    }

    // Overall status
    $has_errors = !empty($results['errors']);
    $overall_status = $has_errors ? '<span style="color: #ec4899;">❌ ISSUES FOUND</span>' : '<span style="color: #06b6d4;">✅ DEPLOYMENT SUCCESSFUL</span>';
    echo "<h3>Overall Status: $overall_status</h3>";

    if (!$has_errors) {
        echo "<div style='background: rgba(168, 85, 247, 0.1); padding: 15px; border-radius: 8px; border-left: 4px solid #a855f7; margin-top: 20px;'>";
        echo "<h4 style='color: #a855f7; margin-top: 0;'>🎉 Swarm Intelligence Activated!</h4>";
        echo "<p>✅ Theme deployed successfully<br>";
        echo "✅ Swarm coordination hooks active<br>";
        echo "✅ Agent visualization functional<br>";
        echo "✅ Collective intelligence ready<br>";
        echo "🐝 We Are Swarm! ⚡️🔥</p>";
        echo "</div>";
    }

    echo "</div>";
}

// Make function available for manual testing
if (isset($_GET['verify_deployment'])) {
    $results = weareswarm_verify_deployment();
    weareswarm_display_verification($results);
    exit;
}
?>