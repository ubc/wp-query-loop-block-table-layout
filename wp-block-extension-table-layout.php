<?php
/**
 *
 * Plugin Name:       WP Query Loop Block Extension - Table Layout
 * Description:       Add table layout support to WP Query Loop block.
 * Version:           1.0
 * Author:            Kelvin Xu
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-query-loop-block-extension-table-layout
 *
 * @package wp-query-loop-block-extension-table-layout
 */

namespace UBC\CTLT\BLOCKS\EXTENSION\QUERY_LOOP\TABLE_LAYOUT;

define( 'WP_QUERY_LOOP_BLOCK_EXTENSION_TABLE_LAYOUT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_QUERY_LOOP_BLOCK_EXTENSION_TABLE_LAYOUT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once WP_QUERY_LOOP_BLOCK_EXTENSION_TABLE_LAYOUT_PLUGIN_DIR . 'src/core-query-extend.php';

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

add_action( 'enqueue_block_assets', __NAMESPACE__ . '\\enqueue_assets' );
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_editor_assets' );

/**
 * Enqueue block assets.
 *
 * @return void
 */
function enqueue_assets() {
	wp_enqueue_style(
		'wp-query-block-extension-table-layout-frontend-css',
		plugin_dir_url( __FILE__ ) . 'src/core-query-extend-frontend.css',
		array(),
		filemtime( plugin_dir_path( __FILE__ ) . 'src/core-query-extend-frontend.css' )
	);
}//end enqueue_assets()

/**
 * Enqueue editor assets.
 *
 * @return void
 */
function enqueue_editor_assets() {
	wp_enqueue_script(
		'wp-query-block-extension-table-layout-js',
		plugin_dir_url( __FILE__ ) . 'build/core-query-extend.js',
		array(),
		filemtime( plugin_dir_path( __FILE__ ) . 'build/core-query-extend.js' ),
		true
	);

	wp_enqueue_style(
		'wp-query-block-extension-table-layout-editor-css',
		plugin_dir_url( __FILE__ ) . 'src/core-query-extend-editor.css',
		array(),
		filemtime( plugin_dir_path( __FILE__ ) . 'src/core-query-extend-editor.css' )
	);
}//end enqueue_editor_assets()
