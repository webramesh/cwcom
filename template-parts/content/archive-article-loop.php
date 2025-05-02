<?php		
		while ( have_posts() ) {
						the_post();

									/**
						 * Hook in loop entry template.
						 */
						do_action( 'kadence_loop_entry' );
					}
?>					