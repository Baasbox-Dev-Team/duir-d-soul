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
        
          register_rest_route( 'bbwiki/v1', '/suggest', array(
            'methods'   =>  'GET',
            'callback'  =>  [$this, 'sugget_related_posts']
        ) );

 	}

      public function sugget_related_posts() {
    $tag_ids = explode(',', $_GET['tags']);
    $posts = get_posts([
      'post_type' => 'post',
      // 'tag__in' => $tag_ids,
      'nopaging' => true
    ])

    $suggested_posts = [];
    foreach ($posts as $post) {
        $author = get_the_author_meta('display_name', $post->post_author);
        $data = [
            "id" => $post->ID,
            "date" => $post->post_date,
            "title" => $post->post_title,
            "img" => get_the_post_thumbnail_url($post),
            "url" => get_permalink($post->ID),
            "author" => $author
        ];

        array_push($suggested_posts, $data);
    }

    return $suggested_posts;
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
              if(get_the_tags($post)) {
                $tags = array_map(function($tag) {
                  return [
                    'name' => $tag->name,
                    'id' => $tag->term_id
                  ];
                }, get_the_tags($post));
              } else {
                $tags = [];
              }

                $data[$post->post_title] = ['url' => get_permalink($post->ID), 'tags' => $tags];
            }

        };

        $keys = array_map('strlen', array_keys($data));
        array_multisort($keys, SORT_DESC, $data);
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
