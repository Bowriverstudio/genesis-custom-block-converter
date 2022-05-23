<?php
/**
 * Plugin Name: Genesis Custom Block Converter
 * Plugin URI: https://github.com/Bowriverstudio/genesis-custom-block-converter
 * GitHub Plugin URI: https://github.com/Bowriverstudio/genesis-custom-block-converter
 * Description: Converts Genesis Custom Blocks to html for ease parsing using headless WordPress.
 * Author: Maurice Tadros
 * Author URI: http://www.bowriverstudio.com
 * Version: 0.9.5
 * Text Domain: gcbc
 * Domain Path: /languages/
 * Requires PHP: 7.1
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

namespace GenesisCustomBlocksConverter;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Define paths.
define( 'GENESIS_CUSTOM_BLOCKS_CONVERTER_DIR', wp_normalize_path( trailingslashit( __DIR__ ) ) );
define( 'GENESIS_CUSTOM_BLOCKS_CONVERTER_INCLUDES_DIR', GENESIS_CUSTOM_BLOCKS_CONVERTER_DIR . trailingslashit( 'includes/' ) );
define( 'GENESIS_CUSTOM_BLOCKS_CONVERTER_VENDOR_DIR', GENESIS_CUSTOM_BLOCKS_CONVERTER_DIR . trailingslashit( 'vendor/' ) );

// Define urls.
define( 'GENESIS_CUSTOM_BLOCKS_CONVERTER_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'GENESIS_CUSTOM_BLOCKS_CONVERTER_BUILD_URL', trailingslashit( plugins_url( 'build', __FILE__ ) ) );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_code() {
	$included_files = array(
		'functions.php',
		'lib/functions.php',
	);
	foreach ( $included_files as $file ) {
		include_once GENESIS_CUSTOM_BLOCKS_CONVERTER_INCLUDES_DIR . $file;
	}
}
run_code();


