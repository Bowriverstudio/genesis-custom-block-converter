<?php
/**
 * Utility functions used throughout the plugin.
 *
 * @package GenesisCustomBlocksConverter
 */

namespace GenesisCustomBlocksConverter;

/**
 * Returns the Args used to get all genesis_custom_block(s) from the DB via WP_Query
 */
function brs_get_wp_query_args() {
	return array(
		'post_type'      => 'genesis_custom_block',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'order'          => 'ASC',
		'orderby'        => 'title',
	);
}


/**
 * Loops through all
 *
 * @param boolean $include_repeater_blocks Includes the repeater blocks.
 * @return GenesisCustomBlockSummaryObject
 */
function get_genesis_block_summary( $include_repeater_blocks = false ) {
	$args      = brs_get_wp_query_args();
	$the_query = new \WP_Query( $args );
	$list      = array();
	while ( $the_query->have_posts() ) {
		$the_query->the_post();
		$json_a = json_decode( get_the_content(), true );

		$slug          = get_post_field( 'post_name', get_the_ID() );
		$componentName = get_component_name( $json_a );
		$list[]        = array(
			'slug'                => $slug,
			'componentName'       => $componentName,
			'parentComponentName' => '',
		);

		if ( $include_repeater_blocks ) {
			$repeater_field = get_repeater_field( $json_a );
			if ( $repeater_field ) {
				// graphql_debug( $repeater_field, array( 'type' => 'REPEATER_JSON' ) );
				// graphql_debug( $repeater_field['name'], array( 'type' => 'REPATER NAME' ) );
				$repeater_slug = $slug . '-' . $repeater_field['name'];
				$list[]        = array(
					'slug'                => $repeater_slug,
					'componentName'       => get_component_name_from_string( $repeater_slug ),
					'parentComponentName' => $componentName,
				);
			}
		}
	}

	return $list;
}

/**
 * Loads the json for specified slug.
 *
 * @param string $slug Genesis Custom Block slug.
 *
 * @return array | null
 */
function get_json_from_slug( $slug ) {
	$args      = array(
		'post_type'      => 'genesis_custom_block',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'name'           => $slug,
	);
	$the_query = new \WP_Query( $args );
	while ( $the_query->have_posts() ) {
		$the_query->the_post();
		$json_a = json_decode( get_the_content(), true );
		return( $json_a );
	}

	return null;
}

/**
 * Returns the component name from json.
 *
 * @param array $json_a json associated array.
 *
 * @return string ie: example-block -> ExampleBlock
 */
function get_component_name( $json_a ) {
	$key  = array_key_first( $json_a );
	$name = $json_a[ $key ]['name'];

	// example-block -> ExampleBlock.
	return get_component_name_from_string( $name );
}

/**
 * Strips - and camel case the name
 *
 * @param string $name ie example-block.
 *
 * @return string ie: example-block -> ExampleBlock
 */
function get_component_name_from_string( string $name ) {
	$name = explode( '-', $name );
	$name = implode( '', array_map( 'ucfirst', $name ) );
	return $name;
}

/**
 * Gets the fields.
 *
 * @param array $json_a json associated array.
 *
 * @return array
 */
function get_genesis_fields( $json_a ) {
	$key = array_key_first( $json_a );
	return $json_a[ $key ]['fields'];
}


/**
 * Gets the repeater field if exists.
 *
 * @param array $json_a the JSON for this genesis block.
 *
 * @return array|false
 */
function get_repeater_field( $json_a ) {
	$fields = get_genesis_fields( $json_a );
	foreach ( $fields as $field ) {
		if ( 'repeater' === $field['control'] ) {
			return $field;
		}
	}
	return false;
}

/**
 * Builds the HTML for a block.
 *
 * @param string       $slug The WordPress slug.
 * @param array        $attributes - Array.
 *           [key] -> attribute
 *           [value] -> Value of Attribute
 *           [control]  -> if image it gets the src and alt.
 * @param string|false $children - either the html of the children or false.
 *
 * @return string The HTML.
 */
function brs_build_block_html( string $slug, $attributes, $children = false ) {
	$html = ' <brs tag-name="' . $slug . '"';
	foreach ( $attributes as $attribute => $value ) {
		switch ( $value['control'] ) {
			case 'image':
				$html .= ' ' . $attribute . " = '";
				if ( str_contains( $value['value'], 'http' ) ) {
					// Image is a url
					$html .= $value['value'];
					$html .= '\'';
				} else {
					// Image contains the id
					$image_attributes = wp_get_attachment_image_src( $value['value'], 'full' );
					$html            .= $image_attributes[0];
					$html            .= '\'';
				}
				break;
			case 'toggle':
				$html .= ' ' . $attribute . " = '" . ( $value['value'] ? 'true' : 'false ' ) . '\'';
				break;
			case 'number':
			case 'rich_text':
			case 'classic_text':
			default:
				$html .= ' ' . $attribute . " = '";
				$html .= brs_clean_string( $value['value'] );
				$html .= '\'';
		}
	}
	if ( $children ) {
		$html .= '>';
		$html .= $children;
		$html .= '</brs>';
	} else {
		// Self closing tags are not processed properly html-react-parser.
		$html .= '>';
		$html .= '</brs>';
	}
	return $html;
}

/**
 * Removes trailing whitespace and html from string.
 *
 * @param string $str input string.
 *
 * @return string cleaned string.
 */
function brs_clean_string( $str ) {
	// Remove all trailing whitespaces.
	// By default rtrim will remove all trailing whitespaces (including space, tab, newline, etc.).
	$str = rtrim( $str );

	// Escaping for HTML blocks.
	return esc_html( $str );
}


/**
 * Get the value based on the inputs.
 *
 * @param string $field_name the field name.
 * @param string $control the control.
 * @param string $block_value the value.
 *
 * @return string|boolean|number
 */
function get_attribute_value( $field_name, $control, $block_value ) {
	if ( 'the_title' === $field_name || 'the-title' === $field_name ) {
		// Default value of the_title is "get_the_title()".
		return '' === $block_value ? get_the_title() : $block_value;
	}

	switch ( $control ) {
		case 'image':
			$image_attributes = wp_get_attachment_image_src( $block_value, 'full' );
			return $image_attributes[0];
		case 'toggle':
		case 'number':
			return $block_value;
		case 'rich_text':
		case 'classic_text':
		default:
			return brs_clean_string( $block_value );
	}
}


function brs_update_or_insert_genesis_custom_block( array $json_a ) {
	$key        = array_key_first( $json_a );
	$name       = $json_a[ $key ]['name'];
	$post_title = $json_a[ $key ]['title'];
	// Does Post Exists
	$args = array(
		'post_name'   => $name,
		'post_type'   => 'genesis_custom_block',
		'post_status' => 'publish',
	);

	$post_data = array(
		'post_type'    => 'genesis_custom_block',
		'post_title'   => $post_title,
		'post_status'  => 'publish',
		'post_name'    => $name,
		'post_content' => json_encode( $json_a ),
	);

	$posts = \get_posts( $args );
	if ( $posts ) {
		$post_data['ID'] = $posts[0]->ID;
		return \wp_update_post( $post_data );
	} else {
		// Insert Post.
		return \wp_insert_post( $post_data );
	}

}
