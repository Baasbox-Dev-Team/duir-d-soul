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

    public function register_routes() {

        register_rest_route( 'bbwiki/v1', '/random', array(
            'methods'   =>  'GET',
            'callback'  =>  [$this, 'get_random_word']
        ) );

        register_rest_route( 'bbwiki/v1', '/all', array(
            'methods'   =>  'GET',
            'callback'  =>  [$this, 'get_all_words']
        ) );

 	}

    public function get_all_words() {
        $posts = get_posts([
            'post_type' => 'wiki', 
            'orderby' => 'title', 
            'order' => 'DESC',
            'nopaging' => true
            ]);
        
        $data = [];

        foreach($posts as $post) {
            $wiki_meta = get_post_meta($post->ID, 'bb-wiki', true);

            if(!isset($wiki_meta['autolink_enabled'])) {
                if(!is_array($wiki_meta)) {
                    $wiki_meta = [];
                }
                $wiki_meta['autolink_enabled'] = "yes";
            }

            if($wiki_meta['autolink_enabled'] == "yes") {
                $data[$post->post_title] = get_permalink($post->ID);
            }

        };

        return $data;
    }

    public function get_random_word() {
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