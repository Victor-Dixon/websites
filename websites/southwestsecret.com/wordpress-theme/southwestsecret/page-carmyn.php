<?php
/**
 * Template Name: Carmyn Page
 * Template for Carmyn's dedicated showcase page
 * 
 * @package SouthWestSecret
 */

get_header();
?>

<style>
    /* Carmyn's Solid Pink Background - No Water Theme */
    body {
        background: #ff00ff;
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }

    .carmyn-section {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 3px solid #ffffff;
        box-shadow: 0 0 30px rgba(255, 255, 255, 0.3), inset 0 0 20px rgba(255, 255, 255, 0.1);
        position: relative;
        z-index: 1;
    }

    .carmyn-title {
        color: #ffffff;
        text-shadow: 0 0 10px #ffffff, 0 0 20px #ffffff, 0 0 30px rgba(255, 255, 255, 0.8);
        animation: carmyn-glow 2s ease-in-out infinite;
    }

    @keyframes carmyn-glow {
        0%, 100% {
            text-shadow: 0 0 10px #ffffff, 0 0 20px #ffffff, 0 0 30px rgba(255, 255, 255, 0.8);
        }
        50% {
            text-shadow: 0 0 20px #ffffff, 0 0 40px #ffffff, 0 0 60px rgba(255, 255, 255, 0.8);
        }
    }

    .carmyn-text {
        color: #ffffff;
        text-shadow: 0 0 5px #ffffff, 0 0 10px rgba(255, 255, 255, 0.5);
    }

    .carmyn-music-badge {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid #ffffff;
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 20px;
        display: inline-block;
        margin: 5px;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        text-shadow: 0 0 5px #ffffff;
    }

    .carmyn-learning-badge {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid #ffffff;
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 20px;
        display: inline-block;
        margin: 5px;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        text-shadow: 0 0 5px #ffffff;
    }

    /* Ensure content is above curls */
    header, main, footer {
        position: relative;
        z-index: 1;
    }

    /* ============================================
       BIRTHDAY FUN SECTION STYLES
       ============================================ */
    .birthday-fun-section {
        padding: 60px 0;
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
        background: #ffffff;
        margin: 0 auto;
        border-radius: 4px 4px 0 0;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
    }

    .hat-base {
        width: 50px;
        height: 15px;
        background: #ffffff;
        border-radius: 50%;
        margin-top: -5px;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
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
        background: #ffffff;
        box-shadow: 0 0 5px rgba(255, 255, 255, 0.8);
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
    }

    .fun-messages {
        margin-top: 30px;
        min-height: 50px;
    }

    .fun-message {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid #ffffff;
        border-radius: 10px;
        padding: 15px;
        margin: 10px auto;
        max-width: 400px;
        color: #ffffff;
        text-shadow: 0 0 5px #ffffff;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        animation: messagePop 0.5s ease;
    }

    @keyframes messagePop {
        0% { transform: scale(0); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    /* ============================================
       GUESTBOOK SECTION STYLES
       ============================================ */
    .guestbook-section {
        padding: 60px 0;
    }

    .guestbook-form-container {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 3px solid #ffffff;
        box-shadow: 0 0 30px rgba(255, 255, 255, 0.3), inset 0 0 20px rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 40px;
        margin-bottom: 60px;
    }

    .guestbook-form .form-group {
        margin-bottom: 25px;
    }

    .guestbook-form input[type="text"]:focus,
    .guestbook-form textarea:focus {
        outline: none;
        border-color: #ffffff;
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
        background: rgba(255, 255, 255, 0.25);
    }

    .guestbook-form textarea {
        resize: vertical;
        min-height: 120px;
    }

    .form-message {
        margin-top: 20px;
        padding: 15px;
        border-radius: 5px;
        display: none;
        border: 2px solid;
    }

    .form-message.success {
        background: rgba(0, 255, 0, 0.2);
        border-color: #ffffff;
        color: #ffffff;
        text-shadow: 0 0 5px #ffffff;
        display: block;
    }

    .form-message.error {
        background: rgba(255, 0, 0, 0.2);
        border-color: #ffffff;
        color: #ffffff;
        text-shadow: 0 0 5px #ffffff;
        display: block;
    }

    .guestbook-messages {
        margin-top: 40px;
    }

    .messages-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
    }

    .message-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 2px solid #ffffff;
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        padding: 20px;
        transition: all 0.3s ease;
    }

    .message-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 30px rgba(255, 255, 255, 0.4);
    }

    .message-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    }

    .message-content {
        line-height: 1.6;
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

        .guestbook-form-container {
            padding: 25px;
        }
        
        .messages-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- No water theme decorations - clean solid background -->

<main>
    <!-- Hero Section -->
    <section id="home" class="hero carmyn-section">
        <div class="container">
            <h1 class="graffiti-title carmyn-title">
                <span class="graffiti">CARMYN</span>
            </h1>
            <p class="tagline carmyn-text">Featured DJ</p>
        </div>
    </section>

    <!-- Music Section -->
    <section id="music" class="screw-tapes-library carmyn-section">
        <div class="container">
            <h2 class="section-title carmyn-title">
                <span class="graffiti-sub">CARMYN</span>
                <span class="bubble-sub">MUSIC</span>
            </h2>
            <div class="carmyn-text" style="text-align: center; margin-top: 30px;">
                <p style="font-size: 1.3em; margin-bottom: 20px;">Music Styles</p>
                <div style="margin: 20px 0;">
                    <span class="carmyn-music-badge">🎵 R&B</span>
                    <span class="carmyn-music-badge">💃 Dance</span>
                    <span class="carmyn-music-badge">🎷 Jazz</span>
                </div>
                <span class="carmyn-learning-badge">🌟 Still Learning - Help me get better!</span>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about carmyn-section">
        <div class="container">
            <h2 class="section-title carmyn-title">
                <span class="graffiti-sub">ABOUT</span>
                <span class="bubble-sub">CARMYN</span>
            </h2>
            <div class="about-content">
                <div class="carmyn-text" style="text-align: center; max-width: 800px; margin: 0 auto;">
                    <p style="font-size: 1.2em; margin-bottom: 20px;">
                        Welcome to <strong style="color: #ffffff; text-shadow: 0 0 10px #ffffff;">Carmyn's</strong> space on SouthWest Secret!
                    </p>
                    <p style="font-size: 1.1em; margin-bottom: 20px;">
                        I'm a DJ specializing in <strong style="color: #ffffff; text-shadow: 0 0 5px #ffffff;">R&B</strong>, <strong style="color: #ffffff; text-shadow: 0 0 5px #ffffff;">Dance</strong>, and <strong style="color: #ffffff; text-shadow: 0 0 5px #ffffff;">Jazz</strong> music. 
                        I'm still learning and growing as an artist, so I'd love your help getting better!
                    </p>
                    <p style="font-size: 1.1em; margin-top: 30px; color: #ffffff; text-shadow: 0 0 5px #ffffff;">
                        Check back soon for mixes and updates!
                    </p>
                </div>
                <div class="website-link" style="margin-top: 30px; text-align: center;">
                    <a href="<?php echo home_url(); ?>" class="btn-subscribe" style="display: inline-block; background: rgba(255, 255, 255, 0.2); border: 2px solid #ffffff; color: #ffffff; text-shadow: 0 0 5px #ffffff; box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);">
                        Visit SouthWest Secret
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact carmyn-section">
        <div class="container">
            <h2 class="section-title carmyn-title">
                <span class="graffiti-sub">STAY</span>
                <span class="bubble-sub">CONNECTED</span>
            </h2>
            <div class="contact-content">
                <p class="carmyn-text">Subscribe to SouthWest Secret for the latest mixes!</p>
                <a href="https://www.youtube.com/@SouthWestSecret" target="_blank" class="btn-subscribe" style="background: rgba(255, 255, 255, 0.2); border: 2px solid #ffffff; color: #ffffff; text-shadow: 0 0 5px #ffffff; box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);">
                    Subscribe Now
                </a>
            </div>
        </div>
    </section>

    <!-- Birthday Fun Section -->
    <section id="birthday-fun" class="birthday-fun-section carmyn-section">
        <div class="container">
            <h2 class="section-title carmyn-title">
                <span class="graffiti-sub">BIRTHDAY</span>
                <span class="bubble-sub">FUN</span>
            </h2>
            <p class="carmyn-text" style="margin-bottom: 30px;">Click or tap the birthday cat for some fun!</p>

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
                <p class="carmyn-text">Clicks: <span id="click-count" style="color: #ffffff; text-shadow: 0 0 10px #ffffff;">0</span></p>
            </div>

            <!-- Fun Messages -->
            <div id="fun-messages" class="fun-messages"></div>
        </div>
    </section>

    <!-- Guestbook Section -->
    <section id="guestbook" class="guestbook-section carmyn-section">
        <div class="container">
            <h2 class="section-title carmyn-title">
                <span class="graffiti-sub">BIRTHDAY</span>
                <span class="bubble-sub">GUESTBOOK</span>
            </h2>
            <p class="carmyn-text" style="margin-bottom: 30px;">Leave a birthday message! Your message will appear after approval.</p>

            <!-- Guestbook Form -->
            <div class="guestbook-form-container">
                <form id="guestbook-form" class="guestbook-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('guestbook_submit', 'guestbook_nonce'); ?>
                    <input type="hidden" name="action" value="submit_guestbook_entry">
                    
                    <div class="form-group">
                        <label for="guest_name" class="carmyn-text">Your Name *</label>
                        <input type="text" id="guest_name" name="guest_name" required maxlength="100" placeholder="Enter your name" style="background: rgba(255, 255, 255, 0.2); border: 2px solid #ffffff; color: #ffffff; padding: 12px; border-radius: 5px; width: 100%;">
                    </div>
                    
                    <div class="form-group">
                        <label for="guest_message" class="carmyn-text">Birthday Message *</label>
                        <textarea id="guest_message" name="guest_message" required maxlength="500" rows="5" placeholder="Write your birthday message here..." style="background: rgba(255, 255, 255, 0.2); border: 2px solid #ffffff; color: #ffffff; padding: 12px; border-radius: 5px; width: 100%; font-family: inherit;"></textarea>
                        <small class="char-count carmyn-text" style="display: block; text-align: right; margin-top: 5px; font-size: 0.85rem;">0 / 500 characters</small>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn-subscribe" style="background: rgba(255, 255, 255, 0.2); border: 2px solid #ffffff; color: #ffffff; text-shadow: 0 0 5px #ffffff; box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); padding: 12px 30px; border-radius: 25px; cursor: pointer; font-size: 16px; font-weight: bold;">Submit Message</button>
                    </div>
                    
                    <div id="form-message" class="form-message"></div>
                </form>
            </div>

            <!-- Approved Messages Display -->
            <div class="guestbook-messages">
                <h3 class="messages-title carmyn-title" style="margin-top: 40px; margin-bottom: 30px;">
                    <span class="graffiti-sub">BIRTHDAY</span>
                    <span class="bubble-sub">MESSAGES</span>
                </h3>
                <div id="guestbook-entries" class="messages-grid">
                    <?php
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'guestbook_entries';
                    
                    $entries = $wpdb->get_results(
                        $wpdb->prepare(
                            "SELECT * FROM $table_name WHERE status = %s ORDER BY created_at DESC LIMIT 50",
                            'approved'
                        )
                    );
                    
                    if ($entries) {
                        foreach ($entries as $entry) {
                            echo '<div class="message-card">';
                            echo '<div class="message-header">';
                            echo '<span class="message-name carmyn-text" style="font-weight: bold; font-size: 1.1rem;">' . esc_html($entry->guest_name) . '</span>';
                            echo '<span class="message-date carmyn-text" style="font-size: 0.9rem; opacity: 0.8;">' . date('M j, Y', strtotime($entry->created_at)) . '</span>';
                            echo '</div>';
                            echo '<div class="message-content carmyn-text" style="line-height: 1.6; margin-top: 10px;">' . esc_html($entry->message) . '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p class="no-messages carmyn-text" style="text-align: center; padding: 40px; background: rgba(255, 255, 255, 0.1); border-radius: 10px; border: 2px solid rgba(255, 255, 255, 0.3);">No messages yet. Be the first to leave a birthday wish!</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Music Player Controls -->
<div id="musicControls" style="position:fixed; bottom:20px; left:20px; z-index:9999;">
    <button id="playBtn" style="background: rgba(255, 255, 255, 0.2); border: 2px solid #ffffff; color: #ffffff; padding: 10px 20px; border-radius: 25px; cursor: pointer; text-shadow: 0 0 5px #ffffff; box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); font-size: 16px; font-weight: bold;">Play Lofi Music</button>
</div>

<!-- Hidden YouTube Player -->
<div id="ytplayer" style="width:1px; height:1px; position:absolute; left:-9999px;"></div>

<script>
    var tag = document.createElement("script");
    tag.src = "https://www.youtube.com/iframe_api";
    document.head.appendChild(tag);

    var player;
    function onYouTubeIframeAPIReady() {
        player = new YT.Player("ytplayer", {
            videoId: "sF80I-TQiW0",
            playerVars: { autoplay: 1, mute: 1, loop: 1, playlist: "sF80I-TQiW0" }
        });
    }

    document.addEventListener("DOMContentLoaded", function(){
        document.getElementById("playBtn").onclick = function(){
            player.unMute();
            player.playVideo();
            this.textContent = "Pause Music";
            this.onclick = function(){
                if(player.getPlayerState() === 1){
                    player.pauseVideo();
                    this.textContent = "Play Lofi Music";
                } else {
                    player.playVideo();
                    player.unMute();
                    this.textContent = "Pause Music";
                }
            };
        };
    });
</script>

<!-- Birthday Fun Script -->
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
        const colors = ['#ffffff', '#ff00ff', '#ff1493', '#ffffff', '#ff00ff'];
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
        try {
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
        } catch (e) {
            console.log('Sound not available');
        }
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
            
            cat.classList.add('clicked');
            setTimeout(() => cat.classList.remove('clicked'), 500);
            
            createConfetti();
            playSound();
            showFunMessage();
        });
        
        cat.addEventListener('touchstart', function(e) {
            e.preventDefault();
            cat.click();
        });
    }
});
</script>

<!-- Guestbook Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('guestbook-form');
    const messageDiv = document.getElementById('form-message');
    const charCount = document.querySelector('.char-count');
    const textarea = document.getElementById('guest_message');
    
    if (textarea && charCount) {
        textarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count + ' / 500 characters';
        });
    }
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            
            fetch('<?php echo esc_url(admin_url('admin-post.php')); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.includes('success')) {
                    messageDiv.className = 'form-message success';
                    messageDiv.textContent = 'Thank you! Your message has been submitted and will appear after approval.';
                    form.reset();
                    charCount.textContent = '0 / 500 characters';
                    
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    messageDiv.className = 'form-message error';
                    messageDiv.textContent = 'There was an error submitting your message. Please try again.';
                }
            })
            .catch(error => {
                messageDiv.className = 'form-message error';
                messageDiv.textContent = 'Network error. Please try again.';
            });
        });
    }
});
</script>

<?php get_footer(); ?>

