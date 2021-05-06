<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Bb_Wiki
 * @subpackage Bb_Wiki/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bb_Wiki
 * @subpackage Bb_Wiki/admin
 * @author     Your Name <email@example.com>
 */
class Bb_Wiki_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bb-wiki-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bb-wiki-admin.js', array( 'jquery' ), $this->version, false );

	}


	public function create_menu() {

		/*
		* Create a submenu page under Plugins.
		* Framework also add "Settings" to your plugin in plugins list.
		*/
		$config_submenu = array(
	
			'type'              => 'menu',                          // Required, menu or metabox
			'id'                => $this->plugin_name,    // Required, meta box id, unique per page, to save: get_option( id )
			'parent'            => 'edit.php?post_type=wiki',                   // Required, sub page to your options page
			// 'parent'            => 'edit.php?post_type=your_post_type',
			'submenu'           => true,                            // Required for submenu
			'title'             => esc_html__( 'Impostazioni', 'bb-wiki' ),    //The name of this page
			'capability'        => 'manage_options',                // The capability needed to view the page
			'plugin_basename'   => plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' ),
			'multilang' => false
			// 'tabbed'            => false,
	
		);
	
		$fields[] = array(
			'name'   => 'dettaglio-parole',
			'title'  => 'Dettaglio Parole',
			'icon'   => 'dashicons-admin-generic',
			'fields' => array(
	
				/**
				 * Available fields:
				 * - ACE field
				 * - attached
				 * - backup
				 * - button
				 * - botton_bar
				 * - card
				 * - checkbox
				 * - color
				 * - content
				 * - date
				 * - editor
				 * - group
				 * - hidden
				 * - image
				 * - image_select
				 * - meta
				 * - notice
				 * - number
				 * - password
				 * - radio
				 * - range
				 * - select
				 * - switcher
				 * - tap_list
				 * - text
				 * - textarea
				 * - upload
				 * - video mp4/oembed
				 *
				 * Add your fields, eg.:
				 */
	
				 /*
				array(
					'id'          => 'text_1',
					'type'        => 'text',
					'title'       => 'Text',
					'before'      => 'Text Before',
					'after'       => 'Text After',
					'class'       => 'text-class',
					'attributes'  => 'data-test="test"',
					'description' => 'Description',
					'default'     => 'Default Text',
					'attributes'    => array(
						'rows'        => 10,
						'cols'        => 5,
						'placeholder' => 'do stuff',
					),
					'help'        => 'Help text',
	
				),
				*/
	
				array(
					'id'     => 'enable_related_articles',
					'type'   => 'switcher',
					'title'  => 'Abilita widget articoli correlati',
					'description' => 'Inserisci nel dettaglio della singola parola un widget che mostra gli articoli correlati che contengono quella parola.'
				),
	
	
				array(
					'id'     => 'max_related_articles',
					'type'   => 'range',
					'title'  => 'Numero massimo di articoli correlati',
					'description' => 'Definisci il numero massimo di articoli correlati da mostrare nel widget presente nel dettaglio della singola parola.',
					'default' => 3,
					'step' => 1,
					'min' => 1,
					'max' => 6,
				)
			),
		);

		$fields[] = array(
			'name'   => 'license',
			'title'  => 'Licenza',
			'icon'   => 'dashicons-admin-generic',
			'fields' => array(
	
				array(
					'id'     => 'license_number',
					'type'   => 'text',
					'title'  => 'Numero di licenza',
					'description' => 'Inserisci il numero di licenza che ti è stato fornito al momento dell\'acquisto per attivare il plugin.'
				),
			),
		);

		/**
		 * instantiate your admin page
		 */
		$options_panel = new Exopite_Simple_Options_Framework( $config_submenu, $fields );
	
	}

}
