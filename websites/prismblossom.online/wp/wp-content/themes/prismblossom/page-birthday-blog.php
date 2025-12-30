<?php
/**
 * Template Name: Birthday Blog Post
 * 
 * Blog post draft about Carmyn's birthday
 * 
 * @package PrismBlossom
 */

get_header();
?>

<style>
    /* Birthday Blog Post - Black and Gold Theme */
    body {
        background: #000000;
        min-height: 100vh;
    }

    .birthday-blog-section {
        padding: 120px 0 60px;
        min-height: 100vh;
        background: #000000;
    }

    .blog-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 40px;
    }

    .blog-header {
        text-align: center;
        margin-bottom: 50px;
        padding: 40px;
        background: rgba(0, 0, 0, 0.8);
        border: 3px solid #FFD700;
        border-radius: 20px;
        box-shadow: 0 0 30px rgba(255, 215, 0, 0.5);
    }

    .blog-title {
        color: #FFD700;
        text-shadow: 0 0 10px #FFD700, 0 0 20px #FFD700, 0 0 30px rgba(255, 215, 0, 0.8);
        font-size: 3.5rem;
        margin-bottom: 20px;
        animation: glow 2s ease-in-out infinite;
    }

    @keyframes glow {
        0%, 100% {
            text-shadow: 0 0 10px #FFD700, 0 0 20px #FFD700, 0 0 30px rgba(255, 215, 0, 0.8);
        }
        50% {
            text-shadow: 0 0 20px #FFD700, 0 0 40px #FFD700, 0 0 60px rgba(255, 215, 0, 0.8);
        }
    }

    .blog-date {
        color: #FFD700;
        text-shadow: 0 0 5px #FFD700;
        font-size: 1.2rem;
        opacity: 0.8;
    }

    .blog-content {
        background: rgba(0, 0, 0, 0.8);
        border: 2px solid #FFD700;
        border-radius: 15px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
    }

    .blog-content p {
        color: #FFD700;
        text-shadow: 0 0 3px rgba(255, 215, 0, 0.5);
        font-size: 1.2rem;
        line-height: 1.8;
        margin-bottom: 25px;
    }

    .blog-content h2 {
        color: #FFD700;
        text-shadow: 0 0 10px #FFD700;
        font-size: 2rem;
        margin-top: 40px;
        margin-bottom: 20px;
    }

    .blog-content h3 {
        color: #FFD700;
        text-shadow: 0 0 8px #FFD700;
        font-size: 1.5rem;
        margin-top: 30px;
        margin-bottom: 15px;
    }

    .image-placeholder {
        background: rgba(0, 0, 0, 0.6);
        border: 2px dashed #FFD700;
        border-radius: 10px;
        padding: 60px 20px;
        margin: 30px 0;
        text-align: center;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-placeholder p {
        color: #FFD700;
        text-shadow: 0 0 5px #FFD700;
        font-size: 1.1rem;
        opacity: 0.7;
    }

    .link-placeholder {
        color: #FFD700;
        text-shadow: 0 0 5px #FFD700;
        text-decoration: underline;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .link-placeholder:hover {
        text-shadow: 0 0 10px #FFD700;
        opacity: 0.8;
    }

    .celebration-emoji {
        font-size: 2rem;
        margin: 0 10px;
        filter: drop-shadow(0 0 5px #FFD700);
    }

    .blog-cta {
        background: rgba(0, 0, 0, 0.8);
        border: 2px solid #FFD700;
        border-radius: 15px;
        padding: 30px;
        margin-top: 40px;
        text-align: center;
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
    }

    .blog-cta a {
        display: inline-block;
        background: rgba(0, 0, 0, 0.6);
        border: 2px solid #FFD700;
        color: #FFD700;
        text-shadow: 0 0 5px #FFD700;
        box-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
        padding: 15px 30px;
        border-radius: 25px;
        text-decoration: none;
        font-size: 1.1rem;
        font-weight: bold;
        margin: 10px;
        transition: all 0.3s ease;
    }

    .blog-cta a:hover {
        background: rgba(255, 215, 0, 0.2);
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.8);
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .blog-container {
            padding: 20px;
        }

        .blog-title {
            font-size: 2.5rem;
        }

        .blog-content {
            padding: 25px;
        }
    }
</style>

<section class="birthday-blog-section">
    <div class="blog-container">
        <div class="blog-header">
            <h1 class="blog-title">
                <span class="celebration-emoji">🎉</span>
                Celebrating Carmyn's Birthday!
                <span class="celebration-emoji">🎂</span>
            </h1>
            <p class="blog-date">Published: [Date Placeholder]</p>
        </div>

        <div class="blog-content">
            <p>
                <span class="celebration-emoji">🎈</span>
                <strong>Happy Birthday, Carmyn!</strong>
                <span class="celebration-emoji">🎈</span>
            </p>

            <p>
                Today we're celebrating an amazing DJ, artist, and friend! Carmyn has been creating incredible music mixes, 
                sharing her passion for R&B, Dance, and Jazz with the world, and constantly learning and growing as an artist.
            </p>

            <!-- Image Placeholder 1 -->
            <div class="image-placeholder">
                <p>[Birthday Celebration Image 1]<br><small>Click to upload image</small></p>
            </div>

            <h2>🎵 A Year of Musical Growth</h2>

            <p>
                This past year has been incredible for Carmyn! From learning new DJ techniques to creating memorable mixes, 
                she's been on an amazing journey. Her dedication to improving her craft and sharing her love of music is truly inspiring.
            </p>

            <p>
                Whether you've been following along on <a href="#" class="link-placeholder">[YouTube Channel Link]</a> or 
                listening to her latest mixes, you know that Carmyn brings something special to every track she touches.
            </p>

            <!-- Image Placeholder 2 -->
            <div class="image-placeholder">
                <p>[DJ Performance Image]<br><small>Click to upload image</small></p>
            </div>

            <h2>🎊 Birthday Wishes & Celebrations</h2>

            <p>
                We want to make this birthday extra special! That's why we've created this entire birthday section on the website 
                with interactive features, a guestbook for messages, and fun activities.
            </p>

            <h3>🎁 How to Celebrate:</h3>

            <p>
                • <strong>Leave a Birthday Message:</strong> Visit the <a href="/guestbook" class="link-placeholder">Guestbook</a> 
                and share your birthday wishes for Carmyn!
            </p>

            <p>
                • <strong>Try the Birthday Fun Page:</strong> Check out the <a href="/birthday-fun" class="link-placeholder">Birthday Fun</a> 
                page for interactive games and activities!
            </p>

            <p>
                • <strong>Share the Celebration:</strong> Spread the word on social media using 
                <a href="#" class="link-placeholder">[Social Media Link]</a> and tag Carmyn!
            </p>

            <!-- Image Placeholder 3 -->
            <div class="image-placeholder">
                <p>[Birthday Party Image]<br><small>Click to upload image</small></p>
            </div>

            <h2>🌟 Looking Forward</h2>

            <p>
                As we celebrate today, we're also excited about what's coming next! Carmyn has big plans for the year ahead, 
                including new mixes, collaborations, and continued growth as an artist.
            </p>

            <p>
                Stay tuned for updates on <a href="#" class="link-placeholder">[Newsletter Link]</a> and follow along on 
                <a href="#" class="link-placeholder">[Social Media Link]</a> for the latest news and releases!
            </p>

            <p style="text-align: center; font-size: 1.5rem; margin-top: 40px;">
                <span class="celebration-emoji">🎉</span>
                <strong>Here's to another amazing year, Carmyn!</strong>
                <span class="celebration-emoji">🎉</span>
            </p>

            <p style="text-align: center; font-size: 1.3rem; margin-top: 20px;">
                <span class="celebration-emoji">🎂</span>
                <strong>Happy Birthday and Keep Making Great Music!</strong>
                <span class="celebration-emoji">🎂</span>
            </p>
        </div>

        <div class="blog-cta">
            <h3 style="color: #FFD700; text-shadow: 0 0 10px #FFD700; margin-bottom: 20px;">Join the Celebration!</h3>
            <a href="/guestbook">Leave a Birthday Message</a>
            <a href="/birthday-fun">Try Birthday Fun Activities</a>
            <a href="/carmyn">Visit Carmyn's Page</a>
        </div>
    </div>
</section>

<?php get_footer(); ?>







