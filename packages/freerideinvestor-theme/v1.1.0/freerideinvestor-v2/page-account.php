<?php
/**
 * Template Name: My Account
 * Template Post Type: page
 *
 * User account dashboard template for FreeRide Investor
 *
 * @package FreeRideInvestor_V2
 */

// Redirect if not logged in
if (!is_user_logged_in()) {
    wp_redirect('/login');
    exit;
}

get_header();
$current_user = wp_get_current_user();
$user_membership = freerideinvestor_get_user_membership();
$membership_data = freerideinvestor_get_membership_hierarchy()[$user_membership];
?>

<main class="account-page">
    <div class="container">
        <div class="account-header">
            <div class="user-info">
                <div class="user-avatar">
                    <span class="avatar-initials"><?php echo esc_html(substr($current_user->display_name, 0, 2)); ?></span>
                </div>
                <div class="user-details">
                    <h1><?php echo esc_html($current_user->display_name); ?></h1>
                    <div class="membership-badge membership-<?php echo esc_attr($user_membership); ?>">
                        <?php echo esc_html($membership_data['name']); ?> Member
                    </div>
                    <p class="user-email"><?php echo esc_html($current_user->user_email); ?></p>
                </div>
            </div>
            <div class="account-actions">
                <a href="/pricing" class="btn btn-outline">Upgrade Plan</a>
                <a href="<?php echo wp_logout_url(home_url()); ?>" class="btn btn-secondary">Logout</a>
            </div>
        </div>

        <div class="account-grid">
            <!-- Membership Status -->
            <div class="account-card membership-card">
                <h3>Current Membership</h3>
                <div class="membership-details">
                    <div class="membership-level">
                        <span class="level-name"><?php echo esc_html($membership_data['name']); ?></span>
                        <?php if ($user_membership !== MEMBERSHIP_FREE): ?>
                            <span class="level-price">$<?php echo esc_html($membership_data['price']); ?>/month</span>
                        <?php else: ?>
                            <span class="level-price">Free</span>
                        <?php endif; ?>
                    </div>

                    <div class="membership-features">
                        <h4>Plan Features</h4>
                        <ul>
                            <?php foreach ($membership_data['features'] as $feature): ?>
                            <li>
                                <span class="feature-check">✓</span>
                                <?php echo esc_html($feature); ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <?php if ($user_membership !== MEMBERSHIP_PRO): ?>
                    <div class="upgrade-prompt">
                        <p>Ready to unlock more features?</p>
                        <a href="/pricing" class="btn btn-primary">View Upgrade Options</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Account Settings -->
            <div class="account-card settings-card">
                <h3>Account Settings</h3>
                <div class="settings-section">
                    <h4>Profile Information</h4>
                    <form class="settings-form" id="profileForm">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="firstName" value="<?php echo esc_attr($current_user->first_name); ?>">
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="lastName" value="<?php echo esc_attr($current_user->last_name); ?>">
                        </div>
                        <div class="form-group">
                            <label for="displayName">Display Name</label>
                            <input type="text" id="displayName" name="displayName" value="<?php echo esc_attr($current_user->display_name); ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo esc_attr($current_user->user_email); ?>" readonly>
                            <small>Email changes require verification. <a href="/contact">Contact support</a> to update.</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>

                <div class="settings-section">
                    <h4>Password & Security</h4>
                    <form class="settings-form" id="passwordForm">
                        <div class="form-group">
                            <label for="currentPassword">Current Password</label>
                            <input type="password" id="currentPassword" name="currentPassword">
                        </div>
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" id="newPassword" name="newPassword">
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm New Password</label>
                            <input type="password" id="confirmPassword" name="confirmPassword">
                        </div>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>
            </div>

            <!-- Subscription History -->
            <div class="account-card subscription-card">
                <h3>Subscription History</h3>
                <?php
                // Get user's subscription history (this would typically come from a custom table or payment processor)
                $subscription_start = get_user_meta($current_user->ID, 'freerideinvestor_membership_start', true);
                $subscription_history = get_user_meta($current_user->ID, 'freerideinvestor_subscription_history', true) ?: array();
                ?>

                <div class="subscription-status">
                    <div class="status-item">
                        <span class="status-label">Member Since:</span>
                        <span class="status-value"><?php echo $subscription_start ? date('M j, Y', strtotime($subscription_start)) : 'N/A'; ?></span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Current Plan:</span>
                        <span class="status-value"><?php echo esc_html($membership_data['name']); ?></span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Next Billing:</span>
                        <span class="status-value"><?php echo $user_membership !== MEMBERSHIP_FREE ? date('M j, Y', strtotime('+1 month', strtotime($subscription_start ?: 'now'))) : 'N/A'; ?></span>
                    </div>
                </div>

                <?php if (!empty($subscription_history)): ?>
                <div class="subscription-history">
                    <h4>Recent Activity</h4>
                    <div class="history-list">
                        <?php foreach (array_slice($subscription_history, 0, 5) as $activity): ?>
                        <div class="history-item">
                            <span class="history-date"><?php echo date('M j, Y', strtotime($activity['date'])); ?></span>
                            <span class="history-description"><?php echo esc_html($activity['description']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="no-history">
                    <p>No subscription activity yet.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Performance Dashboard -->
            <div class="account-card performance-card">
                <h3>Trading Performance</h3>
                <div class="performance-overview">
                    <div class="performance-metrics">
                        <div class="metric">
                            <span class="metric-value">+12.4%</span>
                            <span class="metric-label">Total Return</span>
                        </div>
                        <div class="metric">
                            <span class="metric-value">68.2%</span>
                            <span class="metric-label">Win Rate</span>
                        </div>
                        <div class="metric">
                            <span class="metric-value">1.8:1</span>
                            <span class="metric-label">Risk-Reward</span>
                        </div>
                    </div>

                    <div class="performance-chart-placeholder">
                        <div class="chart-placeholder">
                            <span class="chart-icon">📈</span>
                            <p>Performance Chart</p>
                            <small>Coming Soon</small>
                        </div>
                    </div>
                </div>

                <div class="performance-actions">
                    <a href="/trading-strategies" class="btn btn-outline">View Strategies</a>
                    <button class="btn btn-primary" onclick="exportPerformance()">Export Data</button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Profile update functionality
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profileForm');
    const passwordForm = document.getElementById('passwordForm');

    // Profile form submission
    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(profileForm);
        const submitBtn = profileForm.querySelector('.btn-primary');
        const originalText = submitBtn.textContent;

        submitBtn.textContent = 'Updating...';
        submitBtn.disabled = true;

        // Simulate AJAX call
        setTimeout(() => {
            submitBtn.textContent = 'Profile Updated!';
            submitBtn.style.background = '#48bb78';

            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.style.background = '';
                submitBtn.disabled = false;
            }, 2000);
        }, 1000);
    });

    // Password form submission
    passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (newPassword !== confirmPassword) {
            alert('Passwords do not match!');
            return;
        }

        const submitBtn = passwordForm.querySelector('.btn-primary');
        const originalText = submitBtn.textContent;

        submitBtn.textContent = 'Changing...';
        submitBtn.disabled = true;

        // Simulate AJAX call
        setTimeout(() => {
            submitBtn.textContent = 'Password Changed!';
            submitBtn.style.background = '#48bb78';
            passwordForm.reset();

            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.style.background = '';
                submitBtn.disabled = false;
            }, 2000);
        }, 1000);
    });
});

