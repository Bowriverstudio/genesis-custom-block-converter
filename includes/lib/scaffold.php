<?php
/**
 * Scaffold functions.
 *
 * @package GenesisCustomBlocksConverter
 */

namespace GenesisCustomBlocksConverter;

function get_file_scaffold( $json_a ) {
	$componentName = get_component_name( $json_a );
	$fields        = get_genesis_fields( $json_a );

	$scaffold = array();
	$dir      = new \DirectoryIterator( GENESIS_CUSTOM_BLOCKS_TEMPLATE_DIR );
	foreach ( $dir as $fileinfo ) {
		if ( ! $fileinfo->isDot() ) {
			$content = require GENESIS_CUSTOM_BLOCKS_TEMPLATE_DIR . '/' . $fileinfo->getFilename();

			$scaffold[] = $content( $componentName, $fields, $json_a );
		}
	}
	return $scaffold;

	wp_send_json( $fileCount );
}

