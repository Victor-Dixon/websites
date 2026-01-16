<!-- User Testing Panel for A/B Testing -->
<div class="user-testing-panel">
    <div class="testing-header">
        <h3>User Experience Testing</h3>
        <p>You've been assigned to test group: <strong class="test-group"><?php echo esc_html($user_group); ?></strong></p>
    </div>

    <div class="testing-content">
        <!-- Dynamic content based on test group -->
        <?php if ($user_group === 'variant_a'): ?>
            <!-- Variant A: Enhanced Strategy Cards -->
            <div class="testing-variant">
                <h4>🧪 Testing: Enhanced Strategy Cards</h4>
                <p>Thank you for participating in our user testing! You're seeing our enhanced strategy marketplace design.</p>

                <div class="test-feedback">
                    <h5>Your Feedback Matters</h5>
                    <form id="variant-a-feedback" class="feedback-form">
                        <div class="form-group">
                            <label>How easy was it to find strategies you're interested in?</label>
                            <select name="ease_of_navigation" required>
                                <option value="">Select rating...</option>
                                <option value="1">Very Difficult</option>
                                <option value="2">Difficult</option>
                                <option value="3">Neutral</option>
                                <option value="4">Easy</option>
                                <option value="5">Very Easy</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>How clear are the strategy performance metrics?</label>
                            <select name="metric_clarity" required>
                                <option value="">Select rating...</option>
                                <option value="1">Very Unclear</option>
                                <option value="2">Unclear</option>
                                <option value="3">Neutral</option>
                                <option value="4">Clear</option>
                                <option value="5">Very Clear</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="variant-a-comments">Additional comments (optional):</label>
                            <textarea name="comments" id="variant-a-comments" rows="3" placeholder="Share your thoughts on the new design..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit Feedback</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Control Group: Standard Design -->
            <div class="testing-control">
                <h4>📊 Control Group: Standard Design</h4>
                <p>You're seeing our current strategy marketplace design. Your feedback on the standard experience is valuable!</p>

                <div class="test-feedback">
                    <h5>Your Feedback Matters</h5>
                    <form id="control-feedback" class="feedback-form">
                        <div class="form-group">
                            <label>How satisfied are you with the current strategy browsing experience?</label>
                            <select name="satisfaction" required>
                                <option value="">Select rating...</option>
                                <option value="1">Very Dissatisfied</option>
                                <option value="2">Dissatisfied</option>
                                <option value="3">Neutral</option>
                                <option value="4">Satisfied</option>
                                <option value="5">Very Satisfied</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>What improvements would you suggest for finding strategies?</label>
                            <select name="suggested_improvements" required>
                                <option value="">Select suggestion...</option>
                                <option value="filters">Better filters</option>
                                <option value="search">Improved search</option>
                                <option value="sorting">Better sorting options</option>
                                <option value="layout">Different layout</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="control-comments">Additional comments (optional):</label>
                            <textarea name="comments" id="control-comments" rows="3" placeholder="Share your thoughts on the current design..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit Feedback</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Testing Progress -->
    <div class="testing-progress">
        <h5>Testing Progress</h5>
        <div class="progress-bar">
            <div class="progress-fill" style="width: 75%"></div>
        </div>
        <p class="progress-text">75% of beta testers have completed their feedback</p>
    </div>
</div>

<style>
.user-testing-panel {
    background: linear-gradient(135deg, #f8f9ff 0%, #e8f2ff 100%);
    border: 2px solid #e3f2fd;
    border-radius: 12px;
    padding: 2rem;
    margin: 2rem 0;
    position: relative;
}

.user-testing-panel::before {
    content: '🧪';
    position: absolute;
    top: -15px;
    left: 20px;
    background: white;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    border: 2px solid #e3f2fd;
}

.testing-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e3f2fd;
}

.testing-header h3 {
    margin: 0 0 0.5rem 0;
    color: #1976d2;
    font-size: 1.5rem;
}

