<div class="trp-marketplace-grid">
    <?php foreach ($robots as $robot): ?>
        <div class="trp-robot-card">
            <div class="robot-header">
                <h3><?php echo esc_html($robot['name']); ?></h3>
                <span class="robot-type"><?php echo esc_html($robot['type']); ?></span>
            </div>
            <div class="robot-stats">
                <div class="stat">
                    <span class="label">Win Rate</span>
                    <span class="value"><?php echo esc_html($robot['win_rate']); ?>%</span>
                </div>
            </div>
            <div class="robot-footer">
                <span class="tier-badge <?php echo esc_attr($robot['tier']); ?>">
                    <?php echo ucfirst($robot['tier']); ?>
                </span>
                <button class="trp-btn btn-small">View Details</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>
