<?php
/**
 * Twenty Twenty-Four functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Twenty Twenty-Four
 * @since Twenty Twenty-Four 1.0
 */

/**
 * Register block styles.
 */

if ( ! function_exists( 'twentytwentyfour_block_styles' ) ) :
	/**
	 * Register custom block styles
	 *
	 * @since Twenty Twenty-Four 1.0
	 * @return void
	 */
	function twentytwentyfour_block_styles() {

		register_block_style(
			'core/details',
			array(
				'name'         => 'arrow-icon-details',
				'label'        => __( 'Arrow icon', 'twentytwentyfour' ),
				/*
				 * Styles for the custom Arrow icon style of the Details block
				 */
				'inline_style' => '
				.is-style-arrow-icon-details {
					padding-top: var(--wp--preset--spacing--10);
					padding-bottom: var(--wp--preset--spacing--10);
				}

				.is-style-arrow-icon-details summary {
					list-style-type: "\2193\00a0\00a0\00a0";
				}

				.is-style-arrow-icon-details[open]>summary {
					list-style-type: "\2192\00a0\00a0\00a0";
				}',
			)
		);
		register_block_style(
			'core/post-terms',
			array(
				'name'         => 'pill',
				'label'        => __( 'Pill', 'twentytwentyfour' ),
				/*
				 * Styles variation for post terms
				 * https://github.com/WordPress/gutenberg/issues/24956
				 */
				'inline_style' => '
				.is-style-pill a,
				.is-style-pill span:not([class], [data-rich-text-placeholder]) {
					display: inline-block;
					background-color: var(--wp--preset--color--base-2);
					padding: 0.375rem 0.875rem;
					border-radius: var(--wp--preset--spacing--20);
				}

				.is-style-pill a:hover {
					background-color: var(--wp--preset--color--contrast-3);
				}',
			)
		);
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfour' ),
				/*
				 * Styles for the custom checkmark list block style
				 * https://github.com/WordPress/gutenberg/issues/51480
				 */
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
		register_block_style(
			'core/navigation-link',
			array(
				'name'         => 'arrow-link',
				'label'        => __( 'With arrow', 'twentytwentyfour' ),
				/*
				 * Styles for the custom arrow nav link block style
				 */
				'inline_style' => '
				.is-style-arrow-link .wp-block-navigation-item__label:after {
					content: "\2197";
					padding-inline-start: 0.25rem;
					vertical-align: middle;
					text-decoration: none;
					display: inline-block;
				}',
			)
		);
		register_block_style(
			'core/heading',
			array(
				'name'         => 'asterisk',
				'label'        => __( 'With asterisk', 'twentytwentyfour' ),
				'inline_style' => "
				.is-style-asterisk:before {
					content: '';
					width: 1.5rem;
					height: 3rem;
					background: var(--wp--preset--color--contrast-2, currentColor);
					clip-path: path('M11.93.684v8.039l5.633-5.633 1.216 1.23-5.66 5.66h8.04v1.737H13.2l5.701 5.701-1.23 1.23-5.742-5.742V21h-1.737v-8.094l-5.77 5.77-1.23-1.217 5.743-5.742H.842V9.98h8.162l-5.701-5.7 1.23-1.231 5.66 5.66V.684h1.737Z');
					display: block;
				}

				/* Hide the asterisk if the heading has no content, to avoid using empty headings to display the asterisk only, which is an A11Y issue */
				.is-style-asterisk:empty:before {
					content: none;
				}

				.is-style-asterisk:-moz-only-whitespace:before {
					content: none;
				}

				.is-style-asterisk.has-text-align-center:before {
					margin: 0 auto;
				}

				.is-style-asterisk.has-text-align-right:before {
					margin-left: auto;
				}

				.rtl .is-style-asterisk.has-text-align-left:before {
					margin-right: auto;
				}",
			)
		);
	}
endif;

add_action( 'init', 'twentytwentyfour_block_styles' );

/**
 * Enqueue block stylesheets.
 */

