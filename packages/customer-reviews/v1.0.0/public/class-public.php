<?php
class Review_Public {
    public function __construct() {
        add_shortcode('customer_reviews', array($this, 'reviews_shortcode'));
    }
    public function reviews_shortcode() {
        $reviews = get_posts(array('post_type' => 'review', 'posts_per_page' => 5));
        $output = '<div class="customer-reviews">';
        foreach ($reviews as $review) {
            $output .= '<div class="review-item"><h4>' . get_the_title($review) . '</h4><div class="review-content">' . get_the_content($review) . '</div></div>';
        }
        $output .= '</div>';
        return $output;
    }
}