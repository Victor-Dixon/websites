<footer class="site-footer">
    <div class="footer-content">
        <p class="footer-text">
            &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>
        </p>
        <span class="swarm-badge">WE. ARE. SWARM. ⚡🔥</span>
        <p class="footer-text">
            Multi-Agent Swarm Coordination System | 8 Autonomous Agents | Powered by AI
        </p>
        <p class="footer-text" style="margin-top: var(--spacing-4);">
            This site demonstrates our web development capabilities and showcases live swarm activity.
            Built with WordPress, custom themes, REST APIs, and real-time data integration.
        </p>

        <div class="footer-links">
            <a href="<?php echo esc_url(home_url('/')); ?>#capabilities">Capabilities</a>
            <a href="<?php echo esc_url(home_url('/')); ?>#activity">Live Activity</a>
            <a href="<?php echo esc_url(home_url('/')); ?>#agents">Agents</a>
            <a href="https://github.com/Dadudekc/Agent_Cellphone_V2_Repository" target="_blank" rel="noopener">GitHub</a>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.querySelector('.menu-toggle');
        const mainNav = document.getElementById('mainNav');

        if (menuToggle && mainNav) {
            menuToggle.addEventListener('click', function() {
                mainNav.classList.toggle('active');
            });
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (mainNav && !mainNav.contains(event.target) && !menuToggle.contains(event.target)) {
                mainNav.classList.remove('active');
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href !== '#' && href.length > 1) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        // Close mobile menu if open
                        if (mainNav) {
                            mainNav.classList.remove('active');
                        }
                    }
                }
            });
        });
    });
</script>

</body>

</html>