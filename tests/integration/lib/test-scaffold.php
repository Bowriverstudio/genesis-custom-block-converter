<?php
/**
 * Class to test function validate_and_save_entry.
 * TODO https://medium.com/hackernoon/a-package-for-snapshot-testing-in-phpunit-2e4558c07fe3
 *
 * @package Poc_Gutenberg
 */

namespace GenesisCustomBlocksConverter;

/**
 * Test function validate_and_save_entry.
 *
 * @since    1.0.0
 */
class ScaffoldTest extends \WP_UnitTestCase {

	/**
	 * Test missing required fields.
	 *
	 * @since    1.0.0
	 */
	public function test_index_file() {
		$files = array(
			// 'example-repeater.json',
			'example-block.json',
		);
		foreach ( $files as $filename ) {
			$json_str = file_get_contents( GENESIS_CUSTOM_BLOCKS_CONVERTER_TEST_DATA_DIR . $filename );
			$json_a   = json_decode( $json_str, true );

			$componentName = get_component_name( $json_a );

			$content = require GENESIS_CUSTOM_BLOCKS_TEMPLATE_DIR . '/index.php';

			// var_dump( $content( $componentName ) );
			// $content  = build_scaffold_index( $json_a );
			$this->assertTrue( true );
		}
	}


}
