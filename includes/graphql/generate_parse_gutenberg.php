<?php
namespace GenesisCustomBlocksConverter;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Registers the Object Type and the graphql field
 *
 * @param $name - string -> The Name in Camel Case
 * @param $attributes -> array -> attributes for register_graphql_object_type
 * @param $resolvedData -> array -> return for resolve register_graphql_field.
 */

add_action(
	'graphql_register_types',
	function() {
		register_graphql_field(
			'RootQuery',
			'genesisGenerateParseGutenberg',
			array(
				'description' => __( 'Return a list of the type genesis-custom-blocks ComponentName', 'brs' ),
				'type'        => 'String',
				'args'        => array(),
				'resolve'     => function( $source, $args, $context, $info ) {
					$blocks = get_genesis_block_summary();

					// Builds dynamic parts of the file.
					$imports = array();
					$cases = array();
					$componentNameProps = array();

					foreach ( $blocks as $block ) {
						$componentName = $block['componentName'];
						$_componentNameProps = $componentName . 'Props';
						$componentNameProps[] = $_componentNameProps;
						$slug = $block['slug'];
						$imports[] = "const  $componentName = dynamic(() => import('components/Gutenberg/$componentName'));";
						$cases[] = "case '$slug':";
						$cases[] = "return < $componentName data={data as $_componentNameProps} />;";
					}

					$import_str = implode( "\n", $imports );
					$cases_str = implode( "\n", $cases );
					$componentNameProps_str = implode( ',', $componentNameProps );

					return <<<_END
					import dynamic from 'next/dynamic';

					$import_str
					import { $componentNameProps_str } from 'client';

					type Props = {
					name: string;
					data: any;
					};
					/**
					 * Parses HTML with options.
					 */
					export default function parseGutenberg({ name, data }: Props) {
					switch (name) {
						$cases_str
					}
					return null;
					}
					_END;
				},
			)
		);
	}
);

