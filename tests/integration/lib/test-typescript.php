<?php
/**
 * Class to test function validate_and_save_entry.
 *
 * @package Poc_Gutenberg
 */

namespace GenesisCustomBlocksConverter;

/**
 * Test function validate_and_save_entry.
 *
 * @since    1.0.0
 */
class TypescriptTest extends \WP_UnitTestCase {

	/**
	 * Test missing required fields.
	 *
	 * @since    1.0.0
	 */
	public function test_typescript() {
		$files = array(
			// 'example-repeater.json',
			'example-block.json',
		);
		foreach ( $files as $filename ) {
			$json_str   = file_get_contents( GENESIS_CUSTOM_BLOCKS_CONVERTER_TEST_DATA_DIR . $filename );
			$json_a     = json_decode( $json_str, true );
			$typescript = build_typescript_from_json( $json_a );
			$this->assertTrue( true );
		}
	}

	/**
	 * Tests we extract the ComponentName from the json object.
	 */
	function test_get_component_name() {
		$json_str      = file_get_contents( GENESIS_CUSTOM_BLOCKS_CONVERTER_TEST_DATA_DIR . 'example-repeater.json' );
		$json_a        = json_decode( $json_str, true );
		$componentName = get_component_name( $json_a );
		$this->assertSame( 'ExampleRepeater', $componentName );
	}


}
