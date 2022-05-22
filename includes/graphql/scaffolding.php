<?php
/**
 *
 */
namespace GenesisCustomBlocksConverter;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates an endpoint
 *
 * Uses action - graphql_register_types
 */
add_action(
	'graphql_register_types',
	function() {
		register_graphql_field(
			'RootQuery',
			'brsGenesisScaffolding',
			array(
				'type'    => array( 'list_of' => 'String' ),
				'args'    => array(),
				'resolve' => function( $root, $args, $context, $info ) {
					return brs_genesis_scaffolding();
				},
			)
		);
	}
);




/**
 * Registers the Object Type and the graphql field
 *
 * @param $name - string -> The Name in Camel Case
 * @param $attributes -> array -> attributes for register_graphql_object_type
 * @param $resolvedData -> array -> return for resolve register_graphql_field.
 */
function brs_genesis_scaffolding() {
	$args      = brs_get_wp_query_args();
	$the_query = new \WP_Query( $args );
	$return    = array();

	while ( $the_query->have_posts() ) {
		$the_query->the_post();
		$json = json_decode( get_the_content(), true );

		$key         = array_key_first( $json );
		$fields      = $json[ $key ]['fields'];
		$hasRepeater = false;

		if ( $fields ) {
			foreach ( $fields as $field ) {
				if ( 'repeater' === $field['control'] ) {
					$hasRepeater = true;
				}
			}
		}
		$template  = $hasRepeater ? 'gcfr' : 'gcf';
		$component = brs_convert_to_graphql_name( $json[ $key ]['name'] );

		$return[] = "brs -c  $component -t $template";

		// Register

	}
	wp_reset_postdata();

	return $return;
}


