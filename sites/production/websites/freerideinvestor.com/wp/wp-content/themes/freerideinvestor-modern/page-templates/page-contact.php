<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 * Description: A fully redesigned Contact page template with enhanced design, layout, and functionality, including Discord integration.
 */

get_header();
?>

<main id="main-content" class="site-main">
  
  <!-- Hero Section -->
  <section class="hero blog-hero" aria-labelledby="contact-hero-heading">
    <div class="hero-content">
      <h1 id="contact-hero-heading" class="hero-title"><?php esc_html_e('Get in Touch with Us', 'simplifiedtradingtheme'); ?></h1>
      <p class="hero-description">
        <?php esc_html_e('We would love to hear from you. Whether you have a question about features, trials, pricing, need a demo, or anything else, our team is ready to answer all your questions.', 'simplifiedtradingtheme'); ?>
      </p>
    </div>
  </section>

  <div class="container">
    <div class="content-area">
      <div class="main-content">
        
        <!-- Contact Page Styles -->
        <style>
          /* Contact Information Section */
          .contact-info-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
          }

          .contact-info-item,
          .discord-section {
            background: var(--color-dark-grey, #1a1a1a);
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
          }

          .contact-info-item:hover,
          .discord-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
          }

          .contact-info-item h3,
          .discord-section h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--color-accent, #007bff);
          }

          .contact-info-item p,
          .discord-section p {
            font-size: 1rem;
            color: var(--color-text-muted, #ccc);
            margin-bottom: 1rem;
          }

          .contact-info-item a {
            color: var(--color-accent, #007bff);
            text-decoration: none;
            font-weight: bold;
          }

          .contact-info-item a:hover {
            text-decoration: underline;
          }

          .discord-section {
            background: linear-gradient(135deg, #5865F2 0%, #4752C4 100%);
            color: #ffffff;
          }

          .discord-section h3,
          .discord-section p {
            color: #ffffff;
          }

          .discord-section a {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 700;
            transition: background 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
          }

          .discord-section a:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
          }

          /* Contact Form Section */
          .contact-form-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3rem;
            margin: 3rem 0;
            align-items: start;
          }

          .contact-form-container {
            background: var(--color-dark-grey, #1a1a1a);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          }

          .contact-form-container h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--color-accent, #007bff);
          }

          .contact-form-container p {
            font-size: 1rem;
            margin-bottom: 1.5rem;
            color: var(--color-text-muted, #ccc);
          }

          .contact-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
          }

          .form-group {
            display: flex;
            flex-direction: column;
          }

          .form-group label {
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--color-text-base, #fff);
          }

          .form-group input,
          .form-group textarea {
            padding: 0.75rem 1rem;
            border: 1px solid var(--color-border, #333);
            border-radius: 4px;
            font-size: 1rem;
            background: var(--color-dark-grey, #1a1a1a);
            color: var(--color-text-base, #fff);
            transition: border-color 0.3s ease;
            font-family: inherit;
          }

          .form-group input:focus,
          .form-group textarea:focus {
            border-color: var(--color-accent, #007bff);
            outline: none;
          }

          .submit-button {
            padding: 0.75rem 1.5rem;
            background: var(--color-accent, #007bff);
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s ease;
            align-self: flex-start;
          }

          .submit-button:hover {
            background: var(--color-nav-hover-bg, #0056b3);
          }

          .contact-image {
            text-align: center;
          }

          .contact-image img {
            max-width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            object-fit: cover;
          }

          /* Success and Error Messages */
          .form-message {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            font-weight: 600;
            text-align: center;
          }

          .form-success {
            background-color: #d4edda;
            color: #155724;
          }

          .form-error {
            background-color: #f8d7da;
            color: #721c24;
          }

          /* Responsive Design */
          @media (max-width: 992px) {
            .contact-form-section {
              grid-template-columns: 1fr;
            }

            .contact-info-section {
              grid-template-columns: 1fr;
            }
          }
        </style>


        <!-- Contact Information and Discord Section -->
        <section class="contact-info-section" aria-labelledby="contact-info-heading">
    <!-- Email -->
    <div class="contact-info-item">
      <h3><?php esc_html_e('Email Us', 'simplifiedtradingtheme'); ?></h3>
      <p>
        <a href="mailto:support@freerideinvestor.com">support@freerideinvestor.com</a>
      </p>
    </div>

    <!-- Discord -->
    <div class="discord-section">
      <h3><?php esc_html_e('Join Our Discord', 'simplifiedtradingtheme'); ?></h3>
      <p><?php esc_html_e('Connect with our community on Discord for real-time support, discussions, and exclusive content.', 'simplifiedtradingtheme'); ?></p>
      <a href="https://discord.gg/D5cfvw5R79" target="_blank" rel="noopener noreferrer">
        <?php esc_html_e('Join Discord', 'simplifiedtradingtheme'); ?>
      </a>
    </div>
  </section>

        <!-- Contact Form Section -->
        <section class="contact-form-section" aria-labelledby="contact-form-heading">
          <!-- Contact Form -->
          <div class="contact-form-container">
            <h2 id="contact-form-heading"><?php esc_html_e('Send Us a Message', 'simplifiedtradingtheme'); ?></h2>
      <p><?php esc_html_e('Fill out the form below and we will get back to you shortly.', 'simplifiedtradingtheme'); ?></p>

      <!-- Display Success or Error Messages -->
      <?php
      if (isset($_GET['contact']) && $_GET['contact'] == 'success') {
        echo '<div class="form-message form-success">' . esc_html__('Thank you for contacting us! We will respond to you shortly.', 'simplifiedtradingtheme') . '</div>';
      }

      if (isset($_GET['contact']) && $_GET['contact'] == 'error') {
        echo '<div class="form-message form-error">' . esc_html__('There was an error submitting the form. Please try again.', 'simplifiedtradingtheme') . '</div>';
      }
      ?>

      <!-- Contact Form -->
      <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="contact-form" novalidate>
        <?php
        // Security fields for WordPress
        wp_nonce_field('custom_contact_form', 'custom_contact_nonce');
        ?>
        <input type="hidden" name="action" value="handle_custom_contact_form">

        <!-- Name Field -->
        <div class="form-group">
          <label for="contact-name"><?php esc_html_e('Name', 'simplifiedtradingtheme'); ?></label>
          <input type="text" id="contact-name" name="contact_name" required placeholder="<?php esc_attr_e('Your Name', 'simplifiedtradingtheme'); ?>">
        </div>

        <!-- Email Field -->
        <div class="form-group">
          <label for="contact-email"><?php esc_html_e('Email', 'simplifiedtradingtheme'); ?></label>
          <input type="email" id="contact-email" name="contact_email" required placeholder="<?php esc_attr_e('you@example.com', 'simplifiedtradingtheme'); ?>">
        </div>

        <!-- Subject Field -->
        <div class="form-group">
          <label for="contact-subject"><?php esc_html_e('Subject', 'simplifiedtradingtheme'); ?></label>
          <input type="text" id="contact-subject" name="contact_subject" required placeholder="<?php esc_attr_e('Subject', 'simplifiedtradingtheme'); ?>">
        </div>

        <!-- Message Field -->
        <div class="form-group">
          <label for="contact-message"><?php esc_html_e('Message', 'simplifiedtradingtheme'); ?></label>
          <textarea id="contact-message" name="contact_message" rows="5" required placeholder="<?php esc_attr_e('Your Message', 'simplifiedtradingtheme'); ?>"></textarea>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="submit-button"><?php esc_html_e('Send Message', 'simplifiedtradingtheme'); ?></button>
      </form>
    </div>

          <!-- Contact Image -->
          <div class="contact-image">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/contact-us.jpg" alt="<?php esc_attr_e('Contact Us', 'simplifiedtradingtheme'); ?>">
          </div>
        </section>

      </div>
    </div>
  </div>
</main>

<?php get_footer(); ?>
