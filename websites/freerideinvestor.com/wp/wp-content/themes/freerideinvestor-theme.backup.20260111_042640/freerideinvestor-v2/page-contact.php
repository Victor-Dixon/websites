<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 *
 * Modern contact page template for FreeRide Investor
 *
 * @package FreeRideInvestor_V2
 */

get_header();
?>

<main class="contact-page">
    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Get in Touch</h1>
                <p class="hero-subtitle">Have questions about our strategies or need personalized guidance? We're here to help.</p>
                <div class="hero-features">
                    <div class="feature-item">
                        <span class="feature-icon">⚡</span>
                        <span>Quick Response</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">🔒</span>
                        <span>Secure & Private</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">🎯</span>
                        <span>Expert Guidance</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Form -->
                <div class="contact-form-container">
                    <h2>Send us a Message</h2>
                    <form class="contact-form" id="contactForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstName">First Name *</label>
                                <input type="text" id="firstName" name="firstName" required>
                            </div>
                            <div class="form-group">
                                <label for="lastName">Last Name *</label>
                                <input type="text" id="lastName" name="lastName" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject *</label>
                            <select id="subject" name="subject" required>
                                <option value="">Select a topic</option>
                                <option value="strategy">Trading Strategies</option>
                                <option value="support">Technical Support</option>
                                <option value="partnership">Partnership Inquiry</option>
                                <option value="media">Media Inquiry</option>
                                <option value="general">General Question</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="experience">Trading Experience</label>
                            <select id="experience" name="experience">
                                <option value="">Select your experience level</option>
                                <option value="beginner">Beginner (0-1 years)</option>
                                <option value="intermediate">Intermediate (1-3 years)</option>
                                <option value="advanced">Advanced (3-5 years)</option>
                                <option value="expert">Expert (5+ years)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" rows="6" placeholder="Tell us how we can help you..." required></textarea>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="newsletter" value="1">
                                <span class="checkmark"></span>
                                Subscribe to our newsletter for trading insights and market updates
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <span class="btn-text">Send Message</span>
                            <span class="btn-loading" style="display: none;">Sending...</span>
                        </button>
                    </form>
                </div>

                <!-- Contact Info -->
                <div class="contact-info">
                    <h2>Contact Information</h2>

                    <div class="info-section">
                        <h3>General Inquiries</h3>
                        <div class="contact-item">
                            <span class="contact-icon">📧</span>
                            <div>
                                <strong>Email:</strong>
                                <a href="mailto:info@freerideinvestor.com">info@freerideinvestor.com</a>
                            </div>
                        </div>
                        <div class="contact-item">
                            <span class="contact-icon">📞</span>
                            <div>
                                <strong>Phone:</strong>
                                <a href="tel:+1-555-INVEST">+1 (555) INVEST</a>
                            </div>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3>Business Hours</h3>
                        <div class="hours-grid">
                            <div class="day-hours">
                                <span class="day">Monday - Friday</span>
                                <span class="hours">9:00 AM - 6:00 PM EST</span>
                            </div>
                            <div class="day-hours">
                                <span class="day">Saturday</span>
                                <span class="hours">10:00 AM - 4:00 PM EST</span>
                            </div>
                            <div class="day-hours">
                                <span class="day">Sunday</span>
                                <span class="hours">Closed</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3>Follow Us</h3>
                        <div class="social-links">
                            <a href="#" class="social-link" title="Twitter">
                                <span class="social-icon">🐦</span>
                                <span>Twitter</span>
                            </a>
                            <a href="#" class="social-link" title="LinkedIn">
                                <span class="social-icon">💼</span>
                                <span>LinkedIn</span>
                            </a>
                            <a href="#" class="social-link" title="YouTube">
                                <span class="social-icon">📺</span>
                                <span>YouTube</span>
                            </a>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3>Office Location</h3>
                        <div class="address">
                            <p>FreeRide Investor<br>
                            123 Trading Street<br>
                            Financial District<br>
                            New York, NY 10004<br>
                            United States</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <h3>How do I get started with your strategies?</h3>
                    <p>We offer a comprehensive onboarding process that includes strategy selection, risk assessment, and personalized setup guidance.</p>
                </div>

                <div class="faq-item">
                    <h3>What is your minimum investment requirement?</h3>
                    <p>Our strategies are designed to work with various account sizes. We recommend starting with at least $1,000 for optimal risk management.</p>
                </div>

                <div class="faq-item">
                    <h3>How do you handle risk management?</h3>
                    <p>Risk management is our highest priority. All strategies include built-in stop-losses, position sizing rules, and portfolio diversification guidelines.</p>
                </div>

                <div class="faq-item">
                    <h3>Do you provide ongoing support?</h3>
                    <p>Yes, we provide comprehensive support including strategy updates, market analysis, and technical assistance through our client portal.</p>
                </div>

                <div class="faq-item">
                    <h3>What markets do you trade?</h3>
                    <p>We focus on liquid markets including stocks, ETFs, futures, and forex. Our strategies are adaptable to various market conditions.</p>
                </div>

                <div class="faq-item">
                    <h3>Is my data secure?</h3>
                    <p>Absolutely. We use bank-level encryption, secure servers, and follow strict data privacy regulations to protect your information.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = contactForm.querySelector('.btn-primary');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoading = submitBtn.querySelector('.btn-loading');

        // Show loading state
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline';

        // Simulate form submission (replace with actual submission logic)
        setTimeout(() => {
            // Hide loading state
            btnText.style.display = 'inline';
            btnLoading.style.display = 'none';

            // Show success message
            alert('Thank you for your message! We\'ll get back to you within 24 hours.');
            contactForm.reset();
        }, 2000);
    });
});
</script>

