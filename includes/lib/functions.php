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
 * @return GenesisCustomBlockSummaryObject
 */
function get_genesis_block_summary() {
	$args      = brs_get_wp_query_args();
	$the_query = new \WP_Query( $args );
	$list      = array();
	while ( $the_query->have_posts() ) {
		$the_query->the_post();
		$json_a = json_decode( get_the_content(), true );

		$list[] = array(
			'slug'          => get_post_field( 'post_name', get_the_ID() ),
			'componentName' => get_component_name( $json_a ),
		);
	}

	return $list;
}

/**
 * Loads the json for specified slug.
 *
 * @param string slug
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
 * Strips - and camel case the name
 *
 * @param array $json_a
 *
 * @return string ie: example-block -> ExampleBlock
 */
function get_component_name( $json_a ) {
	$key  = array_key_first( $json_a );
	$name = $json_a[ $key ]['name'];

	// example-block -> ExampleBlock
	$name = explode( '-', $name );
	$name = implode( '', array_map( 'ucfirst', $name ) );
	return $name;
}

function get_genesis_fields( $json_a ) {
	$key = array_key_first( $json_a );
	return $json_a[ $key ]['fields'];
}







/**
 * Builds the HTML for a block.
 * $blockName - String
 * $attributes - Array.
 *    [key] -> attribute
 *    [value] -> Value of Attribute
 *    [control]  -> if image it gets the src and alt.
 */
function brs_build_block_html( $blockName, $attributes, $children = false ) {
	$html = ' < ' . $blockName;
	foreach ( $attributes as $attribute => $values ) {
		$html .= ' ' . $attribute . " = '";
		if ( $values['control'] == 'image' ) {
			$image_attributes = wp_get_attachment_image_src( $values['value'], 'full' );
			$html            .= $image_attributes[0];
		} else {
			$html .= brs_clean_string( $values['value'] );
		}
		$html .= '\'';
	}
	if ( $children ) {
		$html .= '>';
		$html .= $children;
		$html .= '</' . $blockName . '>';
	} else {
		// Self closing tags are not processed properly html-react-parser
		// $html .= " / > ";
		$html .= '>';
		$html .= '</' . $blockName . '>';
	}
	return $html;
}

/**
 * Removes trailing whitespace and html from string
 */
function brs_clean_string( $str ) {
	// Remove all trailing whitespaces
	// By default rtrim will remove all trailing whitespaces (including space, tab, newline, etc.)
	$str = rtrim( $str );

	// Escaping for HTML blocks.
	return esc_html( $str );
}


function get_attribute_value( $field_name, $control, $block_value ) {
	if ( 'the_title' === $field_name || 'the-title' === $field_name ) {
		// Default value of the_title is "get_the_title()"
		return $block_value != '' ? $block_value : get_the_title();
	}

	switch ( $control ) {
		case 'rich_text':
		case 'classic_text':
			return 'Not Supported'; // wpautop( brs_clean_string( block_value( $field_name ) ) );
		case 'image':
			$image_attributes = wp_get_attachment_image_src( $block_value, 'full' );
			return $image_attributes[0];
		case 'toggle':
		case 'number':
			return $block_value;
		default:
			return brs_clean_string( $block_value );
	}
}
