<?php $block_config = block_config(); ?>

<section style="width: 100%;
  padding:60px 0;
  text-align: center;
  background: #33cccc;
  color: white;">
	
  <?php
	$keywords = $block_config['keywords'];
	if ( $keywords ) {
		foreach ( $keywords as $keyword ) {
			// var_dump( $keyword );
			// block_field( $keyword );

			$field = $block_config['fields'][ $keyword ];
			if ( $field ) {
				$field_config = block_field_config( $keyword );

				// var_dump( $field_config );
				if ( $field_config['control'] == 'image' ) {
					$image_attributes = wp_get_attachment_image_src( block_value( $keyword ), 'full' );
					$src              = $image_attributes[0];
					// echo block_value( $keyword );
					echo "<img src='$src' alt='preview'/>";
				} else {
					if ( $keyword === 'the_title' ) {
						$value = block_value( $keyword ) != '' ? block_value( $keyword ) : get_the_title();

						echo '<h2>' . $value . '</h2>';
					} else {
						echo '<h2>' . block_field( $keyword ) . '</h2>';
					}
				}

				// var_dump( $field );
			}
		}
	} else {
		echo '<h2>' . $block_config['name'] . '</h2>';
	}

	$data = array(
		'test'      => 'test',
		'num'       => 3,
		'booltrue'  => true,
		'boolfalse' => false,
		'repeater'  => array(
			array(
				'repeater-title' => 'Title 1',
				'repeater-desc'  => 'dd',
			),
			array(
				'repeater-title' => 'Title 2',
				'repeater-desc'  => 'dd',
			),
		),
	);

	echo json_encode( $data );
	?>
	<p>Placeholder for the block (displayed correctly in the frontend)</p>
</section>

