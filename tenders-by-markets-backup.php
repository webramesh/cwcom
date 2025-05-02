<?php

/**
 * Template Name: Tenders by market template
 *
 * This is the template that displays search tenders page.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package
 */

namespace Kadence;

if (!defined('ABSPATH')) {
    exit;
}

get_header();
kadence()->print_styles('kadence-content');
get_template_part('template-parts/content/entry_hero');
$support_email = get_theme_mod('email-support');
$no_tenders = '<div class="no-tender-mes">' . __('At this stage it is no tenders for this country.', 'kadence-child') . '</div>' . '<div class="sing-up-text">' . __('Sign up to be updated for the future.', 'kadence-child') . ' <span>' . __('Email to:', 'kadence-child') . " <a href='mailto:" . antispambot($support_email) . "'>" . antispambot($support_email) . '</a></span></div>';
// Params for our query.
$query_args0 = array(
    'post_type'      => 'tenders',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'order' => 'ASC',
    'orderby' => 'meta_value',
    'meta_key' => 'wpcf-tender-offer-deadline',
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key'     => 'wpcf-tender-archive-date', /* current tenders */
            'value'   => time(),
            'compare' =>  '>=',
        ),
        array(
            'key'     => 'wpcf-tender-launch-plan', /* current tenders */
            'value'   => 0,
            'compare' =>  '=',
        ),
    ),
    'no_found_rows'  => false
);
/* First loop  sweden-systembolaget current*/
$tax_q2 = $tax_q = array();
$query_args1 = $query_args0;
$tax_q = array('relation' => 'AND');
array_push($tax_q2, array(
    'taxonomy' => 'tender-market',
    'field' => 'slug',
    'terms' => 'sweden-systembolaget'
));
array_push($tax_q2, array(
    'taxonomy' => 'tender-products',
    'field'    => 'slug',
    'terms'    => array('spirits', 'beer', 'cider'),
    'operator' => 'NOT IN'
));
if (!empty($_GET['country'])) {
    array_push($tax_q2, array(
        'taxonomy' => 'tender-countries',
        'field'    => 'slug',
        'terms'    => $_GET['country'],
    ));
}
array_push($tax_q, $tax_q2);
$query_args1["tax_query"] = $tax_q;


