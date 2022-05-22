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

	// $checkFiles = scandir( GENESIS_CUSTOM_BLOCKS_TEMPLATE_DIR ); // scan folder content
	// $fileCount  = count( $checkFiles ); // count number of files in the directory
	// $i          = 0; // set for iteration;
	// while ( $i < $fileCount ) {
	// $file = $checkFiles[ $i ]; // each file is stored in an array ...
	// if ( $file = '.' || $file = '..' ) {
	// } else {
	// wp_send_json( $file );
	// }
	// }

	wp_send_json( $fileCount );
}

