<?php

return function( string $componentName ) {
	 return array(
		 'name'    => 'index.js',
		 'content' => <<<_END
import $componentName from "./$componentName";
export default $componentName;
_END,
	 );
};
