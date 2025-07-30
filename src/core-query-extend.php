<?php
/**
 * Extend core query block.
 *
 * @package wp-query-block-extension-table-layout
 */

namespace UBC\CTLT\BLOCKS\EXTENSION\TABLE_LAYOUT;

add_filter( 'render_block', __NAMESPACE__ . '\\render_inner_blocks_table_layout', 10, 2 );
add_filter( 'render_block', __NAMESPACE__ . '\\render_post_template_table_layout', 10, 2 );
add_filter( 'render_block_data', __NAMESPACE__ . '\\add_inner_blocks_table_layout_data', 10, 3 );

/**
 * Update the mockup of the innerblocks.
 * Wrapper each of the innerblocks in a <td> tag.
 *
 * @param  array $block_content Block content.
 * @param  array $parsed_block Parsed block.
 * @return array
 */
function render_inner_blocks_table_layout( $block_content, $parsed_block ) {
	// Current set the layout to empty if the layout is table view.
	// This is definitely not ideal, and hopefully WordPress will provide a way to register custom layouts at a later point.
	if ( ! isset( $parsed_block['useTableLayout'] ) || ! boolval( $parsed_block['useTableLayout'] ) ) {
		return $block_content;
	}

	return sprintf(
		'<td>%s</td>',
		$block_content
	);
}//end render_inner_blocks_table_layout()

/**
 * Update the mockup of the post template block.
 * Turn the UL structure into a table.
 * Add table header based on the name of the innerblocks.
 *
 * @param  array $block_content Block content.
 * @param  array $parsed_block  Parsed block.
 * @return array
 */
function render_post_template_table_layout( $block_content, $parsed_block ) {
	if ( ! isset( $parsed_block['blockName'] ) || 'core/post-template' !== $parsed_block['blockName'] ) {
		return $block_content;
	}

	if ( ! isset( $parsed_block['attrs']['useTableLayout'] ) || ! boolval( $parsed_block['attrs']['useTableLayout'] ) ) {
		return $block_content;
	}

	$doc = new \DOMDocument();
	libxml_use_internal_errors( true ); // Suppress HTML5 warnings.
	$doc->loadHTML( '<div id="wrapper">' . $block_content . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );

	// Create table element.
	$table = $doc->createElement( 'table' );

	// Add header.
	$thead      = $doc->createElement( 'thead' );
	$header_row = $doc->createElement( 'tr' );

	// Add table header based on the name of the innerblocks.
	foreach ( $parsed_block['innerBlocks'] as $inner_block ) {
		$th = $doc->createElement( 'th' );
		if ( isset( $inner_block['attrs'] ) && isset( $inner_block['attrs']['metadata'] ) && isset( $inner_block['attrs']['metadata']['name'] ) ) {
			$th->nodeValue = $inner_block['attrs']['metadata']['name'];
		} else {
			$th->nodeValue = $inner_block['blockName'];
		}
		$header_row->appendChild( $th );
	}
	$thead->appendChild( $header_row );
	$table->appendChild( $thead );

	// Create tbody.
	$tbody = $doc->createElement( 'tbody' );

	// Find the UL element and convert into HTML table.
	foreach ( $doc->getElementsByTagName( 'ul' ) as $ul ) {
		// Making sure that we only targeting post template block, not any unordered list block within innderblocks.
		if ( ! $ul->hasAttribute( 'class' ) || ! str_contains( $ul->getAttribute( 'class' ), 'wp-block-post-template' ) ) {
			continue;
		}

		$table->setAttribute( 'class', $ul->getAttribute( 'class' ) );

		foreach ( $ul->getElementsByTagName( 'li' ) as $li ) {
			if ( ! $li->hasAttribute( 'class' ) || ! str_contains( $li->getAttribute( 'class' ), 'wp-block-post' ) ) {
				continue;
			}

			$tr = $doc->createElement( 'tr' );
			$tr->setAttribute( 'class', $li->getAttribute( 'class' ) );

			// Move all child nodes from li to td.
			while ( $li->hasChildNodes() ) {
				$tr->appendChild( $li->firstChild );
			}

			$tbody->appendChild( $tr );
		}

		// Replace the UL with our new table.
		$table->appendChild( $tbody );
		$ul->parentNode->replaceChild( $table, $ul );
	}

	// Now remove the wrapper, but keep the innerHTML.
	$wrapper = $doc->getElementById( 'wrapper' );
	$wrapper->parentNode->replaceChild( $table, $wrapper );

	return $doc->saveHTML();
}//end render_post_template_table_layout()

/**
 * Add parent block layout data to parsed block.
 *
 * @param  array $parsed_block  Parsed block.
 * @param  array $source_block  Source block.
 * @param  array $parent_block  Parent block.
 * @return array
 */
function add_inner_blocks_table_layout_data( $parsed_block, $source_block, $parent_block ) {
	/*
	 * Check if the parent block exists and if it has a layout attribute.
	 * If it does, add the parent layout to the parsed block.
	 */
	if ( $parent_block ) {
		$parsed_block['useTableLayout'] = $parent_block->parsed_block['attrs']['useTableLayout'];
	}
	return $parsed_block;
}//end add_inner_blocks_table_layout_data()
