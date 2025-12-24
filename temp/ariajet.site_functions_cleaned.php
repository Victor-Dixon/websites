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
    <table class="form-table">
        <tr>
            <th><label for="ariajet_game_url"><?php _e('Game URL', 'ariajet'); 


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


?></label></th>
            <td>
                <input type="url" id="ariajet_game_url" name="ariajet_game_url" 
                       value="<?php echo esc_attr($game_url); 


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
                       placeholder="https://ariajet.site/games/game-name.html" />
                <p class="description"><?php _e('URL to the game HTML file', 'ariajet'); 


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


?></p>
            </td>
        </tr>
        <tr>
            <th><label for="ariajet_game_type"><?php _e('Game Type', 'ariajet'); 


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


?></label></th>
            <td>
                <select id="ariajet_game_type" name="ariajet_game_type">
                    <option value="2d" <?php selected($game_type, '2d'); 


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


?>>2D Game</option>
                    <option value="puzzle" <?php selected($game_type, 'puzzle'); 


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


?>>Puzzle</option>
                    <option value="adventure" <?php selected($game_type, 'adventure'); 


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


?>>Adventure</option>
                    <option value="survival" <?php selected($game_type, 'survival'); 


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


?>>Survival</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="ariajet_game_status"><?php _e('Status', 'ariajet'); 


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


?></label></th>
            <td>
                <select id="ariajet_game_status" name="ariajet_game_status">
                    <option value="published" <?php selected($game_status, 'published'); 


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


?>>Published</option>
                    <option value="beta" <?php selected($game_status, 'beta'); 


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


?>>Beta</option>
                    <option value="development" <?php selected($game_status, 'development'); 


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


?>>In Development</option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}