if ( ! function_exists( 'twentytwentyfour_block_stylesheets' ) ) :
	/**
	 * Enqueue custom block stylesheets
	 *
	 * @since Twenty Twenty-Four 1.0
	 * @return void
	 */
	function twentytwentyfour_block_stylesheets() {
		/**
		 * The wp_enqueue_block_style() function allows us to enqueue a stylesheet
		 * for a specific block. These will only get loaded when the block is rendered
		 * (both in the editor and on the front end), improving performance
		 * and reducing the amount of data requested by visitors.
		 *
		 * See https://make.wordpress.org/core/2021/12/15/using-multiple-stylesheets-per-block/ for more info.
		 */
		wp_enqueue_block_style(
			'core/button',
			array(
				'handle' => 'twentytwentyfour-button-style-outline',
				'src'    => get_parent_theme_file_uri( 'assets/css/button-outline.css' ),
				'ver'    => wp_get_theme( get_template() )->get( 'Version' ),
				'path'   => get_parent_theme_file_path( 'assets/css/button-outline.css' ),
			)
		);
	}
endif;

add_action( 'init', 'twentytwentyfour_block_stylesheets' );

/**
 * Register pattern categories.
 */

if ( ! function_exists( 'twentytwentyfour_pattern_categories' ) ) :
	/**
	 * Register pattern categories
	 *
	 * @since Twenty Twenty-Four 1.0
	 * @return void
	 */
	function twentytwentyfour_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfour_page',
			array(
				'label'       => _x( 'Pages', 'Block pattern category', 'twentytwentyfour' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfour' ),
			)
		);
	}
endif;

add_action( 'init', 'twentytwentyfour_pattern_categories' );


/**
 * Set SEO-optimized title tag (30-60 characters)
 */
function set_seo_title($title) {
    $site_title = "We Are Swarm - Autonomous Agent Civilization & AI Technology";
    if (is_front_page() || is_home()) {
        return $site_title;
    }
    return $site_title . " | " . get_bloginfo('name');
}
add_filter('wp_title', 'set_seo_title', 10, 1);
add_filter('document_title_parts', function($title_parts) {
    $title_parts['title'] = "We Are Swarm - Autonomous Agent Civilization & AI Technology";
    return $title_parts;
}, 10, 1);


/**
 * Add Strict-Transport-Security (HSTS) header
 * Forces browsers to use HTTPS for all future requests
 */
