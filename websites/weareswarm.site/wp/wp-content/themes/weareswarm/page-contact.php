<?php
/**
 * Template Name: Contact Page
 * Description: Futuristic contact page with interactive elements
 */

get_header();
?>

<section class="contact-hero">
    <div class="section-container">
        <div class="section-header">
            <h1 class="section-title">
                <span class="title-accent">Connect</span> with the Swarm
            </h1>
            <p class="section-subtitle">
                Ready to experience the future of AI coordination? Join our revolutionary ecosystem.
            </p>
        </div>

        <div class="contact-grid">
            <div class="contact-card featured">
                <div class="card-background">
                    <div class="bg-particles"></div>
                </div>
                <div class="card-icon">🚀</div>
                <h3>Start a Project</h3>
                <p>Experience swarm intelligence in action with our advanced coordination systems.</p>
                <div class="card-features">
                    <span class="feature-tag">Free Consultation</span>
                    <span class="feature-tag">Custom Solutions</span>
                    <span class="feature-tag">Full Support</span>
                </div>
                <a href="#" class="cta-button cta-primary">
                    <span class="button-text">Begin Coordination</span>
                    <span class="button-arrow">→</span>
                </a>
            </div>

            <div class="contact-card">
                <div class="card-icon">🤝</div>
                <h3>Partnership</h3>
                <p>Explore collaboration opportunities and integration possibilities.</p>
                <div class="card-features">
                    <span class="feature-tag">API Access</span>
                    <span class="feature-tag">White-label</span>
                    <span class="feature-tag">Custom Integration</span>
                </div>
                <a href="#" class="cta-button cta-secondary">
                    <span class="button-text">Discuss Partnership</span>
                </a>
            </div>

            <div class="contact-card">
                <div class="card-icon">📚</div>
                <h3>Documentation</h3>
                <p>Dive deep into swarm technology with comprehensive documentation.</p>
                <div class="card-features">
                    <span class="feature-tag">API Docs</span>
                    <span class="feature-tag">Integration Guide</span>
                    <span class="feature-tag">Best Practices</span>
                </div>
                <a href="#" class="cta-button cta-ghost">
                    <span class="button-text">Explore Docs</span>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="contact-form-section">
    <div class="section-container">
        <div class="contact-form-container">
            <div class="form-header">
                <h2>Get in Touch</h2>
                <p>Send us a message and we'll respond with swarm-like efficiency.</p>
            </div>

            <form class="contact-form" method="post">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                        <div class="input-border"></div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                        <div class="input-border"></div>
                    </div>

                    <div class="form-group">
                        <label for="company">Company/Organization</label>
                        <input type="text" id="company" name="company">
                        <div class="input-border"></div>
                    </div>

                    <div class="form-group">
                        <label for="interest">Primary Interest</label>
                        <select id="interest" name="interest">
                            <option value="">Select an option</option>
                            <option value="consulting">AI Consulting</option>
                            <option value="integration">System Integration</option>
                            <option value="partnership">Partnership</option>
                            <option value="research">Research Collaboration</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="input-border"></div>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="6" required></textarea>
                    <div class="input-border"></div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="cta-button cta-primary">
                        <span class="button-text">Send Message</span>
                        <span class="button-icon">📤</span>
                    </button>
                    <p class="form-note">We'll respond within 24 hours with swarm-like efficiency.</p>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="connect-section">
    <div class="section-container">
        <div class="section-header">
            <h2 class="section-title">
                Stay <span class="title-accent">Connected</span>
            </h2>
            <p class="section-subtitle">
                Follow our journey and join the swarm intelligence revolution
            </p>
        </div>

        <div class="connect-grid">
            <div class="connect-item">
                <div class="connect-icon">🐝</div>
                <h3>Swarm Intelligence</h3>
                <p>Real-time updates on our AI coordination breakthroughs</p>
                <a href="#" class="connect-link">Follow Progress</a>
            </div>

            <div class="connect-item">
                <div class="connect-icon">📊</div>
                <h3>Performance Metrics</h3>
                <p>Live dashboards showing swarm efficiency and productivity</p>
                <a href="#" class="connect-link">View Stats</a>
            </div>

            <div class="connect-item">
                <div class="connect-icon">🔬</div>
                <h3>Research Updates</h3>
                <p>Cutting-edge AI research and swarm optimization techniques</p>
                <a href="#" class="connect-link">Read Research</a>
            </div>

            <div class="connect-item">
                <div class="connect-icon">🤝</div>
                <h3>Community</h3>
                <p>Join our community of AI enthusiasts and swarm operators</p>
                <a href="#" class="connect-link">Join Community</a>
            </div>
        </div>
    </div>
</section>

<style>
/* Contact Page Styles */
.contact-hero {
    padding: 5rem 0;
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0a0a0a 100%);
    position: relative;
    overflow: hidden;
}

.contact-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background:
        radial-gradient(circle at 20% 50%, rgba(0, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 0, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 80%, rgba(255, 255, 0, 0.1) 0%, transparent 50%);
}

.contact-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-top: 4rem;
    position: relative;
    z-index: 2;
}

.contact-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 2.5rem;
    text-align: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.contact-card.featured {
    grid-column: 1 / -1;
    background: linear-gradient(135deg, rgba(0, 255, 255, 0.1), rgba(255, 0, 255, 0.1));
    border: 2px solid transparent;
    background-clip: padding-box;
    position: relative;
}

.contact-card.featured::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(135deg, #00ffff, #ff00ff, #ffff00);
    border-radius: 18px;
    z-index: -1;
    animation: borderGlow 3s ease-in-out infinite;
}

