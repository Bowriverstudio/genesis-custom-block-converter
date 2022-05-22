<?php
/**
 * Filters the genesis_custom_blocks_template_path to `blocks`.
 */
namespace GenesisCustomBlocksConverter;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filters the genesis_custom_blocks_template_path.
 */
add_filter(
	'genesis_custom_blocks_template_path',
	function( $path, $template_names ) {

		// This command is causing a bug.
		// $blocks = gcb_get_block_names();

		foreach ( (array) $template_names as $template_name ) {
			if ( 'blocks/blocks.json' == $template_name ) {
				return $path;
			}

			return __DIR__;
		}
	},
	10,
	2
);


