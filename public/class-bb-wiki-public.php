<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Bb_Wiki
 * @subpackage Bb_Wiki/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Bb_Wiki
 * @subpackage Bb_Wiki/public
 * @author     Your Name <email@example.com>
 */
class Bb_Wiki_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bb_Wiki_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bb_Wiki_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bb-wiki-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bb_Wiki_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bb_Wiki_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bb-wiki-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Override archive template location for custom post type
	 *
	 * If the archive template file not exist in the theme folder, then use  the plugin template.
	 * In this case, file can be overridden inside the [child] theme.
	 *
	 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/archive_template
	 * @link http://wordpress.stackexchange.com/a/116025/90212
	 */
	public function get_custom_post_type_archive_template($archive_template) {

		global $post;
		$custom_post_type = 'wiki';
		$templates_dir = 'templates';

		if ( is_post_type_archive( $custom_post_type ) ) {

			$theme_files = array('archive-' . $custom_post_type . '.php', $this->plugin_name . '/archive-' . $custom_post_type . '.php');
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme != '' ) {
				// Try to locate in theme first
				return $archive_template;
			} else {
				// Try to locate in plugin templates folder
				if ( file_exists( WP_PLUGIN_DIR . '/' . $this->plugin_name . '/' . $templates_dir . '/archive-' . $custom_post_type . '.php' ) ) {
					return WP_PLUGIN_DIR . '/' . $this->plugin_name . '/' . $templates_dir . '/archive-' . $custom_post_type . '.php';
				} elseif ( file_exists( WP_PLUGIN_DIR . '/' . $this->plugin_name . '/archive-' . $custom_post_type . '.php' ) ) {
					// Try to locate in plugin base folder
					return WP_PLUGIN_DIR . '/' . $this->plugin_name . '/archive-' . $custom_post_type . '.php';
				} else {
					return null;
				}

			}

		}

		return $archive_template;

	}

	// OR

	public function locate_template( $template, $settings, $page_type ) {

		$theme_files = array(
			$page_type . '-' . $settings['custom_post_type'] . '.php',
			$this->plugin_name . DIRECTORY_SEPARATOR . $page_type . '-' . $settings['custom_post_type'] . '.php',
		);

		$exists_in_theme = locate_template( $theme_files, false );

		if ( $exists_in_theme != '' ) {

			// Try to locate in theme first
			return $template;

		} else {

			// Try to locate in plugin base folder,
			// try to locate in plugin $settings['templates'] folder,
			// return $template if non of above exist
			$locations = array(
				join( DIRECTORY_SEPARATOR, array( WP_PLUGIN_DIR, $this->plugin_name, '' ) ),
				join( DIRECTORY_SEPARATOR, array( WP_PLUGIN_DIR, $this->plugin_name, $settings['templates_dir'], '' ) ), //plugin $settings['templates'] folder
			);

			foreach ( $locations as $location ) {
				if ( file_exists( $location . $theme_files[0] ) ) {
					return $location . $theme_files[0];
				}
			}

			return $template;

		}

	}

	public function get_custom_post_type_templates( $template ) {
		global $post;

		$settings = array(
			'custom_post_type' => 'wiki',
			'templates_dir' => 'templates',
		);

		//if ( $settings['custom_post_type'] == get_post_type() && ! is_archive() && ! is_search() ) {
		if ( $settings['custom_post_type'] == get_post_type() && is_single() ) {

			return $this->locate_template( $template, $settings, 'single' );

		}

		return $template;
	}

	public function related_articles_shortcode( $atts ) {

		$args = shortcode_atts([],$atts);

		// Getting actual wiki word
		global $post;
		$word = $post->post_title;
		$options = get_option('bb-wiki');

		// Searching for related articles that contain the wiki word
		$posts = get_posts([
			'post_type' => 'post',
			's' => $word,
			'posts_per_page' => $options['max_related_articles'],
			'no_found_row' => true,
			'sentence' => true,
		]);


		$heading = '
			<h2>Articoli dal blog che contengono questa parola</h2>
		';

		$articles = '<ul>';

		if(!empty($posts)) {
			foreach($posts as $post) {
				$articles = $articles."<li><a href=".get_permalink($post->ID)."><div class=\"bb-wiki-related-article\">";
				if(get_the_post_thumbnail($post->ID) != null) {
					$articles = $articles.'<div class="bb-wiki-related-article-image">'.get_the_post_thumbnail($post->ID).'</div>';
				}
				$articles = $articles.'<div class="bb-wiki-related-article-content"><h4>$post->post_title</h4><p>'.get_the_excerpt($post->ID).'</p></div></div></a></li>';
			};
		}

		if(!empty($posts)) {
			$result = '<div class="bb-wiki related-articles">'.$heading.$articles.'</div>';
		} else {
			$result = '';
		}

		return $result;
		
	}

	function add_related_articles_to_wiki($the_content) {
		$options = get_option('bb-wiki');
        if(get_post_type() == "wiki" && $options['enable_related_articles'] != 'no') { // Checking that we're in the single.php of a wiki
			$new_content = $the_content."[bbwiki-related-articles]";
            return $new_content;
        } else {
            return $the_content;
        }
 	}

	private static function postsQuery($page) {
		$options = get_option('bb-wiki');
	
		$query  = new WP_Query([ 
			'post_type'      => 'wiki',
			'paged' => $page,
			'posts_per_page' => $options['posts_per_page'],
			'no_found_rows'  => true,
			'orderby' => 'title', 
			'order' => 'ASC'
		]);
	
		return $query;
	}
	
	private static function makeFirstLetterPostsArray($query) {
	
		if($query->post_count == 0) {
			$query = self::postsQuery(1);
		}
	
		if ($query->posts) 
		{
			foreach ( $query->posts as $key => $post ) {
				$first_letter = substr($post->post_title,0,1);
	
				if(!empty($first_letter)) {
					$results[$first_letter][] = array(
						'id' => $post->ID,
						'title' => $post->post_title,
					);
				}
			}
		}
	
		if(!empty($results)) {
			ksort( $results );
		}
	
		return $results;
	}

	public static function getPaginatedWikiByLetters($page) {
		$results = self::makeFirstLetterPostsArray(self::postsQuery($page));

		return $results;
	}

}
