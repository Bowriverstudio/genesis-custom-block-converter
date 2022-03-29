<?php
namespace GenesisCustomBlocksConverter;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/graphql/functions.php';

/**
 * Filters the genesis_custom_blocks_template_path.
 */
add_filter( 'genesis_custom_blocks_template_path', function( $path, $template_names ) {
  
	// This command is causing a bug.
    // $blocks = gcb_get_block_names();

    foreach ( (array) $template_names as $template_name ) {
        if ( 'blocks/blocks.json' == $template_name ) {
            return $path ;
        }

		return __DIR__ ;
    }
}, 10, 2 );

/**
 * Builds the HTML for a block.
 * $blockName - String
 * $attributes - Array.  
 *    [key] -> attribute
 *    [value] -> Value of Attribute
 *    [control]  -> if image it gets the src and alt.
 */
function brs_build_block_html($blockName, $attributes, $children = false){
    $html = "<" .$blockName;
    foreach($attributes as $attribute => $values){
        $html .= " ".$attribute."='";
        if( $values['control'] == 'image' ){
            $image_attributes = wp_get_attachment_image_src( $values['value'], 'full' );
            $html .= $image_attributes[0];
        } else {
            $html .= brs_clean_string($values['value']);
        }
        $html .= '\'';
    }
    if( $children ){
        $html .= ">";
        $html .= $children;
        $html .= "</" .$blockName .">";
    } else {
        // Self closing tags are not processed properly html-react-parser
        // $html .= " />";
        $html .= ">";
        $html .= "</" .$blockName .">";
    }
    return $html;
}

/**
 * Removes trailing whitespace and html from string
 */
function brs_clean_string($str){
    // Remove all trailing whitespaces
    // By default rtrim will remove all trailing whitespaces (including space, tab, newline, etc.)
    $str = rtrim( $str) ;

    // Escaping for HTML blocks.
    return esc_html($str);
}



/**
 * Returns array of all the custom blocks.
 * Not currently used.
 */
function gcb_get_block_names(){
	$args = array(
		'post_type'=> 'genesis_custom_block',
		'post_status' => 'publish',
		'posts_per_page' => -1 // this will retrive all the post that is published 
	);

	$the_query = new \WP_Query( $args );
	$names = array();

	while ( $the_query->have_posts() ) {
	   $the_query->the_post(); 
	   $json = json_decode(get_the_content(), true);
	   $key = array_key_first($json);
       $names[] = $json[$key]['name']; // print array
   }
   wp_reset_postdata();
//    wp_reset_postdata();
   return $names;
}