function add_hsts_header() {
    if (is_ssl() || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
}
add_action('send_headers', 'add_hsts_header', 1);


/**
 * Automatically add alt text to images missing it
 * Improves SEO and accessibility
 */
function add_missing_alt_text($attr, $attachment = null) {
    // If alt text is missing or empty, generate from filename
    if (empty($attr['alt']) && $attachment) {
        $filename = get_post_meta($attachment->ID, '_wp_attached_file', true);
        if ($filename) {
            // Generate alt text from filename
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $name = preg_replace('/[0-9]+[-_]?/', '', $name); // Remove leading numbers
            $name = preg_replace('/[-_]/', ' ', $name); // Replace dashes/underscores
            $name = preg_replace('/\s+/', ' ', $name); // Normalize spaces
            $name = ucwords(strtolower($name)); // Capitalize words
            $attr['alt'] = $name ?: 'Image';
        }
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'add_missing_alt_text', 10, 2);

/**
 * Add alt text to images in content (post/page content)
 */
function add_alt_to_content_images($content) {
    if (empty($content)) {
        return $content;
    }
    
    // Find all img tags without alt attribute or with empty alt
    $pattern = '/<img([^>]*?)(?:alt=["']([^"']*)["'])?([^>]*?)>/i';
    
    $content = preg_replace_callback($pattern, function($matches) {
        $before_alt = $matches[1];
        $alt_value = isset($matches[2]) ? $matches[2] : '';
        $after_alt = $matches[3];
        
        // If alt is missing or empty, generate from src
        if (empty($alt_value)) {
            // Extract src
            if (preg_match('/src=["']([^"']+)["']/', $matches[0], $src_match)) {
                $src = $src_match[1];
                $filename = basename(parse_url($src, PHP_URL_PATH));
                $name = pathinfo($filename, PATHINFO_FILENAME);
                $name = preg_replace('/[0-9]+[-_]?/', '', $name);
                $name = preg_replace('/[-_]/', ' ', $name);
                $name = preg_replace('/\s+/', ' ', $name);
                $name = ucwords(strtolower($name));
                $alt_value = $name ?: 'Image';
            } else {
                $alt_value = 'Image';
            }
        }
        
        // Reconstruct img tag with alt
        return '<img' . $before_alt . ' alt="' . esc_attr($alt_value) . '"' . $after_alt . '>';
    }, $content);
    
    return $content;
}
add_filter('the_content', 'add_alt_to_content_images', 10, 1);


/**
 * Automatically add alt text to images missing it
 * Improves SEO and accessibility
 */
function add_missing_alt_text($attr, $attachment = null) {
    // If alt text is missing or empty, generate from filename
    if (empty($attr['alt']) && $attachment) {
        $filename = get_post_meta($attachment->ID, '_wp_attached_file', true);
        if ($filename) {
            // Generate alt text from filename
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $name = preg_replace('/[0-9]+[-_]?/', '', $name); // Remove leading numbers
            $name = preg_replace('/[-_]/', ' ', $name); // Replace dashes/underscores
            $name = preg_replace('/\\s+/', ' ', $name); // Normalize spaces
            $name = ucwords(strtolower($name)); // Capitalize words
            $attr['alt'] = $name ?: 'Image';
        }
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'add_missing_alt_text', 10, 2);

/**
 * Add alt text to images in content (post/page content)
 */
function add_alt_to_content_images($content) {
    if (empty($content)) {
        return $content;
    }
    
    // Find all img tags without alt attribute or with empty alt
    $pattern = '/<img([^>]*?)(?:alt=["\']([^"\']*)["\'])?([^>]*?)>/i';
    
    $content = preg_replace_callback($pattern, function($matches) {
        $before_alt = $matches[1];
        $alt_value = isset($matches[2]) ? $matches[2] : '';
        $after_alt = $matches[3];
        
        // If alt is missing or empty, generate from src
        if (empty($alt_value)) {
            // Extract src
            if (preg_match('/src=["\']([^"\']+)["\']/', $matches[0], $src_match)) {
                $src = $src_match[1];
                $filename = basename(parse_url($src, PHP_URL_PATH));
                $name = pathinfo($filename, PATHINFO_FILENAME);
                $name = preg_replace('/[0-9]+[-_]?/', '', $name);
                $name = preg_replace('/[-_]/', ' ', $name);
                $name = preg_replace('/\\s+/', ' ', $name);
                $name = ucwords(strtolower($name));
                $alt_value = $name ?: 'Image';
            } else {
                $alt_value = 'Image';
            }
        }
        
        // Reconstruct img tag with alt
        return '<img' . $before_alt . ' alt="' . esc_attr($alt_value) . '"' . $after_alt . '>';
    }, $content);
    
    return $content;
}
add_filter('the_content', 'add_alt_to_content_images', 10, 1);

<?php
/**
 * Add Missing Alt Text to Images - Added by Agent-7
 * Automatically adds descriptive alt text to images that are missing it
 */

// Add alt text to images in post content when missing
function add_missing_alt_text_to_content($content) {
    if (empty($content)) {
        return $content;
    }
    
    // Use DOMDocument to parse HTML
    if (!class_exists('DOMDocument')) {
        return $content;
    }
    
    $dom = new DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    $images = $dom->getElementsByTagName('img');
    
    foreach ($images as $img) {
        $alt = $img->getAttribute('alt');
        
        // If alt is empty or missing, generate from image attributes
        if (empty($alt) || $alt === '') {
            // Try to get alt from title attribute
            $title = $img->getAttribute('title');
            if (!empty($title)) {
                $img->setAttribute('alt', $title);
            } else {
                // Try to get from src filename
                $src = $img->getAttribute('src');
                if (!empty($src)) {
                    $filename = basename($src);
                    $name_without_ext = preg_replace('/\.[^.\s]{3,4}$/', '', $filename);
                    $name_formatted = str_replace(['-', '_'], ' ', $name_without_ext);
                    $name_formatted = ucwords($name_formatted);
                    if (!empty($name_formatted)) {
                        $img->setAttribute('alt', $name_formatted);
                    } else {
                        // Default fallback
                        $img->setAttribute('alt', 'Image');
                    }
                } else {
                    // Default fallback
                    $img->setAttribute('alt', 'Image');
                }
            }
        }
    }
    
    // Get updated HTML
    $new_content = $dom->saveHTML();
    
    // Clean up (remove DOCTYPE, html, body tags added by DOMDocument)
    $new_content = preg_replace('/^<!DOCTYPE.+
/**
 * Add missing alt text to images for SEO and accessibility
 */
function add_missing_image_alt($attr, $attachment = null) {
    if (empty($attr['alt']) && $attachment) {
        $filename = get_post_meta($attachment->ID, '_wp_attached_file', true);
        if ($filename) {
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $name = str_replace(array('-', '_'), ' ', $name);
            $name = ucwords(strtolower(trim($name)));
            $attr['alt'] = $name ? $name : 'Image';
        }
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'add_missing_image_alt', 10, 2);

?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing
function add_missing_alt_text_to_thumbnails($html, $post_id, $post_thumbnail_id, $size, $attr) {
    // Check if alt is already set
    if (strpos($html, 'alt=') !== false) {
        // Extract existing alt value
        preg_match('/alt=["']([^"']*)["']/', $html, $matches);
        if (!empty($matches[1])) {
            return $html; // Alt already exists
        }
    }
    
    // Get alt text from attachment meta or post title
    $alt_text = get_post_meta($post_thumbnail_id, '_wp_attachment_image_alt', true);
    
    if (empty($alt_text)) {
        // Try to get from attachment title
        $attachment = get_post($post_thumbnail_id);
        if ($attachment) {
            $alt_text = $attachment->post_title;
        }
        
        if (empty($alt_text)) {
            // Fallback to post title
            $post = get_post($post_id);
            if ($post) {
                $alt_text = $post->post_title . ' - Featured Image';
            } else {
                $alt_text = 'Featured Image';
            }
        }
    }
    
    // Add or update alt attribute
    if (strpos($html, '<img') !== false) {
        if (strpos($html, 'alt=') !== false) {
            // Replace existing empty alt
            $html = preg_replace('/alt=["'][^"']*["']/', 'alt="' . esc_attr($alt_text) . '"', $html);
        } else {
            // Add alt attribute
            $html = preg_replace('/(<img[^>]+)(\s*\/
/**
 * Add missing alt text to images for SEO and accessibility
 */
function add_missing_image_alt($attr, $attachment = null) {
    if (empty($attr['alt']) && $attachment) {
        $filename = get_post_meta($attachment->ID, '_wp_attached_file', true);
        if ($filename) {
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $name = str_replace(array('-', '_'), ' ', $name);
            $name = ucwords(strtolower(trim($name)));
            $attr['alt'] = $name ? $name : 'Image';
        }
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'add_missing_image_alt', 10, 2);

?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing
function add_missing_alt_text_to_widgets($content) {
    if (empty($content)) {
        return $content;
    }
    
    // Simple regex approach for widget content
    $content = preg_replace_callback(
        '/<img([^>]*?)(?:alt=["']([^"']*)["'])?([^>]*?)>/i',
        function($matches) {
            $before_alt = $matches[1];
            $existing_alt = isset($matches[2]) ? $matches[2] : '';
            $after_alt = $matches[3];
            
            // If alt is empty, try to extract from src or use default
            if (empty($existing_alt)) {
                // Try to get from src
                preg_match('/src=["']([^"']*)["']/', $before_alt . $after_alt, $src_match);
                if (!empty($src_match[1])) {
                    $filename = basename($src_match[1]);
                    $name_without_ext = preg_replace('/\.[^.\s]{3,4}$/', '', $filename);
                    $name_formatted = str_replace(['-', '_'], ' ', $name_without_ext);
                    $name_formatted = ucwords($name_formatted);
                    $alt_text = !empty($name_formatted) ? $name_formatted : 'Image';
                } else {
                    $alt_text = 'Image';
                }
                
                // Add alt attribute
                return '<img' . $before_alt . ' alt="' . esc_attr($alt_text) . '"' . $after_alt . '>';
            }
            
            return $matches[0];
        },
        $content
    );
    
    return $content;
}
add_filter('widget_text', 'add_missing_alt_text_to_widgets', 20);
add_filter('the_excerpt', 'add_missing_alt_text_to_widgets', 20);

/**
 * Add missing alt text to images for SEO and accessibility
 */
function add_missing_image_alt($attr, $attachment = null) {
    if (empty($attr['alt']) && $attachment) {
        $filename = get_post_meta($attachment->ID, '_wp_attached_file', true);
        if ($filename) {
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $name = str_replace(array('-', '_'), ' ', $name);
            $name = ucwords(strtolower(trim($name)));
            $attr['alt'] = $name ? $name : 'Image';
        }
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'add_missing_image_alt', 10, 2);
