<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 *
 * The template for displaying contact page
 *
 * @package FreeRideInvestor_V2
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="content-area">
            <div class="main-content">

                <?php while (have_posts()) : the_post(); ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class('contact-page'); ?>>
                        <header class="entry-header">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                        </header>

                        <div class="entry-content">
                            <div class="contact-grid">
                                <!-- Contact Information -->
                                <section class="contact-info">
                                    <h2>Get In Touch</h2>

                                    <div class="contact-methods">
                                        <div class="contact-method">
                                            <h3>📧 Email</h3>
                                            <p><a href="mailto:support@freerideinvestor.com">support@freerideinvestor.com</a></p>
                                            <p>We respond within 24 hours</p>
                                        </div>

                                        <div class="contact-method">
                                            <h3>💬 Live Chat</h3>
                                            <p>Available 9 AM - 6 PM EST</p>
                                            <p>Click the chat button below</p>
                                        </div>

                                        <div class="contact-method">
                                            <h3>📞 Phone</h3>
                                            <p>Coming Soon</p>
                                            <p>Priority support for premium members</p>
                                        </div>
                                    </div>

                                    <div class="office-hours">
                                        <h3>🕒 Office Hours</h3>
                                        <ul>
                                            <li>Monday - Friday: 9:00 AM - 6:00 PM EST</li>
                                            <li>Saturday: 10:00 AM - 4:00 PM EST</li>
                                            <li>Sunday: Closed</li>
                                        </ul>
                                    </div>
                                </section>

                                <!-- Contact Form -->
                                <section class="contact-form-section">
                                    <h2>Send us a Message</h2>

                                    <?php if (get_field('contact_form_shortcode')) : ?>
                                        <?php echo do_shortcode(get_field('contact_form_shortcode')); ?>
                                    <?php else : ?>
                                        <form class="contact-form" method="post">
                                            <div class="form-group">
                                                <label for="name">Name *</label>
                                                <input type="text" id="name" name="name" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="email">Email *</label>
                                                <input type="email" id="email" name="email" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="subject">Subject</label>
                                                <select id="subject" name="subject">
                                                    <option value="general">General Inquiry</option>
                                                    <option value="support">Technical Support</option>
                                                    <option value="billing">Billing Question</option>
                                                    <option value="partnership">Partnership</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="message">Message *</label>
                                                <textarea id="message" name="message" rows="5" required></textarea>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Send Message</button>
                                        </form>
                                    <?php endif; ?>
                                </section>
                            </div>

                            <!-- Main Content -->
                            <div class="contact-content">
                                <?php the_content(); ?>
                            </div>
                        </div>
                    </article>

                <?php endwhile; ?>

            </div>

            <?php get_sidebar(); ?>
        </div>
    </div>
</main>

<style>
.contact-page .contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin-bottom: 3rem;
}

.contact-info h2,
.contact-form-section h2 {
    font-size: 1.8rem;
    margin-bottom: 2rem;
    color: #2c3e50;
}

.contact-methods {
    display: grid;
    gap: 2rem;
    margin-bottom: 2rem;
}

.contact-method {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.contact-method h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.contact-method p {
    margin-bottom: 0.5rem;
    color: #666;
}

.contact-method a {
    color: #667eea;
    text-decoration: none;
}

.contact-method a:hover {
    text-decoration: underline;
}

.office-hours {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 8px;
}

.office-hours h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.office-hours ul {
    list-style: none;
    padding: 0;
}

.office-hours li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.office-hours li:last-child {
    border-bottom: none;
}

.contact-form {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #2c3e50;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.btn-primary {
    background: #48bb78;
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s ease;
}

.btn-primary:hover {
    background: #38a169;
}

@media (max-width: 768px) {
    .contact-page .contact-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
}
</style>

<?php
get_footer();
?>