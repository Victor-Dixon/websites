<?php
/**
 * Template Name: Pricing
 * Template Post Type: page
 *
 * Pricing page template for FreeRide Investor membership plans
 *
 * @package FreeRideInvestor_V2
 */

get_header();
?>

<main class="pricing-page">
    <!-- Hero Section -->
    <section class="pricing-hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Choose Your Trading Advantage</h1>
                <p class="hero-subtitle">Unlock institutional-grade strategies and tools that put professional trading power in your hands.</p>
                <div class="hero-features">
                    <div class="feature-item">
                        <span class="feature-icon">🔒</span>
                        <span>30-Day Money Back</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">⚡</span>
                        <span>Cancel Anytime</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">🎯</span>
                        <span>No Hidden Fees</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Cards -->
    <section class="pricing-section">
        <div class="container">
            <div class="pricing-grid">
                <?php
                $hierarchy = freerideinvestor_get_membership_hierarchy();
                $current_membership = freerideinvestor_get_user_membership();

                foreach ($hierarchy as $level => $data):
                    $is_current = ($level === $current_membership);
                    $is_free = ($level === MEMBERSHIP_FREE);
                    $is_recommended = ($level === MEMBERSHIP_PREMIUM);
                ?>
                <div class="pricing-card <?php echo $is_recommended ? 'recommended' : ''; ?> <?php echo $is_current ? 'current' : ''; ?>">
                    <?php if ($is_current): ?>
                        <div class="current-badge">Current Plan</div>
                    <?php elseif ($is_recommended): ?>
                        <div class="recommended-badge">Most Popular</div>
                    <?php endif; ?>

                    <div class="card-header">
                        <h3><?php echo esc_html($data['name']); ?></h3>
                        <div class="price">
                            <?php if ($is_free): ?>
                                <span class="price-amount">Free</span>
                            <?php else: ?>
                                <span class="price-amount">$<?php echo esc_html($data['price']); ?></span>
                                <span class="price-period">/month</span>
                            <?php endif; ?>
                        </div>
                        <p class="card-description">
                            <?php
                            switch ($level) {
                                case MEMBERSHIP_FREE:
                                    echo "Perfect for getting started with trading education.";
                                    break;
                                case MEMBERSHIP_BASIC:
                                    echo "Ideal for serious traders wanting consistent results.";
                                    break;
                                case MEMBERSHIP_PREMIUM:
                                    echo "Complete toolkit for professional-level trading.";
                                    break;
                                case MEMBERSHIP_PRO:
                                    echo "Everything plus personal strategy development.";
                                    break;
                            }
                            ?>
                        </p>
                    </div>

                    <div class="card-features">
                        <ul>
                            <?php foreach ($data['features'] as $feature): ?>
                            <li>
                                <span class="feature-check">✓</span>
                                <?php echo esc_html($feature); ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="card-action">
                        <?php if ($is_current): ?>
                            <button class="btn btn-secondary" disabled>Current Plan</button>
                        <?php elseif ($is_free): ?>
                            <a href="/register" class="btn btn-outline">Get Started Free</a>
                        <?php else: ?>
                            <button class="btn btn-primary upgrade-btn"
                                    data-level="<?php echo esc_attr($level); ?>"
                                    data-price="<?php echo esc_attr($data['price']); ?>">
                                <?php echo $is_recommended ? 'Get Premium' : 'Upgrade Now'; ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="pricing-faq">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <h3>Can I cancel anytime?</h3>
                    <p>Yes, you can cancel your subscription at any time. You'll retain access to premium features until the end of your billing period.</p>
                </div>

                <div class="faq-item">
                    <h3>Is there a money-back guarantee?</h3>
                    <p>We offer a 30-day money-back guarantee. If you're not completely satisfied, contact us within 30 days for a full refund.</p>
                </div>

                <div class="faq-item">
                    <h3>Can I change plans?</h3>
                    <p>Absolutely! You can upgrade or downgrade your plan at any time. Changes take effect immediately, with prorated billing.</p>
                </div>

                <div class="faq-item">
                    <h3>What payment methods do you accept?</h3>
                    <p>We accept all major credit cards, PayPal, and bank transfers. All payments are processed securely through Stripe.</p>
                </div>

                <div class="faq-item">
                    <h3>Do you offer discounts for annual plans?</h3>
                    <p>Yes! Annual plans receive a 20% discount. Contact our sales team for enterprise pricing and custom solutions.</p>
                </div>

                <div class="faq-item">
                    <h3>Is my payment information secure?</h3>
                    <p>We use bank-level encryption and never store your payment information. All transactions are processed through certified PCI-compliant gateways.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="pricing-cta">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Transform Your Trading?</h2>
                <p>Join thousands of traders who have elevated their results with our proven strategies and tools.</p>
                <div class="cta-buttons">
                    <a href="/contact" class="btn btn-secondary">Have Questions?</a>
                    <a href="#pricing" class="btn btn-primary">Start Free Trial</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Upgrade Modal -->
    <div id="upgradeModal" class="upgrade-modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Upgrade Your Membership</h3>
                <button class="modal-close" onclick="closeUpgradeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="upgradeContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Upgrade modal functionality
