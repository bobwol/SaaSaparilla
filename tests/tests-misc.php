<?php


/**
 * @group saasaparilla_misc
 */
class Test_Misc extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function test_is_odd() {
		$this->assertTrue( saasaparilla_is_odd( 3 ) );
		$this->assertFalse( saasaparilla_is_odd( 4 ) );
	}

	public function test_get_ip() {
		$this->assertEquals( '127.0.0.1', saasaparilla_get_ip() );
	}

	public function test_month_num_to_name() {
		$this->assertEquals( 'Jan', saasaparilla_month_num_to_name( 1 ) );
	}

	public function test_get_php_arg_separator_output() {
		$this->assertEquals( '&', saasaparilla_get_php_arg_separator_output() );
	}
}
