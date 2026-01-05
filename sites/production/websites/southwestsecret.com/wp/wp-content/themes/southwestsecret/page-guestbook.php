<?php
/**
 * Template Name: Guestbook
 * 
 * Guestbook page template for birthday messages
 * 
 * @package SouthWestSecret
 */

get_header();
?>

<section class="guestbook-section">
    <div class="container">
        <h1 class="section-title">
            <span class="graffiti-sub">BIRTHDAY</span>
            <span class="bubble-sub">GUESTBOOK</span>
        </h1>
        <p class="section-description">Leave a birthday message! Your message will appear after approval.</p>

        <!-- Guestbook Form -->
        <div class="guestbook-form-container">
            <form id="guestbook-form" class="guestbook-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('guestbook_submit', 'guestbook_nonce'); ?>
                <input type="hidden" name="action" value="submit_guestbook_entry">
                
                <div class="form-group">
                    <label for="guest_name">Your Name *</label>
                    <input type="text" id="guest_name" name="guest_name" required maxlength="100" placeholder="Enter your name">
                </div>
                
                <div class="form-group">
                    <label for="guest_message">Birthday Message *</label>
                    <textarea id="guest_message" name="guest_message" required maxlength="500" rows="5" placeholder="Write your birthday message here..."></textarea>
                    <small class="char-count">0 / 500 characters</small>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-primary">Submit Message</button>
                </div>
                
                <div id="form-message" class="form-message"></div>
            </form>
        </div>

        <!-- Approved Messages Display -->
        <div class="guestbook-messages">
            <h2 class="messages-title">Birthday Messages</h2>
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
                        echo '<span class="message-name">' . esc_html($entry->guest_name) . '</span>';
                        echo '<span class="message-date">' . date('M j, Y', strtotime($entry->created_at)) . '</span>';
                        echo '</div>';
                        echo '<div class="message-content">' . esc_html($entry->message) . '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="no-messages">No messages yet. Be the first to leave a birthday wish!</p>';
                }
                ?>
            </div>
        </div>
    </div>
</section>

<style>
.guestbook-section {
    padding: 120px 0 60px;
    min-height: 100vh;
}

.guestbook-form-container {
    background: rgba(0, 0, 0, 0.6);
    border: 2px solid var(--primary-color);
    border-radius: 15px;
    padding: 40px;
    margin-bottom: 60px;
    box-shadow: 0 0 20px rgba(255, 0, 255, 0.3);
}

.guestbook-form .form-group {
    margin-bottom: 25px;
}

.guestbook-form label {
    display: block;
    color: var(--text-light);
    margin-bottom: 8px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.guestbook-form input[type="text"],
.guestbook-form textarea {
    width: 100%;
    padding: 12px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--secondary-color);
    border-radius: 5px;
    color: var(--text-light);
    font-family: inherit;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.guestbook-form input[type="text"]:focus,
.guestbook-form textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 10px rgba(255, 0, 255, 0.5);
    background: rgba(255, 255, 255, 0.15);
}

.guestbook-form textarea {
    resize: vertical;
    min-height: 120px;
}

.char-count {
    display: block;
    color: var(--text-gray);
    font-size: 0.85rem;
    margin-top: 5px;
    text-align: right;
}

.form-message {
    margin-top: 20px;
    padding: 15px;
    border-radius: 5px;
    display: none;
}

.form-message.success {
    background: rgba(0, 255, 0, 0.2);
    border: 1px solid #00ff00;
    color: #00ff00;
    display: block;
}

.form-message.error {
    background: rgba(255, 0, 0, 0.2);
    border: 1px solid #ff0000;
    color: #ff0000;
    display: block;
}

.guestbook-messages {
    margin-top: 60px;
}

.messages-title {
    text-align: center;
    margin-bottom: 40px;
    font-size: 2.5rem;
    color: var(--text-light);
}

.messages-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
}

.message-card {
    background: rgba(0, 0, 0, 0.6);
    border: 1px solid var(--secondary-color);
    border-radius: 10px;
    padding: 20px;
    transition: all 0.3s ease;
}

.message-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0, 255, 255, 0.3);
    border-color: var(--primary-color);
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.message-name {
    font-weight: bold;
    color: var(--primary-color);
    font-size: 1.1rem;
}

.message-date {
    color: var(--text-gray);
    font-size: 0.9rem;
}

.message-content {
    color: var(--text-light);
    line-height: 1.6;
}

.no-messages {
    text-align: center;
    color: var(--text-gray);
    font-size: 1.2rem;
    padding: 40px;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 10px;
}

@media (max-width: 768px) {
    .guestbook-form-container {
        padding: 25px;
    }
    
    .messages-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('guestbook-form');
    const messageDiv = document.getElementById('form-message');
    const charCount = document.querySelector('.char-count');
    const textarea = document.getElementById('guest_message');
    
    // Character counter
    if (textarea && charCount) {
        textarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count + ' / 500 characters';
        });
    }
    
    // Form submission
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
                    
                    // Reload messages after 2 seconds
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

