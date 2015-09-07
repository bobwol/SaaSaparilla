<?php
/**
 * Plugin Name:     SaaSaparilla
 * Plugin URI:      http://saasaparilla.com
 * Description:     The easiest way to build your own Software as a Service platform with WordPress
 * Version:         1.0.0
 * Author:          Daniel J Griffiths
 * Author URI:      http://section214.com
 * Text Domain:     saasaparilla
 *
 * @package         SaaSaparilla
 * @author          Daniel J Griffiths <dgriffiths@section214.com>
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


if( ! class_exists( 'SaaSaparilla' ) ) {


	/**
	 * Main SaaSaparilla class
	 *
	 * @since       1.0.0
	 */
	final class SaaSaparilla {


		/**
		 * @access      private
		 * @since       1.0.0
		 * @var         SaaSaparilla $instance The one true SaaSaparilla
		 */
		private static $instance;


		/**
		 * @access      public
		 * @since       1.0.0
		 * @var         object $loader The SaaSaparilla loader object
		 */
		public $loader;


		/**
		 * Get active instance
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      self::$instance The one true SaaSaparilla
		 */
		public static function instance() {
			if( ! self::$instance ) {
				self::$instance = new SaaSaparilla();

				// Instantiate the loader
				require_once 'includes/class.loader.php';
				self::$instance->loader = new SaaSaparilla_Loader( __FILE__ );
				
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object. Therefore, we don't want the object to be cloned.
		 *
		 * @access      protected
		 * @since       1.0.0
		 * @return      void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'saasaparilla' ), '1.6' );
		}


		/**
		 * Disable unserializing of the class
		 *
		 * @access      protected
		 * @since       1.0.0
		 * @return      void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'saasaparilla' ), '1.6' );
		}


		/**
		 * Plugin actions and filters
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function hooks() {
			// Load plugin language files
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		}


		/**
		 * Load plugin language files
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      void
		 */
		public function load_textdomain() {
			// Set filter for plugin languages directory
			$lang_dir   = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$lang_dir   = apply_filters( 'saasaparilla_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale	 = apply_filters( 'plugin_locale', get_locale(), 'saasaparilla' );
			$mofile	 = sprintf( '%1$s-%2$s.mo', 'saasaparilla', $locale );

			// Setup paths to current locale file
			$mofile_local   = $lang_dir . $mofile;
			$mofile_global  = WP_LANG_DIR . '/saasaparilla/' . $mofile;

			if( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/saasaparilla folder
				load_textdomain( 'saasaparilla', $mofile_global );
			} elseif( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/saasaparilla/languages/ folder
				load_textdomain( 'saasaparilla', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'saasaparilla', false, $lang_dir );
			}
		}
	}
}


/**
 * The main function responsible for returning the one true SaaSaparilla
 * instance to functions everywhere.
 *
 * @since       1.0.0
 * @return      object The one true SaaSaparilla instance
 */
function saasaparilla() {
	return SaaSaparilla::instance();
}
add_action( 'plugins_loaded', 'saasaparilla', 9 );


/**
 * Plugin activation hook for install/update routines
 *
 * @since       1.0.0
 * @return      void
 */
function saasaparilla_install() {
	// Instantiate the loader
	require_once 'includes/class.loader.php';
	$loader = new SaaSaparilla_Loader( __FILE__ );
}
register_activation_hook( '__FILE__', 'saasaparilla_install' );
