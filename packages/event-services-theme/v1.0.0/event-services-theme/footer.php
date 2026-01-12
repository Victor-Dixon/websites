        </div><!-- #content -->

        <footer id="colophon" class="site-footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-branding">
                        <h3><?php bloginfo('name'); ?></h3>
                        <p><?php bloginfo('description'); ?></p>
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
                        <p>Built with 🥏 Ultimate Frisbee Spirit</p>
                    </div>

                    <div class="footer-links">
                        <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>">Privacy Policy</a>
                        <a href="<?php echo esc_url(home_url('/terms-of-service')); ?>">Terms of Service</a>
                        <a href="<?php echo esc_url(home_url('/contact')); ?>">Contact</a>
                    </div>
                </div>

                <!-- Ultimate Frisbee Footer Animation -->
                <div class="ultimate-footer-animation">
                    <div class="field-animation-footer">
                        <svg viewBox="0 0 1200 100" class="w-full h-16 opacity-30">
                            <defs>
                                <linearGradient id="ultimateGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#52b788;stop-opacity:1" />
                                    <stop offset="50%" style="stop-color:#74c69d;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#2d6a4f;stop-opacity:1" />
                                </linearGradient>
                            </defs>

                            <!-- Animated frisbee field -->
                            <g class="field-animation">
                                <line x1="100" y1="50" x2="300" y2="30" stroke="url(#ultimateGradient)" stroke-width="1" class="field-line" style="animation-delay: 0s;"/>
                                <line x1="300" y1="30" x2="500" y2="70" stroke="url(#ultimateGradient)" stroke-width="1" class="field-line" style="animation-delay: 0.5s;"/>
                                <line x1="500" y1="70" x2="700" y2="20" stroke="url(#ultimateGradient)" stroke-width="1" class="field-line" style="animation-delay: 1s;"/>
                                <line x1="700" y1="20" x2="900" y2="60" stroke="url(#ultimateGradient)" stroke-width="1" class="field-line" style="animation-delay: 1.5s;"/>
                                <line x1="900" y1="60" x2="1100" y2="40" stroke="url(#ultimateGradient)" stroke-width="1" class="field-line" style="animation-delay: 2s;"/>

                                <!-- Frisbee nodes -->
                                <circle cx="100" cy="50" r="2" fill="#52b788" class="frisbee-node" style="animation-delay: 0s;"/>
                                <circle cx="300" cy="30" r="2" fill="#74c69d" class="frisbee-node" style="animation-delay: 0.5s;"/>
                                <circle cx="500" cy="70" r="2" fill="#2d6a4f" class="frisbee-node" style="animation-delay: 1s;"/>
                                <circle cx="700" cy="20" r="2" fill="#52b788" class="frisbee-node" style="animation-delay: 1.5s;"/>
                                <circle cx="900" cy="60" r="2" fill="#74c69d" class="frisbee-node" style="animation-delay: 2s;"/>
                                <circle cx="1100" cy="40" r="2" fill="#2d6a4f" class="frisbee-node" style="animation-delay: 2.5s;"/>
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
        </footer>
    </div><!-- #page -->

    <?php wp_footer(); ?>

    <!-- Footer JavaScript for animations -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add field animation
        const fieldLines = document.querySelectorAll('.field-line');
        const frisbeeNodes = document.querySelectorAll('.frisbee-node');

        // Pulse animation for field
        const pulseField = () => {
            fieldLines.forEach((line, index) => {
                setTimeout(() => {
                    line.style.animation = 'fieldPulse 3s ease-in-out infinite';
                    line.style.animationDelay = `${index * 0.2}s`;
                }, index * 200);
            });

            frisbeeNodes.forEach((node, index) => {
                setTimeout(() => {
                    node.style.animation = 'nodePulse 2s ease-in-out infinite';
                    node.style.animationDelay = `${index * 0.3}s`;
                }, index * 200);
            });
        };

        pulseField();
    });
    </script>

    <style>
    @keyframes fieldPulse {
        0%, 100% { opacity: 0.3; stroke-width: 1; }
        50% { opacity: 1; stroke-width: 2; }
    }

    @keyframes nodePulse {
        0%, 100% { opacity: 0.5; r: 2; }
        50% { opacity: 1; r: 3; }
    }

    .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
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
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-links a:hover {
        color: #52b788;
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
        color: white;
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