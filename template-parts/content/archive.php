<?php
/**
 * The main archive template file for inner content.
 *
 * @package kadence
 */

namespace Kadence;

/**
 * Hook for Hero Section
 */
do_action( 'kadence_hero_header' );
	   	global $wp_query;
      wp_localize_script ( 'ajax-filter', 'loadmore_params', array(
		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
		'posts' => json_encode( $wp_query->query_vars ), // everything about your loop is here
		'current_page' => $wp_query->query_vars['paged'] ? $wp_query->query_vars['paged'] : 1,
		'max_page' => $wp_query->max_num_pages
	) );
$add_class='';
if (get_post_type() == 'tenders' ) {  $add_class='tenders lm_wrapper ';}
?>

<div id="primary" class="content-area">
	<div class="content-container site-container">
		<main id="main" class="site-main isotope_wrapper" role="main">
		

			<?php
			/**
			 * Hook for anything before main content
			 */
			 	do_action( 'kadence_before_main_content' );
			if ( kadence()->show_in_content_title() ) {
				get_template_part( 'template-parts/content/archive_header' );
			}
            add_action( 'kadence_before_main_content', 'tenders_found_posts', 20 );

                if (get_query_var('post_type') === 'tenders') { ?>
                    <div id="found-posts"> <?php
                        echo __('Total tenders found:', 'kadence-child') ?> <span> <?php echo $wp_query->found_posts; ?> </span>
                    </div> <?php
                }

             if ( have_posts() ) {
                    ?>
                    <div id="archive-container" data-type="<?php echo get_post_type(); ?>"  class="<?php echo $add_class.esc_attr( implode( ' ', get_archive_container_classes() ) ); ?>"<?php echo ( get_archive_infinite_attributes() ? " data-infinite-scroll='" . esc_attr( get_archive_infinite_attributes() ) . "'" : '' ); ?>>
                        <?php
                        get_template_part( 'template-parts/content/archive_main', get_post_type() );
                        ?>
                    </div>
                <?php
            if (get_query_var('post_type') !== 'tenders') {
		         get_template_part( 'template-parts/content/pagination' );}
			} else {
                if (get_query_var('post_type') === 'tenders'){ ?>
				     <div id="archive-container" data-type="tenders"  class="<?php echo $add_class.esc_attr( implode( ' ', get_archive_container_classes() ) ); ?>"<?php echo ( get_archive_infinite_attributes() ? " data-infinite-scroll='" . esc_attr( get_archive_infinite_attributes() ) . "'" : '' ); ?>>
                     </div>

                <?php } else {get_template_part( 'template-parts/content/error' );}
			}
            if (get_query_var('post_type') === 'tenders') {

                echo \ajax_pagination();
            }
			/**
			 * Hook for anything after main content
			 */
			do_action( 'kadence_after_main_content' );
			?>
		</main><!-- #main -->
		<?php
		get_sidebar();
		?>
	</div>
</div><!-- #primary -->
