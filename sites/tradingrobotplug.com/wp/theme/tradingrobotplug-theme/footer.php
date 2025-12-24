</div><!-- #content -->

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h3>TradingRobotPlug</h3>
                <p>Automated trading strategies for everyone. Validate before you trade.</p>
            </div>
            <div class="footer-col">
                <h4>Platform</h4>
                <ul>
                    <li><a href="/marketplace">Marketplace</a></li>
                    <li><a href="/pricing">Pricing</a></li>
                    <li><a href="/how-it-works">How It Works</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Support</h4>
                <ul>
                    <li><a href="/contact">Contact</a></li>
                    <li><a href="/docs">Documentation</a></li>
                    <li><a href="/status">Status</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Legal</h4>
                <ul>
                    <li><a href="/terms">Terms of Service</a></li>
                    <li><a href="/privacy">Privacy Policy</a></li>
                    <li><a href="/risk-disclosure">Risk Disclosure</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date( 'Y' ); ?> Trading Robot Plug. All rights reserved.</p>
            <p class="risk-warning">Trading involves substantial risk of loss and is not suitable for every investor.</p>
        </div>
    </div>
    <style>
        .site-footer {
            background: #f8f9fa;
            padding: 60px 0 30px;
            margin-top: 60px;
            border-top: 1px solid #eee;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        .footer-col h3 {
            color: #007bff;
            margin-top: 0;
        }
        .footer-col h4 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #333;
        }
        .footer-col ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer-col ul li {
            margin-bottom: 10px;
        }
        .footer-col a {
            text-decoration: none;
            color: #666;
            transition: color 0.2s;
        }
        .footer-col a:hover {
            color: #007bff;
        }
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #e9ecef;
            color: #888;
            font-size: 14px;
        }
        .risk-warning {
            font-size: 12px;
            margin-top: 10px;
            color: #999;
        }
    </style>
</footer>

<?php wp_footer(); ?>
</body>
</html>
