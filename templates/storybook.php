<?php

return function( string $componentName ) {
	$typescriptProp = $componentName . 'Props';

	 return array(
		 'name'    => 'tests/' . $componentName . '.stories.tsx',
		 'content' => <<<_END
/* eslint-disable */ 
import React from 'react';

import { $typescriptProp } from 'client';

import { ComponentStory, ComponentMeta } from '@storybook/react';

import { Html2React } from 'components';
import $componentName from '../$componentName';
import { exampleHtml } from './_data';
const storyBookTitle = {
	title: 'Components/$componentName',
	component: $componentName,
	argTypes: {
	  children: {
		control: { type: 'text' },
	  },
	},
  } as ComponentMeta<typeof $componentName>;
  
  const Template: ComponentStory<typeof $componentName> = (args) => (
	<>
	  <Html2React html={exampleHtml} />
	</>
  );
  
  export const Default = Template.bind({});
  export default storyBookTitle;

_END,
	 );
};
