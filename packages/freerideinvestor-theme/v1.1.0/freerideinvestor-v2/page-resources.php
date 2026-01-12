<?php
/**
 * Template Name: Resources
 * Template Post Type: page
 *
 * Resources page template for FreeRide Investor
 *
 * @package FreeRideInvestor_V2
 */

get_header();
?>

<main class="resources-page">
    <!-- Hero Section -->
    <section class="resources-hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Trading Resources</h1>
                <p class="hero-subtitle">Comprehensive educational resources, tools, and community support to accelerate your trading success</p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">500+</span>
                        <span class="stat-label">Educational Articles</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">50+</span>
                        <span class="stat-label">Video Tutorials</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">25+</span>
                        <span class="stat-label">Interactive Tools</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Resource Categories -->
    <section class="resource-categories">
        <div class="container">
            <div class="categories-grid">
                <div class="category-card" onclick="showCategory('education')">
                    <div class="category-icon">📚</div>
                    <h3>Educational Content</h3>
                    <p>Comprehensive learning materials covering trading fundamentals, advanced strategies, and market analysis.</p>
                    <span class="category-count">200+ articles</span>
                </div>

                <div class="category-card" onclick="showCategory('tools')">
                    <div class="category-icon">🛠️</div>
                    <h3>Trading Tools</h3>
                    <p>Interactive calculators, risk assessment tools, and performance tracking utilities.</p>
                    <span class="category-count">25+ tools</span>
                </div>

                <div class="category-card" onclick="showCategory('videos')">
                    <div class="category-icon">🎥</div>
                    <h3>Video Library</h3>
                    <p>Expert-led video tutorials, strategy walkthroughs, and live trading sessions.</p>
                    <span class="category-count">50+ videos</span>
                </div>

                <div class="category-card" onclick="showCategory('community')">
                    <div class="category-icon">🤝</div>
                    <h3>Community Resources</h3>
                    <p>Forums, webinars, mentorship programs, and trader networking opportunities.</p>
                    <span class="category-count">10K+ members</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Resources -->
    <section class="featured-resources">
        <div class="container">
            <h2>Featured Resources</h2>
            <div class="resources-grid">
                <!-- Getting Started Guide -->
                <div class="resource-card featured">
                    <div class="resource-image">
                        <span class="resource-icon">🚀</span>
                    </div>
                    <div class="resource-content">
                        <div class="resource-category">Getting Started</div>
                        <h3>Complete Beginner's Guide to Trading</h3>
                        <p>Everything you need to know to start trading confidently. From basic concepts to your first trade.</p>
                        <div class="resource-meta">
                            <span class="duration">📖 45 min read</span>
                            <span class="level">Beginner</span>
                        </div>
                        <a href="#" class="resource-link">Start Reading →</a>
                    </div>
                </div>

                <!-- Risk Management -->
                <div class="resource-card">
                    <div class="resource-image">
                        <span class="resource-icon">🛡️</span>
                    </div>
                    <div class="resource-content">
                        <div class="resource-category">Risk Management</div>
                        <h3>Advanced Risk Management Strategies</h3>
                        <p>Learn institutional-grade risk management techniques used by professional traders.</p>
                        <div class="resource-meta">
                            <span class="duration">📊 Interactive Tool</span>
                            <span class="level">Advanced</span>
                        </div>
                        <a href="#" class="resource-link">Access Tool →</a>
                    </div>
                </div>

                <!-- Strategy Library -->
                <div class="resource-card">
                    <div class="resource-image">
                        <span class="resource-icon">📈</span>
                    </div>
                    <div class="resource-content">
                        <div class="resource-category">Strategies</div>
                        <h3>Strategy Library</h3>
                        <p>Browse our comprehensive collection of proven trading strategies with performance metrics.</p>
                        <div class="resource-meta">
                            <span class="duration">🎯 15 strategies</span>
                            <span class="level">All Levels</span>
                        </div>
                        <a href="/trading-strategies" class="resource-link">Browse Library →</a>
                    </div>
                </div>

                <!-- Market Analysis -->
                <div class="resource-card">
                    <div class="resource-image">
                        <span class="resource-icon">🔍</span>
                    </div>
                    <div class="resource-content">
                        <div class="resource-category">Analysis</div>
                        <h3>Daily Market Analysis</h3>
                        <p>Professional market analysis with key levels, trend analysis, and trading opportunities.</p>
                        <div class="resource-meta">
                            <span class="duration">📰 Daily updates</span>
                            <span class="level">Intermediate</span>
                        </div>
                        <a href="#" class="resource-link">View Analysis →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Resource Categories Content -->
    <div id="education-content" class="category-content" style="display: none;">
        <section class="education-section">
            <div class="container">
                <h2>Trading Education Library</h2>
                <div class="education-grid">
                    <div class="education-category">
                        <h3>📊 Market Fundamentals</h3>
                        <ul>
                            <li><a href="#">What is Trading?</a></li>
                            <li><a href="#">Understanding Financial Markets</a></li>
                            <li><a href="#">Asset Classes Overview</a></li>
                            <li><a href="#">Order Types Explained</a></li>
                        </ul>
                    </div>

                    <div class="education-category">
                        <h3>📈 Technical Analysis</h3>
                        <ul>
                            <li><a href="#">Chart Reading Basics</a></li>
                            <li><a href="#">Support & Resistance</a></li>
                            <li><a href="#">Trend Analysis</a></li>
                            <li><a href="#">Technical Indicators</a></li>
                        </ul>
                    </div>

                    <div class="education-category">
                        <h3>🧠 Trading Psychology</h3>
                        <ul>
                            <li><a href="#">Mindset for Success</a></li>
                            <li><a href="#">Emotional Discipline</a></li>
                            <li><a href="#">Risk Tolerance Assessment</a></li>
                            <li><a href="#">Trading Journal Best Practices</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div id="tools-content" class="category-content" style="display: none;">
        <section class="tools-section">
            <div class="container">
                <h2>Trading Tools & Calculators</h2>
                <div class="tools-grid">
                    <div class="tool-card">
                        <h3>Position Size Calculator</h3>
                        <p>Calculate optimal position sizes based on your risk tolerance and account balance.</p>
                        <a href="#" class="tool-link">Use Calculator</a>
                    </div>

                    <div class="tool-card">
                        <h3>Risk-Reward Calculator</h3>
                        <p>Determine optimal entry, stop-loss, and take-profit levels for any trade setup.</p>
                        <a href="#" class="tool-link">Use Calculator</a>
                    </div>

                    <div class="tool-card">
                        <h3>Portfolio Risk Analyzer</h3>
                        <p>Analyze your portfolio's risk exposure and diversification across different assets.</p>
                        <a href="#" class="tool-link">Analyze Portfolio</a>
                    </div>

                    <div class="tool-card">
                        <h3>Performance Tracker</h3>
                        <p>Track your trading performance with detailed metrics and analytics.</p>
                        <a href="#" class="tool-link">View Dashboard</a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Newsletter Signup -->
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-content">
                <h2>Stay Updated</h2>
                <p>Get weekly market insights, trading tips, and exclusive content delivered to your inbox.</p>
                <form class="newsletter-form" id="newsletterForm">
                    <input type="email" placeholder="Enter your email address" required>
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
                <p class="newsletter-disclaimer">No spam, unsubscribe anytime. We respect your privacy.</p>
            </div>
        </div>
    </section>