.test-group {
    background: #1976d2;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.testing-content {
    margin-bottom: 2rem;
}

.testing-variant,
.testing-control {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e3f2fd;
}

.testing-variant h4,
.testing-control h4 {
    margin: 0 0 1rem 0;
    color: #1976d2;
    font-size: 1.2rem;
}

.test-feedback h5 {
    margin: 1.5rem 0 1rem 0;
    color: #333;
    font-size: 1.1rem;
}

.feedback-form {
    max-width: 600px;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #333;
}

.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e3f2fd;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #1976d2;
    box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-block;
}

.btn-primary {
    background: #1976d2;
    color: white;
}

.btn-primary:hover {
    background: #1565c0;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
}

.btn:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Testing Progress */
.testing-progress {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e3f2fd;
}

.testing-progress h5 {
    margin: 0 0 1rem 0;
    color: #333;
}

.progress-bar {
    background: #e3f2fd;
    border-radius: 10px;
    height: 8px;
    margin-bottom: 0.5rem;
    overflow: hidden;
}

.progress-fill {
    background: linear-gradient(90deg, #1976d2, #42a5f5);
    height: 100%;
    border-radius: 10px;
    transition: width 0.3s ease;
}

.progress-text {
    margin: 0;
    font-size: 0.9rem;
    color: #666;
    text-align: center;
}

/* Success Message */
.feedback-success {
    background: #e8f5e8;
    border: 1px solid #4caf50;
    color: #2e7d32;
    padding: 1rem;
    border-radius: 6px;
    margin-top: 1rem;
    text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
    .user-testing-panel {
        padding: 1.5rem;
        margin: 1rem 0;
    }

    .testing-header h3 {
        font-size: 1.3rem;
    }

    .testing-variant,
    .testing-control {
        padding: 1rem;
    }

    .feedback-form {
        max-width: 100%;
    }
}
</style>

<script>
(function($) {
    'use strict';

    $(document).ready(function() {
        setupFeedbackForms();
        trackTestingEngagement();
    });

    function setupFeedbackForms() {
        $('.feedback-form').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('.btn-primary');
            const originalText = submitBtn.text();

            // Disable button and show loading
            submitBtn.prop('disabled', true).text('Submitting...');

            // Collect form data
            const formData = new FormData(this);
            const feedbackData = {};
            for (let [key, value] of formData.entries()) {
                feedbackData[key] = value;
            }

            // Track the feedback submission
            trackUserAction('feedback_submission', {
                form_type: form.attr('id'),
                feedback_data: feedbackData
            });

            // Show success message
            form.hide();
            form.after('<div class="feedback-success">✅ Thank you for your feedback! Your input helps us improve the platform.</div>');

            // Re-enable button (in case user wants to submit again)
            setTimeout(function() {
                submitBtn.prop('disabled', false).text(originalText);
            }, 2000);
        });
    }

    function trackTestingEngagement() {
        // Track that user viewed the testing panel
        trackUserAction('testing_panel_view', {
            test_group: $('.test-group').text().trim(),
            timestamp: new Date().toISOString()
        });

        // Track time spent on testing panel
        let startTime = Date.now();
        $(window).on('beforeunload', function() {
            const timeSpent = Math.round((Date.now() - startTime) / 1000);
            trackUserAction('testing_panel_engagement', {
                time_spent_seconds: timeSpent,
                test_group: $('.test-group').text().trim()
            });
        });
    }

    function trackUserAction(actionType, actionData) {
        $.ajax({
            url: performanceValidationAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'track_user_action',
                nonce: performanceValidationAjax.nonce,
                action_type: actionType,
                action_data: JSON.stringify(actionData),
                page_url: window.location.href,
                session_id: performanceValidationAjax.session_id,
                user_id: performanceValidationAjax.user_id
            },
            success: function(response) {
                console.log('User action tracked:', actionType);
            },
            error: function() {
                console.error('Failed to track user action:', actionType);
            }
        });
    }

})(jQuery);
</script>