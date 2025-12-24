<?php
// functions.php 
namespace freerideinvestortheme;

\add_action('rest_api_init', function () {
    // Checklist Endpoints
    \register_rest_route('freeride/v1', '/checklist', [
        'methods' => 'GET',
        'callback' => __NAMESPACE__ . '\\get_user_checklist',
        'permission_callback' => __NAMESPACE__ . '\\is_user_logged_in_rest',
    ]);

    \register_rest_route('freeride/v1', '/checklist', [
        'methods' => 'POST',
        'callback' => __NAMESPACE__ . '\\update_user_checklist',
        'permission_callback' => __NAMESPACE__ . '\\is_user_logged_in_rest',
    ]);

    // Performance Endpoint
    \register_rest_route('freeride/v1', '/performance', [
        'methods' => 'GET',
        'callback' => __NAMESPACE__ . '\\get_trading_performance',
        'permission_callback' => __NAMESPACE__ . '\\is_user_logged_in_rest',
    ]);

    // AI Recommendations Endpoint
    \register_rest_route('freeride/v1', '/ai-recommendations', [
        'methods' => 'POST',
        'callback' => __NAMESPACE__ . '\\generate_ai_recommendations',
        'permission_callback' => __NAMESPACE__ . '\\is_user_logged_in_rest',
    ]);
});



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
    <label for="custom_stylesheet">
        <?php esc_html_e('Enter relative path to custom stylesheet:', 'simplifiedtradingtheme'); 


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
    </label>
    <input 
        type="text" 
        name="custom_stylesheet" 
        id="custom_stylesheet" 
        value="<?php echo esc_attr($value); 


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
        placeholder="/assets/css/posts/custom-style.css">
    <?php
}



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
    <!-- Productivity Board HTML Structure -->
    <div id="freeride-orb">
        <div id="astra-ignis"></div>
        <div id="progress-ring"></div>
        <div id="focus-streak"></div>
        <div id="session-goals">Sessions: <span id="session-count">0</span></div>
    </div>

    <div id="timer">25:00</div>
    <button class="button" id="startBtn">Start</button>
    <button class="button" id="resetBtn" disabled>Reset</button>

    <div id="task-list">
        <h2>Guided Tasks</h2>
        <div id="task-lists">
            <div class="list" id="to-do">
                <h3>To Do</h3>
                <div class="tasks"></div>
            </div>
            <div class="list" id="in-progress">
                <h3>In Progress</h3>
                <div class="tasks"></div>
            </div>
            <div class="list" id="done">
                <h3>Done</h3>
                <div class="tasks"></div>
            </div>
        </div>

        <!-- Task Input Controls -->
        <div id="task-controls">
            <input type="text" id="taskInput" placeholder="New Task" />
            <select id="prioritySelect">
                <option value="high">High</option>
                <option value="medium" selected>Medium</option>
                <option value="low">Low</option>
            </select>
            <button class="button" id="addTaskBtn">Add Task</button>
        </div>

        <!-- JSON Upload Controls -->
        <div id="json-upload-controls">
            <input type="file" id="jsonFileInput" accept=".json" />
            <button class="button" id="uploadJSONBtn">Upload JSON</button>
        </div>
    </div>

    <!-- Analytics Panel -->
    <div id="analytics-panel" class="collapsed">
        <h2>Productivity Analytics</h2>
        <canvas id="tasksChart" width="400" height="200"></canvas>
    </div>
    <button id="toggle-analytics" class="button">Show Analytics</button>
    <?php
    return ob_get_clean();
}
add_shortcode('freeride_productivity_board', __NAMESPACE__ . '\\freeride_productivity_board_shortcode');

/* ==================================================
 * 5. OPTIONAL: ADVANCED TASK PERSISTENCE (AJAX)
 * ================================================== */


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
    <form id="trade-journal-form">
        <label><?php esc_html_e('Symbol', 'simplifiedtradingtheme'); 


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


?></label>
        <input type="text" name="symbol" required>
        
        <label><?php esc_html_e('Entry Price', 'simplifiedtradingtheme'); 


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


?></label>
        <input type="number" name="entry_price" step="0.01" required>
        
        <label><?php esc_html_e('Exit Price', 'simplifiedtradingtheme'); 


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


?></label>
        <input type="number" name="exit_price" step="0.01" required>
        
        <label><?php esc_html_e('Strategy', 'simplifiedtradingtheme'); 


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


?></label>
        <input type="text" name="strategy" required>
        
        <label><?php esc_html_e('Comments', 'simplifiedtradingtheme'); 


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


?></label>
        <textarea name="comments"></textarea>
        
        <button type="submit"><?php esc_html_e('Submit', 'simplifiedtradingtheme'); 


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


?></button>
    </form>
    <div id="response-message"></div>
    <script>
    (function() {
        const form = document.getElementById('trade-journal-form');
        const resp = document.getElementById('response-message');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const tradeDetails = {};
            formData.forEach((val, key) => tradeDetails[key] = val);

            const response = await fetch('<?php echo esc_url(rest_url('simplifiedtrading/v1/trade-journal')); 


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


?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': '<?php echo esc_js($nonce); 


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


?>'
                },
                body: JSON.stringify({ trade_details: tradeDetails })
            });

            const data = await response.json();
            if (data.status === 'success') {
                resp.innerHTML = '<?php esc_js_e("Trade saved successfully!", "simplifiedtradingtheme"); 


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


?>';
                form.reset();
            } else {
                resp.innerHTML = 'Error: ' + (data.message || 'Unknown error');
            }
        });
    })();
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('trade_journal_form', __NAMESPACE__ . '\\trade_journal_form_shortcode');

/* ==================================================
 * 10. SHORTCODE FOR EBOOK DOWNLOAD FORM
 * ================================================== */



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
    <form action="<?php echo esc_url( admin_url('admin-post.php') ); 


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


?>" method="POST" 
          class="ebook-download-form" 
          aria-label="<?php esc_attr_e('eBook Download Form', 'simplifiedtradingtheme'); 


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
        <?php 
            // Nonce Field for Security
            wp_nonce_field('ebook_download', 'ebook_download_nonce'); 
        


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
        <input type="hidden" name="action" value="ebook_download_form">
        <input type="hidden" name="redirect_to" 
               value="<?php echo esc_url( get_permalink() ); 


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
        
        <label for="ebook-email" class="screen-reader-text">
            <?php esc_html_e('Email Address', 'simplifiedtradingtheme'); 


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
        </label>
        <input type="email" id="ebook-email" name="ebook_email" 
               placeholder="<?php esc_attr_e('Your email', 'simplifiedtradingtheme'); 


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


?>" required>

        <!-- Honeypot Field -->
        <div style="display:none;">
            <label for="website"><?php esc_html_e('Website', 'simplifiedtradingtheme'); 


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


?></label>
            <input type="text" id="website" name="website" />
        </div>

        <!-- Consent Checkbox -->
        <label for="consent">
            <input type="checkbox" id="consent" name="consent" required>
            <?php esc_html_e('I agree to the Privacy Policy', 'simplifiedtradingtheme'); 


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
        </label>

        <button type="submit" class="cta-button">
            <?php esc_html_e('Download Now', 'simplifiedtradingtheme'); 


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
        </button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('ebook_download_form', __NAMESPACE__ . '\\ebook_download_form_shortcode');



