<?php
/**
 * Plugin Name: Genesis Custom Block Converter
 * Plugin URI: https://github.com/Bowriverstudio/genesis-custom-block-converter
 * GitHub Plugin URI: https://github.com/Bowriverstudio/genesis-custom-block-converter
 * Description: Converts Genesis Custom Blocks to html for ease parsing using headless WordPress.
 * Author: Maurice Tadros
 * Author URI: http://www.bowriverstudio.com
 * Version: 0.4.2
 * Text Domain: genesis-custom-block-converter
 * Domain Path: /languages/
 * Requires PHP: 7.1
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/src/functions.php';

