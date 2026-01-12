<?php
class Event_Public {
    public function __construct() {
        add_shortcode('event_list', array($this, 'event_list_shortcode'));
    }
    public function event_list_shortcode() {
        $events = get_posts(array('post_type' => 'event', 'posts_per_page' => 10));
        $output = '<div class="event-list">';
        foreach ($events as $event) {
            $output .= '<div class="event-item"><h3>' . get_the_title($event) . '</h3><div class="event-content">' . get_the_excerpt($event) . '</div></div>';
        }
        $output .= '</div>';
        return $output;
    }
}