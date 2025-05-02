<?php
/**
 * Template Name: Product tender template
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

		// Params for our query.
		$query_args = array (
			'post_type'      => 'tenders',
			'post_status'    => 'publish',
			'paged'          => $paged,
			'order' => 'DESC',
            'orderby' => 'ID',
			'meta_query' => array(
               array(
                  'key'     => 'wpcf-tender-archive-date',
                  'value'   => time(),
                  'compare' => '>=',
                   ),
             ),
			'no_found_rows'  => false
		);

if (!empty($_GET['wpvtender-products'])) {
    $query_args['tax_query'] = array(
        array(
            'taxonomy' => 'tender-products',
            'field' => 'slug',
            'terms' => $_GET['wpvtender-products'],
             )
        );
        }
		// The Query.
		$wp_query = new \WP_Query( $query_args );
	   	global $wp_query;
	
      wp_localize_script( 'ajax-filter', 'loadmore_params', array(
		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
		'posts' => json_encode( $wp_query->query_vars ), // everything about your loop is here
		'current_page' => $wp_query->query_vars['paged'] ? $wp_query->query_vars['paged'] : 1,
		'max_page' => $wp_query->max_num_pages
	) );

?>
<div id="primary" class="content-area">
	<div class="content-container site-container">
		<main id="main" class="site-main isotope_wrapper" role="main">

			<?php
			/**
			 * Hook for anything before main content
			 */

            global $post;
				$page   = get_post( $post->ID);	//TODO Change this to the ID of the page you want to use for the blog archive
				$output =  apply_filters( 'the_content', $page ->post_content );
				echo "<div class='tenders__intro'>".$output."</div>";

			do_action( 'kadence_before_main_content' );
			if ( kadence()->show_in_content_title() ) {
				get_template_part( 'template-parts/content/archive_header' );
			}
			?>
			<div id="found-posts"> <?php

			echo __('Total tenders found:', 'kadence-child') ?> <span> <?php echo $wp_query->found_posts; ?> </span>

            </div> <?php
            if ( have_posts() ) {    ?>
                   <div id="archive-container" data-type="tenders"  class="tenders lm_wrapper <?php echo esc_attr( implode( ' ', get_archive_container_classes() ) ); ?>"<?php echo ( get_archive_infinite_attributes() ? " data-infinite-scroll='" . esc_attr( get_archive_infinite_attributes() ) . "'" : '' ); ?>>
                        <?php
                        get_template_part( 'template-parts/content/archive_main', 'tenders');
                        ?>
                   </div>
                <?php
			    } else { ?>

                    <div id="archive-container" data-type="tenders"  class="tenders lm_wrapper <?php echo esc_attr( implode( ' ', get_archive_container_classes() ) ); ?>"<?php echo ( get_archive_infinite_attributes() ? " data-infinite-scroll='" . esc_attr( get_archive_infinite_attributes() ) . "'" : '' ); ?>></div>
              <?php  }

		echo \ajax_pagination();
	
            if ( !empty($_GET['wpvtender-products'])) {               
				$prod = $_GET['wpvtender-products']; 
            ?>

                <?php
				$page   = get_page_by_path("current-tenders/tenders-for-country", OBJECT, 'page');
				$id = $page->ID;
                $repeatable_fields = get_post_meta($id, 'repeatable_fields', true);			
				$product_term = 'tender-products:' . get_term_by('slug', $prod, 'tender-products')->term_id;

               // print_r($repeatable_fields);				
				?>
                <div class="wp-block-kadence-accordion alignnone accordion_country faq_main faq_block_tenders">
                    <div class="kt-accordion-wrap kt-accordion-wrap kt-accordion-has-2-panes kt-active-pane-0 kt-accordion-block kt-pane-header-alignment-left kt-accodion-icon-style-arrow kt-accodion-icon-side-right" style="max-width:none">
                        <div class="kt-accordion-inner-wrap" data-allow-multiple-open="false" data-start-open="0" itemscope="" itemtype="https://schema.org/FAQPage">

                            <?php
						
                            foreach ($repeatable_fields as $key => $value) { 
                                if (($product_term == $value['select']) ) { ?>
                                <div class="faq_title has-text-align-center"> <?php if (!empty($value['faq_title'])) echo $value['faq_title']; ?></div>
                                    <?php if (!empty($value['q1'])) { ?>
                                        <div class="wp-block-kadence-pane kt-accordion-pane kt-accordion-1"  itemscope="" itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <div class="kt-accordion-header-wrap"><button class="kt-blocks-accordion-header kt-acccordion-button-label-show"><span class="kt-blocks-accordion-title-wrap">
                                                        <span class="kt-blocks-accordion-title" itemprop="name"><?php echo $value['q1']; ?></span>
                                                    </span><span class="kt-blocks-accordion-icon-trigger"></span></button></div>
                                            <div class="kt-accordion-panel">
                                                <div class="kt-accordion-panel-inner" itemscope="" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                                    <p itemprop="text"><?php echo $value['a1']; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($value['q2'])) { ?>
                                         <div class="wp-block-kadence-pane kt-accordion-pane kt-accordion-2"  itemscope="" itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <div class="kt-accordion-header-wrap"><button class="kt-blocks-accordion-header kt-acccordion-button-label-show"><span class="kt-blocks-accordion-title-wrap">
                                                        <span class="kt-blocks-accordion-title" itemprop="name"><?php echo $value['q2']; ?></span>
                                                    </span><span class="kt-blocks-accordion-icon-trigger"></span></button></div>
                                            <div class="kt-accordion-panel">
                                                <div class="kt-accordion-panel-inner" itemscope="" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                                    <p itemprop="text"><?php echo $value['a2']; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($value['q3'])) { ?>
                                         <div class="wp-block-kadence-pane kt-accordion-pane kt-accordion-3"  itemscope="" itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <div class="kt-accordion-header-wrap"><button class="kt-blocks-accordion-header kt-acccordion-button-label-show"><span class="kt-blocks-accordion-title-wrap">
                                                        <span class="kt-blocks-accordion-title" itemprop="name"><?php echo $value['q3']; ?></span>
                                                    </span><span class="kt-blocks-accordion-icon-trigger"></span></button></div>
                                            <div class="kt-accordion-panel">
                                                <div class="kt-accordion-panel-inner" itemscope="" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                                    <p itemprop="text"><?php echo $value['a3']; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($value['q4'])) { ?>
                                         <div class="wp-block-kadence-pane kt-accordion-pane kt-accordion-4"  itemscope="" itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <div class="kt-accordion-header-wrap"><button class="kt-blocks-accordion-header kt-acccordion-button-label-show"><span class="kt-blocks-accordion-title-wrap">
                                                        <span class="kt-blocks-accordion-title" itemprop="name"><?php echo $value['q4']; ?></span>
                                                    </span><span class="kt-blocks-accordion-icon-trigger"></span></button></div>
                                            <div class="kt-accordion-panel">
                                                <div class="kt-accordion-panel-inner" itemscope="" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                                    <p itemprop="text"><?php echo $value['a4']; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($value['q5'])) { ?>
                                        <div class="wp-block-kadence-pane kt-accordion-pane kt-accordion-5"  itemscope="" itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <div class="kt-accordion-header-wrap"><button class="kt-blocks-accordion-header kt-acccordion-button-label-show"><span class="kt-blocks-accordion-title-wrap">
                                                        <span class="kt-blocks-accordion-title" itemprop="name"><?php echo $value['q5']; ?></span>
                                                    </span><span class="kt-blocks-accordion-icon-trigger"></span></button></div>
                                            <div class="kt-accordion-panel">
                                                <div class="kt-accordion-panel-inner" itemscope="" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                                    <p itemprop="text"><?php echo $value['a5']; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                            <?php }
                            } 
						?>
                        </div>
                    </div>
                </div>
            <?php } ?>		
		
		</main><!-- #main -->
		<?php get_sidebar();
		?>
	</div>
</div><!-- #primary -->
<?php get_footer();
