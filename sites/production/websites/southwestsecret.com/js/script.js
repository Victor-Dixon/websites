// SouthWest Secret - Interactive Features
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    
    // Smooth scrolling for navigation links
    const navLinks = document.querySelectorAll('.nav-menu a, a[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Only handle internal links
            if (href.startsWith('#')) {
                e.preventDefault();
                const targetId = href.substring(1);
                const targetSection = document.getElementById(targetId);
                
                if (targetSection) {
                    const headerOffset = 80;
                    const elementPosition = targetSection.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // Add scroll animation to elements
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe sections for scroll animations
    const sections = document.querySelectorAll('section');
    sections.forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(30px)';
        section.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
        observer.observe(section);
    });

    // === CASSETTE TAPE FUNCTIONALITY ===
    
    // Track data for each tape
    const tapeData = {
        '1': {
            youtubeId: 'oYqlfb2sghc',
            title: 'Screw Tape Vol. 1',
            type: 'youtube',
            tracks: [
                'Track 1 - Chopped & Screwed Mix',
                'Track 2 - Houston Vibes',
                'Track 3 - Slow Motion Flow',
                'Track 4 - Screwed Up Classics'
            ]
        },
        '2': {
            youtubeId: 'jBQ0gArMvzc',
            title: 'Screw Tape Vol. 2',
            type: 'youtube',
            tracks: [
                'Track 1 - Fresh Screwed Mix',
                'Track 2 - Laid Back Flow',
                'Track 3 - Chopped Classics',
                'Track 4 - Houston Sound'
            ]
        },
        '3': {
            audioFile: 'Another_Night_clara_la_san.mp3',
            title: 'Another Night - Clara La San',
            type: 'audio',
            tracks: [
                'Another Night - Clara La San',
                'Chopped & Screwed Version',
                'SouthWest Secret Remix'
            ]
        }
    };

    // Handle cassette tape clicks
    const cassetteTapes = document.querySelectorAll('.cassette-tape');
    const playerContainer = document.getElementById('tape-player');
    const currentVolumeSpan = document.querySelector('.current-volume');
    const youtubeContainer = document.querySelector('.youtube-container');
    const tracklistContent = document.querySelector('.tracklist-content');

    cassetteTapes.forEach(tape => {
        tape.addEventListener('click', function() {
            const volume = this.getAttribute('data-volume');
            const tapeInfo = tapeData[volume];
            
            if (tapeInfo) {
                // Update current playing info
                currentVolumeSpan.textContent = tapeInfo.title;
                
                // Handle different media types
                if (tapeInfo.type === 'youtube') {
                    // Create YouTube embed
                    const embedUrl = `https://www.youtube.com/embed/${tapeInfo.youtubeId}?autoplay=1&rel=0`;
                    youtubeContainer.innerHTML = `
                        <iframe 
                            class="youtube-embed" 
                            src="${embedUrl}" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    `;
                } else if (tapeInfo.type === 'audio') {
                    // Create HTML5 audio player
                    youtubeContainer.innerHTML = `
                        <div class="audio-player">
                            <audio controls autoplay style="width: 100%; height: 60px;">
                                <source src="music/${tapeInfo.audioFile}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                            <div class="audio-info">
                                <h4>${tapeInfo.title}</h4>
                                <p>Click play to start the chopped & screwed version</p>
                            </div>
                        </div>
                    `;
                }
                
                // Update tracklist
                tracklistContent.innerHTML = tapeInfo.tracks.map(track => 
                    `<div class="track-item">${track}</div>`
                ).join('');
                
                // Add visual feedback
                cassetteTapes.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Scroll to player
                playerContainer.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
        });
    });

    // Add active state styling
    const style = document.createElement('style');
    style.textContent = `
        .cassette-tape.active .tape-body {
            border-color: #ff00ff;
            box-shadow: 
                inset 0 2px 4px rgba(255, 0, 255, 0.3),
                0 4px 8px rgba(255, 0, 255, 0.5);
        }
        
        .track-item {
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 0, 255, 0.2);
            color: var(--text-light);
        }
        
        .track-item:last-child {
            border-bottom: none;
        }
    `;
    document.head.appendChild(style);

    // Add active state to navigation based on scroll position
    window.addEventListener('scroll', function() {
        let current = '';
        const sections = document.querySelectorAll('section');
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            
            if (window.pageYOffset >= (sectionTop - 200)) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    });

    // Add parallax effect to hero section
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const hero = document.querySelector('.hero');
        
        if (hero) {
            hero.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
    });

    // Make video thumbnail clickable
    const videoThumbnail = document.querySelector('.video-thumbnail');
    if (videoThumbnail) {
        videoThumbnail.addEventListener('click', function() {
            window.open('https://youtu.be/jBQ0gArMvzc', '_blank');
        });
    }

    // Console art (just for fun!)
    console.log('%c SOUTHWEST SECRET ', 'background: linear-gradient(45deg, #ff00ff, #00ffff); color: white; font-size: 24px; font-weight: bold; padding: 10px;');
    console.log('%c Chopped & Screwed DJ 🎵 ', 'background: #000; color: #00ffff; font-size: 16px; padding: 5px;');
    console.log('Welcome to the code! 🔥');

    // Easter egg: Konami code
    let konamiCode = [];
    const konamiSequence = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'b', 'a'];
    
    document.addEventListener('keydown', function(e) {
        konamiCode.push(e.key);
        konamiCode.splice(-konamiSequence.length - 1, konamiCode.length - konamiSequence.length);
        
        if (konamiCode.join(',').includes(konamiSequence.join(','))) {
            activateEasterEgg();
        }
    });

    function activateEasterEgg() {
        const body = document.body;
        body.style.animation = 'rainbow 2s linear infinite';
        
        // Add rainbow animation
        const style = document.createElement('style');
        style.innerHTML = `
            @keyframes rainbow {
                0% { filter: hue-rotate(0deg); }
                100% { filter: hue-rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
        
        console.log('%c 🎉 EASTER EGG ACTIVATED! 🎉 ', 'background: #ff00ff; color: white; font-size: 20px; padding: 10px;');
        
        setTimeout(() => {
            body.style.animation = '';
        }, 5000);
    }

    // Add loading animation
    window.addEventListener('load', function() {
        document.body.classList.add('loaded');
    });

    // Mobile menu toggle (if needed in future)
    const createMobileMenu = () => {
        const nav = document.querySelector('nav');
        const menuBtn = document.createElement('button');
        menuBtn.classList.add('mobile-menu-btn');
        menuBtn.innerHTML = '☰';
        menuBtn.setAttribute('aria-label', 'Toggle menu');
        
        menuBtn.addEventListener('click', () => {
            nav.classList.toggle('active');
        });
        
        if (window.innerWidth <= 768) {
            nav.parentNode.insertBefore(menuBtn, nav);
        }
    };

    // Initialize mobile menu on small screens
    if (window.innerWidth <= 768) {
        createMobileMenu();
    }

    // Reinitialize on resize
    window.addEventListener('resize', function() {
        if (window.innerWidth <= 768) {
            const existingBtn = document.querySelector('.mobile-menu-btn');
            if (!existingBtn) {
                createMobileMenu();
            }
        }
    });
});

// Add custom cursor effect (optional, can be removed if too much)
document.addEventListener('mousemove', function(e) {
    const cursor = document.createElement('div');
    cursor.className = 'cursor-trail';
    cursor.style.left = e.pageX + 'px';
    cursor.style.top = e.pageY + 'px';
    
    document.body.appendChild(cursor);
    
    setTimeout(() => {
        cursor.remove();
    }, 1000);
});

