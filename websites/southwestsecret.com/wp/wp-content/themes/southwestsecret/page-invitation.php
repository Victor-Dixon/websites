<?php
/**
 * Template Name: Birthday Invitation
 * 
 * Birthday invitation page
 * 
 * @package SouthWestSecret
 */

get_header();
?>

<style>
    /* Invitation Page Styles */
    body {
        background: #ff00ff;
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }

    .invitation-section {
        padding: 120px 0 60px;
        min-height: 100vh;
        text-align: center;
    }

    .invitation-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px;
    }

    .invitation-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 3px solid #ffffff;
        box-shadow: 0 0 30px rgba(255, 255, 255, 0.3), inset 0 0 20px rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 60px 40px;
        margin-bottom: 40px;
    }

    .invitation-title {
        color: #ffffff;
        text-shadow: 0 0 10px #ffffff, 0 0 20px #ffffff, 0 0 30px rgba(255, 255, 255, 0.8);
        font-size: 3rem;
        margin-bottom: 30px;
        animation: glow 2s ease-in-out infinite;
    }

    @keyframes glow {
        0%, 100% {
            text-shadow: 0 0 10px #ffffff, 0 0 20px #ffffff, 0 0 30px rgba(255, 255, 255, 0.8);
        }
        50% {
            text-shadow: 0 0 20px #ffffff, 0 0 40px #ffffff, 0 0 60px rgba(255, 255, 255, 0.8);
        }
    }

    .invitation-text {
        color: #ffffff;
        text-shadow: 0 0 5px #ffffff, 0 0 10px rgba(255, 255, 255, 0.5);
        font-size: 1.3rem;
        line-height: 1.8;
        margin-bottom: 30px;
    }

    .invitation-details {
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid #ffffff;
        border-radius: 15px;
        padding: 30px;
        margin: 30px 0;
    }

    .invitation-details h3 {
        color: #ffffff;
        text-shadow: 0 0 10px #ffffff;
        font-size: 1.8rem;
        margin-bottom: 20px;
    }

    .invitation-details p {
        color: #ffffff;
        text-shadow: 0 0 5px #ffffff;
        font-size: 1.2rem;
        margin: 10px 0;
    }

    .invitation-buttons {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 40px;
    }

    .btn-invitation {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid #ffffff;
        color: #ffffff;
        text-shadow: 0 0 5px #ffffff;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        padding: 15px 30px;
        border-radius: 25px;
        cursor: pointer;
        font-size: 1.1rem;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .btn-invitation:hover {
        background: rgba(255, 255, 255, 0.3);
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
    }

    .invitation-emoji {
        font-size: 4rem;
        margin: 20px 0;
        animation: bounce 2s ease-in-out infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }

    @media (max-width: 768px) {
        .invitation-container {
            padding: 20px;
        }

        .invitation-card {
            padding: 40px 20px;
        }

        .invitation-title {
            font-size: 2rem;
        }

        .invitation-text {
            font-size: 1.1rem;
        }

        .invitation-buttons {
            flex-direction: column;
        }

        .btn-invitation {
            width: 100%;
        }
    }
</style>

<section class="invitation-section">
    <div class="container">
        <div class="invitation-container">
            <div class="invitation-card">
                <div class="invitation-emoji">🎉</div>
                <h1 class="invitation-title">You're Invited!</h1>
                <p class="invitation-text">
                    Join us for an amazing birthday celebration! 
                    We can't wait to celebrate with you.
                </p>

                <div class="invitation-details">
                    <h3>Event Details</h3>
                    <p><strong>Date:</strong> [Edit in WordPress]</p>
                    <p><strong>Time:</strong> [Edit in WordPress]</p>
                    <p><strong>Location:</strong> [Edit in WordPress]</p>
                    <p><strong>RSVP:</strong> [Edit in WordPress]</p>
                </div>

                <div class="invitation-buttons">
                    <a href="#guestbook" class="btn-invitation">Leave a Message</a>
                    <a href="#birthday-fun" class="btn-invitation">Birthday Fun</a>
                    <a href="/carmyn" class="btn-invitation">Visit My Page</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>

