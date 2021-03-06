<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Bb_Wiki
 * @subpackage Bb_Wiki/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Bb_Wiki
 * @subpackage Bb_Wiki/includes
 * @author     Your Name <email@example.com>
 */
class Bb_Wiki {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Bb_Wiki_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'duir-d-soul';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Bb_Wiki_Loader. Orchestrates the hooks of the plugin.
	 * - Bb_Wiki_i18n. Defines internationalization functionality.
	 * - Bb_Wiki_Admin. Defines all hooks for the admin area.
	 * - Bb_Wiki_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ). '/vendor/autoload.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bb-wiki-loader.php';

		/**
		 * License Checker
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bb-wiki-license_checker.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bb-wiki-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bb-wiki-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bb-wiki-public.php';

		/**
		 * Custom Post Types
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bb-wiki-post_types.php';

		/**
		 * Articles to Wiki Words Linker
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bb-wiki-words_linker.php';

		/**
		 * REST API
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bb-wiki-rest_api.php';

		/**
		 * Exopite Simple Options Framework
		 *
		 * @link https://github.com/JoeSz/Exopite-Simple-Options-Framework
		 * @author Joe Szalai
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/exopite-simple-options/exopite-simple-options-framework-class.php';


		$this->loader = new Bb_Wiki_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Bb_Wiki_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Bb_Wiki_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Bb_Wiki_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_post_types = new Bb_Wiki_Post_Types();

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		/**
		 * The problem with the initial activation code is that when the activation hook runs, it's after the init hook has run,
		 * so hooking into init from the activation hook won't do anything.
		 * You don't need to register the CPT within the activation function unless you need rewrite rules to be added
		 * via flush_rewrite_rules() on activation. In that case, you'll want to register the CPT normally, via the
		 * loader on the init hook, and also re-register it within the activation function and
		 * call flush_rewrite_rules() to add the CPT rewrite rules.
		 *
		 * @link https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/issues/261
		 */
		$this->loader->add_action( 'init', $plugin_post_types, 'create_custom_post_type', 999 );
		$this->loader->add_action( 'init', $plugin_post_types, 'pagination_rewrite', 999 );

		// Save/Update our plugin options
		$this->loader->add_action( 'init', $plugin_admin, 'create_menu', 999 );

		$this->loader->add_action('exopite_sof_do_save_options', $plugin_admin, 'changed_license', 999);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Bb_Wiki_Public( $this->get_plugin_name(), $this->get_version() );
		$rest_api = new Bb_Wiki_Rest_Api();
		$words_linker = new Bb_Wiki_Words_Linker();

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		/**
		 * Register shortcode via loader
		 *
		 * Use: [short-code-name args]
		 *
		 * @link https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/issues/262
		 */
		$this->loader->add_shortcode( "bbwiki-related-articles", $plugin_public, "related_articles_shortcode", $priority = 10, $accepted_args = 2 );

        $this->loader->add_shortcode( "duir-list", $plugin_public, "bb_wiki_list_shortcode", $priority = 10, $accepted_args = 2 );


        // Override archive template location for custom post type
		$this->loader->add_filter( 'archive_template', $plugin_public, 'get_custom_post_type_archive_template' );
		//OR
		$this->loader->add_filter( 'template_include', $plugin_public, 'get_custom_post_type_templates' );

		// Registering REST API Routes
		$this->loader->add_action( 'rest_api_init', $rest_api, 'register_routes' );

		$this->loader->add_action( 'template_redirect', $plugin_public, 'redirect_if_external' );

		// Link words in Articles
		$this->loader->add_filter('the_content', $words_linker, 'replace_words');

		// Add Related Articles to Wiki
		$this->loader->add_filter('the_content', $plugin_public, 'add_related_articles_to_wiki');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Bb_Wiki_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	public function d() {
        call_user_func_array( 'dump' , func_get_args() );
    }

	public function dd() {
        call_user_func_array( 'dump' , func_get_args() );
        die();
    }

}
