/**
 * FreeRide Automated Trading Plan JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Auto-refresh strategy status every 5 minutes
        if ($('.fratp-strategy-status').length) {
            setInterval(function() {
                // Could implement auto-refresh here if needed
            }, 300000); // 5 minutes
        }
        
        // Handle plan generation forms
        $('.fratp-generate-form').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $result = $form.siblings('.fratp-result');
            
            if ($result.length === 0) {
                $result = $('<div class="fratp-result"></div>');
                $form.after($result);
            }
            
            $result.html('<p>Generating plan...</p>');
            
            $.ajax({
                url: fratp_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'fratp_generate_plan',
                    nonce: fratp_ajax.nonce,
                    symbol: $form.find('[name="symbol"]').val()
                },
                success: function(response) {
                    if (response.success) {
                        $result.html('<div class="notice notice-success"><p>Plan generated successfully!</p></div>');
                        // Optionally reload page or update content
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        $result.html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                    }
                },
                error: function() {
                    $result.html('<div class="notice notice-error"><p>Error generating plan. Please try again.</p></div>');
                }
            });
        });
    });
    
})(jQuery);

