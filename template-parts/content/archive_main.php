<?php
/**
 * The main archive template file for inner content - main part of content.
 *
 * @package kadence
 */

namespace Kadence;

		while ( have_posts() ) {
						the_post();

									/**
						 * Hook in loop entry template.
						 */
						do_action( 'kadence_loop_entry' );
					}
?>