</main>

<script>
// Category switching functionality
function showCategory(category) {
    // Hide all category content
    document.querySelectorAll('.category-content').forEach(content => {
        content.style.display = 'none';
    });

    // Remove active class from all cards
    document.querySelectorAll('.category-card').forEach(card => {
        card.classList.remove('active');
    });

    // Show selected category
    const selectedContent = document.getElementById(category + '-content');
    if (selectedContent) {
        selectedContent.style.display = 'block';

        // Scroll to content
        selectedContent.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    // Add active class to clicked card
    event.currentTarget.classList.add('active');
}

// Newsletter form handling
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.getElementById('newsletterForm');

    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = newsletterForm.querySelector('.btn-primary');
            const originalText = submitBtn.textContent;

            submitBtn.textContent = 'Subscribing...';
            submitBtn.disabled = true;

            // Simulate API call
            setTimeout(() => {
                submitBtn.textContent = 'Subscribed!';
                submitBtn.style.background = '#48bb78';

                setTimeout(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.style.background = '';
                    submitBtn.disabled = false;
                    newsletterForm.reset();
                }, 2000);
            }, 1000);
        });
    }
});
</script>

<style>
/* Resources Page Styles */
.resources-page {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Hero Section */
.resources-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    color: white;
    padding: 6rem 0;
    text-align: center;
}

.hero-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 2rem;
}

.resources-hero h1 {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    line-height: 1.1;
}

.hero-subtitle {
    font-size: 1.4rem;
    opacity: 0.9;
    margin-bottom: 3rem;
    line-height: 1.6;
}

