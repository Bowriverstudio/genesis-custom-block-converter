<?php
/**
 * Loops through the config file and builds html to be parsed on the frontend.
 */
namespace GenesisCustomBlocksConverter;

$block_config = block_config();

$field_names = array_keys( $block_config['fields'] );

$attributes = array();
foreach ( $field_names as $field_name ) {
	$field_config = block_field_config( $field_name );
	if ( 'repeater' === $field_config['control'] ) {
		$repeater_attributes = array();
		if ( block_rows( $field_name ) ) {
			while ( block_rows( $field_name ) ) {
				block_row( $field_name );
				$repeater_row_atts = array();
				foreach ( $field_config['settings']['sub_fields'] as $sub_field ) {
					// $sub_attributes[ $sub_field->name ] = block_sub_value( $sub_field->name );
					$repeater_row_atts[ $sub_field->name ] = get_attribute_value( $sub_field->name, $sub_field->control, block_sub_value( $sub_field->name ) );
				}
				$repeater_attributes[] = $repeater_row_atts;
			}
		}
		$attributes[ $field_name ] = $repeater_attributes;
		reset_block_rows( $field_name );
	} else {
		$attributes[ $field_name ] = get_attribute_value( $field_name, $field_config['control'], block_value( $field_name ) );
	}
}

echo '<brs name="' . $block_config['name'] . '" data=\'' . wp_json_encode( $attributes, JSON_UNESCAPED_SLASHES ) . '\'/>';
