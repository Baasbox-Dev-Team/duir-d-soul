<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://baasbox.com
 * @since      1.0.0
 *
 * @package    Bb_Wiki
 * @subpackage Bb_Wiki/includes
 */

class Bb_Wiki_Rest_Api {

    public function get_random_word() {

        register_rest_route( 'bbwiki/v1', '/random', array(
            'methods'   =>  'GET',
            'callback'  =>  [$this, 'get_random']
        ) );

 	}

    public function get_random() {
        $post = get_posts( array('type' => 'wiki', 'orderby' => 'rand', 'posts_per_page' => 1) )[0];

        $data = [
            "id" => $post->ID,
            "date" => $post->post_date,
            "title" => $post->post_title,
            "content" => wp_strip_all_tags($post->post_content)
        ];

        return $data;
    }

}