document.addEventListener('DOMContentLoaded', function() {
    const upgradeButtons = document.querySelectorAll('.upgrade-btn');
    const modal = document.getElementById('upgradeModal');
    const upgradeContent = document.getElementById('upgradeContent');

    upgradeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const level = this.getAttribute('data-level');
            const price = this.getAttribute('data-price');

            // Populate modal content
            upgradeContent.innerHTML = `
                <div class="upgrade-summary">
                    <h4>Upgrade to ${level.charAt(0).toUpperCase() + level.slice(1)} Membership</h4>
                    <div class="upgrade-price">$${price}<span>/month</span></div>
                    <div class="upgrade-features">
                        <h5>You'll get access to:</h5>
                        <ul>
                            ${getFeaturesForLevel(level).map(feature => `<li>${feature}</li>`).join('')}
                        </ul>
                    </div>
                    <div class="upgrade-actions">
                        <button class="btn btn-secondary" onclick="closeUpgradeModal()">Cancel</button>
                        <button class="btn btn-primary" onclick="processUpgrade('${level}')">Upgrade Now</button>
                    </div>
                </div>
            `;

            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
    });
});

function getFeaturesForLevel(level) {
    const features = {
        basic: ['Advanced strategies', 'Real-time alerts', 'Performance reports', 'Email support'],
        premium: ['Institutional strategies', 'Portfolio management', 'Priority support', 'API access', 'Custom alerts'],
        pro: ['All Premium features', 'Direct strategy access', 'Personal account manager', 'Custom strategy development', 'White-label solutions']
    };
    return features[level] || [];
}

function closeUpgradeModal() {
    document.getElementById('upgradeModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function processUpgrade(level) {
    // This would integrate with your payment processor (Stripe, PayPal, etc.)
    alert(`Redirecting to payment processor for ${level} membership upgrade...`);

    // In a real implementation, this would:
    // 1. Redirect to payment processor
    // 2. Process payment
    // 3. Update user membership
    // 4. Redirect to success page
}

// Close modal when clicking outside
document.getElementById('upgradeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUpgradeModal();
    }
});
</script>

<style>
/* Pricing Page Styles */
.pricing-page {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Hero Section */
.pricing-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    color: white;
    padding: 6rem 0;
    text-align: center;
}

.hero-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 2rem;
}

.hero-content h1 {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    line-height: 1.1;
}

.hero-subtitle {
    font-size: 1.4rem;
    opacity: 0.9;
    margin-bottom: 3rem;
    line-height: 1.6;
}

.hero-features {
    display: flex;
    justify-content: center;
    gap: 3rem;
    flex-wrap: wrap;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: white;
    opacity: 0.9;
}

.feature-icon {
    font-size: 1.5rem;
}

/* Pricing Section */
.pricing-section {
    padding: 6rem 0;
    background: #f8fafc;
}

.pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.pricing-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e2e8f0;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.pricing-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.pricing-card.recommended {
    border-color: #48bb78;
    box-shadow: 0 4px 20px rgba(72, 187, 120, 0.2);
}

.pricing-card.current {
    border-color: #4299e1;
    box-shadow: 0 4px 20px rgba(66, 153, 225, 0.2);
}

.recommended-badge,
.current-badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: #48bb78;
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.current-badge {
    background: #4299e1;
}

.card-header {
    padding: 2.5rem 2rem 1.5rem;
    text-align: center;
    border-bottom: 1px solid #f7fafc;
}

.card-header h3 {
    font-size: 1.8rem;
    color: #1a202c;
    margin-bottom: 1rem;
    font-weight: 600;
}