<style>
/* Contact Page Styles */
.contact-page {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Hero Section */
.contact-hero {
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

/* Contact Section */
.contact-section {
    padding: 6rem 0;
    background: #f8fafc;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: start;
}

/* Contact Form */
.contact-form-container h2 {
    font-size: 2.5rem;
    color: #1a202c;
    margin-bottom: 2rem;
    font-weight: 600;
}

.contact-form {
    background: white;
    padding: 3rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e2e8f0;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 500;
    color: #2d3748;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
    background: white;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #00d4ff;
    box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.checkbox-group {
    margin-top: 2rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: normal;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: auto;
    margin: 0;
    opacity: 0;
    position: absolute;
}

.checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid #e2e8f0;
    border-radius: 4px;
    position: relative;
    transition: all 0.3s ease;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark {
    background: #00d4ff;
    border-color: #00d4ff;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

/* Contact Info */
.contact-info h2 {
    font-size: 2.5rem;
    color: #1a202c;
    margin-bottom: 2rem;
    font-weight: 600;
}

.info-section {
    margin-bottom: 3rem;
}

.info-section h3 {
    font-size: 1.3rem;
    color: #2d3748;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.contact-icon {
    font-size: 1.5rem;
    margin-top: 0.25rem;
}

.contact-item a {
    color: #00d4ff;
    text-decoration: none;
    font-weight: 500;
}

.contact-item a:hover {
    text-decoration: underline;
}

.hours-grid {
    display: grid;
    gap: 1rem;
}

.day-hours {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.day-hours:last-child {
    border-bottom: none;
}

.day {
    font-weight: 500;
    color: #2d3748;
}

.hours {
    color: #4a5568;
}

.social-links {
    display: flex;
    gap: 1.5rem;
}

.social-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #4a5568;
    text-decoration: none;
    padding: 0.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.social-link:hover {
    background: #f7fafc;
    color: #00d4ff;
}

.social-icon {
    font-size: 1.2rem;
}

.address {
    color: #4a5568;
    line-height: 1.6;
}

/* FAQ Section */
.faq-section {
    padding: 6rem 0;
    background: white;
}

.faq-section h2 {
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

/* Button Styles */
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

    .contact-grid {
        grid-template-columns: 1fr;
        gap: 3rem;
    }

    .contact-form {
        padding: 2rem;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .faq-grid {
        grid-template-columns: 1fr;
    }

    .hours-grid {
        gap: 0.75rem;
    }

    .day-hours {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }

    .social-links {
        flex-direction: column;
        gap: 1rem;
    }
}

@media (max-width: 480px) {
    .hero-content h1 {
        font-size: 2rem;
    }

    .contact-form-container h2,
    .contact-info h2,
    .faq-section h2 {
        font-size: 2rem;
    }

    .contact-form {
        padding: 1.5rem;
    }

    .contact-item {
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-start;
    }

    .feature-item {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
}
</style>

<?php get_footer(); ?>