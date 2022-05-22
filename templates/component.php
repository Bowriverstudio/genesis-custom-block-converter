<?php


function get_table_row( $variable_name, $control, $value = null, $bg = false ) {
	$actual_field_name = str_replace( '-', '_', $variable_name );

	$tableRow  = '<tr ' . ( $bg ? ' className="bg-blue-50"' : '' ) . '>';
	$tableRow .= '<td className="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8">';
	$tableRow .= $actual_field_name;
	$tableRow .= '</td>';
	$tableRow .= '<td className="whitespace-nowrap px-3 py-4 text-sm text-gray-500">';
	$tableRow .= $control;
	$tableRow .= '</td>';
	$tableRow .= '<td className="whitespace-nowrap px-3 py-4 text-sm text-gray-500">';
	$tableRow .= ( $value ? $value : '{ JSON.stringify(' . $actual_field_name . ', null, 4) }' );
	$tableRow .= '</td>';
	$tableRow .= '</tr>';
	return $tableRow;
}

function get_field_props( $name ) {
	if ( strpos( $name, '-' ) > 0 ) {
		return '"' . $name . '": ' . str_replace( '-', '_', $name );
	}
	return $name;
}

return function( string $componentName, array $fields, array $json_a ) {
	$typescriptProp = $componentName . 'Props';

	$tableBodyHTML = '';

	foreach ( $fields as $field ) {
		if ( 'repeater' === $field['control'] ) {
			$tableBodyHTML .= get_table_row( $field['name'], $field['control'], '-' );
			foreach ( $field['sub_fields'] as $subfield ) {
				$subfield_props[] = get_field_props( $subfield['name'] );
			}
			$repeater_name  = str_replace( '-', '_', $field['name'] );
			$tableBodyHTML .= "{ $repeater_name && " . $repeater_name . '.map((item) => {';
			$tableBodyHTML .= 'const { ' . implode( ',', $subfield_props ) . ' } = item;';
			$tableBodyHTML .= 'return ( <>';
			foreach ( $field['sub_fields'] as $subfield ) {
				$tableBodyHTML .= get_table_row( $subfield['name'], $subfield['control'], null, true );
			}
			$tableBodyHTML .= ' </>)})}';
		} else {
			$tableBodyHTML .= get_table_row( $field['name'], $field['control'] );
		}
		// Add Props
		$props[] = get_field_props( $field['name'] );
	}

	$propnames = implode( ',', $props );

	 return array(
		 'name'    => $componentName . '.tsx',
		 'content' => <<<_END
import React from 'react';

import Image from "next/image"

import { $typescriptProp } from 'client';

type Props = {
  data: $typescriptProp;
};

/**
 * $componentName
 */
const $componentName = ({ data }: Props) => {
	const { $propnames } = data;

  return <>
  <h2 className="text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
  	$componentName
  </h2>

  <div className="overflow-hidden shadow-sm ring-1 ring-black ring-opacity-5 mt-5">
	<table className="min-w-full divide-y divide-gray-300">
		<thead className="bg-gray-50">
			<tr>
				<th
					scope="col"
					className="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 lg:pl-8"
				>
					Variable Name
				</th>
				<th
					scope="col"
					className="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
				>
					Control Type
				</th>
				<th
					scope="col"
					className="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
				>
					Value
				</th>
			</tr>
		</thead>
		<tbody className="divide-y divide-gray-200 bg-white">
			$tableBodyHTML
		</tbody>
	</table>
</div>

<h2 className="text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
Images Sample (Will be dynamic at one point)
</h2>
<div className="h-64 w-96 relative">
	<Image
		src="https://i0.wp.com/9to5mac.com/wp-content/uploads/sites/6/2021/09/iPhone-13-macro-photography.jpg"
		alt="Picture of the author"
		layout="fill"
		objectFit="cover"
		className="rounded-full"
	/>
	</div>
<div>
	<div className="alignfull">
		<div className="h-32 w-full lg:h-48 relative">
		<Image
			src="https://i0.wp.com/9to5mac.com/wp-content/uploads/sites/6/2021/09/iPhone-13-macro-photography.jpg"
			alt="Picture of the author"
			layout="fill"
			objectFit="cover"
		/>
		</div>
	</div>

	<div className="container">
		<div className="-mt-12 sm:-mt-16 sm:flex sm:items-end sm:space-x-5">
		<div className="flex">
			<div className="h-24 w-24 rounded-full ring-4 ring-white sm:h-32 sm:w-32 z-10 overflow-hidden relative">
			<Image
				src="https://i0.wp.com/9to5mac.com/wp-content/uploads/sites/6/2021/09/iPhone-13-macro-photography.jpg"
				alt="Picture of the author"
				layout="fill"
				objectFit="cover"
			/>
			</div>
		</div>

		<div className="mt-6 sm:flex-1 sm:min-w-0 sm:flex sm:items-center sm:justify-end sm:space-x-6 sm:pb-1">
			<div className="sm:hidden md:block mt-6 min-w-0 flex-1">
			<h1 className="text-2xl font-bold text-gray-900 truncate">
				Ricardo Cooper
			</h1>
			</div>
			<div className="mt-6 flex flex-col justify-stretch space-y-3 sm:flex-row sm:space-y-0 sm:space-x-4">
			<button
				type="button"
				className="inline-flex justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500"
			>
				<svg
				className="-ml-1 mr-2 h-5 w-5 text-gray-400"
				xmlns="http://www.w3.org/2000/svg"
				viewBox="0 0 20 20"
				fill="currentColor"
				aria-hidden="true"
				>
				<path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
				<path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
				</svg>
				<span>Message</span>
			</button>
			<button
				type="button"
				className="inline-flex justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500"
			>
				<svg
				className="-ml-1 mr-2 h-5 w-5 text-gray-400"
				xmlns="http://www.w3.org/2000/svg"
				viewBox="0 0 20 20"
				fill="currentColor"
				aria-hidden="true"
				>
				<path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
				</svg>
				<span>Call</span>
			</button>
			</div>
		</div>
		</div>
		<div className="hidden sm:block md:hidden mt-6 min-w-0 flex-1">
		<h1 className="text-2xl font-bold text-gray-900 truncate">
			Ricardo Cooper
		</h1>
		</div>
	</div>
</div>
  <pre>{JSON.stringify(data, null, 4)}</pre>
  </>
};

export default $componentName;
_END,
	 );
};
