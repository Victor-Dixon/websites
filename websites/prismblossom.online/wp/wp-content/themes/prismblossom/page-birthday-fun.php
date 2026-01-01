<?php

/**
 * Template Name: Birthday Fun
 * 
 * Interactive birthday fun page with animated cat
 * 
 * @package SouthWestSecret
 */

get_header();
?>

<section class="birthday-fun-section">
    <div class="container">
        <h1 class="section-title" style="color: #FFD700; text-shadow: 0 0 10px #FFD700;">
            <span class="graffiti-sub">BIRTHDAY</span>
            <span class="bubble-sub">FUN</span>
        </h1>
        <p class="section-description" style="color: #FFD700; text-shadow: 0 0 5px #FFD700;">Click or tap the birthday cat for some fun!</p>
        
        <!-- Interactive Mini-Games Section -->
        <div class="mini-games-container" style="margin: 40px 0; padding: 30px; background: rgba(0, 0, 0, 0.8); border: 2px solid #FFD700; border-radius: 15px; box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);">
            <h2 style="color: #FFD700; text-shadow: 0 0 10px #FFD700; margin-bottom: 20px;">🎮 Mini Games</h2>
            <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                <button id="confetti-burst" style="background: rgba(0, 0, 0, 0.6); border: 2px solid #FFD700; color: #FFD700; padding: 15px 25px; border-radius: 25px; cursor: pointer; font-size: 16px; font-weight: bold; text-shadow: 0 0 5px #FFD700; box-shadow: 0 0 10px rgba(255, 215, 0, 0.5); transition: all 0.3s ease;">🎉 Confetti Burst</button>
                <button id="golden-sparkles" style="background: rgba(0, 0, 0, 0.6); border: 2px solid #FFD700; color: #FFD700; padding: 15px 25px; border-radius: 25px; cursor: pointer; font-size: 16px; font-weight: bold; text-shadow: 0 0 5px #FFD700; box-shadow: 0 0 10px rgba(255, 215, 0, 0.5); transition: all 0.3s ease;">✨ Golden Sparkles</button>
                <button id="birthday-song" style="background: rgba(0, 0, 0, 0.6); border: 2px solid #FFD700; color: #FFD700; padding: 15px 25px; border-radius: 25px; cursor: pointer; font-size: 16px; font-weight: bold; text-shadow: 0 0 5px #FFD700; box-shadow: 0 0 10px rgba(255, 215, 0, 0.5); transition: all 0.3s ease;">🎵 Birthday Song</button>
            </div>
        </div>
        
        <!-- Birthday Images Gallery -->
        <div class="birthday-gallery" style="margin: 40px 0; padding: 30px; background: rgba(0, 0, 0, 0.8); border: 2px solid #FFD700; border-radius: 15px; box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);">
            <h2 style="color: #FFD700; text-shadow: 0 0 10px #FFD700; margin-bottom: 20px;">📸 Birthday Memories</h2>

            <!-- Upload Button -->
            <div style="text-align: center; margin-bottom: 30px;">
                <input type="file" id="gallery-upload-input" accept="image/*,video/*" multiple style="display: none;" onchange="if(this.files && this.files.length > 0 && window.processGalleryFiles) { window.processGalleryFiles(this.files); this.value=''; }">
                <button id="upload-btn" type="button" onclick="var input = document.getElementById('gallery-upload-input'); if(input) input.click(); else alert('File upload not available');" style="background: rgba(0, 0, 0, 0.6); border: 2px solid #FFD700; color: #FFD700; padding: 15px 40px; border-radius: 25px; cursor: pointer; font-size: 18px; font-weight: bold; text-shadow: 0 0 5px #FFD700; box-shadow: 0 0 10px rgba(255, 215, 0, 0.5); transition: all 0.3s ease;">
                    📷 Add Photos & Videos
                </button>
                <p style="color: #FFD700; opacity: 0.8; margin-top: 15px; font-size: 0.9rem;">Select multiple photos and videos at once (Max 10MB each)</p>
                <div id="upload-status" style="margin-top: 10px; color: #FFD700; font-size: 0.9rem;"></div>
            </div>

            <!-- Slideshow Gallery -->
            <div id="gallery-slideshow" class="gallery-slideshow" style="display: none; position: relative; max-width: 800px; margin: 0 auto; min-height: 500px; background: rgba(0, 0, 0, 0.9); border: 2px solid #FFD700; border-radius: 15px; overflow: hidden;">
                <!-- Navigation Arrows -->
                <button class="gallery-nav gallery-prev" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); background: rgba(255, 215, 0, 0.8); border: 2px solid #FFD700; color: #000; width: 50px; height: 50px; border-radius: 50%; cursor: pointer; font-size: 24px; font-weight: bold; z-index: 100; transition: all 0.3s ease;">‹</button>
                <button class="gallery-nav gallery-next" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: rgba(255, 215, 0, 0.8); border: 2px solid #FFD700; color: #000; width: 50px; height: 50px; border-radius: 50%; cursor: pointer; font-size: 24px; font-weight: bold; z-index: 100; transition: all 0.3s ease;">›</button>

                <!-- Gallery Items Container -->
                <div id="gallery-items-container" style="position: relative; width: 100%; height: 500px; display: flex; align-items: center; justify-content: center;">
                    <!-- Items will be inserted here -->
                </div>

                <!-- Gallery Thumbnails -->
                <div id="gallery-thumbnails" style="display: flex; gap: 10px; padding: 15px; overflow-x: auto; background: rgba(0, 0, 0, 0.7); justify-content: center; align-items: center;">
                    <!-- Thumbnails will be inserted here -->
                </div>

                <!-- Counter -->
                <div class="gallery-counter" style="position: absolute; bottom: 60px; left: 50%; transform: translateX(-50%); background: rgba(0, 0, 0, 0.8); color: #FFD700; padding: 8px 15px; border-radius: 20px; font-size: 14px; z-index: 50;">
                    <span id="current-index">1</span> / <span id="total-count">0</span>
                </div>

                <!-- Remove All Button -->
                <button id="clear-all-btn" style="position: absolute; top: 10px; right: 10px; background: rgba(255, 0, 0, 0.8); border: 2px solid #ff0000; color: white; padding: 8px 15px; border-radius: 20px; cursor: pointer; font-size: 14px; z-index: 100; transition: all 0.3s ease;">Clear All</button>
            </div>

            <!-- Empty State -->
            <div id="gallery-empty" style="text-align: center; padding: 40px; color: #FFD700; opacity: 0.7;">
                <p style="font-size: 1.2rem;">No photos or videos added yet. Click the button above to add some memories! 📸</p>
            </div>
        </div>

        <!-- Animated Cat Container -->
        <div class="cat-container">
            <div id="birthday-cat" class="birthday-cat">
                <div class="cat-body">
                    <div class="cat-head">
                        <div class="cat-ears">
                            <div class="ear left-ear"></div>
                            <div class="ear right-ear"></div>
                        </div>
                        <div class="cat-face">
                            <div class="cat-eyes">
                                <div class="eye left-eye">
                                    <div class="pupil"></div>
                                </div>
                                <div class="eye right-eye">
                                    <div class="pupil"></div>
                                </div>
                            </div>
                            <div class="cat-nose"></div>
                            <div class="cat-mouth">
                                <div class="mouth-left"></div>
                                <div class="mouth-right"></div>
                            </div>
                        </div>
                        <div class="party-hat">
                            <div class="hat-top"></div>
                            <div class="hat-base"></div>
                        </div>
                    </div>
                    <div class="cat-body-main">
                        <div class="cat-belly"></div>
                    </div>
                </div>
            </div>
            
            <!-- Confetti Container -->
            <div id="confetti-container" class="confetti-container"></div>
        </div>

        <!-- Click Counter -->
        <div class="click-counter">
            <p>Clicks: <span id="click-count">0</span></p>
        </div>

        <!-- Fun Messages -->
        <div id="fun-messages" class="fun-messages"></div>
    </div>
