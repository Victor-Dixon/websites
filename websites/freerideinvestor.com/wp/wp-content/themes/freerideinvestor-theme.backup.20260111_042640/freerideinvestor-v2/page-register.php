<?php
/**
 * Template Name: Register
 * Template Post Type: page
 *
 * Registration page template for FreeRide Investor
 *
 * @package FreeRideInvestor_V2
 */

get_header();
?>

<main class="register-page">
    <div class="register-container">
        <div class="register-form-container">
            <div class="register-header">
                <h1>Join FreeRide Investor</h1>
                <p>Start your journey to profitable trading with our free membership</p>
            </div>

            <form class="register-form" id="registerForm" method="post">
                <?php wp_nonce_field('freerideinvestor_register', 'register_nonce'); ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name *</label>
                        <input type="text" id="firstName" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name *</label>
                        <input type="text" id="lastName" name="last_name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" id="username" name="username" required>
                    <small>Choose a unique username for your account</small>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required>
                    <small>We'll send you account confirmation and updates</small>
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <div class="password-input-container">
                        <input type="password" id="password" name="password" required minlength="8">
                        <button type="button" class="password-toggle" id="passwordToggle">
                            <span class="show-icon">👁️</span>
                            <span class="hide-icon" style="display: none;">🙈</span>
                        </button>
                    </div>
                    <div class="password-strength" id="passwordStrength"></div>
                    <small>Minimum 8 characters with letters and numbers</small>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirm Password *</label>
                    <input type="password" id="confirmPassword" name="confirm_password" required>
                </div>

                <div class="form-group">
                    <label for="experience">Trading Experience</label>
                    <select id="experience" name="trading_experience">
                        <option value="">Select your experience level (optional)</option>
                        <option value="beginner">Beginner (0-1 years)</option>
                        <option value="intermediate">Intermediate (1-3 years)</option>
                        <option value="advanced">Advanced (3-5 years)</option>
                        <option value="expert">Expert (5+ years)</option>
                        <option value="professional">Professional Trader</option>
                    </select>
                </div>

                <div class="terms-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="terms" name="accept_terms" required>
                        <span class="checkmark"></span>
                        I agree to the <a href="/terms-of-service" target="_blank">Terms of Service</a> and <a href="/privacy-policy" target="_blank">Privacy Policy</a> *
                    </label>

                    <label class="checkbox-label">
                        <input type="checkbox" name="newsletter" value="1">
                        <span class="checkmark"></span>
                        Subscribe to our newsletter for trading insights and market updates
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    <span class="btn-text">Create Free Account</span>
                    <span class="btn-loading" style="display: none;">Creating account...</span>
                </button>
            </form>

            <div class="register-footer">
                <p>Already have an account? <a href="/login">Sign in here</a></p>
            </div>
        </div>

        <div class="register-sidebar">
            <div class="sidebar-content">
                <h2>Why Choose FreeRide Investor?</h2>
                <div class="benefits-grid">
                    <div class="benefit-item">
                        <div class="benefit-icon">🎓</div>
                        <h3>Free Education</h3>
                        <p>Access comprehensive trading education and market analysis at no cost.</p>
                    </div>

                    <div class="benefit-item">
                        <div class="benefit-icon">📊</div>
                        <h3>Advanced Tools</h3>
                        <p>Use our suite of analysis tools and calculators to make informed decisions.</p>
                    </div>

                    <div class="benefit-item">
                        <h3>Community Access</h3>
                        <div class="benefit-icon">🤝</div>
                        <p>Join a community of traders sharing insights and strategies.</p>
                    </div>

                    <div class="benefit-item">
                        <h3>Proven Strategies</h3>
                        <div class="benefit-icon">🎯</div>
                        <p>Learn from battle-tested trading strategies with real performance data.</p>
                    </div>
                </div>

                <div class="upgrade-teaser">
                    <h3>Ready to Go Pro?</h3>
                    <p>Upgrade anytime to unlock premium features and institutional-grade tools.</p>
                    <a href="/pricing" class="btn btn-secondary">View Premium Plans</a>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const passwordStrength = document.getElementById('passwordStrength');
    const passwordToggle = document.getElementById('passwordToggle');
    const showIcon = passwordToggle.querySelector('.show-icon');
    const hideIcon = passwordToggle.querySelector('.hide-icon');

    // Password toggle functionality
    passwordToggle.addEventListener('click', function() {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        showIcon.style.display = isPassword ? 'none' : 'inline';
        hideIcon.style.display = isPassword ? 'inline' : 'none';
    });

    // Password strength checker
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = checkPasswordStrength(password);
        updatePasswordStrengthIndicator(strength);
    });

    // Password confirmation validation
    confirmPasswordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const confirmPassword = this.value;

        if (confirmPassword && password !== confirmPassword) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });

    // Form submission
    registerForm.addEventListener('submit', function(e) {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const termsAccepted = document.getElementById('terms').checked;

        // Additional validation
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match. Please try again.');
            return;
        }

        if (password.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long.');
            return;
        }

        if (!termsAccepted) {
            e.preventDefault();
            alert('Please accept the Terms of Service and Privacy Policy.');
            return;
        }

        const submitBtn = registerForm.querySelector('.btn-primary');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoading = submitBtn.querySelector('.btn-loading');

        btnText.style.display = 'none';
        btnLoading.style.display = 'inline';
        submitBtn.disabled = true;

        // Let the form submit normally
        // WordPress will handle the registration
    });

    function checkPasswordStrength(password) {
        let strength = 0;

        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        return strength;
    }

    function updatePasswordStrengthIndicator(strength) {
        const messages = [
            '',
            'Very Weak',
            'Weak',
            'Fair',
            'Good',
            'Strong'
        ];

        const colors = [
            '',
            '#e53e3e',
            '#dd6b20',
            '#d69e2e',
            '#38a169',
            '#38a169'
        ];

        passwordStrength.textContent = messages[strength] || '';
        passwordStrength.style.color = colors[strength] || '';
    }
});
</script>

