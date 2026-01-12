<?php
/**
 * Hero Section Template Part for Houston Sip Queen Theme
 * Displays the luxury mobile bartending hero section
 */
?>

<section class="hero-luxury">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">
                Houston Sip Queen
            </h1>
            <p class="hero-subtitle">
                Luxury Mobile Bartending for Your Most Precious Moments
            </p>

            <div class="hero-cta-group">
                <a href="#quote" class="btn">Get Your Quote</a>
                <a href="#packages" class="btn btn-secondary">View Packages</a>
            </div>
        </div>
    </div>

    <!-- Floating champagne glasses -->
    <div class="champagne-decoration">
        <div class="champagne-glass glass-1">🥂</div>
        <div class="champagne-glass glass-2">🍸</div>
        <div class="champagne-glass glass-3">🍷</div>
        <div class="champagne-glass glass-4">🥂</div>
    </div>

    <!-- Scroll indicator -->
    <div class="scroll-indicator">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M7 13l3 3 3-3M7 6l3 3 3-3"/>
        </svg>
        <span>Discover Our World</span>
    </div>
</section>

<style>
.champagne-decoration {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    overflow: hidden;
}

.champagne-glass {
    position: absolute;
    font-size: 2rem;
    opacity: 0.6;
    animation: float 6s ease-in-out infinite;
}

.glass-1 {
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.glass-2 {
    top: 60%;
    right: 15%;
    animation-delay: 2s;
}

.glass-3 {
    top: 40%;
    left: 80%;
    animation-delay: 4s;
}

.glass-4 {
    bottom: 25%;
    left: 5%;
    animation-delay: 1s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-10px) rotate(2deg); }
    66% { transform: translateY(5px) rotate(-1deg); }
}

.scroll-indicator {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    color: var(--champagne);
    font-family: 'Montserrat', sans-serif;
    font-size: 0.9rem;
    animation: bounce 2s infinite;
}

.scroll-indicator svg {
    margin-bottom: 0.5rem;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
    40% { transform: translateX(-50%) translateY(-10px); }
    60% { transform: translateX(-50%) translateY(-5px); }
}

@media (max-width: 768px) {
    .champagne-glass {
        font-size: 1.5rem;
    }

    .hero-title {
        font-size: 3rem !important;
    }

    .hero-subtitle {
        font-size: 1.1rem !important;
    }

    .glass-1, .glass-4 {
        left: 5%;
    }

    .glass-2 {
        right: 5%;
    }

    .glass-3 {
        left: 70%;
    }
}
</style>