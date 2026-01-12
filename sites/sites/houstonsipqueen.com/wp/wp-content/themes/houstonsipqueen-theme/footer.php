        </div><!-- #content -->

        <footer id="colophon" class="site-footer footer-luxury">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-branding">
                        <h3><?php bloginfo('name'); ?></h3>
                        <p><?php bloginfo('description'); ?></p>
                        <div class="footer-contact">
                            <p><strong>Phone:</strong> <?php echo get_theme_mod('business_phone', '(281) 555-SIPQ'); ?></p>
                            <p><strong>Service Area:</strong> <?php echo get_theme_mod('service_area', 'Houston, TX and surrounding areas'); ?></p>
                        </div>
                    </div>

                    <div class="footer-navigation">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'menu_id'        => 'footer-menu',
                            'menu_class'     => 'footer-menu',
                            'container'      => false,
                            'fallback_cb'    => false,
                        ));
                        ?>
                    </div>

                    <div class="footer-widgets">
                        <?php if (is_active_sidebar('footer-1')) : ?>
                            <?php dynamic_sidebar('footer-1'); ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="footer-bottom">
                    <div class="footer-info">
                        <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
                        <p>Serving Houston with elegance and excellence since 2018.</p>
                        <p>Built with 💫 Southern Glam & Luxury Spirit</p>
                    </div>

                    <div class="footer-links">
                        <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>">Privacy Policy</a>
                        <a href="<?php echo esc_url(home_url('/terms-of-service')); ?>">Terms of Service</a>
                        <a href="<?php echo esc_url(home_url('/contact')); ?>">Contact</a>
                    </div>
                </div>

                <!-- Luxury Footer Animation -->
                <div class="luxury-footer-animation">
                    <div class="champagne-bubbles">
                        <svg viewBox="0 0 1200 100" class="w-full h-16 opacity-20">
                            <defs>
                                <linearGradient id="luxuryGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#C9A26A;stop-opacity:1" />
                                    <stop offset="50%" style="stop-color:#F5E6C8;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#C9A26A;stop-opacity:1" />
                                </linearGradient>
                            </defs>

                            <!-- Champagne bubbles rising -->
                            <g class="bubbles-animation">
                                <circle cx="100" cy="80" r="2" fill="url(#luxuryGradient)" class="bubble" style="animation-delay: 0s;"/>
                                <circle cx="200" cy="60" r="1.5" fill="url(#luxuryGradient)" class="bubble" style="animation-delay: 0.5s;"/>
                                <circle cx="300" cy="85" r="1" fill="url(#luxuryGradient)" class="bubble" style="animation-delay: 1s;"/>
                                <circle cx="400" cy="70" r="2.5" fill="url(#luxuryGradient)" class="bubble" style="animation-delay: 1.5s;"/>
                                <circle cx="500" cy="75" r="1.8" fill="url(#luxuryGradient)" class="bubble" style="animation-delay: 2s;"/>
                                <circle cx="600" cy="90" r="1.2" fill="url(#luxuryGradient)" class="bubble" style="animation-delay: 2.5s;"/>
                                <circle cx="700" cy="65" r="2.2" fill="url(#luxuryGradient)" class="bubble" style="animation-delay: 3s;"/>
                                <circle cx="800" cy="80" r="1.6" fill="url(#luxuryGradient)" class="bubble" style="animation-delay: 3.5s;"/>
                                <circle cx="900" cy="55" r="1.9" fill="url(#luxuryGradient)" class="bubble" style="animation-delay: 4s;"/>
                                <circle cx="1000" cy="85" r="1.4" fill="url(#luxuryGradient)" class="bubble" style="animation-delay: 4.5s;"/>
                                <circle cx="1100" cy="75" r="2.1" fill="url(#luxuryGradient)" class="bubble" style="animation-delay: 5s;"/>
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
        </footer>
    </div><!-- #page -->

    <?php wp_footer(); ?>

    <!-- Footer JavaScript for luxury animations -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add champagne bubble animation
        const bubbles = document.querySelectorAll('.bubble');
        if (bubbles.length) {
            bubbles.forEach((bubble, index) => {
                bubble.style.animation = 'bubbleRise 6s ease-in-out infinite';
                bubble.style.animationDelay = `${index * 0.3}s`;
            });
        }

        // Add sparkle effects to footer
        const footer = document.querySelector('.footer-luxury');
        if (footer) {
            setInterval(() => {
                createSparkle();
            }, 2000);
        }

        function createSparkle() {
            const sparkle = document.createElement('div');
            sparkle.className = 'sparkle';
            sparkle.style.cssText = `
                position: absolute;
                width: 4px;
                height: 4px;
                background: #C9A26A;
                border-radius: 50%;
                pointer-events: none;
                animation: sparkleFade 1.5s ease-out forwards;
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
            `;

            footer.appendChild(sparkle);

            setTimeout(() => {
                sparkle.remove();
            }, 1500);
        }
    });
    </script>

    <style>
    @keyframes bubbleRise {
        0% { transform: translateY(20px); opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { transform: translateY(-100px); opacity: 0; }
    }

    @keyframes sparkleFade {
        0% { transform: scale(0); opacity: 1; }
        50% { transform: scale(1); opacity: 0.8; }
        100% { transform: scale(2); opacity: 0; }
    }

    .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .footer-bottom {
        border-top: 1px solid rgba(245, 230, 200, 0.1);
        padding-top: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .footer-links {
        display: flex;
        gap: 1.5rem;
    }

    .footer-links a {
        color: var(--champagne);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-links a:hover {
        color: var(--rosegold);
    }

    .footer-contact {
        margin-top: 1rem;
    }

    .footer-contact p {
        margin: 0.25rem 0;
        font-size: 0.9rem;
        color: var(--champagne);
    }

    .site-title-wrapper {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .site-tagline {
        font-size: 0.8rem;
        color: var(--champagne);
        margin-top: 0.25rem;
        font-family: 'Montserrat', sans-serif;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .queen-icon {
        margin-right: 0.5rem;
        font-size: 1.2em;
    }

    .mobile-menu-toggle {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
    }

    .mobile-menu-icon::before {
        content: '☰';
        font-size: 1.5rem;
        color: var(--champagne);
    }

    @media (max-width: 768px) {
        .footer-bottom {
            flex-direction: column;
            text-align: center;
        }

        .footer-links {
            justify-content: center;
        }

        .mobile-menu-toggle {
            display: block;
        }
    }
    </style>
</body>
</html>