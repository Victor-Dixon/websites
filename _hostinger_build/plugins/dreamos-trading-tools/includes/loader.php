<?php
/**
 * Runtime loader for DreamOS Trading Tools.
 */

if (!defined('ABSPATH')) {
    exit;
}

$dreamos_trading_tools_includes = [
    'class-thetradingrobotplugin-activator.php',
    'class-thetradingrobotplugin-deactivator.php',
    'class-thetradingrobotplugin-admin.php',
    'class-thetradingrobotplugin-runner.php',
    'class-thetradingrobotplugin.php',
];

foreach ($dreamos_trading_tools_includes as $dreamos_trading_tools_include) {
    $dreamos_trading_tools_path = DREAMOS_TRADING_TOOLS_PLUGIN_DIR . 'includes/' . $dreamos_trading_tools_include;
    if (file_exists($dreamos_trading_tools_path)) {
        require_once $dreamos_trading_tools_path;
    }
}
