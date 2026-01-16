<!-- Lead Capture Form -->
<div class="lead-capture-form" id="lead-capture-form">
    <div class="form-header">
        <h3>Join Our Beta Program</h3>
        <p>Get early access to AI-powered Tesla trading intelligence before anyone else.</p>
    </div>

    <form id="beta-lead-form" class="beta-lead-form">
        <input type="hidden" name="source" value="<?php echo esc_attr($atts['source']); ?>">

        <div class="form-row">
            <div class="form-group">
                <label for="lead-first-name">First Name *</label>
                <input type="text" id="lead-first-name" name="first_name" required>
            </div>

            <div class="form-group">
                <label for="lead-last-name">Last Name *</label>
                <input type="text" id="lead-last-name" name="last_name" required>
            </div>
        </div>

        <div class="form-group">
            <label for="lead-email">Email Address *</label>
            <input type="email" id="lead-email" name="email" required>
            <small class="form-hint">We'll never share your email with anyone else.</small>
        </div>

        <div class="form-group">
            <label for="lead-experience">Trading Experience Level</label>
            <select id="lead-experience" name="experience_level">
                <option value="beginner">Beginner - New to trading</option>
                <option value="intermediate">Intermediate - Some experience</option>
                <option value="advanced">Advanced - Regular trader</option>
                <option value="expert">Expert - Professional trader</option>
            </select>
        </div>

        <div class="form-group">
            <label for="lead-interests">What interests you most? (Optional)</label>
            <textarea id="lead-interests" name="interests" rows="3" placeholder="e.g., Tesla trading, AI algorithms, automated strategies..."></textarea>
        </div>

        <div class="form-group checkbox-group">
            <label class="checkbox-label">
                <input type="checkbox" name="newsletter" checked>
                <span class="checkmark"></span>
                Send me updates about new features and trading insights
            </label>
        </div>

        <div class="form-group checkbox-group">
            <label class="checkbox-label">
                <input type="checkbox" name="beta_testing" checked>
                <span class="checkmark"></span>
                I'd like to participate in beta testing new trading algorithms
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-large" id="submit-lead">
            <span class="btn-text">Get Beta Access Now</span>
            <span class="btn-loading" style="display: none;">Registering...</span>
        </button>

        <div class="form-footer">
            <p class="form-disclaimer">
                By joining, you agree to receive occasional updates about our AI trading platform.
                You can unsubscribe at any time.
            </p>
        </div>
    </form>

    <!-- Success Message -->
    <div class="form-success" id="form-success" style="display: none;">
        <div class="success-icon">✅</div>
        <h3>Welcome to the Beta!</h3>
        <p>Thank you for joining our exclusive beta program. You'll receive an email confirmation shortly with your access details.</p>
        <div class="next-steps">
            <h4>What happens next?</h4>
            <ul>
                <li>📧 Email confirmation with beta access instructions</li>
                <li>🚀 Early access to live Tesla trading algorithms</li>
                <li>📊 Real-time performance tracking dashboard</li>
                <li>💬 Direct communication with our development team</li>
            </ul>
        </div>
    </div>

    <!-- Error Message -->
    <div class="form-error" id="form-error" style="display: none;">
        <div class="error-icon">❌</div>
        <h3>Registration Failed</h3>
        <p id="error-message">Please check your information and try again.</p>
        <button class="btn btn-secondary" onclick="hideError()">Try Again</button>
    </div>
</div>

<style>
.lead-capture-form {
    max-width: 600px;
    margin: 2rem auto;
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    overflow: hidden;
}

.form-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    text-align: center;
}

.form-header h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.8rem;
    font-weight: 700;
}

.form-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 1.1rem;
}

.beta-lead-form {
    padding: 2rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #333;
    font-size: 0.95rem;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    background: white;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.form-hint {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.85rem;
    color: #6c757d;
}

.checkbox-group {
    margin-bottom: 1rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-weight: normal;
    margin-bottom: 0.5rem;
}

.checkbox-label input[type="checkbox"] {
    margin-right: 0.75rem;
    width: auto;
}

.btn {
    padding: 0.875rem 2rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-block;
    text-align: center;
    min-width: 200px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}

.btn:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.btn-large {
    padding: 1rem 2.5rem;
    font-size: 1.1rem;
    min-width: 250px;
}

.form-footer {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

.form-disclaimer {
    margin: 0;
    font-size: 0.85rem;
    color: #6c757d;
    line-height: 1.5;
}

/* Success/Error States */
.form-success,
.form-error {
    text-align: center;
    padding: 3rem 2rem;
}

.success-icon,
.error-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.form-success h3,
.form-error h3 {
    color: #333;
    margin-bottom: 1rem;
}

.form-success p,
.form-error p {
    color: #6c757d;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.next-steps {
    text-align: left;
    max-width: 400px;
    margin: 0 auto;
}

.next-steps h4 {
    color: #333;
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.next-steps ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.next-steps li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
    color: #555;
}

.next-steps li:last-child {
    border-bottom: none;
}

/* Loading state */
.btn-loading {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-loading::after {
    content: '';
    width: 16px;
    height: 16px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .lead-capture-form {
        margin: 1rem;
        border-radius: 12px;
    }

    .form-header,
    .beta-lead-form {
        padding: 1.5rem;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .btn-large {
        min-width: 100%;
    }

    .next-steps {
        text-align: left;
    }
}
</style>

<script>
(function($) {
    'use strict';

    $(document).ready(function() {
        $('#beta-lead-form').on('submit', function(e) {
            e.preventDefault();
            submitLeadForm($(this));
        });
    });

    function submitLeadForm(form) {
        const submitBtn = form.find('#submit-lead');
        const btnText = submitBtn.find('.btn-text');
        const btnLoading = submitBtn.find('.btn-loading');

        // Show loading state
        submitBtn.prop('disabled', true);
        btnText.hide();
        btnLoading.show();

        // Collect form data
        const formData = new FormData(form[0]);

        $.ajax({
            url: leadGenerationAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'submit_lead',
                nonce: leadGenerationAjax.nonce,
                email: formData.get('email'),
                first_name: formData.get('first_name'),
                last_name: formData.get('last_name'),
                experience_level: formData.get('experience_level'),
                interests: formData.get('interests'),
                source: formData.get('source')
            },
            success: function(response) {
                if (response.success) {
                    showSuccess();
                    // Track conversion
                    if (typeof trackConversion === 'function') {
                        trackConversion('beta_signup', null, {
                            source: formData.get('source'),
                            experience_level: formData.get('experience_level')
                        });
                    }
                } else {
                    showError(response.data || 'Registration failed. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                showError('Network error. Please check your connection and try again.');
            },
            complete: function() {
                // Reset button state
                submitBtn.prop('disabled', false);
                btnText.show();
                btnLoading.hide();
            }
        });
    }

    function showSuccess() {
        $('#beta-lead-form').hide();
        $('#form-success').show();
        $('#lead-capture-form').addClass('success-state');
    }

    function showError(message) {
        $('#error-message').text(message);
        $('#form-error').show();
        $('#beta-lead-form').addClass('error-state');
    }

    function hideError() {
        $('#form-error').hide();
        $('#beta-lead-form').removeClass('error-state');
    }

})(jQuery);
</script>