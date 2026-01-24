/**
 * Futuristic Hero Section Animations
 * ================================
 *
 * Advanced animations and interactions for the weareswarm.site hero section.
 * Features particle systems, data streams, and intelligent user engagement.
 *
 * Author: Swarm Intelligence - Agent-7 (Web Development Specialist)
 * Date: 2026-01-15
 */

class FuturisticHero {
    constructor() {
        this.heroSection = document.getElementById('hero');
        this.heroStats = document.querySelectorAll('.stat-number');
        this.ctaButtons = document.querySelectorAll('.cta-button');
        this.navIndicators = document.querySelectorAll('.nav-indicator');
        this.particles = [];
        this.dataStreams = [];
        this.animationFrameId = null;
        this.isVisible = false;

        this.init();
    }

    init() {
        if (!this.heroSection) return;

        this.setupIntersectionObserver();
        this.setupEventListeners();
        this.createParticleSystem();
        this.createDataStreams();
        this.setupAccessibility();
        this.initializeCounters();
    }

    setupIntersectionObserver() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !this.isVisible) {
                    this.startAnimations();
                    this.isVisible = true;
                } else if (!entry.isIntersecting && this.isVisible) {
                    this.pauseAnimations();
                    this.isVisible = false;
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        });

        observer.observe(this.heroSection);
    }

    setupEventListeners() {
        // CTA button interactions
        this.ctaButtons.forEach(button => {
            button.addEventListener('mouseenter', (e) => this.handleButtonHover(e));
            button.addEventListener('mouseleave', (e) => this.handleButtonLeave(e));
            button.addEventListener('click', (e) => this.handleButtonClick(e));
        });

        // Navigation interactions
        this.navIndicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => this.navigateToSection(index));
        });

        // Scroll indicator
        const scrollIndicator = document.querySelector('.scroll-indicator');
        if (scrollIndicator) {
            scrollIndicator.addEventListener('click', () => this.scrollToNextSection());
        }

        // Keyboard navigation
        document.addEventListener('keydown', (e) => this.handleKeydown(e));

        // Window resize
        window.addEventListener('resize', () => this.handleResize());

        // Mouse movement for interactive effects
        document.addEventListener('mousemove', (e) => this.handleMouseMove(e));
    }

    createParticleSystem() {
        const particlesContainer = document.querySelector('.particles-container');
        if (!particlesContainer) return;

        // Create 15 floating particles
        for (let i = 0; i < 15; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 20 + 's';
            particle.style.animationDuration = (15 + Math.random() * 10) + 's';

            particlesContainer.appendChild(particle);
            this.particles.push(particle);
        }
    }

    createDataStreams() {
        const dataStream = document.querySelector('.data-stream');
        if (!dataStream) return;

        // Create 5 data streams
        for (let i = 0; i < 5; i++) {
            const stream = document.createElement('div');
            stream.className = 'stream-line';
            stream.style.top = (20 + Math.random() * 60) + '%';
            stream.style.width = (20 + Math.random() * 30) + '%';
            stream.style.animationDelay = Math.random() * 8 + 's';
            stream.style.animationDuration = (6 + Math.random() * 4) + 's';

            if (Math.random() > 0.5) {
                stream.style.left = '-30%';
                stream.style.right = 'auto';
            } else {
                stream.style.right = '-30%';
                stream.style.left = 'auto';
            }

            dataStream.appendChild(stream);
            this.dataStreams.push(stream);
        }
    }

    setupAccessibility() {
        // Add ARIA labels and roles
        const heroSection = this.heroSection;
        heroSection.setAttribute('aria-label', 'Futuristic hero section with animated swarm intelligence showcase');
        heroSection.setAttribute('role', 'banner');

        // Make interactive elements focusable
        this.ctaButtons.forEach((button, index) => {
            button.setAttribute('tabindex', '0');
            button.setAttribute('aria-label', `Call to action button ${index + 1}: ${button.querySelector('.button-text')?.textContent || 'Action'}`);
        });

        this.navIndicators.forEach((indicator, index) => {
            indicator.setAttribute('tabindex', '0');
            indicator.setAttribute('aria-label', `Navigate to section ${index + 1}`);
        });
    }

    initializeCounters() {
        // Set up intersection observer for stat counters
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.startCounters();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        this.heroStats.forEach(stat => {
            observer.observe(stat);
        });
    }

    startCounters() {
        this.heroStats.forEach(stat => {
            const target = parseFloat(stat.getAttribute('data-target') || '0');
            this.animateCounter(stat, 0, target, 2000);
        });
    }

    animateCounter(element, start, end, duration) {
        const startTime = performance.now();
        const endTime = startTime + duration;

        const animate = (currentTime) => {
            if (currentTime >= endTime) {
                element.textContent = Math.floor(end).toLocaleString();
                return;
            }

            const progress = (currentTime - startTime) / duration;
            const easeProgress = 1 - Math.pow(1 - progress, 3); // Cubic ease out
            const current = Math.floor(start + (end - start) * easeProgress);

            element.textContent = current.toLocaleString();

            requestAnimationFrame(animate);
        };

        requestAnimationFrame(animate);
    }

    startAnimations() {
        // Background layers are already animated via CSS
        // Particle system is already active
        // Data streams are already animated

        // Trigger any additional entrance animations
        this.triggerEntranceAnimations();
    }

    pauseAnimations() {
        // Pause expensive animations when not visible
        // CSS animations will continue, but we can reduce JavaScript animations
    }

    triggerEntranceAnimations() {
        // Add entrance animations to elements that need them
        const elementsToAnimate = [
            '.hero-badge',
            '.hero-title',
            '.hero-subtitle',
            '.hero-actions',
            '.hero-stats'
        ];

        elementsToAnimate.forEach((selector, index) => {
            const element = document.querySelector(selector);
            if (element) {
                element.style.animationDelay = (index * 0.2) + 's';
                element.style.animationFillMode = 'both';
            }
        });
    }

    handleButtonHover(event) {
        const button = event.currentTarget;

        // Add glow effect
        button.style.boxShadow = '0 0 30px rgba(0, 255, 255, 0.5)';

        // Trigger particle burst effect
        this.createParticleBurst(event.clientX, event.clientY);
    }

    handleButtonLeave(event) {
        const button = event.currentTarget;

        // Remove glow effect
        button.style.boxShadow = '';
    }

    handleButtonClick(event) {
        const button = event.currentTarget;
        const action = button.getAttribute('data-action');

        // Add click animation
        button.style.transform = 'scale(0.95)';
        setTimeout(() => {
            button.style.transform = '';
        }, 150);

        // Handle different actions
        switch (action) {
            case 'start-coordination':
                this.handleStartCoordination();
                break;
            case 'view-demo':
                this.handleViewDemo();
                break;
            case 'learn-more':
                this.scrollToNextSection();
                break;
        }

        // Analytics tracking
        this.trackEvent('hero_cta_click', { action: action });
    }

    handleStartCoordination() {
        // Show coordination interface
        this.showNotification('🚀 Initializing Swarm Coordination...', 'success');

        // Simulate coordination startup
        setTimeout(() => {
            this.showNotification('✅ Swarm Intelligence Activated', 'success');
        }, 2000);
    }

    handleViewDemo() {
        // Show live demo
        this.showNotification('🎮 Loading Live Demonstration...', 'info');

        setTimeout(() => {
            this.scrollToNextSection();
        }, 1500);
    }

    scrollToNextSection() {
        const missionsSection = document.getElementById('missions');
        if (missionsSection) {
            missionsSection.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    navigateToSection(index) {
        const sections = ['hero', 'missions', 'agents', 'contact'];
        const targetSection = document.getElementById(sections[index]);

        if (targetSection) {
            targetSection.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        // Update navigation indicators
        this.updateNavIndicators(index);
    }

    updateNavIndicators(activeIndex) {
        this.navIndicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === activeIndex);
        });
    }

    handleKeydown(event) {
        // Keyboard navigation
        switch (event.key) {
            case 'ArrowDown':
            case ' ':
                event.preventDefault();
                this.scrollToNextSection();
                break;
            case 'ArrowUp':
                event.preventDefault();
                this.scrollToPreviousSection();
                break;
            case 'Enter':
                if (document.activeElement.classList.contains('cta-button')) {
                    document.activeElement.click();
                }
                break;
        }
    }

    scrollToPreviousSection() {
        // Scroll to previous section logic
        const currentScroll = window.pageYOffset;
        const sections = document.querySelectorAll('section[id]');
        let targetSection = null;

        for (let i = sections.length - 1; i >= 0; i--) {
            const section = sections[i];
            if (section.offsetTop < currentScroll - 100) {
                targetSection = section;
                break;
            }
        }

        if (targetSection) {
            targetSection.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    handleResize() {
        // Handle responsive adjustments
        this.updateResponsiveLayout();
    }

    updateResponsiveLayout() {
        const isMobile = window.innerWidth < 768;

        if (isMobile) {
            // Mobile-specific adjustments
            this.heroSection.style.padding = '2rem 1rem';
        } else {
            // Desktop adjustments
            this.heroSection.style.padding = '0 3rem';
        }
    }

    handleMouseMove(event) {
        // Create subtle parallax effect on background elements
        const mouseX = event.clientX / window.innerWidth;
        const mouseY = event.clientY / window.innerHeight;

        // Subtle movement of background elements
        const bgLayers = document.querySelectorAll('.hero-bg-layer');
        bgLayers.forEach((layer, index) => {
            const speed = (index + 1) * 0.5;
            const x = (mouseX - 0.5) * speed;
            const y = (mouseY - 0.5) * speed;
            layer.style.transform = `translate(${x}px, ${y}px) scale(${1 + speed * 0.01})`;
        });
    }

    createParticleBurst(x, y) {
        // Create a burst of particles at the specified coordinates
        const burstContainer = document.querySelector('.particles-container');
        if (!burstContainer) return;

        for (let i = 0; i < 8; i++) {
            const particle = document.createElement('div');
            particle.className = 'burst-particle';
            particle.style.left = x + 'px';
            particle.style.top = y + 'px';
            particle.style.setProperty('--angle', (i * 45) + 'deg');

            burstContainer.appendChild(particle);

            // Remove particle after animation
            setTimeout(() => {
                if (particle.parentNode) {
                    particle.parentNode.removeChild(particle);
                }
            }, 1000);
        }
    }

    showNotification(message, type = 'info') {
        // Create and show a notification
        const notification = document.createElement('div');
        notification.className = `hero-notification hero-notification-${type}`;
        notification.textContent = message;

        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#00ff88' : type === 'error' ? '#ff4444' : '#00ffff'};
            color: #0a0a0a;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            z-index: 10000;
            animation: slideInRight 0.3s ease-out;
            font-weight: 600;
        `;

        document.body.appendChild(notification);

        // Auto-remove after 4 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease-in forwards';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 4000);
    }

    trackEvent(eventName, data) {
        // Analytics tracking
        if (window.gtag) {
            window.gtag('event', eventName, {
                hero_type: 'futuristic',
                ...data
            });
        }

        // Swarm intelligence tracking
        if (window.swarmAnalytics) {
            window.swarmAnalytics.track(eventName, data);
        }
    }

    destroy() {
        // Clean up event listeners and animations
        if (this.animationFrameId) {
            cancelAnimationFrame(this.animationFrameId);
        }

        // Remove particles
        this.particles.forEach(particle => {
            if (particle.parentNode) {
                particle.parentNode.removeChild(particle);
            }
        });

        // Remove data streams
        this.dataStreams.forEach(stream => {
            if (stream.parentNode) {
                stream.parentNode.removeChild(stream);
            }
        });
    }
}

// Add CSS for burst particles
const burstParticleCSS = `
@keyframes particleBurst {
    0% {
        opacity: 1;
        transform: translate(0, 0) scale(1);
    }
    100% {
        opacity: 0;
        transform: translate(var(--x), var(--y)) scale(0);
    }
}

.burst-particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: #00ffff;
    border-radius: 50%;
    pointer-events: none;
    animation: particleBurst 1s ease-out forwards;
}

.burst-particle:nth-child(1) { --x: 50px; --y: -50px; }
.burst-particle:nth-child(2) { --x: 35px; --y: -35px; }
.burst-particle:nth-child(3) { --x: 0px; --y: -50px; }
.burst-particle:nth-child(4) { --x: -35px; --y: -35px; }
.burst-particle:nth-child(5) { --x: -50px; --y: -50px; }
.burst-particle:nth-child(6) { --x: -35px; --y: 35px; }
.burst-particle:nth-child(7) { --x: 0px; --y: 50px; }
.burst-particle:nth-child(8) { --x: 35px; --y: 35px; }

@keyframes slideInRight {
    0% {
        opacity: 0;
        transform: translateX(100%);
    }
    100% {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideOutRight {
    0% {
        opacity: 1;
        transform: translateX(0);
    }
    100% {
        opacity: 0;
        transform: translateX(100%);
    }
}

.hero-notification {
    font-family: 'Inter', sans-serif;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}
`;

// Inject the CSS
const style = document.createElement('style');
style.textContent = burstParticleCSS;
document.head.appendChild(style);

// Initialize the hero when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('hero')) {
        window.futuristicHero = new FuturisticHero();
    }
});

// Handle page visibility changes
document.addEventListener('visibilitychange', () => {
    if (window.futuristicHero) {
        if (document.hidden) {
            window.futuristicHero.pauseAnimations();
        } else {
            window.futuristicHero.startAnimations();
        }
    }
});

// Export for potential module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FuturisticHero;
}