</section>

<script>
    // Global function for file processing - will be set up after DOM loads
    window.processGalleryFiles = null; // Placeholder, will be defined in DOMContentLoaded
</script>

<style>
.birthday-fun-section {
    padding: 120px 0 60px;
    min-height: 100vh;
    text-align: center;
    background: #000000;
}

.cat-container {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 400px;
    margin: 40px 0;
}

.birthday-cat {
    cursor: pointer;
    user-select: none;
    transition: transform 0.2s ease;
    position: relative;
    z-index: 10;
}

.birthday-cat:active {
    transform: scale(0.95);
}

.birthday-cat.clicked {
    animation: catBounce 0.5s ease;
}

@keyframes catBounce {

        0%,
        100% {
            transform: translateY(0) scale(1);
        }

        50% {
            transform: translateY(-20px) scale(1.1);
        }
}

.cat-body {
    position: relative;
}

.cat-head {
    position: relative;
    width: 120px;
    height: 100px;
    background: #ffa500;
    border-radius: 50% 50% 45% 45%;
    margin: 0 auto;
}

.cat-ears {
    position: absolute;
    top: -20px;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    display: flex;
    justify-content: space-between;
    padding: 0 15px;
}

.ear {
    width: 30px;
    height: 30px;
    background: #ffa500;
    clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
    position: relative;
}

