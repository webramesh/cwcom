<?php
/**
 * Template Name: User`s applied tenders
 *
 * This is the template that displays search tenders page.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package
 */

namespace Kadence;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
kadence()->print_styles( 'kadence-content' );
do_action( 'kadence_hero_header' );
		$paged =  \get_query_var( 'paged' ) ? \get_query_var( 'paged' ) :  1 ;
		
$user_ID = get_current_user_id();
$offer_meta  = array();
if ($user_ID ) {
$args = array (
			'author' => $user_ID,
			'post_type'      => 'tender-offers',
			'post_status'    => 'publish',
			'order' => 'DESC',
			'posts_per_page' => -1,				
			'no_found_rows'  => false
);

	$user_tenders_query = new \WP_Query($args);
			
    if ( $user_tenders_query->have_posts() ) { 			
        while ( $user_tenders_query->have_posts() ) {
				$user_tenders_query->the_post();
			    $meta = get_post_meta(get_the_ID(), '_wpcf_belongs_tenders_id', true );  
                if ($meta) {array_push($offer_meta, $meta); }			
        }
    }
		wp_reset_postdata(); 
}	

		


?>
<div id="primary" class="content-area">
	<div class="content-container site-container">
		<main id="main" class="site-main isotope_wrapper" role="main">
			<?php
			/**
			 * Hook for anything before main content
			 */
			   $page   = get_post( $post->ID);
				$output =  apply_filters( 'the_content', $page ->post_content );
			    echo "<div class='tenders__intro'>".$output."</div>";			 
if(!empty($offer_meta) && is_user_logged_in()) :
	
		$query_args = array (
			'post__in' => $offer_meta,
			'post_type'      => 'tenders',
			'post_status'    => 'publish',
			'paged'          => $paged,
			'order' => 'DESC',
            'orderby' => 'meta_value',
            'meta_key' => 'wpcf-tender-archive-date',
/*			'meta_query' => array(
               array(
                  'key'     => 'wpcf-tender-archive-date',
                  'value'   => time(),
                  'compare' => '<=',
                   ),
             ),
*/
			'no_found_rows'  => false

		);
      global $post; 
	  $page   = get_post( $post->ID);
	
	  $wp_query = new \WP_Query( $query_args );
	  
	  wp_localize_script( 'ajax-filter', 'loadmore_params', array(
		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
		'posts' => json_encode( $wp_query->query_vars ), // everything about your loop is here
		'current_page' => $wp_query->query_vars['paged'] ? $wp_query->query_vars['paged'] : 1,
		'max_page' => $wp_query->max_num_pages
	) ); 
	  
			   //do_action( 'kadence_before_main_content' );
			if ( kadence()->show_in_content_title() ) {
				//get_template_part( 'template-parts/content/archive_header' );
			}
			?>
			<div id="found-posts"> <?php

			echo __('Total tenders found:', 'kadence-child') ?> <span> <?php echo $wp_query->found_posts; ?> </span>

            </div> <?php
            if ( $wp_query->have_posts() ) {    ?>
                   <div id="archive-container" data-type="past-tenders"  class="tenders lm_wrapper <?php echo esc_attr( implode( ' ', get_archive_container_classes() ) ); ?>"<?php echo ( get_archive_infinite_attributes() ? " data-infinite-scroll='" . esc_attr( get_archive_infinite_attributes() ) . "'" : '' ); ?>>
                        <?php
                        get_template_part( 'template-parts/content/archive_main', 'tenders');
                        ?>
                   </div>
		
                <?php	}
		            if ( $wp_query->have_posts() ) {
	           	if (get_post_type()=='tenders') {
				echo \ajax_pagination();
				}
		        else { get_template_part( 'template-parts/content/pagination' );  }
			} else {
				get_template_part( 'template-parts/content/error' );
			}
			/**
			 * Hook for anything after main content
			 */
			 wp_reset_postdata();
       elseif (empty($offer_meta)&& is_user_logged_in()): ?>			 
			 
			<div id="archive-container">
				<?php echo __('No applied tenders found', 'kadence-child'); ?>
			</div>			 
			 
	<?php	
       elseif (!is_user_logged_in()):
      ?>				 
			<div id="archive-container">
				<?php echo __('You have to be logged in to view the tenders you have applied for', 'kadence-child'); ?>
			</div>			 
			 
	<?php	

	do_action( 'kadence_after_main_content' );
			?>
		</main><!-- #main -->
		<?php
		get_sidebar();
		?>
	</div>
</div><!-- #primary -->


<?php endif; ?>

<?php get_footer();
