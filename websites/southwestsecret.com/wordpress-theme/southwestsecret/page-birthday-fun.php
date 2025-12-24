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
        <h1 class="section-title">
            <span class="graffiti-sub">BIRTHDAY</span>
            <span class="bubble-sub">FUN</span>
        </h1>
        <p class="section-description">Click or tap the birthday cat for some fun!</p>

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

<style>
.birthday-fun-section {
    padding: 120px 0 60px;
    min-height: 100vh;
    text-align: center;
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
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-20px) scale(1.1); }
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
    0%, 90%, 100% { height: 20px; }
    95% { height: 2px; }
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
    background: #ff00ff;
    margin: 0 auto;
    border-radius: 4px 4px 0 0;
}

.hat-base {
    width: 50px;
    height: 15px;
    background: #00ffff;
    border-radius: 50%;
    margin-top: -5px;
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
    background: var(--primary-color);
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
    color: var(--text-light);
}

.click-counter span {
    color: var(--primary-color);
    font-weight: bold;
    font-size: 1.5rem;
}

.fun-messages {
    margin-top: 30px;
    min-height: 50px;
}

.fun-message {
    background: rgba(255, 0, 255, 0.3);
    border: 2px solid var(--primary-color);
    border-radius: 10px;
    padding: 15px;
    margin: 10px auto;
    max-width: 400px;
    color: var(--text-light);
    animation: messagePop 0.5s ease;
}

@keyframes messagePop {
    0% { transform: scale(0); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
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
        const colors = ['#ff00ff', '#00ffff', '#ffff00', '#ff00ff', '#00ff00'];
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
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
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
});
</script>

<?php get_footer(); ?>

