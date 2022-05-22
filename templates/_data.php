<?php
namespace GenesisCustomBlocksConverter;

function get_sample_data( $field ) {
	if ( 'the_title' === $field['name'] ) {
		return 'The Post Title';
	}

	switch ( $field['control'] ) {
		case 'rich_text':
		case 'classic_text':
			return brs_clean_string( 'Not Supported' );
			// return brs_clean_string( '.&lt;/p&gt;\n&lt;h3&gt;&lt;i&gt;- H3 O&lt;/i&gt;&lt;/h3&gt; &lt;p&gt;Text&lt;br /&gt;&lt;strong&gt;Line 2 Bold&lt;br /&gt;&lt;/strong&gt;&lt;em&gt;Line 3 Italic&lt;/em&gt;&lt;br /&gt;Reference to &lt;a href=&quot;http://www.example.com&quot; data-type=&quot;URL&quot; data-id=&quot;www.example.com&quot;&gt;Example&lt;/a&gt;&lt;br /&gt;Reference to &lt;a href=&quot;' . site_url() . '/home-page/&quot; data-type=&quot;page&quot; data-id=&quot;81&quot;&gt;Internal Link&lt;/a&gt;&lt;/p&gt;\n&lt;p&gt;Next Paragraph&lt;/p&gt;' );

		case 'image':
			return 'https://i0.wp.com/9to5mac.com/wp-content/uploads/sites/6/2021/09/iPhone-13-macro-photography.jpg';

		case 'text':
		case 'textarea':
			return $field['description'] ? $field['description'] : 'Text Field';

		case 'toggle':
			return true;

		case 'url':
			return 'https://example.com/';
		case 'number':
			return 3;

		case 'color':
			return '#FF5733';

		case 'email':
			return 'info@example.com';
	}
	return '"Unkonon  ' . $field['control'] . '"';
}


return function( string $componentName, array $fields, array $json_a ) {
	$typescriptProp = $componentName . 'Props';
	$key            = array_key_first( $json_a );
	$name           = $json_a[ $key ]['name'];

	// Build Mock Data.
	$data = array();
	foreach ( $fields as $field ) {
		if ( 'repeater' === $field['control'] ) {
			$subdata = array();
			for ( $i = 0; $i < 3; $i++ ) {
				$repeater = array();
				foreach ( $field['sub_fields'] as $subfield ) {
					$repeater[ $subfield['name'] ] = get_sample_data( $subfield );
				}
				$subdata[ $i ] = $repeater;
			}
			$data[ $field['name'] ] = $subdata;
		} else {
			$data[ $field['name'] ] = get_sample_data( $field );
		}
	}

	$content = '<brs name="' . $name . '" data=\'' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES ) . '\'/>';

	 return array(
		 'name'    => 'tests/_data.ts',
		 'content' => <<<_END

     export const exampleHtml = `$content`

_END,
	 );
};