.ear::before {
    content: '';
    position: absolute;
    top: 5px;
    left: 50%;
    transform: translateX(-50%);
    width: 15px;
    height: 15px;
    background: #ff8c00;
    clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
}

.cat-face {
    position: absolute;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
}

.cat-eyes {
    display: flex;
    gap: 20px;
    margin-bottom: 10px;
}

.eye {
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    position: relative;
    animation: blink 3s infinite;
}

@keyframes blink {

        0%,
        90%,
        100% {
            height: 20px;
        }

        95% {
            height: 2px;
        }
}

.pupil {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 12px;
    height: 12px;
    background: #000;
    border-radius: 50%;
}

.cat-nose {
    width: 12px;
    height: 10px;
    background: #ff69b4;
    clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
    margin: 0 auto 5px;
}

.cat-mouth {
    display: flex;
    justify-content: center;
    gap: 5px;
}

.mouth-left,
.mouth-right {
    width: 15px;
    height: 15px;
    border: 2px solid #000;
    border-top: none;
    border-radius: 0 0 50% 50%;
}

.mouth-left {
    border-right: none;
    border-radius: 0 0 0 50%;
}

.mouth-right {
    border-left: none;
    border-radius: 0 0 50% 0;
}

.party-hat {
    position: absolute;
    top: -40px;
    left: 50%;
    transform: translateX(-50%);
}

.hat-top {
    width: 8px;
    height: 30px;
    background: #FFD700;
    margin: 0 auto;
    border-radius: 4px 4px 0 0;
    box-shadow: 0 0 5px #FFD700;
}

.hat-base {
    width: 50px;
    height: 15px;
    background: #FFD700;
    border-radius: 50%;
    margin-top: -5px;
    box-shadow: 0 0 5px #FFD700;
}

.cat-body-main {
    width: 100px;
    height: 80px;
    background: #ffa500;
    border-radius: 50% 50% 40% 40%;
    margin: -10px auto 0;
    position: relative;
}

.cat-belly {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 60px;
    height: 50px;
    background: #ffd700;
    border-radius: 50%;
}

.confetti-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    overflow: hidden;
    z-index: 5;
}

.confetti {
    position: absolute;
    width: 10px;
    height: 10px;
    background: #FFD700;
    box-shadow: 0 0 5px #FFD700;
    animation: confettiFall linear forwards;
}

@keyframes confettiFall {
    to {
        transform: translateY(500px) rotate(360deg);
        opacity: 0;
    }
}

.click-counter {
    margin-top: 30px;
    font-size: 1.2rem;
    color: #FFD700;
    text-shadow: 0 0 5px #FFD700;
}

.click-counter span {
    color: #FFD700;
    font-weight: bold;
    font-size: 1.5rem;
    text-shadow: 0 0 10px #FFD700;
}

