<?php



// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>
    <div class="wrap">
        <h1>Guestbook Management</h1>
        <p>Review and approve birthday messages from visitors.</p>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($entries) : 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>
                    <?php foreach ($entries as $entry) : 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>
                        <tr>
                            <td><?php echo $entry->id; 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?></td>
                            <td><strong><?php echo esc_html($entry->guest_name); 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?></strong></td>
                            <td><?php echo esc_html(wp_trim_words($entry->message, 20)); 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?></td>
                            <td>
                                <span class="status-<?php echo esc_attr($entry->status); 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>">
                                    <?php echo ucfirst($entry->status); 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y g:i A', strtotime($entry->created_at)); 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?></td>
                            <td>
                                <?php if ($entry->status === 'pending') : 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=guestbook&action=approve&entry_id=' . $entry->id), 'guestbook_action'); 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>" class="button button-primary">Approve</a>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=guestbook&action=reject&entry_id=' . $entry->id), 'guestbook_action'); 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>" class="button">Reject</a>
                                <?php endif; 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>
                                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=guestbook&action=delete&entry_id=' . $entry->id), 'guestbook_action'); 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>" class="button button-link-delete" onclick="return confirm('Are you sure?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>
                <?php else : 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>
                    <tr>
                        <td colspan="6">No guestbook entries yet.</td>
                    </tr>
                <?php endif; 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>
            </tbody>
        </table>
    </div>

    <style>
        .status-pending {
            color: #ffa500;
            font-weight: bold;
        }

        .status-approved {
            color: #00ff00;
            font-weight: bold;
        }

        .status-rejected {
            color: #ff0000;
            font-weight: bold;
        }
    </style>
<?php
}

// Create Guestbook page on theme activation
function prismblossom_create_guestbook_page()
{
    if (get_page_by_path('guestbook')) {
        return; // Page already exists
    }

    $guestbook_page = array(
        'post_title'    => 'Guestbook',
        'post_name'     => 'guestbook',
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'page_template' => 'page-guestbook.php'
    );

    wp_insert_post($guestbook_page);
}
add_action('after_switch_theme', 'prismblossom_create_guestbook_page');

// Create Birthday Fun page on theme activation
function prismblossom_create_birthday_fun_page()
{
    if (get_page_by_path('birthday-fun')) {
        return; // Page already exists
    }

    $birthday_fun_page = array(
        'post_title'    => 'Birthday Fun',
        'post_name'     => 'birthday-fun',
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'page_template' => 'page-birthday-fun.php'
    );

    wp_insert_post($birthday_fun_page);
}
add_action('after_switch_theme', 'prismblossom_create_birthday_fun_page');

// Create Invitation page on theme activation
function prismblossom_create_invitation_page()
{
    if (get_page_by_path('invitation')) {
        return; // Page already exists
    }

    $invitation_page = array(
        'post_title'    => 'Birthday Invitation',
        'post_name'     => 'invitation',
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'page_template' => 'page-invitation.php'
    );

    $page_id = wp_insert_post($invitation_page);

    // Set default event details
    if ($page_id) {
        update_post_meta($page_id, '_invitation_date', 'TBD');
        update_post_meta($page_id, '_invitation_time', 'TBD');
        update_post_meta($page_id, '_invitation_location', 'TBD');
        update_post_meta($page_id, '_invitation_rsvp', 'TBD');
    }
}
add_action('after_switch_theme', 'prismblossom_create_invitation_page');

// ============================================
// INVITATION PAGE FUNCTIONALITY
// ============================================

