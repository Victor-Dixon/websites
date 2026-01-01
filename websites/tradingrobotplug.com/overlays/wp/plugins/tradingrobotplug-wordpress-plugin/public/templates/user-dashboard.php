<div class="trp-user-dashboard">
    <div class="dashboard-header">
        <h2>Welcome back, <?php echo esc_html(wp_get_current_user()->display_name); ?></h2>
        <div class="subscription-status">
            Current Plan: <span class="badge <?php echo esc_attr($subscription['plan_id']); ?>">
                <?php echo ucfirst($subscription['plan_id']); ?>
            </span>
            <?php if ($subscription['plan_id'] === 'free'): ?>
                <a href="/pricing" class="trp-btn btn-small btn-primary">Upgrade</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="dashboard-tabs">
        <button class="tab-btn active" onclick="openTab(event, 'overview')">Overview</button>
        <button class="tab-btn" onclick="openTab(event, 'my-robots')">My Robots</button>
        <button class="tab-btn" onclick="openTab(event, 'accounts')">Trading Accounts</button>
        <button class="tab-btn" onclick="openTab(event, 'settings')">Settings</button>
    </div>

    <div id="overview" class="tab-content active">
        <h3>Performance Summary</h3>
        <?php include TRADINGROBOTPLUG_PLUGIN_DIR . 'public/templates/performance-dashboard.php'; ?>
    </div>

    <div id="my-robots" class="tab-content">
        <h3>Active Robots</h3>
        <p>No active robots found. <a href="/marketplace">Browse Marketplace</a></p>
    </div>

    <div id="accounts" class="tab-content">
        <h3>Connected Accounts</h3>
        <div class="account-card">
            <div class="account-info">
                <strong>Alpaca (Paper)</strong>
                <span>Connected</span>
            </div>
            <button class="trp-btn btn-small btn-secondary">Configure</button>
        </div>
    </div>

    <div id="settings" class="tab-content">
        <h3>Account Settings</h3>
        <p>Manage your notification preferences and API keys.</p>
    </div>
</div>

<script>
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
        tabcontent[i].classList.remove("active");
    }
    tablinks = document.getElementsByClassName("tab-btn");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.className += " active";
}
</script>

<style>
/* Dashboard Specific Styles */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.dashboard-tabs {
    margin-bottom: 20px;
    border-bottom: 1px solid #ddd;
}

.tab-btn {
    background: none;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    font-size: 16px;
    color: #666;
    border-bottom: 2px solid transparent;
}

.tab-btn.active {
    color: #007bff;
    border-bottom: 2px solid #007bff;
    font-weight: bold;
}

.tab-content {
    display: none;
    animation: fadeEffect 0.5s;
}

.tab-content.active {
    display: block;
}

@keyframes fadeEffect {
    from {opacity: 0;}
    to {opacity: 1;}
}

.account-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
</style>
