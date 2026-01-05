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
            // File uploaded to server - using actual filename with spaces
            audioFile: 'music/Another Night clara la san.mp3',
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
    
    // Validate required elements exist
    if (!playerContainer || !currentVolumeSpan || !youtubeContainer || !tracklistContent) {
        console.error('❌ Required elements not found. Check HTML structure.');
        console.error('Missing:', {
            playerContainer: !playerContainer,
            currentVolumeSpan: !currentVolumeSpan,
            youtubeContainer: !youtubeContainer,
            tracklistContent: !tracklistContent
        });
    }
    
    // Track current player to properly stop it
    let currentIframe = null;
    let currentAudio = null;

    // Function to stop current player
    function stopCurrentPlayer() {
        // Stop and remove YouTube iframe
        if (currentIframe) {
            try {
                const iframe = currentIframe;
                if (iframe && iframe.parentNode) {
                    iframe.src = ''; // Stop loading
                    iframe.remove();
                }
                currentIframe = null;
            } catch (e) {
                console.log('Error stopping iframe:', e);
            }
        }
        
        // Stop and remove audio player
        if (currentAudio) {
            try {
                if (currentAudio.pause) currentAudio.pause();
                currentAudio.src = '';
                if (currentAudio.load) currentAudio.load();
                currentAudio = null;
            } catch (e) {
                console.log('Error stopping audio:', e);
            }
        }
        
        // Clear container if it exists
        if (youtubeContainer) {
            youtubeContainer.innerHTML = '';
        }
    }

    cassetteTapes.forEach(tape => {
        tape.addEventListener('click', function() {
            const volume = this.getAttribute('data-volume');
            const tapeInfo = tapeData[volume];
            
            // Validate elements exist before proceeding
            if (!playerContainer || !currentVolumeSpan || !youtubeContainer || !tracklistContent) {
                console.error('❌ Cannot play: Required elements missing');
                return;
            }
            
            if (tapeInfo) {
                // Stop any currently playing media
                stopCurrentPlayer();
                
                // Update current playing info
                if (currentVolumeSpan) {
                    currentVolumeSpan.textContent = tapeInfo.title;
                }
                
                // Small delay to ensure cleanup
                setTimeout(() => {
                    // Handle different media types
                    if (tapeInfo.type === 'youtube') {
                        // Create YouTube embed with unique ID to prevent conflicts
                        const embedUrl = `https://www.youtube.com/embed/${tapeInfo.youtubeId}?autoplay=1&rel=0&enablejsapi=1`;
                        const iframe = document.createElement('iframe');
                        iframe.className = 'youtube-embed';
                        iframe.src = embedUrl;
                        iframe.setAttribute('frameborder', '0');
                        iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
                        iframe.setAttribute('allowfullscreen', '');
                        
                        youtubeContainer.appendChild(iframe);
                        currentIframe = iframe;
                        
                        // Handle iframe load errors
                        iframe.addEventListener('error', function() {
                            youtubeContainer.innerHTML = `
                                <div class="error-message" style="padding: 20px; text-align: center; color: #ff00ff;">
                                    <p>⚠️ Error loading video</p>
                                    <p style="font-size: 14px; margin-top: 10px;">
                                        <a href="https://www.youtube.com/watch?v=${tapeInfo.youtubeId}" target="_blank" style="color: #00ffff;">
                                            Watch on YouTube
                                        </a>
                                    </p>
                                </div>
                            `;
                        });
                        
                    } else if (tapeInfo.type === 'audio') {
                        // Create HTML5 audio player with error handling
                        const audioDiv = document.createElement('div');
                        audioDiv.className = 'audio-player';
                        
                        const audio = document.createElement('audio');
                        audio.controls = true;
                        audio.style.width = '100%';
                        audio.style.height = '60px';
                        // Don't set autoplay - we'll handle it with promise catch
                        
                        // Create primary source with proper path handling
                        const source1 = document.createElement('source');
                        // Handle both relative and absolute paths
                        let audioSrc = tapeInfo.audioFile;
                        if (!audioSrc.startsWith('http') && !audioSrc.startsWith('/')) {
                            // Relative path - ensure it starts with music/
                            audioSrc = audioSrc.startsWith('music/') ? audioSrc : `music/${audioSrc}`;
                        }
                        // URL encode spaces in filename
                        source1.src = encodeURI(audioSrc);
                        source1.type = 'audio/mpeg';
                        audio.appendChild(source1);
                        
                        // Add fallback source if available
                        if (tapeInfo.fallbackUrl) {
                            const source2 = document.createElement('source');
                            let fallbackSrc = tapeInfo.fallbackUrl;
                            if (!fallbackSrc.startsWith('http') && !fallbackSrc.startsWith('/')) {
                                fallbackSrc = fallbackSrc.startsWith('music/') ? fallbackSrc : `music/${fallbackSrc}`;
                            }
                            source2.src = fallbackSrc;
                            source2.type = 'audio/mpeg';
                            audio.appendChild(source2);
                        }
                        
                        const audioInfo = document.createElement('div');
                        audioInfo.className = 'audio-info';
                        audioInfo.innerHTML = `
                            <h4>${tapeInfo.title}</h4>
                            <p>Click play to start the chopped & screwed version</p>
                        `;
                        
                        audioDiv.appendChild(audio);
                        audioDiv.appendChild(audioInfo);
                        
                        youtubeContainer.appendChild(audioDiv);
                        currentAudio = audio;
                        
                        // Handle audio load errors with better diagnostics
                        let errorHandled = false;
                        
                        // Function to show error message
                        function showAudioError(errorMsg) {
                            if (errorHandled) return;
                            errorHandled = true;
                            
                            audioInfo.innerHTML = `
                                <h4>${tapeInfo.title}</h4>
                                <p style="color: #ff00ff;">⚠️ Error loading audio file</p>
                                <p style="font-size: 12px; color: #888;">${errorMsg}</p>
                                <p style="font-size: 11px; color: #666; margin-top: 8px;">
                                    The MP3 file needs to be uploaded to the server.
                                </p>
                                <p style="font-size: 10px; color: #555; margin-top: 4px;">
                                    File: ${tapeInfo.audioFile}
                                </p>
                            `;
                        }
                        
                        // Handle audio load errors
                        audio.addEventListener('error', function(e) {
                            console.error('Audio load error:', e);
                            console.error('Audio source:', audio.src);
                            console.error('Audio error code:', audio.error ? audio.error.code : 'unknown');
                            
                            // Check error type
                            let errorMsg = 'File not found or cannot be loaded';
                            if (audio.error) {
                                switch(audio.error.code) {
                                    case 1: // MEDIA_ERR_ABORTED
                                        errorMsg = 'Loading aborted';
                                        break;
                                    case 2: // MEDIA_ERR_NETWORK
                                        errorMsg = 'Network error - file not found on server';
                                        break;
                                    case 3: // MEDIA_ERR_DECODE
                                        errorMsg = 'File format error';
                                        break;
                                    case 4: // MEDIA_ERR_SRC_NOT_SUPPORTED
                                        errorMsg = 'File format not supported or file missing';
                                        break;
                                }
                            }
                            showAudioError(errorMsg);
                        });
                        
                        // Try to play audio and catch promise rejections (NotSupportedError)
                        // Wait for sources to load first
                        audio.addEventListener('loadeddata', function() {
                            audio.play().catch(function(error) {
                                console.error('Audio play promise rejected:', error);
                                if (error.name === 'NotSupportedError' || error.message.includes('no supported source')) {
                                    showAudioError('No supported audio source found - file needs to be uploaded');
                                }
                            });
                        });
                        
                        // Also catch immediate errors if sources fail before loadeddata
                        setTimeout(function() {
                            if (!errorHandled && audio.readyState === 0) {
                                // No data loaded after 2 seconds
                                showAudioError('Audio file not found - needs to be uploaded to server');
                            }
                        }, 2000);
                        
                        // Add loadstart listener for debugging and error reset
                        audio.addEventListener('loadstart', function() {
                            errorHandled = false; // Reset on new load attempt
                            console.log('Audio load started:', audio.src);
                        });
                        
                        // Add stalled listener
                        audio.addEventListener('stalled', function() {
                            console.warn('Audio load stalled:', audio.src);
                        });
                        
                        // Handle audio load success
                        audio.addEventListener('loadeddata', function() {
                            console.log('Audio loaded successfully');
                        });
                    }
                }, 100); // Small delay to ensure cleanup
                
                // Update tracklist
                if (tracklistContent) {
                    tracklistContent.innerHTML = tapeInfo.tracks.map(track => 
                        `<div class="track-item">${track}</div>`
                    ).join('');
                }
                
                // Add visual feedback
                cassetteTapes.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Scroll to player
                if (playerContainer) {
                    playerContainer.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                }
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

