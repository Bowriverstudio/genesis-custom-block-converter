<?php
/**
 * Utility functions used for Typescript Generation.
 *
 * @package GenesisCustomBlocksConverter
 */

namespace GenesisCustomBlocksConverter;

/**
 * Builds the Typescript object.
 *
 * @param Array $json associated array.
 *
 * @return string
 */
function build_typescript_from_json( $json ) {
	$key           = array_key_first( $json );
	$componentName = get_component_name( $json );
	$typeScript    = "export type $componentName" . "Props = {\n";
	$fields        = $json[ $key ]['fields'];
	if ( $fields ) {
		foreach ( $fields as $field ) {
			if ( 'repeater' === $field['control'] ) {
				$typeScript .= build_typescript_label( $field );
				$typeScript .= get_escaped_field_name( $field['name'] ) . ':[{';
				foreach ( $field['sub_fields'] as $subfield ) {
					$typeScript .= build_typescript_label( $subfield );
					$typeScript .= build_typescript_field( $subfield );
				}
				$typeScript .= '}]';
			} else {
				$typeScript .= build_typescript_label( $field );
				$typeScript .= build_typescript_field( $field );
			}
		}
	}
	$typeScript .= '};';
	return $typeScript;
}


/**
 * Builds the comments for the header of a type script prop.
 *
 * @param array $field
 * @return string
 */
function build_typescript_label( $field ) {
	$typescript_var  = "/**\n";
	$typescript_var .= '* ' . $field['label'] . "\n";
	$typescript_var .= "*/ \n";

	return $typescript_var;
}
/**
 * From the field definition generate the typescript data
 *
 * @param array $field
 * @return string
 */
function build_typescript_field( $field ) {
	$typescript_var  = get_escaped_field_name( $field['name'] );
	$typescript_var .= ':' . get_control_typescript_type( $field['control'] );
	$typescript_var .= ";\n";
	return $typescript_var;
}

/**
 * Escapes the field name if required.
 *
 * @param String $name
 * @return string
 */
function get_escaped_field_name( $name ) {
	return strpos( $name, '-' ) > 0 ? '"' . $name . '"' : $name;
}

/**
 * Gets the Control typescript type.
 */
function get_control_typescript_type( $controlType ) {
	switch ( $controlType ) {
		case 'classic_text':
		case 'image':
		case 'color':
		case 'text':
		case 'textarea':
		case 'rich_text':
		case 'url':
		case 'email':
			return 'string';
		case 'toggle':
			return 'boolean';
		case 'number':
			return 'number';
		default:
			return "$controlType Unknown";
	}
}



