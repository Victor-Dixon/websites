<?php
class Portfolio_Public {
    public function __construct() {
        add_shortcode('portfolio_grid', array($this, 'portfolio_grid_shortcode'));
    }
    public function portfolio_grid_shortcode() {
        $projects = get_posts(array('post_type' => 'portfolio_project', 'posts_per_page' => 6));
        $output = '<div class="portfolio-grid">';
        foreach ($projects as $project) {
            $output .= '<div class="portfolio-item"><h3>' . get_the_title($project) . '</h3><div class="portfolio-content">' . get_the_excerpt($project) . '</div></div>';
        }
        $output .= '</div>';
        return $output;
    }
}