@keyframes borderGlow {
    0%, 100% {
        opacity: 0.5;
    }
    50% {
        opacity: 1;
    }
}

.card-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: -1;
}

.bg-particles {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}

.bg-particles::before,
.bg-particles::after {
    content: '';
    position: absolute;
    width: 4px;
    height: 4px;
    background: rgba(0, 255, 255, 0.6);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

.bg-particles::before {
    top: 20%;
    left: 20%;
    animation-delay: 0s;
}

.bg-particles::after {
    top: 70%;
    right: 25%;
    animation-delay: 3s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.6; }
    50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
}

.contact-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    border-color: rgba(0, 255, 255, 0.3);
}

.card-icon {
    font-size: 3rem;
    margin-bottom: 1.5rem;
    display: block;
}

.contact-card h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    font-family: 'Orbitron', monospace;
}

.contact-card p {
    opacity: 0.9;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.card-features {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    justify-content: center;
    margin-bottom: 2rem;
}

.feature-tag {
    background: rgba(0, 255, 255, 0.1);
    border: 1px solid rgba(0, 255, 255, 0.3);
    color: #00ffff;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Contact Form Section */
.contact-form-section {
    padding: 5rem 0;
    background: #1a1a1a;
}

.contact-form-container {
    max-width: 800px;
    margin: 0 auto;
}

.form-header {
    text-align: center;
    margin-bottom: 3rem;
}

.form-header h2 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    font-family: 'Orbitron', monospace;
    background: linear-gradient(135deg, #00ffff, #ff00ff);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.form-header p {
    opacity: 0.8;
    font-size: 1.125rem;
}

.contact-form {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 3rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.form-group {
    position: relative;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #ffffff;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: #ffffff;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #00ffff;
    box-shadow: 0 0 0 3px rgba(0, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.08);
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.input-border {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #00ffff, #ff00ff);
    transition: width 0.3s ease;
}

.form-group input:focus ~ .input-border,
.form-group select:focus ~ .input-border,
.form-group textarea:focus ~ .input-border {
    width: 100%;
}

.form-actions {
    text-align: center;
}

.form-note {
    margin-top: 1rem;
    opacity: 0.7;
    font-size: 0.875rem;
}

/* Connect Section */
.connect-section {
    padding: 5rem 0;
    background: #0a0a0a;
}

.connect-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-top: 4rem;
}

.connect-item {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
}

.connect-item:hover {
    transform: translateY(-4px);
    border-color: rgba(0, 255, 255, 0.3);
}

.connect-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    display: block;
}

.connect-item h3 {
    font-size: 1.25rem;
    margin-bottom: 0.75rem;
    font-family: 'Orbitron', monospace;
}

.connect-item p {
    opacity: 0.8;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.connect-link {
    color: #00ffff;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.connect-link:hover {
    color: #ff00ff;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .contact-grid {
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    }

    .form-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .contact-hero,
    .contact-form-section,
    .connect-section {
        padding: 3rem 0;
    }

    .contact-card {
        padding: 2rem;
    }

    .contact-form {
        padding: 2rem;
        margin: 0 1rem;
    }

    .connect-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
}

@media (max-width: 480px) {
    .form-header h2 {
        font-size: 2rem;
    }

    .contact-form {
        padding: 1.5rem;
        margin: 0 0.5rem;
    }

    .connect-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Contact form enhancement
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.contact-form');
    if (!form) return;

    // Form validation enhancement
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });

        input.addEventListener('input', function() {
            if (this.classList.contains('error')) {
                validateField(this);
            }
        });
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate all fields
        let isValid = true;
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });

        if (isValid) {
            // Show success message
            showFormMessage('Message sent successfully! We\'ll respond within 24 hours.', 'success');

            // Reset form
            form.reset();

            // Reset input borders
            document.querySelectorAll('.input-border').forEach(border => {
                border.style.width = '0';
            });
        } else {
            showFormMessage('Please fill in all required fields correctly.', 'error');
        }
    });

    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;

        // Remove previous error states
        field.classList.remove('error');
        field.parentNode.classList.remove('error');

        // Required field validation
        if (field.hasAttribute('required') && !value) {
            isValid = false;
        }

        // Email validation
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
            }
        }

        // Apply error state
        if (!isValid) {
            field.classList.add('error');
            field.parentNode.classList.add('error');
        }

        return isValid;
    }

    function showFormMessage(message, type) {
        // Remove existing message
        const existingMessage = form.querySelector('.form-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        // Create new message
        const messageDiv = document.createElement('div');
        messageDiv.className = `form-message form-message-${type}`;
        messageDiv.textContent = message;

        messageDiv.style.cssText = `
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-top: 1.5rem;
            font-weight: 600;
            text-align: center;
            background: ${type === 'success' ? 'rgba(0, 255, 136, 0.1)' : 'rgba(255, 68, 68, 0.1)'};
            border: 1px solid ${type === 'success' ? '#00ff88' : '#ff4444'};
            color: ${type === 'success' ? '#00ff88' : '#ff4444'};
        `;

        form.appendChild(messageDiv);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.parentNode.removeChild(messageDiv);
            }
        }, 5000);
    }

    // Add CSS for error states
    const errorStyles = `
        .form-group.error input,
        .form-group.error select,
        .form-group.error textarea {
            border-color: #ff4444;
            box-shadow: 0 0 0 3px rgba(255, 68, 68, 0.1);
        }

        .form-group.error label {
            color: #ff4444;
        }
    `;

    const style = document.createElement('style');
    style.textContent = errorStyles;
    document.head.appendChild(style);
});
</script>

<?php get_footer(); ?>