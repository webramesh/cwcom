<?php

global $wp_query;

if (is_object($wp_query)) {
$post_type = get_post_type();
    if ($post_type=="tenders") {
	$params = array(
			'post_type'      => 'tenders',
			'post_status'    => 'publish',
			'posts_per_page' => 6,
            'post__not_in' => array(get_the_id()),
			'order' => 'DESC',
            'orderby' => 'meta_value',
            'meta_key' => 'wpcf-tender-archive-date',
			'meta_query' => array(
               array(
                  'key'     => 'wpcf-tender-archive-date',
                  'value'   => time(),
                  'compare' =>  '>=',
                   ),
             ),

			'no_found_rows'  => false	
	);

        $tax_q  = $tax_q2 = $tax_q3=array();
/*Find tenders by all countries and one first product type*/
        $countries = (get_the_terms( get_the_id(), "tender-countries" ));
        $products = (get_the_terms( get_the_id(), "tender-products" ));
        $product = $products[0];
       if (!empty($countries)) {
           foreach ($countries as $country) { $country_ids[] = $country->term_id; }
               $tax_q3 = array(
                   'taxonomy' => 'tender-countries',
                   'field' => 'term_id',
                   'terms' => $country_ids,
               );
           $tax_q2 = $tax_q = array('relation' => 'OR');
           array_push($tax_q2, $tax_q3);
       }
       if ($product->term_id) {
           $tax_q = array('relation' => 'AND');
           array_push($tax_q, array(
               'taxonomy' => 'tender-products',
               'field' => 'term_id',
               'terms' => $product->term_id,
           ));
       }
       if (!empty($tax_q)&&!empty($tax_q2)) {
           array_push($tax_q, $tax_q2);
       } elseif (empty($tax_q)&& !empty($tax_q2)) {
           $tax_q = $tax_q2;
       }

	   $params["tax_query"] = $tax_q;


		// The Quer related by country current tenders
        $tenders = new WP_Query( $params );


            if ( $tenders->have_posts() ) {
  
?>
                <h2 class="list-title"><?php echo __('Related Tenders ', 'kadence-child'); ?></h2>
           <div class="tenders wrap">
	<?php		

					while ( $tenders->have_posts() ) {
						 $tenders->the_post();
						 $prod_types = strip_tags( get_the_term_list( get_the_ID(), 'tender-products', '', ', ') ).': ';
                        $tender_nom = '('.get_post_meta( get_the_ID(), 'wpcf-tender-reference-number', true).')';
						 ?>
						<div class="title_line"> <?php
                            echo Kadence\kadence()->get_icon( 'check' ); ?>		
                            <span class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><span class="type-wine"><?php echo $prod_types ?></span> <?php the_title(); echo $tender_nom ?> </a></span><!-- .entry-header -->
	                    </div>
		<?php			
					} 
					?>
		
           </div>	<?php 
	       }
  wp_reset_postdata();

  }
} ?>