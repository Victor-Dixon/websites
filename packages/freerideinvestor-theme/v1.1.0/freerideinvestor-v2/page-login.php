<?php
/**
 * Template Name: Login
 * Template Post Type: page
 *
 * Login page template for FreeRide Investor
 *
 * @package FreeRideInvestor_V2
 */

get_header();
?>

<main class="login-page">
    <div class="login-container">
        <div class="login-form-container">
            <div class="login-header">
                <h1>Welcome Back</h1>
                <p>Sign in to access your trading dashboard</p>
            </div>

            <form class="login-form" id="loginForm" method="post">
                <?php wp_nonce_field('freerideinvestor_login', 'login_nonce'); ?>

                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="log" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input-container">
                        <input type="password" id="password" name="pwd" required>
                        <button type="button" class="password-toggle" id="passwordToggle">
                            <span class="show-icon">👁️</span>
                            <span class="hide-icon" style="display: none;">🙈</span>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="rememberme" value="forever">
                        <span class="checkmark"></span>
                        Remember me
                    </label>
                    <a href="<?php echo wp_lostpassword_url(); ?>" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    <span class="btn-text">Sign In</span>
                    <span class="btn-loading" style="display: none;">Signing in...</span>
                </button>

                <?php if (isset($_GET['login']) && $_GET['login'] === 'failed'): ?>
                <div class="login-error">
                    <p>Invalid username or password. Please try again.</p>
                </div>
                <?php endif; ?>
            </form>

            <div class="login-footer">
                <p>Don't have an account? <a href="/register">Sign up for free</a></p>
                <div class="social-login">
                    <p>Or continue with:</p>
                    <div class="social-buttons">
                        <button class="social-btn google-btn" onclick="loginWithGoogle()">
                            <span class="social-icon">🌐</span>
                            Google
                        </button>
                        <button class="social-btn github-btn" onclick="loginWithGithub()">
                            <span class="social-icon">💻</span>
                            GitHub
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="login-sidebar">
            <div class="sidebar-content">
                <h2>Start Your Trading Journey</h2>
                <ul class="feature-list">
                    <li>
                        <span class="feature-icon">📊</span>
                        <div>
                            <strong>Advanced Analytics</strong>
                            <p>Track your performance with detailed metrics and insights</p>
                        </div>
                    </li>
                    <li>
                        <span class="feature-icon">🎯</span>
                        <div>
                            <strong>Proven Strategies</strong>
                            <p>Access institutional-grade trading strategies</p>
                        </div>
                    </li>
                    <li>
                        <span class="feature-icon">🤝</span>
                        <div>
                            <strong>Community Support</strong>
                            <p>Connect with fellow traders and share insights</p>
                        </div>
                    </li>
                    <li>
                        <span class="feature-icon">📚</span>
                        <div>
                            <strong>Education Resources</strong>
                            <p>Learn from comprehensive trading education materials</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const passwordToggle = document.getElementById('passwordToggle');
    const passwordInput = document.getElementById('password');
    const showIcon = passwordToggle.querySelector('.show-icon');
    const hideIcon = passwordToggle.querySelector('.hide-icon');

    // Password toggle functionality
    passwordToggle.addEventListener('click', function() {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        showIcon.style.display = isPassword ? 'none' : 'inline';
        hideIcon.style.display = isPassword ? 'inline' : 'none';
    });

    // Form submission
    loginForm.addEventListener('submit', function(e) {
        const submitBtn = loginForm.querySelector('.btn-primary');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoading = submitBtn.querySelector('.btn-loading');

        btnText.style.display = 'none';
        btnLoading.style.display = 'inline';
        submitBtn.disabled = true;

        // Let the form submit normally, but show loading state
        // The actual login will be handled by WordPress
    });
});

function loginWithGoogle() {
    alert('Google login integration coming soon!');
    // Implement Google OAuth login
}

function loginWithGithub() {
    alert('GitHub login integration coming soon!');
    // Implement GitHub OAuth login
}
</script>

<style>
/* Login Page Styles */
.login-page {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    min-height: 100vh;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.login-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    max-width: 1000px;
    width: 100%;
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    min-height: 600px;
}

.login-form-container {
    padding: 3rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.login-header {
    text-align: center;
    margin-bottom: 2rem;
}

.login-header h1 {
    font-size: 2.5rem;
    color: #1a202c;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.login-header p {
    color: #718096;
    font-size: 1.1rem;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
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
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
    background: white;
}

.form-group input:focus {
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

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 1rem 0;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: normal;
    cursor: pointer;
    font-size: 0.9rem;
    color: #4a5568;
}

.checkbox-label input[type="checkbox"] {
    width: auto;
    margin: 0;
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

.forgot-password {
    color: #00d4ff;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
}

.forgot-password:hover {
    text-decoration: underline;
}

.btn-full {
    width: 100%;
    padding: 1rem;
    font-size: 1.1rem;
}

.login-error {
    background: #fed7d7;
    color: #c53030;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #feb2b2;
    margin-top: 1rem;
}

.login-error p {
    margin: 0;
    font-size: 0.9rem;
}

.login-footer {
    margin-top: 2rem;
    text-align: center;
    padding-top: 2rem;
    border-top: 1px solid #f7fafc;
}

.login-footer p {
    margin-bottom: 1.5rem;
    color: #4a5568;
}

.login-footer a {
    color: #00d4ff;
    text-decoration: none;
    font-weight: 500;
}

.login-footer a:hover {
    text-decoration: underline;
}

.social-login {
    margin-top: 1.5rem;
}

.social-login p {
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: #718096;
}

.social-buttons {
    display: flex;
    gap: 1rem;
}

.social-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    background: white;
    color: #4a5568;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    cursor: pointer;
}

.social-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.google-btn:hover {
    border-color: #4285f4;
    color: #4285f4;
}

.github-btn:hover {
    border-color: #24292e;
    color: #24292e;
}

.social-icon {
    font-size: 1.2rem;
}

/* Sidebar */
.login-sidebar {
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

.feature-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.feature-list li {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
}

.feature-icon {
    font-size: 2rem;
    margin-top: 0.25rem;
    flex-shrink: 0;
}

.feature-list li div {
    flex: 1;
}

.feature-list strong {
    display: block;
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
    color: white;
}

.feature-list p {
    margin: 0;
    opacity: 0.9;
    line-height: 1.5;
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
    .login-container {
        grid-template-columns: 1fr;
        max-width: 500px;
    }

    .login-sidebar {
        display: none;
    }

    .login-form-container {
        padding: 2rem;
    }

    .login-header h1 {
        font-size: 2rem;
    }

    .social-buttons {
        flex-direction: column;
    }

    .form-options {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
}

@media (max-width: 480px) {
    .login-page {
        padding: 1rem;
    }

    .login-form-container {
        padding: 1.5rem;
    }

    .login-header h1 {
        font-size: 1.8rem;
    }

    .feature-list li {
        margin-bottom: 1.5rem;
    }
}
</style>

<?php get_footer(); ?>