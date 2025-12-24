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
        .status-pending { color: #ffa500; font-weight: bold; }
        .status-approved { color: #00ff00; font-weight: bold; }
        .status-rejected { color: #ff0000; font-weight: bold; }
    </style>
    <?php
}

// Create Guestbook page on theme activation
function southwestsecret_create_guestbook_page() {
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
add_action('after_switch_theme', 'southwestsecret_create_guestbook_page');

// Create Birthday Fun page on theme activation
function southwestsecret_create_birthday_fun_page() {
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
add_action('after_switch_theme', 'southwestsecret_create_birthday_fun_page');

// Create Invitation page on theme activation
function southwestsecret_create_invitation_page() {
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
    
    wp_insert_post($invitation_page);
}
add_action('after_switch_theme', 'southwestsecret_create_invitation_page');

// ============================================
// FUTURE BLOG STRUCTURE (Not implemented yet)
// ============================================

// Register blog post type for future use (commented out until needed)
/*
function southwestsecret_register_blog_post_type() {
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
// add_action('init', 'southwestsecret_register_blog_post_type'); // Uncomment when ready to implement blog
*/



