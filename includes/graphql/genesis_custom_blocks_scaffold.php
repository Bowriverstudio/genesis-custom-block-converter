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
			'GenesisCustomBlockFileScaffoldObject',
			array(
				'description' => __( 'Component Summary', 'bsr' ),
				'fields'      => array(
					'name'    => array(
						'type'        => 'String',
						'description' => 'File Name',
					),
					'content' => array(
						'type'        => 'String',
						'description' => 'File Content',
					),
				),
			)
		);

		register_graphql_field(
			'RootQuery',
			'genesisCustomBlockFileScaffold',
			array(
				'description' => __( 'Return a list of the scaffolding', 'brs' ),
				'type'        => array( 'list_of' => 'genesisCustomBlockFileScaffoldObject' ),
				'args'        => array(
					'slug' => array(
						'type'        => array( 'non_null' => 'String' ),
						'description' => __( 'The Slug', 'your-textdomain' ),
					),
				),
				'resolve'     => function( $source, $args, $context, $info ) {
					$json_a = get_json_from_slug( $args['slug'] );
					$data = get_file_scaffold( $json_a );

					return $data;
				},
			)
		);
	}
);

