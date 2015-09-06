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
		 * @var         SaaSaparilla $instance The one true Saasaparilla
		 */
		private static $instance;


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
				self::$instance->setup_constants();
				self::$instance->includes();
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
		 * Setup plugin constants
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function setup_constants() {
			// Plugin version
			define( 'SAASAPARILLA_VER', '1.0.0' );

			// Plugin path
			define( 'SAASAPARILLA_DIR', plugin_dir_path( __FILE__ ) );

			// Plugin URL
			define( 'SAASAPARILLA_URL', plugin_dir_url( __FILE__ ) );
		}


		/**
		 * Include required files
		 *
		 * @access      private
		 * @since       1.0.0
		 * @global      array $saasaparilla_options The SaaSaparilla options array
		 * @return      void
		 */
		private function includes() {
			global $saasaparilla_options;

			require_once SAASAPARILLA_DIR . 'includes/admin/settings/register.php';
			$saasaparilla_options = saasaparilla_get_settings();

			require_once SAASAPARILLA_DIR . 'includes/actions.php';
			require_once SAASAPARILLA_DIR . 'includes/class.db.php';
			//require_once SAASAPARILLA_DIR . 'includes/class.html-elements.php';
			//require_once SAASAPARILLA_DIR . 'includes/class.logging.php';
			//require_once SAASAPARILLA_DIR . 'includes/class.roles.php';
			require_once SAASAPARILLA_DIR . 'includes/scripts.php';
			require_once SAASAPARILLA_DIR . 'includes/functions.php';

			if( is_admin() ) {
				require_once SAASAPARILLA_DIR . 'includes/admin/footer.php';
				//require_once SAASAPARILLA_DIR . 'includes/admin/class.notices.php';
				require_once SAASAPARILLA_DIR . 'includes/admin/pages.php';
				require_once SAASAPARILLA_DIR . 'includes/admin/settings/display.php';
				require_once SAASAPARILLA_DIR . 'includes/admin/settings/contextual-help.php';
				//require_once SAASAPARILLA_DIR . 'includes/admin/tools.php';
				//require_once SAASAPARILLA_DIR . 'includes/admin/welcome.php';
			}

			//require_once SAASAPARILLA_DIR . 'includes/admin/upgrades/upgrade-functions.php';
			//require_once SAASAPARILLA_DIR . 'includes/install.php';
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
	require_once 'includes/class.loader.php';
	require_once 'includes/install.php';
}
register_activation_hook( '__FILE__', 'saasaparilla_install' );
