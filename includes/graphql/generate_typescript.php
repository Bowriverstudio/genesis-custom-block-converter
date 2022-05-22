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
		register_graphql_field(
			'RootQuery',
			'genesisCustomBlockTypescript',
			array(
				'type'    => 'String',
				'args'    => array(),
				'resolve' => function( $source, $args, $context, $info ) {
					$args      = brs_get_wp_query_args();
					$the_query = new \WP_Query( $args );
					$typescript_str = '';
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						$json             = json_decode( get_the_content(), true );
						$typescript_str .= build_typescript_from_json( $json );
					}

					return $typescript_str;
				},
			)
		);
	}
);

