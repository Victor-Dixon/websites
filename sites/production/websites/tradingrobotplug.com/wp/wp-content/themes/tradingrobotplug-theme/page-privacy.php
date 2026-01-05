<?php
/**
 * Privacy Policy Page Template
 * P0 Compliance - Required legal page
 * 
 * @package TradingRobotPlug
 * @version 1.0.0
 * @since 2025-12-28
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<section class="legal-page">
    <div class="container">
        <h1>Privacy Policy</h1>
        <p class="last-updated">Last Updated: <?php echo date('F j, Y'); ?></p>
        
        <div class="legal-content">
            <section>
                <h2>1. Introduction</h2>
                <p>TradingRobotPlug ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website tradingrobotplug.com and use our services.</p>
                <p>Please read this privacy policy carefully. If you do not agree with the terms of this privacy policy, please do not access the site.</p>
            </section>
            
            <section>
                <h2>2. Information We Collect</h2>
                <h3>Personal Data</h3>
                <p>We may collect personally identifiable information, such as:</p>
                <ul>
                    <li>Name and email address (when you sign up for our waitlist or newsletter)</li>
                    <li>Contact information you provide when reaching out to us</li>
                    <li>Account information if you create an account with us</li>
                </ul>
                
                <h3>Automatically Collected Data</h3>
                <p>When you visit our website, we may automatically collect:</p>
                <ul>
                    <li>IP address and browser type</li>
                    <li>Pages visited and time spent on pages</li>
                    <li>Referring website addresses</li>
                    <li>Device information</li>
                </ul>
            </section>
            
            <section>
                <h2>3. How We Use Your Information</h2>
                <p>We may use the information we collect for purposes including:</p>
                <ul>
                    <li>Providing and maintaining our services</li>
                    <li>Notifying you about changes to our services</li>
                    <li>Sending you updates about our trading robots and platform development</li>
                    <li>Responding to your inquiries and support requests</li>
                    <li>Improving our website and services</li>
                    <li>Analyzing usage patterns to enhance user experience</li>
                </ul>
            </section>
            
            <section>
                <h2>4. Cookies and Tracking Technologies</h2>
                <p>We use cookies and similar tracking technologies to track activity on our website and hold certain information. You can instruct your browser to refuse all cookies or indicate when a cookie is being sent.</p>
                <p>We use Google Analytics to analyze website traffic and user behavior. This data helps us understand how visitors interact with our site.</p>
            </section>
            
            <section>
                <h2>5. Third-Party Services</h2>
                <p>We may employ third-party companies and individuals to facilitate our services, provide services on our behalf, or assist us in analyzing how our services are used. These third parties have access to your personal data only to perform these tasks on our behalf and are obligated not to disclose or use it for any other purpose.</p>
            </section>
            
            <section>
                <h2>6. Data Security</h2>
                <p>We implement appropriate technical and organizational measures to protect your personal information. However, no method of transmission over the Internet or electronic storage is 100% secure, and we cannot guarantee absolute security.</p>
            </section>
            
            <section>
                <h2>7. Your Rights</h2>
                <p>Depending on your location, you may have rights regarding your personal information, including:</p>
                <ul>
                    <li>The right to access your personal data</li>
                    <li>The right to request correction of inaccurate data</li>
                    <li>The right to request deletion of your data</li>
                    <li>The right to opt-out of marketing communications</li>
                </ul>
                <p>To exercise these rights, please contact us at the information provided below.</p>
            </section>
            
            <section>
                <h2>8. Children's Privacy</h2>
                <p>Our services are not intended for individuals under the age of 18. We do not knowingly collect personal information from children under 18. If we become aware that we have collected personal data from a child under 18, we will take steps to delete that information.</p>
            </section>
            
            <section>
                <h2>9. Changes to This Privacy Policy</h2>
                <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date.</p>
            </section>
            
            <section>
                <h2>10. Contact Us</h2>
                <p>If you have any questions about this Privacy Policy, please contact us:</p>
                <ul>
                    <li>By visiting our <a href="<?php echo esc_url(home_url('/contact')); ?>">Contact Page</a></li>
                </ul>
            </section>
        </div>
        
        <div class="legal-nav">
            <a href="<?php echo esc_url(home_url('/terms-of-service')); ?>" class="legal-link">Terms of Service</a>
            <a href="<?php echo esc_url(home_url('/product-terms')); ?>" class="legal-link">Product Terms & Risk Disclosure</a>
        </div>
    </div>
</section>

<style>
.legal-page {
    padding: 80px 0;
    background: #f9f9f9;
    min-height: 100vh;
}

.legal-page h1 {
    color: #333;
    margin-bottom: 10px;
}

.legal-page .last-updated {
    color: #666;
    font-size: 14px;
    margin-bottom: 40px;
}

.legal-content {
    background: #fff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.legal-content section {
    margin-bottom: 32px;
}

.legal-content h2 {
    color: #667eea;
    font-size: 1.5rem;
    margin-bottom: 16px;
    border-bottom: 2px solid #eee;
    padding-bottom: 8px;
}

.legal-content h3 {
    color: #333;
    font-size: 1.1rem;
    margin: 16px 0 8px;
}

.legal-content p {
    color: #555;
    line-height: 1.8;
    margin-bottom: 12px;
}

.legal-content ul {
    margin: 12px 0 12px 24px;
    color: #555;
}

.legal-content li {
    margin-bottom: 8px;
    line-height: 1.6;
}

.legal-nav {
    margin-top: 40px;
    text-align: center;
}

.legal-link {
    display: inline-block;
    margin: 0 16px;
    color: #667eea;
    text-decoration: none;
}

.legal-link:hover {
    text-decoration: underline;
}
</style>

<?php get_footer(); ?>


