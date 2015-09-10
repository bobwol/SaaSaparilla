<?php
/**
 * Setup pages
 *
 * @package     SaaSaparilla\Admin\Setup\Pages
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Create the setup pages
 *
 * @since       1.0.0
 * @return      void
 */
function saasaparilla_add_setup_pages() {
	add_dashboard_page( __( 'SaaSaparilla Setup', 'saasaparilla' ), __( 'SaaSaparilla Setup', 'saasaparilla' ), 'manage_options', 'saasaparilla-setup', 'saasaparilla_render_setup_page' );
}
add_action( 'admin_menu', 'saasaparilla_add_setup_pages' );


/**
 * Display the setup page
 *
 * @since       1.0.0
 * @return      void
 */
function saasaparilla_render_setup_page() {
	$step = isset( $_GET['step'] ) ? $_GET['step'] : '1';
	?>
	<div class="wrap">
		<h1><?php _e( 'SaaSaparilla Setup', 'saasaparilla' ); ?></h1>

		<div class="card">
			<?php do_action( 'saasaparilla_setup_step_' . $step ); ?>
		</div>
	</div>
	<?php
}


/**
 * Setup step one
 *
 * @since       1.0.0
 * @return      void
 */
function saasaparilla_setup_step_1() {
	?>
	<h3><?php _e( 'Setup WordPress Multisite', 'saasaparilla' ); ?></h3>
	<p><?php _e( 'Because of the nature of SaaSaparilla, it <em>must</em> be run as a network-activated Multisite plugin. Your WordPress install is currently not configured for Multisite... Let\'s do that now!', 'saasaparilla' ); ?></p>
	<?php if( ! defined( 'WP_ALLOW_MULTISITE' ) || WP_ALLOW_MULTISITE != true ) { ?>
		<h4><?php _e( 'Enable Multisite in wp-config.php', 'saasaparilla' ); ?></h4>
		<p><?php _e( 'The following code needs to be added to your wp-config.php file above where it says <code>/* That\'s all, stop editing! Happy blogging. */</code>.', 'saasaparilla' ); ?></p>
		<textarea readonly style="width: 100%">/* Multisite */
define( 'WP_ALLOW_MULTISITE', true );</textarea>
		<?php
		if( is_writable( ABSPATH . 'wp-config.php' ) ) {
			echo '<p><span class="dashicons dashicons-yes" style="color: #008000"></span> ' . __( 'The wp-config.php file is writable. Would you like us to do it for you?', 'saasaparilla' ) . '</p>';
			echo '<a class="button" style="margin-right: 10px" href="' . add_query_arg( 'saasaparilla-action', 'enable_multisite' ) . '">' . __( 'Enable Multisite', 'saasaparilla' ) . '</a>';
			echo '<a class="button secondary" href="' . add_query_arg( 'step', '2' ) . '">' . __( 'I Already Did It', 'saasaparilla' ) . '</a>';
		} else {
			echo '<p><span class="dashicons dashicons-no" style="color: #ff0000"></span> ' . __( 'The wp-config.php file is not writable. Please edit the below file and add the code yourself.', 'saasaparilla' ) . '</p>';
			echo '<code>' . ABSPATH . 'wp-config.php</code>';
		}
	} else {
		echo '<h4>' . __( 'Enable Multisite in wp-config.php', 'saasaparilla' ) . '</h4>';
		echo '<p>' . __( 'Great! It looks like Multisite is enabled properly. Click the \'Next Step\' button to continue the setup process.', 'saasaparilla' ) . '</p>';
		echo '<a class="button" href="' . add_query_arg( 'step', '2' ) . '">' . __( 'Next Step', 'saasaparilla' ) . '</a>';
	}	
}
add_action( 'saasaparilla_setup_step_1', 'saasaparilla_setup_step_1' );


/**
 * Setup step two
 *
 * @since       1.0.0
 * @return      void
 */
function saasaparilla_setup_step_2() {
	?>
	<h3><?php _e( 'Setup WordPress Multisite', 'saasaparilla' ); ?></h3>
	<?php
	if( ! defined( 'WP_ALLOW_MULTISITE' ) || WP_ALLOW_MULTISITE != true ) {
		echo '<p>' . __( 'Oops! It looks like Multisite isn\'t properly enabled. Please go back to step one and try again.', 'saasaparilla' ) . '</p>';
		echo '<a class="button" href="' . add_query_arg( 'step', '1' ) . '">' . __( 'Return To Step 1', 'saasaparilla' ) . '</a>';
	} else {
		echo '<p>' . __( 'Great! It looks like Multisite is enabled properly. Unfortunately, we can\'t do the next step for you because WordPress requires that all plugins be disabled for it.', 'saasaparilla' ) . '</p>';
		echo '<p>' . __( 'When you click the "I\'m Ready!" button, SaaSaparilla will be disabled and you will be redirected to the Network Setup page to finish setting up your install as a WordPress Network.', 'saasaparilla' ) . '</p>';
		echo '<p>' . __( 'Once that is done, go to the network admin site and network activate SaaSaparilla to continue.', 'saasaparilla' ) . '</p>';
		echo '<a class="button" href="' . add_query_arg( 'saasaparilla-action', 'configure_multisite' ) . '">' . __( 'I\'m Ready!', 'saasaparilla' ) . '</a>';
	}
}
add_action( 'saasaparilla_setup_step_2', 'saasaparilla_setup_step_2' );


/**
 * Edit the wp-config.php file
 *
 * @since       1.0.0
 * @return      void
 */
function saasaparilla_enable_multisite() {
	$file_contents = file_get_contents( ABSPATH . 'wp-config.php' );
	$file_contents = str_replace( "/* That's all, stop editing! Happy blogging. */", "/* Multisite */\ndefine( 'WP_ALLOW_MULTISITE', true );\n\n/* That's all, stop editing! Happy blogging. */", $file_contents );
	file_put_contents( ABSPATH . 'wp-config.php', $file_contents );

	wp_safe_redirect( add_query_arg( array( 'step' => '2', 'saasaparilla-action' => false ) ) );
	exit;
}
add_action( 'saasaparilla_enable_multisite', 'saasaparilla_enable_multisite' );


/**
 * Pass configuration off to WordPress
 *
 * @since       1.0.0
 * @return      void
 */
function saasaparilla_configure_multisite() {
	// Deactivate SaaSaparilla
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	deactivate_plugins( plugin_basename( SAASAPARILLA_FILE ) );

	// Redirect
	wp_safe_redirect( admin_url( 'network.php' ) );
	exit;	
}
add_action( 'saasaparilla_configure_multisite', 'saasaparilla_configure_multisite' );