.price {
    margin-bottom: 1rem;
}

.price-amount {
    font-size: 3rem;
    font-weight: 700;
    color: #48bb78;
}

.price-amount.free {
    color: #2d3748;
}

.price-period {
    font-size: 1rem;
    color: #718096;
    font-weight: 400;
}

.card-description {
    color: #4a5568;
    line-height: 1.6;
    margin-bottom: 0;
}

.card-features {
    padding: 2rem;
}

.card-features ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.card-features li {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f7fafc;
    color: #4a5568;
}

.card-features li:last-child {
    border-bottom: none;
}

.feature-check {
    color: #48bb78;
    font-weight: 600;
    font-size: 1.1rem;
    margin-top: 0.1rem;
}

.card-action {
    padding: 1.5rem 2rem 2.5rem;
    text-align: center;
}

/* FAQ Section */
.pricing-faq {
    padding: 6rem 0;
    background: white;
}

.pricing-faq h2 {
    font-size: 2.5rem;
    color: #1a202c;
    text-align: center;
    margin-bottom: 4rem;
    font-weight: 600;
}

.faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.faq-item {
    background: #f8fafc;
    padding: 2rem;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.faq-item h3 {
    font-size: 1.2rem;
    color: #1a202c;
    margin-bottom: 1rem;
    font-weight: 600;
}

.faq-item p {
    color: #4a5568;
    line-height: 1.6;
    margin: 0;
}

/* CTA Section */
.pricing-cta {
    padding: 6rem 0;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    color: white;
}

.cta-content {
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.cta-content h2 {
    font-size: 2.5rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.cta-content p {
    font-size: 1.3rem;
    margin-bottom: 3rem;
    opacity: 0.9;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 1rem 2rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
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

.btn-secondary {
    background: transparent;
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
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

/* Modal Styles */
.upgrade-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 1000;
    animation: fadeIn 0.3s ease;
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 12px;
    max-width: 500px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
}

.modal-header {
    padding: 2rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.8rem;
    color: #1a202c;
}

.modal-close {
    background: none;
    border: none;
    font-size: 2rem;
    cursor: pointer;
    color: #718096;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-body {
    padding: 2rem;
}

.upgrade-summary h4 {
    color: #1a202c;
    margin-bottom: 1rem;
    font-size: 1.4rem;
}

.upgrade-price {
    font-size: 2.5rem;
    font-weight: 700;
    color: #48bb78;
    margin-bottom: 1.5rem;
}

.upgrade-price span {
    font-size: 1rem;
    color: #718096;
}

.upgrade-features h5 {
    color: #2d3748;
    margin-bottom: 1rem;
    font-size: 1rem;
}

.upgrade-features ul {
    list-style: none;
    padding: 0;
    margin-bottom: 2rem;
}

.upgrade-features li {
    padding: 0.5rem 0;
    color: #4a5568;
}

.upgrade-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-content h1 {
        font-size: 2.5rem;
    }

    .hero-subtitle {
        font-size: 1.2rem;
    }

    .hero-features {
        gap: 1.5rem;
    }

    .pricing-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .pricing-card {
        margin: 0 1rem;
    }

    .card-header {
        padding: 2rem 1.5rem 1rem;
    }

    .card-features {
        padding: 1.5rem;
    }

    .card-action {
        padding: 1rem 1.5rem 2rem;
    }

    .faq-grid {
        grid-template-columns: 1fr;
    }

    .modal-content {
        width: 95%;
        margin: 5vh auto;
    }

    .modal-header {
        padding: 1.5rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }

    .btn {
        width: 100%;
        max-width: 300px;
    }

    .container {
        padding: 0 1rem;
    }

    .pricing-hero,
    .pricing-section,
    .pricing-faq,
    .pricing-cta {
        padding: 4rem 0;
    }
}

@media (max-width: 480px) {
    .hero-content h1 {
        font-size: 2rem;
    }

    .pricing-faq h2,
    .pricing-cta h2 {
        font-size: 2rem;
    }

    .card-header h3 {
        font-size: 1.5rem;
    }

    .price-amount {
        font-size: 2.5rem;
    }

    .modal-header h3 {
        font-size: 1.5rem;
    }

    .upgrade-price {
        font-size: 2rem;
    }
}
</style>

<?php get_footer(); ?>