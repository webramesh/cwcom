<?php
/**
 * Template part for displaying a post's title
 *
 * @package kadence
 */

namespace Kadence;

if ( is_404() ) {
	?>
	<h1 class="page-title 404-page-title">
		<?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'kadence' ); ?>
	</h1>
	<?php
} elseif ( is_home() && ! have_posts() ) {
	?>
	<h1 class="page-title post-home-title archive-title">
		<?php esc_html_e( 'Nothing Found', 'kadence' ); ?>
	</h1>
	<?php
} elseif ( is_home() && ! is_front_page() ) {
	?>
	<h1 class="page-title post-home-title archive-title">
		<?php single_post_title(); ?>
	</h1>
	<?php
} elseif ( is_search() ) {
	?>
	<h1 class="page-title search-title">
		<?php
		printf(
			/* translators: %s: search query */
			esc_html__( 'Search Results for: %s', 'kadence' ),
			'<span>' . get_search_query() . '</span>'
		);
		?>
	</h1>
	<?php
} elseif ( is_archive() || is_home() ) { $additional_title = '';
global $wp_query;

	if ($wp_query->is_post_type_archive( 'tenders' ) && is_tax()) { $additional_title = esc_html__( 'Current Tenders, ', 'kadence' ); }
	elseif ($wp_query->is_post_type_archive( 'tenders' )) { $additional_title = esc_html__( 'Search ', 'kadence' ); }
	
	the_archive_title( '<h1 class="page-title archive-title">'.$additional_title, '</h1>' );
}