.hero-stats {
    display: flex;
    justify-content: center;
    gap: 3rem;
    flex-wrap: wrap;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 700;
    color: #00d4ff;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Resource Categories */
.resource-categories {
    padding: 6rem 0;
    background: #f8fafc;
}

.resource-categories .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

.category-card {
    background: white;
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e2e8f0;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.category-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.category-card.active {
    border-color: #00d4ff;
    box-shadow: 0 4px 20px rgba(0, 212, 255, 0.2);
}

.category-icon {
    font-size: 3rem;
    margin-bottom: 1.5rem;
    display: block;
}

.category-card h3 {
    font-size: 1.5rem;
    color: #1a202c;
    margin-bottom: 1rem;
    font-weight: 600;
}

.category-card p {
    color: #4a5568;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.category-count {
    display: inline-block;
    background: #f7fafc;
    color: #2d3748;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

/* Featured Resources */
.featured-resources {
    padding: 6rem 0;
    background: white;
}

.featured-resources .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.featured-resources h2 {
    font-size: 2.5rem;
    color: #1a202c;
    text-align: center;
    margin-bottom: 4rem;
    font-weight: 600;
}

.resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
}

.resource-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.resource-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.resource-card.featured {
    border-color: #48bb78;
    box-shadow: 0 4px 20px rgba(72, 187, 120, 0.2);
}

.resource-image {
    height: 120px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.resource-icon {
    font-size: 3rem;
}

.resource-content {
    padding: 2rem;
}

.resource-category {
    display: inline-block;
    background: #f7fafc;
    color: #00d4ff;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 1rem;
}

.resource-content h3 {
    font-size: 1.4rem;
    color: #1a202c;
    margin-bottom: 1rem;
    font-weight: 600;
}

.resource-content p {
    color: #4a5568;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.resource-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.duration, .level {
    font-size: 0.9rem;
    color: #718096;
    font-weight: 500;
}

.resource-link {
    color: #00d4ff;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    transition: color 0.3s ease;
}

.resource-link:hover {
    color: #0099cc;
}

/* Category Content */
.category-content {
    background: #f8fafc;
}

.education-section,
.tools-section {
    padding: 6rem 0;
}

.education-section h2,
.tools-section h2 {
    font-size: 2.5rem;
    color: #1a202c;
    text-align: center;
    margin-bottom: 4rem;
    font-weight: 600;
}

.education-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 3rem;
    max-width: 1000px;
    margin: 0 auto;
}

.education-category h3 {
    font-size: 1.4rem;
    color: #1a202c;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.education-category ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.education-category li {
    padding: 0.75rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.education-category li:last-child {
    border-bottom: none;
}

.education-category a {
    color: #00d4ff;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.education-category a:hover {
    color: #0099cc;
}

.tools-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    max-width: 1000px;
    margin: 0 auto;
}

.tool-card {
    background: white;
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e2e8f0;
    text-align: center;
}

.tool-card h3 {
    font-size: 1.4rem;
    color: #1a202c;
    margin-bottom: 1rem;
    font-weight: 600;
}

.tool-card p {
    color: #4a5568;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.tool-link {
    display: inline-block;
    background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.tool-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
}

/* Newsletter Section */
.newsletter-section {
    padding: 6rem 0;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    color: white;
}

.newsletter-content {
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
}

.newsletter-content h2 {
    font-size: 2.5rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.newsletter-content p {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.newsletter-form {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.newsletter-form input {
    flex: 1;
    min-width: 250px;
    padding: 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
    background: white;
}

.newsletter-disclaimer {
    font-size: 0.9rem;
    opacity: 0.7;
}

/* Responsive Design */
@media (max-width: 768px) {
    .resources-hero h1 {
        font-size: 2.5rem;
    }

    .hero-subtitle {
        font-size: 1.2rem;
    }

    .hero-stats {
        gap: 1.5rem;
    }

    .categories-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .resources-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .education-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    .tools-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .newsletter-form {
        flex-direction: column;
    }

    .newsletter-form input {
        min-width: auto;
    }

    .container {
        padding: 0 1rem;
    }

    .resources-hero,
    .resource-categories,
    .featured-resources,
    .education-section,
    .tools-section,
    .newsletter-section {
        padding: 4rem 0;
    }
}

@media (max-width: 480px) {
    .resources-hero h1 {
        font-size: 2rem;
    }

    .featured-resources h2,
    .education-section h2,
    .tools-section h2,
    .newsletter-content h2 {
        font-size: 2rem;
    }

    .resource-content {
        padding: 1.5rem;
    }

    .tool-card {
        padding: 2rem;
    }
}
</style>

<?php get_footer(); ?>