<?php
/**
 * Loader class
 *
 * This class bootstraps the plugin. Given we
 * are building in a class-based architecture,
 * and loading on plugins_loaded, we need to
 * separate this from the main class to allow
 * access from the activation hook.
 *
 * @package     SaaSaparilla\Loader
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * SaaSaparilla loader class
 *
 * @since       1.0.0
 */
class SaaSaparilla_Loader {


	/**
	 * @access      public
	 * @since       1.0.0
	 * @var         string $plugin_file The main plugin file
	 */
	public $plugin_file;


	/**
	 * Get things started
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       string $plugin_file The main plugin file
	 * @return      void
	 */
	public function __construct( $plugin_file = false ) {
		// Make sure the main plugin file is accessible
		if( ! $plugin_file ) {
			return;
		}

		$this->plugin_file = $plugin_file;
		$this->setup_constants();

		// Ensure we're running WP Multisite
		if( ! is_multisite() ) {
			// Load the setup page
			require_once SAASAPARILLA_DIR . 'includes/actions.php';
			require_once SAASAPARILLA_DIR . 'includes/admin/setup/pages.php';

			// Display the multisite error
			add_action( 'admin_notices', array( $this, 'display_multisite_error' ) );
			
			return;
		}

		$this->includes();
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
		define( 'SAASAPARILLA_DIR', plugin_dir_path( $this->plugin_file ) );

		// Plugin URL
		define( 'SAASAPARILLA_URL', plugin_dir_url( $this->plugin_file ) );

		// Plugin file
		define( 'SAASAPARILLA_FILE', $this->plugin_file );
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
			require_once SAASAPARILLA_DIR . 'includes/admin/class.notices.php';
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
	 * Display error if Multisite isn't active
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function display_multisite_error() {
		$html  = '<div class="error">';
		$html .= '<p>' . sprintf( __( 'SaaSaparilla is a WordPress Multisite plugin, but your install is not configured for Multisite. Click <a href="%s">here</a> to set up Multisite now.', 'saasaparilla' ), add_query_arg( array( 'page' => 'saasaparilla_setup' ), admin_url() ) ) . '</p>';
		$html .= '</div>';

		echo $html;
	}
}
