// Interactive elements for DaDudeKC Website
class InteractiveElements {
    constructor() {
        this.init();
    }

    init() {
        this.setupEasterEggs();
        this.setupMiniGame();
        this.setupHiddenMessages();
        this.setupInteractiveLogo();
    }

    setupEasterEggs() {
        // Add Easter eggs to the website
        let konamiCode = [];
        const konamiSequence = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'KeyB', 'KeyA'];
        
        document.addEventListener('keydown', (e) => {
            konamiCode.push(e.code);
            if (konamiCode.length > konamiSequence.length) {
                konamiCode.shift();
            }
            
            if (konamiCode.join(',') === konamiSequence.join(',')) {
                this.triggerKonamiEasterEgg();
                konamiCode = [];
            }
        });

        // Hidden clickable areas
        this.addHiddenClickableAreas();
    }

    setupMiniGame() {
        // Simple mini-game in the footer
        const footer = document.querySelector('footer');
        if (footer) {
            const gameButton = document.createElement('button');
            gameButton.textContent = '🎮 Play Mini-Game';
            gameButton.style.cssText = `
                background: var(--primary-color);
                color: var(--text-dark);
                border: none;
                padding: 0.5rem 1rem;
                border-radius: 5px;
                cursor: pointer;
                margin-top: 1rem;
                font-size: 0.9rem;
            `;
            
            gameButton.addEventListener('click', () => {
                this.startMiniGame();
            });
            
            footer.appendChild(gameButton);
        }
    }

    setupHiddenMessages() {
        // Add hidden messages that appear on specific actions
        const mysterySection = document.querySelector('.mystery-section');
        if (mysterySection) {
            const hiddenMessage = document.createElement('div');
            hiddenMessage.className = 'hidden-message';
            hiddenMessage.style.cssText = `
                opacity: 0;
                transition: opacity 0.5s ease;
                text-align: center;
                margin-top: 2rem;
                font-style: italic;
                color: var(--primary-color);
            `;
            hiddenMessage.textContent = 'The future is written in code...';
            
            mysterySection.appendChild(hiddenMessage);
            
            // Show message on hover
            mysterySection.addEventListener('mouseenter', () => {
                hiddenMessage.style.opacity = '1';
            });
            
            mysterySection.addEventListener('mouseleave', () => {
                hiddenMessage.style.opacity = '0';
            });
        }
    }

    setupInteractiveLogo() {
        // Make the logo interactive
        const logo = document.querySelector('.animated-logo');
        if (logo) {
            logo.style.cursor = 'pointer';
            logo.addEventListener('click', () => {
                this.triggerLogoAnimation();
            });
            
            logo.addEventListener('mouseenter', () => {
                logo.style.animation = 'pulse 0.5s ease-in-out';
            });
            
            logo.addEventListener('mouseleave', () => {
                logo.style.animation = 'pulse 2s infinite';
            });
        }
    }

    addHiddenClickableAreas() {
        // Add invisible clickable areas that reveal secrets
        const secretAreas = [
            { x: 50, y: 50, message: 'You found a secret area!' },
            { x: 90, y: 20, message: 'Another hidden message!' },
            { x: 10, y: 80, message: 'Keep exploring...' }
        ];

        secretAreas.forEach(area => {
            const secretDiv = document.createElement('div');
            secretDiv.style.cssText = `
                position: fixed;
                left: ${area.x}%;
                top: ${area.y}%;
                width: 20px;
                height: 20px;
                cursor: pointer;
                z-index: 1000;
            `;
            
            secretDiv.addEventListener('click', () => {
                this.showSecretMessage(area.message);
            });
            
            document.body.appendChild(secretDiv);
        });
    }

    triggerKonamiEasterEgg() {
        // Konami code easter egg
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: var(--primary-color);
            color: var(--text-dark);
            padding: 2rem;
            border-radius: 10px;
            z-index: 10000;
            text-align: center;
            font-size: 1.2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        `;
        notification.innerHTML = `
            <h3>🎉 Konami Code Activated! 🎉</h3>
            <p>You've unlocked the secret mode!</p>
            <button onclick="this.parentElement.remove()" style="
                background: var(--accent-color);
                color: var(--text-dark);
                border: none;
                padding: 0.5rem 1rem;
                border-radius: 5px;
                cursor: pointer;
                margin-top: 1rem;
            ">Close</button>
        `;
        
        document.body.appendChild(notification);
        
        // Add rainbow effect to the page
        document.body.style.animation = 'rainbow 2s ease-in-out infinite';
        
        setTimeout(() => {
            document.body.style.animation = '';
        }, 10000);
    }

    startMiniGame() {
        // Simple click-the-target mini-game
        const gameContainer = document.createElement('div');
        gameContainer.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 10000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
        `;
        
        let score = 0;
        let timeLeft = 30;
        
        gameContainer.innerHTML = `
            <h2>🎯 Click the Targets!</h2>
            <p>Score: <span id="score">0</span></p>
            <p>Time: <span id="time">30</span>s</p>
            <div id="game-area" style="
                width: 600px;
                height: 400px;
                border: 2px solid white;
                position: relative;
                background: rgba(255, 255, 255, 0.1);
            "></div>
            <button onclick="this.parentElement.remove()" style="
                background: var(--primary-color);
                color: var(--text-dark);
                border: none;
                padding: 0.5rem 1rem;
                border-radius: 5px;
                cursor: pointer;
                margin-top: 1rem;
            ">Close Game</button>
        `;
        
        document.body.appendChild(gameContainer);
        
        const gameArea = gameContainer.querySelector('#game-area');
        const scoreDisplay = gameContainer.querySelector('#score');
        const timeDisplay = gameContainer.querySelector('#time');
        
        // Game timer
        const timer = setInterval(() => {
            timeLeft--;
            timeDisplay.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                alert(`Game Over! Final Score: ${score}`);
                gameContainer.remove();
            }
        }, 1000);
        
        // Spawn targets
        const spawnTarget = () => {
            if (timeLeft <= 0) return;
            
            const target = document.createElement('div');
            target.style.cssText = `
                position: absolute;
                width: 30px;
                height: 30px;
                background: var(--secondary-color);
                border-radius: 50%;
                cursor: pointer;
                left: ${Math.random() * 570}px;
                top: ${Math.random() * 370}px;
                transition: all 0.1s ease;
            `;
            
            target.addEventListener('click', () => {
                score++;
                scoreDisplay.textContent = score;
                target.remove();
                setTimeout(spawnTarget, 500);
            });
            
            gameArea.appendChild(target);
            
            // Target disappears after 2 seconds
            setTimeout(() => {
                if (target.parentElement) {
                    target.remove();
                    setTimeout(spawnTarget, 500);
                }
            }, 2000);
        };
        
        spawnTarget();
    }

    showSecretMessage(message) {
        if (window.websiteUtils) {
            window.websiteUtils.showNotification(message, 'info');
        }
    }

    triggerLogoAnimation() {
        const logo = document.querySelector('.animated-logo');
        if (logo) {
            logo.style.animation = 'shake 0.5s ease-in-out';
            setTimeout(() => {
                logo.style.animation = 'pulse 2s infinite';
            }, 500);
        }
    }
}

// Add rainbow animation CSS
const rainbowStyle = document.createElement('style');
rainbowStyle.textContent = `
    @keyframes rainbow {
        0% { filter: hue-rotate(0deg); }
        100% { filter: hue-rotate(360deg); }
    }
`;
document.head.appendChild(rainbowStyle);

// Initialize interactive elements when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new InteractiveElements();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = InteractiveElements;
}