<style>
/* Register Page Styles */
.register-page {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    min-height: 100vh;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.register-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    max-width: 1000px;
    width: 100%;
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    min-height: 700px;
}

.register-form-container {
    padding: 3rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.register-header {
    text-align: center;
    margin-bottom: 2rem;
}

.register-header h1 {
    font-size: 2.5rem;
    color: #1a202c;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.register-header p {
    color: #718096;
    font-size: 1.1rem;
}

.register-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
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

.form-group input,
.form-group select {
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
    background: white;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #00d4ff;
    box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
}

.password-input-container {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: background-color 0.2s ease;
}

.password-toggle:hover {
    background: #f7fafc;
}

.password-strength {
    margin-top: 0.25rem;
    font-size: 0.8rem;
    font-weight: 500;
    min-height: 1rem;
}

.form-group small {
    margin-top: 0.25rem;
    color: #718096;
    font-size: 0.8rem;
}

.terms-group {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin: 1rem 0;
}

.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    font-weight: normal;
    cursor: pointer;
    font-size: 0.9rem;
    line-height: 1.5;
}

.checkbox-label input[type="checkbox"] {
    width: auto;
    margin: 0;
    margin-top: 0.25rem;
    opacity: 0;
    position: absolute;
}

.checkmark {
    width: 18px;
    height: 18px;
    border: 2px solid #e2e8f0;
    border-radius: 4px;
    position: relative;
    transition: all 0.3s ease;
    margin-top: 0.25rem;
    flex-shrink: 0;
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

.checkbox-label a {
    color: #00d4ff;
    text-decoration: none;
}

.checkbox-label a:hover {
    text-decoration: underline;
}

.btn-full {
    width: 100%;
    padding: 1rem;
    font-size: 1.1rem;
}

.register-footer {
    margin-top: 2rem;
    text-align: center;
    padding-top: 2rem;
    border-top: 1px solid #f7fafc;
}

.register-footer p {
    margin-bottom: 0;
    color: #4a5568;
}

.register-footer a {
    color: #00d4ff;
    text-decoration: none;
    font-weight: 500;
}

.register-footer a:hover {
    text-decoration: underline;
}

/* Sidebar */
.register-sidebar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem;
    display: flex;
    align-items: center;
}

.sidebar-content h2 {
    font-size: 2rem;
    margin-bottom: 2rem;
    font-weight: 600;
}

.benefits-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
    margin-bottom: 3rem;
}

.benefit-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.benefit-icon {
    font-size: 2rem;
    flex-shrink: 0;
}

.benefit-item h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.benefit-item p {
    margin: 0;
    opacity: 0.9;
    line-height: 1.5;
}

.upgrade-teaser {
    background: rgba(255, 255, 255, 0.1);
    padding: 2rem;
    border-radius: 8px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.upgrade-teaser h3 {
    margin-bottom: 1rem;
    font-size: 1.4rem;
}

.upgrade-teaser p {
    margin-bottom: 1.5rem;
    opacity: 0.9;
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

.btn-secondary {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
}

/* Responsive Design */
@media (max-width: 768px) {
    .register-container {
        grid-template-columns: 1fr;
        max-width: 500px;
    }

    .register-sidebar {
        display: none;
    }

    .register-form-container {
        padding: 2rem;
    }

    .register-header h1 {
        font-size: 2rem;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .terms-group {
        gap: 0.75rem;
    }
}

@media (max-width: 480px) {
    .register-page {
        padding: 1rem;
    }

    .register-form-container {
        padding: 1.5rem;
    }

    .register-header h1 {
        font-size: 1.8rem;
    }

    .benefits-grid {
        gap: 1.5rem;
    }

    .benefit-item {
        gap: 0.75rem;
    }

    .benefit-icon {
        font-size: 1.5rem;
    }
}
</style>

<?php get_footer(); ?>