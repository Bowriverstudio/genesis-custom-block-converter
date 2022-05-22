<?php
namespace GenesisCustomBlocksConverter;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Registers the Object Type and the graphql field
 *
 * @param $name - string -> The Name in Camel Case
 * @param $attributes -> array -> attributes for register_graphql_object_type
 * @param $resolvedData -> array -> return for resolve register_graphql_field.
 */

add_action(
	'graphql_register_types',
	function() {
		register_graphql_object_type(
			'GenesisCustomBlockSummaryObject',
			array(
				'description' => __( 'Component Summary', 'bsr' ),
				'fields'      => array(
					'slug'          => array(
						'type'        => 'String',
						'description' => 'The Slug',
					),
					'componentName' => array(
						'type'        => 'String',
						'description' => 'Component Name',
					),
				),
			)
		);

		register_graphql_field(
			'RootQuery',
			'genesisCustomBlockSummary',
			array(
				'description' => __( 'Return a list of the type genesis-custom-blocks ComponentName', 'brs' ),
				'type'        => array( 'list_of' => 'genesisCustomBlockSummaryObject' ),
				'args'        => array(),
				'resolve'     => function( $source, $args, $context, $info ) {
					return get_genesis_block_summary();
				},
			)
		);
	}
);

