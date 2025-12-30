<?php
/**
 * Analytics Module
 * GA4 and Facebook Pixel tracking integration
 * 
 * @package TradingRobotPlug
 * @since 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add analytics tracking code to head
 */
function trp_add_analytics_tracking()
{
    $ga4_id = defined('GA4_MEASUREMENT_ID') ? GA4_MEASUREMENT_ID : '';
    $pixel_id = defined('FACEBOOK_PIXEL_ID') ? FACEBOOK_PIXEL_ID : '';
    
    // GA4 Tracking
    if (!empty($ga4_id)) {
        ?>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga4_id); ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo esc_js($ga4_id); ?>');
        </script>
        <?php
    }
    
    // Facebook Pixel
    if (!empty($pixel_id)) {
        ?>
        <!-- Facebook Pixel Code -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '<?php echo esc_js($pixel_id); ?>');
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=<?php echo esc_attr($pixel_id); ?>&ev=PageView&noscript=1"/>
        </noscript>
        <?php
    }
    
    // Combined event tracking
    if (!empty($ga4_id) || !empty($pixel_id)) {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Track CTA clicks
                document.querySelectorAll('a[href*="#cta"], .cta-button, .btn-primary, [data-cta]').forEach(function(button) {
                    button.addEventListener('click', function() {
                        var label = this.textContent.trim() || this.href || this.getAttribute('data-cta');
                        <?php if (!empty($ga4_id)): ?>
                        gtag('event', 'cta_click', {
                            'event_category': 'engagement',
                            'event_label': label
                        });
                        <?php endif; ?>
                        <?php if (!empty($pixel_id)): ?>
                        fbq('track', 'Lead', { content_name: label });
                        <?php endif; ?>
                    });
                });
                
                // Track form submissions
                document.querySelectorAll('form').forEach(function(form) {
                    form.addEventListener('submit', function() {
                        var label = form.id || form.className || 'form_submission';
                        <?php if (!empty($ga4_id)): ?>
                        gtag('event', 'form_submit', {
                            'event_category': 'engagement',
                            'event_label': label
                        });
                        <?php endif; ?>
                        <?php if (!empty($pixel_id)): ?>
                        fbq('track', 'Lead', { content_name: label });
                        <?php endif; ?>
                    });
                });
            });
        </script>
        <?php
    }
}

add_action('wp_head', 'trp_add_analytics_tracking', 10);