// The Query.
$wp_query = new \WP_Query($query_args1);
global $wp_query;  
?>
<div id="primary" class="content-area">
    <div class="content-container site-container">
        <main id="main" class="site-main isotope_wrapper" role="main">
            <?php
            /**
             * Hook for anything before main content
             */

            global $post;
            $page   = get_post($post->ID);    //TODO Change this to the ID of the page you want to use for the blog archive
            $output =  apply_filters('the_content', $page->post_content);

            echo "<div class='tenders__intro'>" . $output . "</div>"; ?>

            <div class="tenders_container">
                <div class="tender-nav">
                    <div class="nav-title"><?php echo __('Navigate to tenders', 'kadence-child'); ?>:</div>
                    <div class="tender_nav_desc">
                        <p><a href="#cur-swed"><span class="tender_nav_title"><?php echo __('Current Tenders for Swedish Monopoly (Systembolaget)', 'kadence-child'); ?></span><span class="arr">&darr;</span></a></p>
                        <p><a href="#upcom-swed"><span class="tender_nav_title"><?php echo __('Upcoming Tenders for Swedish Monopoly (Systembolaget)', 'kadence-child'); ?></span><span class="arr">&darr;</span></a></p>
                        <p><a href="#cur-norw"><span class="tender_nav_title"><?php echo __('Current Tenders for Norwegian Monopoly (Vinmonopolet)', 'kadence-child') ?></span><span class="arr">&darr;</span></a></p>
                        <p><a href="#cur-finn"><span class="tender_nav_title"><?php echo __('Current Tenders for Finnish Monopoly (Alko)', 'kadence-child') ?></span><span class="arr">&darr;</span></a></p>
                    </div>
                </div>
                <div class="wp-block-kadence-spacer aligncenter kt-block-spacer-dashed">
                    <div class="kt-block-spacer kt-block-spacer-halign-center">
                        <hr class="kt-divider first">
                    </div>
                </div>
                <h2 class="tender-market" id="cur-swed">
                    <?php echo __('Current Tenders for Swedish Monopoly (Systembolaget)', 'kadence-child'); ?>
                </h2>
                <?php
                if (have_posts()) {   ?>
                    <div id="found-posts"> <?php

                                            echo __('Total tenders found:', 'kadence-child') ?> <span> <?php echo $wp_query->found_posts; ?> </span>

                    </div>

                    <div id="archive-container" data-type="past-tenders" class="tenders lm_wrapper <?php echo esc_attr(implode(' ', get_archive_container_classes())); ?>" <?php echo (get_archive_infinite_attributes() ? " data-infinite-scroll='" . esc_attr(get_archive_infinite_attributes()) . "'" : ''); ?>>
                        <?php
                        get_template_part('template-parts/content/archive_main', 'tenders');
                        ?>
                    </div>
                <?php    } else {
                    echo $no_tenders;
                }
                wp_reset_postdata();
                /* Second loop sweden-systembolaget upcoming*/
                $tax_q2 = $tax_q = array();
                $query_args2 = array(
                    'post_type'      => 'tenders',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'order' => 'ASC',
                    'orderby' => 'meta_value',
                    'meta_key' => 'wpcf-tender-start-date',
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key'     => 'wpcf-tender-archive-date', /* upcoming tenders */
                            'value'   => time(),
                            'compare' =>  '>=',
                        ),
                        array(
                            'key'     => 'wpcf-tender-launch-plan', /* upcoming tenders */
                            'value'   => 1,
                            'compare' =>  '=',
                        ),
                    ),
                    'no_found_rows'  => false
                );
                $tax_q = array('relation' => 'AND');
                array_push($tax_q2, array(
                    'taxonomy' => 'tender-market',
                    'field' => 'slug',
                    'terms' => 'sweden-systembolaget'
                ));
                array_push($tax_q2, array(
                    'taxonomy' => 'tender-products',
                    'field'    => 'slug',
                    'terms'    => array('spirits', 'beer', 'cider'),
                    'operator' => 'NOT IN'
                ));

                if (!empty($_GET['country'])) {
                    array_push($tax_q2, array(
                        'taxonomy' => 'tender-countries',
                        'field'    => 'slug',
                        'terms'    => $_GET['country'],
                    ));
                }
                array_push($tax_q, $tax_q2);
                $query_args2["tax_query"] = $tax_q;


                // The Query.
                $wp_query = new \WP_Query($query_args2);
                
                ?>
                <div class="wp-block-kadence-spacer aligncenter kt-block-spacer-dashed">
                    <div class="kt-block-spacer kt-block-spacer-halign-center">
                        <hr class="kt-divider">
                    </div>
                </div>
                <h2 class="tender-market" id="upcom-swed">
                    <?php echo __('Upcoming Tenders for Swedish Monopoly (Systembolaget)', 'kadence-child') ?>
                </h2>
                <?php
                if (have_posts()) {    ?>
                    <div id="found-posts">

                        <?php echo __('Total tenders found:', 'kadence-child') ?> <span> <?php echo $wp_query->found_posts; ?> </span>

                    </div>
                    <div id="archive-container" data-type="past-tenders" class="tenders lm_wrapper <?php echo esc_attr(implode(' ', get_archive_container_classes())); ?>" <?php echo (get_archive_infinite_attributes() ? " data-infinite-scroll='" . esc_attr(get_archive_infinite_attributes()) . "'" : ''); ?>>
                        <?php
                        get_template_part('template-parts/content/archive_main', 'tenders');
                        ?>
                    </div>
                <?php    } else {
                    echo $no_tenders;
                }
                wp_reset_postdata();
                /* Third loop norway-vinmonopolet current*/
                $tax_q2 = $tax_q = array();
                $query_args3 = $query_args0;
                $tax_q = array('relation' => 'AND');
                array_push($tax_q2, array(
                    'taxonomy' => 'tender-market',
                    'field' => 'slug',
                    'terms' => 'norway-vinmonopolet'
                ));
                array_push($tax_q2, array(
                    'taxonomy' => 'tender-products',
                    'field'    => 'slug',
                    'terms'    => array('spirits', 'beer', 'cider'),
                    'operator' => 'NOT IN'
                ));
                if (!empty($_GET['country'])) {
                    array_push($tax_q2, array(
                        'taxonomy' => 'tender-countries',
                        'field' => 'slug',
                        'terms' => $_GET['country'],
                    ));
                }
                array_push($tax_q, $tax_q2);
                $query_args3["tax_query"] = $tax_q;


                // The Query.
                $wp_query = new \WP_Query($query_args3);
              
                ?>
                <div class="wp-block-kadence-spacer aligncenter kt-block-spacer-dashed">
                    <div class="kt-block-spacer kt-block-spacer-halign-center">
                        <hr class="kt-divider">
                    </div>
                </div>
                <h2 class="tender-market" id="cur-norw">
                    <?php echo __('Current Tenders for Norwegian Monopoly (Vinmonopolet)', 'kadence-child') ?>
                </h2>
                <?php
                if (have_posts()) {    ?>
                    <div id="found-posts"> <?php

                                            echo __('Total tenders found:', 'kadence-child') ?> <span> <?php echo $wp_query->found_posts; ?> </span>

                    </div>
                    <div id="archive-container" data-type="past-tenders" class="tenders lm_wrapper <?php echo esc_attr(implode(' ', get_archive_container_classes())); ?>" <?php echo (get_archive_infinite_attributes() ? " data-infinite-scroll='" . esc_attr(get_archive_infinite_attributes()) . "'" : ''); ?>>
                        <?php
                        get_template_part('template-parts/content/archive_main', 'tenders');
                        ?>
                    </div>
                <?php    } else {
                    echo $no_tenders;
                }
                wp_reset_postdata();
                /* Fourth loop finland-alko current */
                $tax_q2 = $tax_q = array();
                $query_args3 = $query_args0;
                $tax_q = array('relation' => 'AND');
                array_push($tax_q2, array(
                    'taxonomy' => 'tender-market',
                    'field' => 'slug',
                    'terms' => 'finland-alko'
                ));
                array_push($tax_q2, array(
                    'taxonomy' => 'tender-products',
                    'field'    => 'slug',
                    'terms'    => array('spirits', 'beer', 'cider'),
                    'operator' => 'NOT IN'
                ));
                if (!empty($_GET['country'])) {
                    array_push($tax_q2, array(
                        'taxonomy' => 'tender-countries',
                        'field' => 'slug',
                        'terms' => $_GET['country'],
                    ));
                }
                array_push($tax_q, $tax_q2);
                $query_args3["tax_query"] = $tax_q;
                // The Query.
                $wp_query = new \WP_Query($query_args3);
            

                ?>
                <div class="wp-block-kadence-spacer aligncenter kt-block-spacer-dashed">
                    <div class="kt-block-spacer kt-block-spacer-halign-center">
                        <hr class="kt-divider">
                    </div>
                </div>
                <h2 class="tender-market" id="cur-finn">
                    <?php echo __('Current Tenders for Finnish Monopoly (Alko)', 'kadence-child') ?>
                </h2>
                <?php
                if (have_posts()) {    ?>
                    <div id="found-posts"> <?php

                                            echo __('Total tenders found:', 'kadence-child') ?> <span> <?php echo $wp_query->found_posts; ?> </span>

                    </div>
                    <div id="archive-container" data-type="past-tenders" class="tenders lm_wrapper <?php echo esc_attr(implode(' ', get_archive_container_classes())); ?>" <?php echo (get_archive_infinite_attributes() ? " data-infinite-scroll='" . esc_attr(get_archive_infinite_attributes()) . "'" : ''); ?>>
                        <?php
                        get_template_part('template-parts/content/archive_main', 'tenders');
                        ?>
                    </div>
                <?php    } else {
                    echo $no_tenders;
                }
                wp_reset_postdata();


                ?>
            </div>

            <? //php  dynamic_sidebar('tenders_bottom'); 
            ?>

            <?php



            if (!empty($_GET['country'])) {
                $country = $_GET['country'];			
				$page   = get_page_by_path("current-tenders/tenders-for-country", OBJECT, 'page');
				$id = $page->ID;
                $repeatable_fields = get_post_meta($id, 'repeatable_fields', true);

				$country_term = 'tender-countries:' . get_term_by('slug', $country, 'tender-countries')->term_id;
               // print_r($repeatable_fields);				
				?>
                <div class="wp-block-kadence-accordion alignnone accordion_country faq_main faq_block_tenders">
                    <div class="kt-accordion-wrap kt-accordion-wrap kt-accordion-has-2-panes kt-active-pane-0 kt-accordion-block kt-pane-header-alignment-left kt-accodion-icon-style-arrow kt-accodion-icon-side-right" style="max-width:none">
                        <div class="kt-accordion-inner-wrap" data-allow-multiple-open="false" data-start-open="0" itemscope="" itemtype="https://schema.org/FAQPage">

                            <?php
						
                            foreach ($repeatable_fields as $key => $value) { 
                                if (($country_term == $value['select']) ) { ?>
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
        <?php
        get_sidebar();
        ?>
    </div>
</div><!-- #primary -->
<?php get_footer();
