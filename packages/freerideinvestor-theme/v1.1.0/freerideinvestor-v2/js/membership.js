/**
 * FreeRide Investor Membership JavaScript
 * Handles membership upgrades, paywall interactions, and user account management
 */

(function($) {
    'use strict';

    // Membership functionality
    const MembershipManager = {
        init: function() {
            this.bindEvents();
            this.checkMembershipStatus();
        },

        bindEvents: function() {
            // Upgrade button clicks
            $(document).on('click', '.upgrade-btn, .btn-primary[data-action="upgrade"]', this.handleUpgrade.bind(this));

            // Paywall interactions
            $(document).on('click', '.paywall-upgrade-btn', this.showUpgradeModal.bind(this));

            // Account form submissions
            $(document).on('submit', '#profileForm', this.handleProfileUpdate.bind(this));
            $(document).on('submit', '#passwordForm', this.handlePasswordChange.bind(this));

            // Modal close
            $(document).on('click', '.modal-close, .modal-overlay', this.closeModals.bind(this));

            // Membership content toggles
            $(document).on('click', '.membership-toggle', this.toggleMembershipContent.bind(this));
        },

        checkMembershipStatus: function() {
            if (typeof freerideinvestor_membership !== 'undefined') {
                const userLevel = freerideinvestor_membership.user_membership;
                this.updateUIForMembership(userLevel);
            }
        },

        updateUIForMembership: function(level) {
            // Hide/show content based on membership
            $('.membership-restricted').each(function() {
                const requiredLevel = $(this).data('required-level');
                if (!MembershipManager.canAccessLevel(requiredLevel, level)) {
                    $(this).addClass('hidden');
                } else {
                    $(this).removeClass('hidden');
                }
            });

            // Update membership badges
            $('.membership-badge').each(function() {
                const badgeLevel = $(this).data('level');
                if (badgeLevel === level) {
                    $(this).addClass('current');
                }
            });

            // Update upgrade buttons
            $('.upgrade-btn').each(function() {
                const btnLevel = $(this).data('level');
                if (MembershipManager.getLevelValue(btnLevel) <= MembershipManager.getLevelValue(level)) {
                    $(this).prop('disabled', true).text('Current Plan');
                }
            });
        },

        canAccessLevel: function(requiredLevel, userLevel) {
            const levels = {
                'free': 0,
                'basic': 1,
                'premium': 2,
                'pro': 3
            };

            return levels[userLevel] >= levels[requiredLevel];
        },

        getLevelValue: function(level) {
            const levels = {
                'free': 0,
                'basic': 1,
                'premium': 2,
                'pro': 3
            };
            return levels[level] || 0;
        },

        handleUpgrade: function(e) {
            e.preventDefault();

            const $btn = $(e.target).closest('.upgrade-btn, .btn-primary');
            const level = $btn.data('level') || $btn.data('membership-level');
            const price = $btn.data('price') || freerideinvestor_membership.membership_levels[level].price;

            if (!level) {
                this.showError('Invalid membership level selected');
                return;
            }

            this.showUpgradeConfirmation(level, price);
        },

        showUpgradeConfirmation: function(level, price) {
            const levelData = freerideinvestor_membership.membership_levels[level];
            const features = levelData.features;

            const modalHtml = `
                <div class="modal-overlay" id="upgradeModal">
                    <div class="modal-content upgrade-modal">
                        <div class="modal-header">
                            <h3>Upgrade to ${levelData.name} Membership</h3>
                            <button class="modal-close">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="upgrade-details">
                                <div class="upgrade-price">
                                    <span class="amount">$${price}</span>
                                    <span class="period">/month</span>
                                </div>
                                <div class="upgrade-features">
                                    <h4>You'll get access to:</h4>
                                    <ul>
                                        ${features.map(feature => `<li><span class="check">✓</span>${feature}</li>`).join('')}
                                    </ul>
                                </div>
                                <div class="upgrade-benefits">
                                    <h4>Additional Benefits:</h4>
                                    <ul>
                                        <li>Cancel anytime with 30-day money back guarantee</li>
                                        <li>Immediate access to all features</li>
                                        <li>Priority email support</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="modal-actions">
                                <button class="btn btn-secondary" onclick="MembershipManager.closeModals()">Cancel</button>
                                <button class="btn btn-primary" onclick="MembershipManager.processUpgrade('${level}')">
                                    <span class="btn-text">Upgrade Now</span>
                                    <span class="btn-loading" style="display: none;">Processing...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(modalHtml);
            $('body').addClass('modal-open');
        },

        processUpgrade: function(level) {
            const $btn = $('.upgrade-modal .btn-primary');
            const $btnText = $btn.find('.btn-text');
            const $btnLoading = $btn.find('.btn-loading');

            $btnText.hide();
            $btnLoading.show();
            $btn.prop('disabled', true);

            // Send upgrade request
            $.ajax({
                url: freerideinvestor_membership.ajax_url,
                type: 'POST',
                data: {
                    action: 'freerideinvestor_upgrade_membership',
                    membership_level: level,
                    nonce: freerideinvestor_membership.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.showSuccess('Membership upgraded successfully!');

                        // Update UI
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        this.showError(response.data || 'Upgrade failed. Please try again.');
                    }
                },
                error: (xhr, status, error) => {
                    this.showError('Network error. Please try again.');
                },
                complete: () => {
                    $btnText.show();
                    $btnLoading.hide();
                    $btn.prop('disabled', false);
                }
            });
        },

        handleProfileUpdate: function(e) {
            e.preventDefault();

            const $form = $(e.target);
            const $submitBtn = $form.find('.btn-primary');
            const originalText = $submitBtn.text();

            $submitBtn.text('Updating...').prop('disabled', true);

            const formData = {
                firstName: $('#firstName').val(),
                lastName: $('#lastName').val(),
                displayName: $('#displayName').val()
            };

            // Simulate API call
            setTimeout(() => {
                $submitBtn.text('Profile Updated!').removeClass('btn-primary').addClass('btn-success');

                setTimeout(() => {
                    $submitBtn.text(originalText).removeClass('btn-success').addClass('btn-primary').prop('disabled', false);
                }, 2000);
            }, 1000);
        },

        handlePasswordChange: function(e) {
            e.preventDefault();

            const currentPassword = $('#currentPassword').val();
            const newPassword = $('#newPassword').val();
            const confirmPassword = $('#confirmPassword').val();

            if (newPassword !== confirmPassword) {
                this.showError('New passwords do not match');
                return;
            }

            if (newPassword.length < 8) {
                this.showError('Password must be at least 8 characters long');
                return;
            }

            const $form = $(e.target);
            const $submitBtn = $form.find('.btn-primary');
            const originalText = $submitBtn.text();

            $submitBtn.text('Changing...').prop('disabled', true);

            // Simulate API call
            setTimeout(() => {
                $submitBtn.text('Password Changed!').removeClass('btn-primary').addClass('btn-success');
                $form[0].reset();

                setTimeout(() => {
                    $submitBtn.text(originalText).removeClass('btn-success').addClass('btn-primary').prop('disabled', false);
                }, 2000);
            }, 1000);
        },

        showUpgradeModal: function() {
            // Redirect to pricing page
            window.location.href = '/pricing';
        },

        closeModals: function() {
            $('.modal-overlay').remove();
            $('body').removeClass('modal-open');
        },

        toggleMembershipContent: function(e) {
            e.preventDefault();
            const $toggle = $(e.target);
            const $content = $toggle.closest('.membership-content').find('.membership-details');

            $content.slideToggle();
            $toggle.text($content.is(':visible') ? 'Show Less' : 'Show More');
        },

        showSuccess: function(message) {
            this.showNotification(message, 'success');
        },

        showError: function(message) {
            this.showNotification(message, 'error');
        },

        showNotification: function(message, type) {
            // Remove existing notifications
            $('.notification').remove();

            const notificationClass = type === 'success' ? 'notification-success' : 'notification-error';

            const notification = `
                <div class="notification ${notificationClass}">
                    <span class="notification-message">${message}</span>
                    <button class="notification-close">&times;</button>
                </div>
            `;

            $('body').append(notification);

            // Auto-hide after 5 seconds
            setTimeout(() => {
                $('.notification').fadeOut(() => $(this).remove());
            }, 5000);

            // Close button functionality
            $('.notification-close').on('click', function() {
                $(this).closest('.notification').fadeOut(() => $(this).remove());
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        MembershipManager.init();
    });

    // Export for global access
    window.MembershipManager = MembershipManager;

})(jQuery);