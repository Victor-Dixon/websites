<div class="wrap">
    <h1>Trading Robot Plug Dashboard</h1>
    <p>Welcome to the Trading Robot Service Platform integration.</p>
    
    <div class="card">
        <h2>Connection Status</h2>
        <p>Status: <span style="color: green; font-weight: bold;">Connected</span> (Mock Mode)</p>
    </div>

    <div class="card">
        <h2>Quick Actions</h2>
        <ul>
            <li><a href="<?php echo admin_url('admin.php?page=tradingrobotplug-settings'); ?>">Configure API Settings</a></li>
            <li><a href="#" target="_blank">View API Documentation</a></li>
        </ul>
    </div>
    
    <div class="card">
        <h2>Available Shortcodes</h2>
        <code>[trading_robot_pricing]</code> - Displays the pricing table.<br>
        <code>[trading_robot_performance]</code> - Displays the performance dashboard.<br>
        <code>[trading_robot_marketplace]</code> - Displays the robot marketplace.<br>
        <code>[trading_robot_dashboard]</code> - Displays the full user dashboard.
    </div>
</div>