function exportPerformance() {
    alert('Performance data export feature coming soon!');
    // In a real implementation, this would generate and download a CSV/PDF report
}
</script>

<style>
/* Account Page Styles */
.account-page {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    padding: 2rem 0 4rem;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.account-header {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e2e8f0;
    padding: 2rem;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.user-avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-initials {
    font-size: 2rem;
    color: white;
    font-weight: 600;
}

.user-details h1 {
    font-size: 2rem;
    color: #1a202c;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.membership-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.membership-free { background: #a0aec0; }
.membership-basic { background: #4299e1; }
.membership-premium { background: #48bb78; }
.membership-pro { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

.user-email {
    color: #718096;
    font-size: 0.9rem;
    margin-top: 0.25rem;
}

.account-actions {
    display: flex;
    gap: 1rem;
}

.account-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 2rem;
}

.account-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.account-card h3 {
    font-size: 1.5rem;
    color: #1a202c;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.membership-card,
.settings-card,
.subscription-card,
.performance-card {
    padding: 2rem;
}

.membership-details {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.membership-level {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.level-name {
    font-size: 1.3rem;
    font-weight: 600;
    color: #1a202c;
}

.level-price {
    font-size: 1.2rem;
    font-weight: 600;
    color: #48bb78;
}

.membership-features h4 {
    color: #2d3748;
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.membership-features ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.membership-features li {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.5rem 0;
    color: #4a5568;
}

.feature-check {
    color: #48bb78;
    font-weight: 600;
    font-size: 1.1rem;
    margin-top: 0.1rem;
}

.upgrade-prompt {
    text-align: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    color: white;
}

.upgrade-prompt p {
    margin-bottom: 1rem;
    opacity: 0.9;
}

/* Settings Styles */
.settings-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #f7fafc;
}

.settings-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.settings-section h4 {
    color: #2d3748;
    margin-bottom: 1.5rem;
    font-size: 1.2rem;
}

.settings-form {
    display: grid;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 500;
    color: #2d3748;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.form-group input {
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: #00d4ff;
    box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
}

.form-group input[readonly] {
    background: #f8fafc;
    cursor: not-allowed;
}

.form-group small {
    margin-top: 0.25rem;
    color: #718096;
    font-size: 0.8rem;
}

.form-group small a {
    color: #00d4ff;
}

/* Subscription History */
.subscription-status {
    display: grid;
    gap: 1rem;
    margin-bottom: 2rem;
}

.status-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.status-label {
    font-weight: 500;
    color: #2d3748;
}

.status-value {
    font-weight: 600;
    color: #1a202c;
}

.subscription-history h4 {
    color: #2d3748;
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.history-list {
    display: grid;
    gap: 0.75rem;
}

.history-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
}

.history-date {
    font-size: 0.9rem;
    color: #718096;
    font-weight: 500;
}

.history-description {
    font-size: 0.9rem;
    color: #4a5568;
}

.no-history {
    text-align: center;
    padding: 2rem;
    color: #718096;
}

/* Performance Dashboard */
.performance-overview {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.performance-metrics {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.metric {
    text-align: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.metric-value {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: #00d4ff;
    margin-bottom: 0.5rem;
}

.metric-label {
    font-size: 0.8rem;
    color: #718096;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.performance-chart-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
    background: #f8fafc;
    border-radius: 8px;
    border: 2px dashed #e2e8f0;
}

.chart-placeholder {
    text-align: center;
    color: #718096;
}

.chart-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.chart-placeholder p {
    margin: 0.5rem 0;
    font-weight: 500;
}

.chart-placeholder small {
    opacity: 0.7;
}

.performance-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

/* Button Styles */
.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    display: inline-block;
    text-align: center;
    cursor: pointer;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 212, 255, 0.4);
}

.btn-outline {
    background: transparent;
    color: #48bb78;
    border: 2px solid #48bb78;
}

.btn-outline:hover {
    background: #48bb78;
    color: white;
}

.btn-secondary {
    background: #e2e8f0;
    color: #4a5568;
}

.btn-secondary:hover {
    background: #cbd5e0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .account-header {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }

    .user-info {
        flex-direction: column;
        gap: 1rem;
    }

    .account-actions {
        justify-content: center;
    }

    .account-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .membership-level {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }

    .performance-overview {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .performance-metrics {
        grid-template-columns: 1fr;
    }

    .history-item {
        flex-direction: column;
        gap: 0.25rem;
        align-items: flex-start;
    }

    .container {
        padding: 0 1rem;
    }
}

@media (max-width: 480px) {
    .account-header {
        padding: 1.5rem;
    }

    .user-details h1 {
        font-size: 1.5rem;
    }

    .account-card {
        padding: 1.5rem;
    }

    .membership-level,
    .status-item,
    .history-item {
        padding: 0.75rem;
    }
}
</style>

<?php get_footer(); ?>