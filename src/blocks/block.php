<?php
/**
 * Loops throught the config file and builds html to be parsed.
 */
namespace GenesisCustomBlocksConverter;

$block_config = block_config();

$field_names = array_keys($block_config['fields']);

$attributes = array();
$inner_html = '';
foreach( $field_names as $field_name){
	$field_config = block_field_config($field_name);
	if( 'repeater' === $field_config["control"]){
		if ( block_rows( $field_name ) ){
			while ( block_rows( $field_name ) ){
				block_row( $field_name );
				$sub_attributes = array();
				foreach( $field_config['settings']['sub_fields'] as $sub_field){
					$sub_attributes[$sub_field->name] = array('value' => block_sub_value( $sub_field->name ), 'control' => $sub_field->control);
				}
				$inner_html .= brs_build_block_html($field_name, $sub_attributes);
			}
		}
		reset_block_rows( $field_name  );
	} else {
		$attributes[$field_name] = array('value' => block_value( $field_name ), 'control' => $field_config["control"]);
	}
}

echo brs_build_block_html($block_config['name'], $attributes, $inner_html);
