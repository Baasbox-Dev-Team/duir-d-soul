<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Bb_Wiki
 * @subpackage Bb_Wiki/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Bb_Wiki
 * @subpackage Bb_Wiki/includes
 * @author     Your Name <email@example.com>
 */
class Bb_Wiki_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		/**
		 * This only required if custom post type has rewrite!
		 */
		flush_rewrite_rules();
	}

}