.fun-messages {
    margin-top: 30px;
    min-height: 50px;
}

.fun-message {
    background: rgba(0, 0, 0, 0.8);
    border: 2px solid #FFD700;
    border-radius: 10px;
    padding: 15px;
    margin: 10px auto;
    max-width: 400px;
    color: #FFD700;
    text-shadow: 0 0 5px #FFD700;
    box-shadow: 0 0 15px rgba(255, 215, 0, 0.5);
    animation: messagePop 0.5s ease;
}

@keyframes messagePop {
        0% {
            transform: scale(0);
            opacity: 0;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .gallery-thumbnail {
        cursor: pointer;
        opacity: 0.6;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        border-radius: 5px;
    }

    .gallery-thumbnail:hover {
        opacity: 0.8;
        border-color: #FFD700;
    }

    .gallery-thumbnail.active {
        opacity: 1;
        border-color: #FFD700;
        box-shadow: 0 0 10px rgba(255, 215, 0, 0.8);
    }

    #gallery-items-container img,
    #gallery-items-container video {
        display: block;
        margin: 0 auto;
    }

    #upload-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.8);
}

@media (max-width: 768px) {
    .cat-head {
        width: 100px;
        height: 85px;
    }
    
    .cat-body-main {
        width: 85px;
        height: 70px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cat = document.getElementById('birthday-cat');
    const confettiContainer = document.getElementById('confetti-container');
    const clickCountSpan = document.getElementById('click-count');
    const funMessagesDiv = document.getElementById('fun-messages');
    let clickCount = 0;
    
    const funMessages = [
        "🎉 Happy Birthday! 🎉",
        "🎂 You're awesome! 🎂",
        "🎈 Party time! 🎈",
        "🎊 Let's celebrate! 🎊",
        "🎁 You're amazing! 🎁",
        "✨ Have a great day! ✨",
        "🎪 Fun times ahead! 🎪",
        "🎭 You're the best! 🎭"
    ];
    
    function createConfetti() {
        const colors = ['#FFD700', '#FFA500', '#FFD700', '#FFA500', '#FFD700'];
        const confettiCount = 50;
        
        for (let i = 0; i < confettiCount; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDuration = (Math.random() * 2 + 1) + 's';
            confetti.style.animationDelay = Math.random() * 0.5 + 's';
            confettiContainer.appendChild(confetti);
            
            setTimeout(() => {
                confetti.remove();
            }, 3000);
        }
    }
    
    function playSound() {
        // Create a simple beep sound using Web Audio API
            const audioContext = new(window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 800;
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.1);
    }
    
    function showFunMessage() {
        const message = funMessages[Math.floor(Math.random() * funMessages.length)];
        const messageDiv = document.createElement('div');
        messageDiv.className = 'fun-message';
        messageDiv.textContent = message;
        funMessagesDiv.appendChild(messageDiv);
        
        setTimeout(() => {
            messageDiv.style.animation = 'messagePop 0.5s ease reverse';
            setTimeout(() => messageDiv.remove(), 500);
        }, 2000);
    }
    
    if (cat) {
        cat.addEventListener('click', function() {
            clickCount++;
            if (clickCountSpan) {
                clickCountSpan.textContent = clickCount;
            }
            
            // Add bounce animation
            cat.classList.add('clicked');
            setTimeout(() => cat.classList.remove('clicked'), 500);
            
            // Create confetti
            createConfetti();
            
            // Play sound
            try {
                playSound();
            } catch (e) {
                console.log('Sound not available');
            }
            
            // Show fun message
            showFunMessage();
        });
        
        // Also support touch for mobile
        cat.addEventListener('touchstart', function(e) {
            e.preventDefault();
            cat.click();
        });
    }
    
    // Mini-Games Interactive Features
    const confettiBurstBtn = document.getElementById('confetti-burst');
    const goldenSparklesBtn = document.getElementById('golden-sparkles');
    const birthdaySongBtn = document.getElementById('birthday-song');
    
        // Enhanced confetti burst function
        function createConfettiBurst() {
            if (!confettiContainer) {
                console.error('Confetti container not found!');
                return;
            }

            // Create massive confetti burst
            const colors = ['#FFD700', '#FFA500', '#FFD700', '#FFA500', '#FFFF00', '#FFD700'];
            for (let i = 0; i < 150; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.position = 'absolute';
                    confetti.style.left = Math.random() * 100 + '%';
                    confetti.style.top = '-10px';
                    confetti.style.width = (Math.random() * 8 + 5) + 'px';
                    confetti.style.height = (Math.random() * 8 + 5) + 'px';
                    confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.boxShadow = '0 0 10px ' + colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.animationDuration = (Math.random() * 2 + 1.5) + 's';
                    confetti.style.animationDelay = (Math.random() * 0.3) + 's';
                    confetti.style.animation = 'confettiFall linear forwards';
                    confettiContainer.appendChild(confetti);

                    setTimeout(() => {
                        if (confetti.parentNode) {
                            confetti.remove();
                        }
                    }, 3500);
                }, i * 5);
            }
        }

        if (confettiBurstBtn) {
            confettiBurstBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Confetti burst clicked!');
                createConfettiBurst();
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 100);
        });
        } else {
            console.error('Confetti burst button not found!');
        }

        // Enhanced golden sparkles function
        function createGoldenSparkles() {
            if (!confettiContainer) {
                console.error('Confetti container not found!');
                return;
            }

            // Create golden sparkles effect from multiple points
            const sparkleCount = 50;
            for (let i = 0; i < sparkleCount; i++) {
                const sparkle = document.createElement('div');
                sparkle.style.position = 'absolute';
                sparkle.style.width = (Math.random() * 6 + 4) + 'px';
                sparkle.style.height = (Math.random() * 6 + 4) + 'px';
                sparkle.style.background = '#FFD700';
                sparkle.style.borderRadius = '50%';
                sparkle.style.boxShadow = '0 0 15px #FFD700, 0 0 25px #FFD700';
                sparkle.style.left = Math.random() * 100 + '%';
                sparkle.style.top = Math.random() * 100 + '%';
                sparkle.style.animation = 'confettiFall 3s linear forwards';
                sparkle.style.animationDelay = (Math.random() * 0.5) + 's';
                sparkle.style.zIndex = '1000';
                confettiContainer.appendChild(sparkle);

                // Create additional smaller sparkles
                setTimeout(() => {
                    const miniSparkle = document.createElement('div');
                    miniSparkle.style.position = 'absolute';
                    miniSparkle.style.width = '3px';
                    miniSparkle.style.height = '3px';
                    miniSparkle.style.background = '#FFD700';
                    miniSparkle.style.borderRadius = '50%';
                    miniSparkle.style.boxShadow = '0 0 10px #FFD700';
                    miniSparkle.style.left = sparkle.style.left;
                    miniSparkle.style.top = sparkle.style.top;
                    miniSparkle.style.animation = 'confettiFall 2s linear forwards';
                    miniSparkle.style.opacity = '0.8';
                    confettiContainer.appendChild(miniSparkle);

                    setTimeout(() => {
                        if (miniSparkle.parentNode) miniSparkle.remove();
                    }, 2000);
                }, i * 30);

                setTimeout(() => {
                    if (sparkle.parentNode) {
                        sparkle.remove();
                    }
                }, 3000);
            }
        }

        if (goldenSparklesBtn) {
            goldenSparklesBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Golden sparkles clicked!');
                createGoldenSparkles();
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 100);
        });
        } else {
            console.error('Golden sparkles button not found!');
    }
    
    if (birthdaySongBtn) {
        birthdaySongBtn.addEventListener('click', function() {
            // Play birthday song notes
            const notes = [523.25, 587.33, 659.25, 698.46, 783.99]; // C, D, E, F, G
            notes.forEach((freq, index) => {
                setTimeout(() => {
                    try {
                            const audioContext = new(window.AudioContext || window.webkitAudioContext)();
                        const oscillator = audioContext.createOscillator();
                        const gainNode = audioContext.createGain();
                        
                        oscillator.connect(gainNode);
                        gainNode.connect(audioContext.destination);
                        
                        oscillator.frequency.value = freq;
                        oscillator.type = 'sine';
                        
                        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                        
                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.3);
                    } catch (e) {
                        console.log('Sound not available');
                    }
                }, index * 200);
            });
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 100);
        });
    }
    
        // Slideshow Gallery Functionality
        let galleryItems = [];
        let currentIndex = 0;

        const uploadInput = document.getElementById('gallery-upload-input');
        const slideshow = document.getElementById('gallery-slideshow');
        const emptyState = document.getElementById('gallery-empty');
        const itemsContainer = document.getElementById('gallery-items-container');
        const thumbnailsContainer = document.getElementById('gallery-thumbnails');
        const prevBtn = document.querySelector('.gallery-prev');
        const nextBtn = document.querySelector('.gallery-next');
        const currentIndexSpan = document.getElementById('current-index');
        const totalCountSpan = document.getElementById('total-count');
        const clearAllBtn = document.getElementById('clear-all-btn');

        console.log('Gallery elements initialized:', {
            uploadInput: !!uploadInput,
            slideshow: !!slideshow,
            emptyState: !!emptyState
        });

        // Load saved gallery from localStorage
        function loadGallery() {
            const saved = localStorage.getItem('birthday_gallery_slideshow');
            if (saved) {
                try {
                    galleryItems = JSON.parse(saved);
                    if (galleryItems.length > 0) {
                        renderGallery();
                    }
                } catch (e) {
                    console.error('Error loading gallery:', e);
                }
            }
        }

        // Save gallery to localStorage
        function saveGallery() {
            localStorage.setItem('birthday_gallery_slideshow', JSON.stringify(galleryItems));
        }

        // Render the entire gallery
        function renderGallery() {
            if (galleryItems.length === 0) {
                slideshow.style.display = 'none';
                emptyState.style.display = 'block';
                return;
            }

            slideshow.style.display = 'block';
            emptyState.style.display = 'none';

            // Clear containers
            itemsContainer.innerHTML = '';
            thumbnailsContainer.innerHTML = '';

            // Render main slideshow items
            galleryItems.forEach((item, index) => {
                const mediaElement = createMediaElement(item.data, item.type, 'main');
                mediaElement.style.display = index === currentIndex ? 'block' : 'none';
                mediaElement.dataset.index = index;
                itemsContainer.appendChild(mediaElement);

                // Create thumbnail
                const thumbnail = createMediaElement(item.data, item.type, 'thumbnail');
                thumbnail.classList.add('gallery-thumbnail');
                thumbnail.dataset.index = index;
                thumbnail.addEventListener('click', () => goToSlide(index));
                if (index === currentIndex) {
                    thumbnail.classList.add('active');
                }
                thumbnailsContainer.appendChild(thumbnail);
            });

            updateCounter();
            updateNavigation();
        }

        // Create media element (image or video)
        function createMediaElement(dataUrl, type, size) {
            let element;
            if (type === 'video') {
                element = document.createElement('video');
                element.controls = true;
                element.style.maxWidth = size === 'main' ? '100%' : '100px';
                element.style.maxHeight = size === 'main' ? '500px' : '80px';
                element.style.objectFit = 'contain';
            } else {
                element = document.createElement('img');
                element.style.maxWidth = size === 'main' ? '100%' : '100px';
                element.style.maxHeight = size === 'main' ? '500px' : '80px';
                element.style.objectFit = 'contain';
            }
            element.src = dataUrl;
            element.style.borderRadius = '10px';
            element.style.margin = size === 'main' ? 'auto' : '0 5px';
            return element;
        }

        // Navigate to specific slide
        function goToSlide(index) {
            if (index < 0 || index >= galleryItems.length) return;

            currentIndex = index;

            // Update main display
            itemsContainer.querySelectorAll('img, video').forEach((el, i) => {
                el.style.display = i === currentIndex ? 'block' : 'none';
            });

            // Update thumbnails
            thumbnailsContainer.querySelectorAll('.gallery-thumbnail').forEach((el, i) => {
                if (i === currentIndex) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            });

            updateCounter();
            updateNavigation();
        }

        // Update counter
        function updateCounter() {
            currentIndexSpan.textContent = currentIndex + 1;
            totalCountSpan.textContent = galleryItems.length;
        }

        // Update navigation buttons
        function updateNavigation() {
            if (prevBtn) {
                prevBtn.style.opacity = currentIndex === 0 ? '0.5' : '1';
                prevBtn.style.cursor = currentIndex === 0 ? 'not-allowed' : 'pointer';
            }
            if (nextBtn) {
                nextBtn.style.opacity = currentIndex === galleryItems.length - 1 ? '0.5' : '1';
                nextBtn.style.cursor = currentIndex === galleryItems.length - 1 ? 'not-allowed' : 'pointer';
            }
        }

        // Next slide
        function nextSlide() {
            if (currentIndex < galleryItems.length - 1) {
                goToSlide(currentIndex + 1);
            }
        }

        // Previous slide
        function prevSlide() {
            if (currentIndex > 0) {
                goToSlide(currentIndex - 1);
            }
        }

        // Upload button click handler - keep inline onclick as backup
        const uploadBtn = document.getElementById('upload-btn');
        const uploadStatus = document.getElementById('upload-status');

        if (uploadBtn) {
            // Keep inline onclick AND add event listener for better compatibility
            uploadBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Upload button clicked (event listener)!');

                const input = document.getElementById('gallery-upload-input');
                if (input) {
                    console.log('Triggering file input click');
                    input.click();
                } else {
                    console.error('Upload input not found!');
                    alert('Error: File upload not available. Please refresh the page.');
                }
            });

            console.log('Upload button handler attached');
        } else {
            console.error('Upload button not found!');
            if (uploadStatus) {
                uploadStatus.textContent = 'Error: Upload button not found. Please refresh.';
                uploadStatus.style.color = '#ff0000';
            }
        }

        // Create global file processing function
        function processGalleryFiles(filesArray) {
            const files = Array.from(filesArray);
            console.log('processGalleryFiles called - Files:', files.length);

            if (files.length === 0) {
                console.log('No files selected');
                return;
            }

            if (uploadStatus) {
                uploadStatus.textContent = `Uploading ${files.length} file(s)...`;
                uploadStatus.style.color = '#FFD700';
            }

            let processed = 0;
            let skipped = 0;
            const newItems = [];

            files.forEach((file, fileIndex) => {
                console.log(`Processing file ${fileIndex + 1}/${files.length}:`, file.name, file.type, file.size);

                // Check file size
                if (file.size > 10 * 1024 * 1024) {
                    alert(`File "${file.name}" is too large! Max size is 10MB.`);
                    skipped++;
                    processed++;
                    if (processed === files.length) {
                        finishUpload(newItems, files.length, skipped);
                    }
                    return;
                }

                // Validate file type
                if (!file.type.startsWith('image/') && !file.type.startsWith('video/')) {
                    alert(`File "${file.name}" is not a valid image or video file.`);
                    skipped++;
                    processed++;
                    if (processed === files.length) {
                        finishUpload(newItems, files.length, skipped);
                    }
                    return;
                }

                const reader = new FileReader();

                reader.onload = function(event) {
                    console.log('File read successfully:', file.name);
                    newItems.push({
                        data: event.target.result,
                        type: file.type.startsWith('video/') ? 'video' : 'img',
                        name: file.name
                    });

                    processed++;
                    if (uploadStatus) {
                        uploadStatus.textContent = `Processing ${processed}/${files.length}...`;
                    }

                    if (processed === files.length) {
                        finishUpload(newItems, files.length, skipped);
                    }
                };

                reader.onerror = function(error) {
                    console.error('Error reading file:', file.name, error);
                    alert(`Error reading file: ${file.name}`);
                    skipped++;
                    processed++;
                    if (processed === files.length) {
                        finishUpload(newItems, files.length, skipped);
                    }
                };

                reader.onprogress = function(e) {
                    if (e.lengthComputable) {
                        const percentLoaded = Math.round((e.loaded / e.total) * 100);
                        console.log(`Loading ${file.name}: ${percentLoaded}%`);
                    }
                };

                try {
                    reader.readAsDataURL(file);
                } catch (error) {
                    console.error('Error reading file as data URL:', error);
                    alert(`Error reading file: ${file.name}`);
                    skipped++;
                    processed++;
                    if (processed === files.length) {
                        finishUpload(newItems, files.length, skipped);
                    }
                }
            });

        }

        // Make function globally accessible for inline handlers
        window.processGalleryFiles = processGalleryFiles;

        // File upload handler - set up event listener
        if (uploadInput) {
            uploadInput.addEventListener('change', function(e) {
                console.log('File input change event triggered');
                const files = Array.from(e.target.files);
                if (files.length > 0) {
                    processGalleryFiles(files);
                }
                // Reset input after processing
                this.value = '';
            });

            console.log('File upload handler attached to input');
        } else {
            console.error('Upload input element not found!');
            if (uploadStatus) {
                uploadStatus.textContent = 'Error: Upload functionality not available. Please refresh the page.';
                uploadStatus.style.color = '#ff0000';
            }
        }

        // Finish upload function
        function finishUpload(newItems, totalFiles, skipped) {
            if (newItems.length > 0) {
                galleryItems = galleryItems.concat(newItems);
                saveGallery();
                renderGallery();
                goToSlide(galleryItems.length - newItems.length);

                console.log('Gallery updated!', galleryItems.length, 'items total');

                if (uploadStatus) {
                    const successMsg = `${newItems.length} file(s) uploaded successfully!`;
                    const skipMsg = skipped > 0 ? ` (${skipped} skipped)` : '';
                    uploadStatus.textContent = successMsg + skipMsg;
                    uploadStatus.style.color = '#00ff00';

                    setTimeout(() => {
                        uploadStatus.textContent = '';
                    }, 3000);
                }
            } else {
                if (uploadStatus) {
                    uploadStatus.textContent = skipped > 0 ? 'No files were uploaded. Please check file sizes and types.' : 'No files were uploaded.';
                    uploadStatus.style.color = '#ff0000';

            setTimeout(() => {
                        uploadStatus.textContent = '';
                    }, 3000);
                }
            }
        }

        // Navigation buttons
        if (prevBtn) {
            prevBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (currentIndex > 0) {
                    prevSlide();
                }
            });

            prevBtn.addEventListener('mouseenter', function() {
                if (currentIndex > 0) {
                    this.style.background = 'rgba(255, 215, 0, 1)';
                    this.style.transform = 'translateY(-50%) scale(1.1)';
                }
            });

            prevBtn.addEventListener('mouseleave', function() {
                this.style.background = 'rgba(255, 215, 0, 0.8)';
                this.style.transform = 'translateY(-50%) scale(1)';
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (currentIndex < galleryItems.length - 1) {
                    nextSlide();
                }
            });

            nextBtn.addEventListener('mouseenter', function() {
                if (currentIndex < galleryItems.length - 1) {
                    this.style.background = 'rgba(255, 215, 0, 1)';
                    this.style.transform = 'translateY(-50%) scale(1.1)';
                }
            });

            nextBtn.addEventListener('mouseleave', function() {
                this.style.background = 'rgba(255, 215, 0, 0.8)';
                this.style.transform = 'translateY(-50%) scale(1)';
            });
        }

        // Clear all button
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to remove all photos and videos?')) {
                    galleryItems = [];
                    saveGallery();
                    renderGallery();
                }
            });

            clearAllBtn.addEventListener('mouseenter', function() {
                this.style.background = 'rgba(255, 0, 0, 1)';
            });

            clearAllBtn.addEventListener('mouseleave', function() {
                this.style.background = 'rgba(255, 0, 0, 0.8)';
            });
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (slideshow.style.display === 'none') return;

            if (e.key === 'ArrowLeft') {
                prevSlide();
            } else if (e.key === 'ArrowRight') {
                nextSlide();
            }
        });

        // Initialize gallery
        loadGallery();
});
</script>

<?php get_footer(); ?>