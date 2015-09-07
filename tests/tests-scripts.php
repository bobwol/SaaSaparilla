<?php

/**
 * @group scripts
 */
class Tests_Scripts extends WP_UnitTestCase {

	/**
	 * Test if all the file hooks are working.
	 *
	 * @since 2.3.6
	 */
	public function test_file_hooks() {

		$this->assertNotFalse( has_action( 'admin_enqueue_scripts', 'saasaparilla_load_admin_scripts' ) );

	}

	/**
	 * Test that the saasaparilla_load_admin_scripts() function will bail when not an admin page.
	 *
	 * @since 2.3.6
	 */
	public function test_load_admin_scripts_bail() {

		// Prepare test
		global $pagenow;
		$origin_pagenow = $pagenow;
		$pagenow = 'dashboard';

		if ( ! function_exists( 'saasaparilla_is_admin_page' ) ) {
			include SAASAPARILLA_DIR . 'includes/admin/pages.php';
		}

		// Assert
		$this->assertNull( saasaparilla_load_admin_scripts( 'dashboard' ) );

		// Reset to origin
		$pagenow = $origin_pagenow;

	}

	/**
	 * Test that the saasaparilla_load_admin_scripts() function will enqueue the proper styles.
	 *
	 * @since 1.0.0
	 */
	public function test_load_admin_scripts() {

		if ( ! function_exists( 'saasaparilla_is_admin_page' ) ) {
			include SAASAPARILLA_DIR . 'includes/admin/pages.php';
		}

		saasaparilla_load_admin_scripts( 'settings.php' );

		$this->assertTrue( wp_style_is( 'jquery-chosen', 'enqueued' ) );
		$this->assertTrue( wp_style_is( 'wp-color-picker', 'enqueued' ) );
		$this->assertTrue( wp_style_is( 'colorbox', 'enqueued' ) );
		$this->assertTrue( wp_style_is( 'jquery-ui-css', 'enqueued' ) );
		$this->assertTrue( wp_style_is( 'thickbox', 'enqueued' ) );
		$this->assertTrue( wp_style_is( 'saasaparilla', 'enqueued' ) );
		$this->assertTrue( wp_style_is( 'saasaparilla-font', 'enqueued' ) );

		$this->assertTrue( wp_script_is( 'jquery-chosen', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'saasaparilla', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'colorbox', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'jquery-ui-datepicker', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'jquery-ui-dialog', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'jquery-flot', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'media-upload', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'thickbox', 'enqueued' ) );

	}
}