// Add meta box for Invitation page event details
function prismblossom_add_invitation_meta_box()
{
    add_meta_box(
        'prismblossom_invitation_details',
        'Event Details',
        'prismblossom_invitation_meta_box_callback',
        'page',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'prismblossom_add_invitation_meta_box');

// Invitation meta box callback
function prismblossom_invitation_meta_box_callback($post)
{
    // Only show on invitation page
    if (get_page_template_slug($post->ID) !== 'page-invitation.php') {
        echo '<p>This meta box is only available on the Invitation page template.</p>';
        return;
    }

    wp_nonce_field('prismblossom_save_invitation_details', 'prismblossom_invitation_nonce');

    $event_date = get_post_meta($post->ID, '_invitation_date', true);
    $event_time = get_post_meta($post->ID, '_invitation_time', true);
    $event_location = get_post_meta($post->ID, '_invitation_location', true);
    $event_rsvp = get_post_meta($post->ID, '_invitation_rsvp', true);




// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>
    <table class="form-table">
        <tr>
            <th><label for="invitation_date">Event Date</label></th>
            <td>
                <input type="text" id="invitation_date" name="invitation_date"
                    value="<?php echo esc_attr($event_date); 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>"
                    class="regular-text"
                    placeholder="e.g., December 25, 2025">
                <p class="description">Enter the event date</p>
            </td>
        </tr>
        <tr>
            <th><label for="invitation_time">Event Time</label></th>
            <td>
                <input type="text" id="invitation_time" name="invitation_time"
                    value="<?php echo esc_attr($event_time); 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>"
                    class="regular-text"
                    placeholder="e.g., 7:00 PM">
                <p class="description">Enter the event time</p>
            </td>
        </tr>
        <tr>
            <th><label for="invitation_location">Event Location</label></th>
            <td>
                <input type="text" id="invitation_location" name="invitation_location"
                    value="<?php echo esc_attr($event_location); 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>"
                    class="regular-text"
                    placeholder="e.g., 123 Main Street, City, State">
                <p class="description">Enter the event location</p>
            </td>
        </tr>
        <tr>
            <th><label for="invitation_rsvp">RSVP Information</label></th>
            <td>
                <input type="text" id="invitation_rsvp" name="invitation_rsvp"
                    value="<?php echo esc_attr($event_rsvp); 


// Add alt text to images in post content when missing


?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing


?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing


?>"
                    class="regular-text"
                    placeholder="e.g., RSVP by December 20th">
                <p class="description">Enter RSVP instructions or contact information</p>
            </td>
        </tr>
    </table>
<?php
}

// Save invitation meta box data
function prismblossom_save_invitation_meta($post_id)
{
    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Verify nonce
    if (
        !isset($_POST['prismblossom_invitation_nonce']) ||
        !wp_verify_nonce($_POST['prismblossom_invitation_nonce'], 'prismblossom_save_invitation_details')
    ) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_page', $post_id)) {
        return;
    }

    // Only save if this is the invitation page template
    if (get_page_template_slug($post_id) !== 'page-invitation.php') {
        return;
    }

    // Save meta fields
    if (isset($_POST['invitation_date'])) {
        update_post_meta($post_id, '_invitation_date', sanitize_text_field($_POST['invitation_date']));
    }
    if (isset($_POST['invitation_time'])) {
        update_post_meta($post_id, '_invitation_time', sanitize_text_field($_POST['invitation_time']));
    }
    if (isset($_POST['invitation_location'])) {
        update_post_meta($post_id, '_invitation_location', sanitize_text_field($_POST['invitation_location']));
    }
    if (isset($_POST['invitation_rsvp'])) {
        update_post_meta($post_id, '_invitation_rsvp', sanitize_text_field($_POST['invitation_rsvp']));
    }
}
add_action('save_post', 'prismblossom_save_invitation_meta');

// AJAX handler for invitation message submission
function prismblossom_ajax_invitation_message_submission()
{
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'invitation_message_submit')) {
        wp_send_json_error('Security check failed');
        return;
    }

    // Sanitize input
    $message_name = isset($_POST['message_name']) ? sanitize_text_field($_POST['message_name']) : '';
    $message_text = isset($_POST['message_text']) ? sanitize_textarea_field($_POST['message_text']) : '';

    // Validate input
    if (empty($message_name) || empty($message_text)) {
        wp_send_json_error('Name and message are required');
        return;
    }

    // Insert into guestbook database (reuse existing guestbook table)
    global $wpdb;
    $table_name = $wpdb->prefix . 'guestbook_entries';

    $result = $wpdb->insert(
        $table_name,
        array(
            'guest_name' => $message_name,
            'message' => $message_text,
            'status' => 'pending'
        ),
        array('%s', '%s', '%s')
    );

    if ($result) {
        wp_send_json_success('Message sent successfully! Thank you for your message.');
    } else {
        wp_send_json_error('Database error. Please try again.');
    }
}
add_action('wp_ajax_prismblossom_submit_invitation_message', 'prismblossom_ajax_invitation_message_submission');
add_action('wp_ajax_nopriv_prismblossom_submit_invitation_message', 'prismblossom_ajax_invitation_message_submission');

// ============================================
// FUTURE BLOG STRUCTURE (Not implemented yet)
// ============================================

// Register blog post type for future use (commented out until needed)
/*
function prismblossom_register_blog_post_type() {
    $labels = array(
        'name' => 'Blog Posts',
        'singular_name' => 'Blog Post',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Blog Post',
        'edit_item' => 'Edit Blog Post',
        'new_item' => 'New Blog Post',
        'view_item' => 'View Blog Post',
        'search_items' => 'Search Blog Posts',
        'not_found' => 'No blog posts found',
        'not_found_in_trash' => 'No blog posts found in trash',
        'menu_name' => 'Blog'
    );
    
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-edit',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
        'rewrite' => array('slug' => 'blog'),
    );
    
    register_post_type('blog_post', $args);
}
// add_action('init', 'prismblossom_register_blog_post_type'); // Uncomment when ready to implement blog
*/



