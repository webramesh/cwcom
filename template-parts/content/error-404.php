<?php
/**
 * Template part for displaying the page content when a 404 error has occurred
 *
 * @package kadence
 */

namespace Kadence;

?>
<section class="error">

	<div class="page-content entry content-bg">

		<div class="entry-content-wrap">

			<?php
			do_action( 'kadence_404_before_inner_content' );

			get_template_part( 'template-parts/content/page_header' ); ?>
			<div>
			<p>
				<?php esc_html_e('It looks like nothing was found at this location. Maybe try a search?', 'kadence-child');
				get_search_form();
				 ?>
			</p>
			<p><a href="/"><?php esc_html_e('Go to the main page', 'kadence-child'); ?></a></p>
			<p><?php esc_html_e('You may be interested in the following sections:', 'kadence-child'); ?></p>
			<ul>
				<li><a href="/current-tenders/"><?php esc_html_e('Current tenders in the market', 'kadence-child'); ?></a></li>
				<li><a href="/company-profile/"><?php esc_html_e('Learn about Concealed Wines', 'kadence-child'); ?></a></li>
				<li><a href="/questions-answers/"><?php esc_html_e('Q & A to understand us and our markets', 'kadence-child'); ?></a></li>
				<li><a href="/work/"><?php esc_html_e('How we can work together with you as a partner', 'kadence-child'); ?></a></li>
				<li><a href="/contact-us/"><?php esc_html_e('Contact us', 'kadence-child'); ?></a></li>
			</ul><br>
            </div>
			<?php
			

			do_action( 'kadence_404_after_inner_content' );
			?>
		</div>
	</div><!-- .page-content -->
</section><!-- .error -->
