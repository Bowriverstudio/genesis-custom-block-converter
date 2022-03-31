<?php
namespace GenesisCustomBlocksConverter;

/**
 * Loops throught all genesis_custom_block types and builds a GraphQL Object and Endpoint for testings.  Including repeater.
 * 
 * Uses action - graphql_register_types
 */
add_action( 'graphql_register_types', function() {

    $args = brs_get_wp_query_args();
    $the_query = new \WP_Query( $args );
    $names = array();
  
    while ( $the_query->have_posts() ) {
      $the_query->the_post(); 
      $json = json_decode(get_the_content(), true);
      $key = array_key_first($json);

      $attributes = brs_get_default_attributes();
      $fields = $json[$key]['fields'];

      if( $fields){
        foreach($fields as $field){
          if( 'repeater' === $field['control'] ){
            $subAttributes = brs_get_default_attributes();
            foreach( $field['sub_fields'] as $subField ){
              $subFieldName = str_replace('-', '_', $subField['name']);
              $subAttributes[$subFieldName] = brs_getFieldAttributes($subField);
            }
            $subFieldGraphName = brs_convert_to_graphql_name($field['name']);
            brs_register_graphql_genesis_custom_block($subFieldGraphName, $subAttributes);
          } else {
            // Not a Repeater
            $fieldName = str_replace('-', '_', $field['name']);
            $attributes[$fieldName] = brs_getFieldAttributes($field);
          }
        }
      }

      // Register 
      $name = brs_convert_to_graphql_name($json[$key]['name']);
      $graphName = brs_convert_to_graphql_name($name);
      brs_register_graphql_genesis_custom_block($graphName, $attributes);
    }
    wp_reset_postdata();
});




/**
 * Registers the Object Type and the graphql field
 * 
 * @param $name - string -> The Name in Camel Case
 * @param $attributes -> array -> attributes for register_graphql_object_type
 * @param $resolvedData -> array -> return for resolve register_graphql_field.
 *  
 */
function brs_register_graphql_genesis_custom_block($name, $attributes){

  $object_type = brs_get_object_type_name($name);

  register_graphql_object_type(
    $object_type,[
      'description' => __( "Genesis Custom Block Type - attributes for $name", 'your-textdomain' ),
      'fields' =>  $attributes
      ]
  );

  $fieldName = 'brsTest'.$name;

  // var_dump($resolvedData);
  // exit();

  register_graphql_field( 'RootQuery', $fieldName, [
		'type' =>   $object_type,
		'args' => [],
		'resolve' => function( $root, $args, $context, $info ) {

      $resolvedData = brs_get_fields_from_object_type($info->returnType);
			return $resolvedData;
		},
	]);

}

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

function brs_get_fields_from_object_type($object_type){


  $args = brs_get_wp_query_args();
  $the_query = new \WP_Query( $args );

  while ( $the_query->have_posts() ) {
    $the_query->the_post(); 
    $json = json_decode(get_the_content(), true);
    $key = array_key_first($json);
    $name = brs_convert_to_graphql_name($json[$key]['name']);
    $post_object_name = brs_get_object_type_name($name);

    $fields = $json[$key]['fields'];

    if( $post_object_name == $object_type){
      $resolvedData = brs_getResolveDataForFields($json[$key]['name'], $fields);
      wp_reset_postdata();
      return $resolvedData;
    }

    // Check if object_type is a subtype of the repeater control.
    if( $fields){
      foreach($fields as $field){
        if( 'repeater' === $field['control'] ){
          $subFieldName = brs_convert_to_graphql_name($field['name']);
          $_object_type = brs_get_object_type_name($subFieldName);
          if( $_object_type == $object_type){
            wp_reset_postdata();
            return brs_resolveDataForRepeater($field);
          }
        }
      }
    }
  }
  graphql_debug( $object_type, [ 'type' =>  'object_type NOT FOUND' ] );
}



function brs_getResolveDataForFields($tagName, $fields){
  $fieldControls = array();
  $attributes = brs_get_default_attributes();
  $children = '';

  foreach( $fields as $field ){

    if( 'repeater' === $field['control'] ){
      $repeaterData = brs_resolveDataForRepeater($field);
      $children .= $repeaterData['toString'] . $repeaterData['toString'] . $repeaterData['toString'];

   } else {
      // Not a Repeater
      $fieldName = str_replace('-', '_', $field['name']);
      $fieldControls[$fieldName] = $field['control'];
      $attributes[$fieldName] = brs_getFieldAttributes($field);
    }
  }

  $resolvedData = brs_get_resolvedData( $tagName, $attributes, $fieldControls, $children);
  $innerHTML .= $resolvedData['toString'] . $resolvedData['toString'] . $resolvedData['toString'];
  return  $resolvedData;
}

