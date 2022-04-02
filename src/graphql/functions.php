<?php
namespace GenesisCustomBlocksConverter;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/genesis_custom_block.php';
require_once dirname( __FILE__ ) . '/scaffolding.php';

/**
 * Returns the Args used to get all genesis_custom_block(s) from the DB via WP_Query
 */
function brs_get_wp_query_args(){
  return array(
    'post_type'=> 'genesis_custom_block',
    'post_status' => 'publish',
    'posts_per_page' => -1 
  );
}


/**
 * Strips - and camel case the name
 * 
 * @param $name - string
 * 
 * @return string ie: example-block -> ExampleBlock
 */
function brs_convert_to_graphql_name($name){
  // example-block -> ExampleBlock
  $name = explode('-', $name);
  $name = implode('',array_map('ucfirst', $name));
  return $name;
}