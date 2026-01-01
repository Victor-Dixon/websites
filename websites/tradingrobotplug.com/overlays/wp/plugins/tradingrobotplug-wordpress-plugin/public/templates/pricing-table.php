<div class="trp-pricing-container">
    <?php foreach ($plans as $plan): ?>
        <div class="trp-pricing-card <?php echo $plan['id'] === 'mid' ? 'popular' : ''; ?>">
            <?php if ($plan['id'] === 'mid'): ?>
                <div class="trp-badge">Most Popular</div>
            <?php endif; ?>
            <h3><?php echo esc_html($plan['name']); ?></h3>
            <div class="price">
                $<?php echo esc_html($plan['price']); ?><span>/mo</span>
            </div>
            <ul class="features">
                <?php foreach ($plan['features'] as $feature): ?>
                    <li><?php echo esc_html($feature); ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="/checkout?plan=<?php echo esc_attr($plan['id']); ?>" class="trp-btn <?php echo $plan['id'] === 'mid' ? 'btn-primary' : 'btn-secondary'; ?>">
                <?php echo $plan['price'] == 0 ? 'Start Free' : 'Subscribe'; ?>
            </a>
        </div>
    <?php endforeach; ?>
</div>