/**
 * Builds the ResolveData for a repeater Field.
 * 
 * @param $field -> Array -> Defined by Genesis-Custom-Blocks.
 */
function brs_resolveDataForRepeater($field){

  $subFieldControls = array();
  $subAttributes = brs_get_default_attributes();
  foreach( $field['sub_fields'] as $subField ){
    $subFieldName = str_replace('-', '_', $subField['name']);
    $subFieldControls[$subFieldName] = $subField['control'];
    $subAttributes[$subFieldName] = brs_getFieldAttributes($subField);
  }

  $resolvedData = brs_get_resolvedData( $field['name'], $subAttributes, $subFieldControls);
  $innerHTML .= $resolvedData['toString'] . $resolvedData['toString'] . $resolvedData['toString'];

  return $resolvedData;
}

/**
 * Builds Test Data for resolvedData
 * 
 * 
 */
function brs_get_resolvedData($tag, $attributes, $controls, $children = '' ){
  
  $blockAttributes = array(); 
  $graphQLAttributes = array();
  if( $attributes ) {
    foreach(  $attributes as $attributeName => $attributeValues){

      if( 'id' === $attributeName ){
        $graphQLAttributes[$attributeName] = 'Not Used ID';
      } else if( 'toString' === $attributeName ){
        $graphQLAttributes[$attributeName] = 'Temp will be overwritten';
      } else {

        $value = "Unknown $controls[$attributeName]";
        switch( $controls[$attributeName] ){
          case 'image':
            $value = 'https://placebear.com/640/360';
            break;
          case 'text':
            $value = $attributeValues['description'];
            break;
          case 'textarea':
            $value = str_repeat($attributeValues['description']. ' ', 10);
            break;
          case 'toggle':
            $value = '1';
            break;
          case 'rich_text':
            $value = '&lt;p&gt;Text&lt;br /&gt;&lt;strong&gt;Line 2 Bold&lt;br /&gt;&lt;/strong&gt;&lt;em&gt;Line 3 Italic&lt;/em&gt;&lt;br /&gt;Reference to &lt;a href=&quot;http://www.example.com&quot; data-type=&quot;URL&quot; data-id=&quot;www.example.com&quot;&gt;Example&lt;/a&gt;&lt;br /&gt;Reference to &lt;a href=&quot;'.site_url().'/home-page/&quot; data-type=&quot;page&quot; data-id=&quot;81&quot;&gt;Internal Link&lt;/a&gt;&lt;/p&gt;\n&lt;p&gt;Next Paragraph&lt;/p&gt;';
            break;
          case 'url':
            $value = 'https://example.com/';
            break;
          default: 
            graphql_debug($controls[$attributeName], [ 'type' =>  'UNKOWN TYPE' ] );

        }

        $graphQLAttributes[$attributeName] = $value;
        $blockAttributes[$attributeName]['value'] = $value; 
      }
    }
    $graphQLAttributes['toString'] = brs_build_block_html($tag, $blockAttributes, $children);
  }
   
  return $graphQLAttributes;
}

/**
 * Graphql Object Type Name.  Prepends BRS_ to the name;
 */
function brs_get_object_type_name($name){
  return "BRS_$name";
}

/**
 * returns the attributes for one field for register_graphql_object_type.
 * @param $field array of type Field for genesis_custom_block.
 * 
 * @return array [type, description] 
 */
function brs_getFieldAttributes($field){
  $atts = array();
  $atts['type'] =  brs_get_graphql_type($field['control']);
  $atts['description'] = $field['label'] . ' ' .  $field['help'];
  return $atts;
}

/**
 * Returns the Graphql type based on the control - so far due to gutenberg everything is a string.
 */
function brs_get_graphql_type($control){
  return 'String';
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

/**
 * Returns the default attriibutes for each object
 */
function brs_get_default_attributes(){
  return [
    'id' => 
    [ 'type' =>[ 'non_null' => 'String' ], 
      'description' => __( 'Not Used ID', 'your-textdomain' ),
    ],
    'toString' => 
    [ 'type' => [ 'non_null' => 'String' ], 
      'description' => __( 'test - to html string', 'your-textdomain' ),
    ],         
  ];
}