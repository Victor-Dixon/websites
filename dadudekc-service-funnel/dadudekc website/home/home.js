// Home-specific JavaScript for DaDudeKC Website
class HomePage {
    constructor() {
        this.init();
    }

    init() {
        this.setupHeroAnimation();
        this.setupServiceGrid();
        this.setupBlogGrid();
        this.setupContactForm();
        this.setupScrollAnimations();
    }

    setupHeroAnimation() {
        // Add entrance animations to hero elements
        const heroElements = document.querySelectorAll('.hero-section > *');
        heroElements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                element.style.transition = 'all 0.8s ease';
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, index * 200);
        });
    }

    setupServiceGrid() {
        // Create service items dynamically
        const servicesSection = document.querySelector('.services-section');
        if (servicesSection) {
            const serviceGrid = document.createElement('div');
            serviceGrid.className = 'service-grid';
            
            const services = [
                {
                    title: 'Web Development',
                    description: 'Modern, responsive websites built with cutting-edge technologies',
                    icon: '💻'
                },
                {
                    title: 'AI Integration',
                    description: 'Intelligent chatbots and AI-powered features for your business',
                    icon: '🤖'
                },
                {
                    title: 'Gaming Solutions',
                    description: 'Custom game development and gaming platform integration',
                    icon: '🎮'
                },
                {
                    title: 'Data Analytics',
                    description: 'Insights and visualizations to drive your business decisions',
                    icon: '📊'
                }
            ];

            services.forEach(service => {
                const serviceItem = document.createElement('div');
                serviceItem.className = 'service-item';
                serviceItem.innerHTML = `
                    <div style="font-size: 3rem; margin-bottom: 1rem;">${service.icon}</div>
                    <h3>${service.title}</h3>
                    <p>${service.description}</p>
                `;
                serviceGrid.appendChild(serviceItem);
            });

            servicesSection.appendChild(serviceGrid);
        }
    }

    setupBlogGrid() {
        // Create blog preview cards
        const blogSection = document.querySelector('.blog-section');
        if (blogSection) {
            const blogGrid = document.createElement('div');
            blogGrid.className = 'blog-grid';
            
            const blogPosts = [
                {
                    title: 'Getting Started with AI Integration',
                    excerpt: 'Learn how to add intelligent features to your website...',
                    date: '2024-01-15'
                },
                {
                    title: 'Modern Web Development Trends',
                    excerpt: 'Explore the latest technologies shaping the web...',
                    date: '2024-01-10'
                },
                {
                    title: 'Gaming Platform Development',
                    excerpt: 'Building scalable gaming solutions for the modern web...',
                    date: '2024-01-05'
                }
            ];

            blogPosts.forEach(post => {
                const blogCard = document.createElement('div');
                blogCard.className = 'blog-card';
                blogCard.innerHTML = `
                    <h3>${post.title}</h3>
                    <p>${post.excerpt}</p>
                    <small style="color: var(--primary-color);">${post.date}</small>
                `;
                blogGrid.appendChild(blogCard);
            });

            blogSection.appendChild(blogGrid);
        }
    }

    setupContactForm() {
        // Create contact form dynamically
        const contactSection = document.querySelector('.contact-section');
        if (contactSection) {
            const contactForm = document.createElement('form');
            contactForm.className = 'contact-form';
            contactForm.innerHTML = `
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Your name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Your email" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" placeholder="Your message" required></textarea>
                </div>
                <button type="submit" class="btn-learn-more">Send Message</button>
            `;

            contactForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleContactSubmit(contactForm);
            });

            contactSection.appendChild(contactForm);
        }
    }

    handleContactSubmit(form) {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Show loading state
        if (window.websiteUtils) {
            window.websiteUtils.showLoading(submitButton);
        }

        // Simulate form submission
        setTimeout(() => {
            if (window.websiteUtils) {
                window.websiteUtils.hideLoading(submitButton);
                window.websiteUtils.showNotification('Message sent successfully!', 'success');
                form.reset();
            }
        }, 1500);
    }

    setupScrollAnimations() {
        // Add scroll-triggered animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        // Observe sections for animation
        document.querySelectorAll('section').forEach(section => {
            observer.observe(section);
        });
    }
}

// Initialize home page when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new HomePage();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = HomePage;
}

