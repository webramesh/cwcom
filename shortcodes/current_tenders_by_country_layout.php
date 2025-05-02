<?php
/* attributes
country = ids of country comma separated
launch-plan = 1 or 0
  */

        $params = array(
            'post_type'      => 'tenders',
            'post_status'    => 'publish',
            'posts_per_page' => 6,
            'no_found_rows'  => false
        );

        $tax_q2=array();

        if ($atts['country']) {
            $tax_q = array('relation' => 'AND');
            $countries = explode(',', $atts['country']);
            foreach ($countries as $country) {
                $country = intval($country);
                array_push($tax_q2, array(
                    'taxonomy' => 'tender-countries',
                    'field' => 'term_id',
                    'terms' => $country,
                ));
            }
            array_push($tax_q, $tax_q2);

            $params["tax_query"] = $tax_q;
        }

         if ($atts['launch-plan']!=='') {
             if  ($atts['launch-plan']=='1') {
                 $params['order'] = 'DESC';
                 $params['orderby'] = 'meta_value';
                 $params['meta_key'] = 'wpcf-tender-start-date';
             } else {
                 $params['order'] = 'ASC';
                 $params['orderby'] = 'meta_value';
                 $params['meta_key'] = 'wpcf-tender-offer-deadline';
             }
          $val = intval($atts['launch-plan']);
             $params["meta_query"] = array(
                 array(
                     'key'     => 'wpcf-tender-launch-plan',
                     'value'   => $val,
                     'compare' =>  '=',
                 ),
             );
         } else {
             $params["meta_query"] = array(
                 array(
                     'key'     => 'wpcf-tender-archive-date',
                     'value'   => time(),
                     'compare' =>  '>',
                 ),
             );
             $params['order'] = 'ASC';
             $params['orderby'] = 'meta_value';
             $params['meta_key'] = 'wpcf-tender-archive-date';
         }

            // The Quer related by country current tenders
            $tenders = new WP_Query( $params );
          if ( $tenders->have_posts() ) {
                ?>
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


?>