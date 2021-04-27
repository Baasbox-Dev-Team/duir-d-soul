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

class Bb_Wiki_Words_Linker {

    public function replace_words($the_content) {
        if(get_post_type() == "post") { // Checking that we're in the single.php of an article, this exclude wiki posts
            $wiki_titles = $this->get_all_wiki_titles();
            $new_content = $the_content;

            //dd($wiki_titles);
            foreach($wiki_titles as $title) {
                $permalink = rtrim($title['permalink'], "/");
                $word = $title['word'];
                $substitute = '<a href="'.$permalink.'">'.'${1}'.'</a>';
                $new_content = preg_replace("/\b($word)\b/im", $substitute, $new_content);
            }

            return $new_content;
        } else {
            return $the_content;
        }
 	}

    private function get_all_wiki_titles() {
        $args = [
            'post_type' => 'wiki'
        ];

        $loop = new WP_Query($args);

        $wiki_posts = $loop->posts;

        $wiki_titles = [];

        foreach($wiki_posts as $post) {
            $wiki_titles[] = [
                "word" => $post->post_title,
                "permalink" => get_permalink($post->ID)
            ];
        }

        return $wiki_titles;
        
    }

}