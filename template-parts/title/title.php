<?php
/**
 * Template part for displaying a post's title
 *
 * @package kadence
 */

namespace Kadence;

do_action( 'kadence_single_before_entry_title' );
$query_term  =''.
$subtitle = get_post_meta(get_the_ID(), 'subtitle', true);
    $page   = get_page_by_path("current-tenders/tenders-for-country", OBJECT, 'page');
    $repeatable_fields = get_post_meta($page->ID, 'repeatable_fields', true);
   
if (get_post_field( 'post_name', get_the_ID() )==="tenders-for-country"  && !empty($_GET['country'])) {
    $country = $_GET['country']; 
	$query_term = 'tender-countries:' . get_term_by('slug', $country, 'tender-countries')->term_id;
} elseif (get_post_field( 'post_name', get_the_ID() )==="tenders-for-product"  && !empty($_GET['wpvtender-products'])) {
	    $prod = $_GET['wpvtender-products']; 
	    $query_term = 'tender-products:' . get_term_by('slug', $prod, 'tender-products')->term_id;
	  
} else {
    the_title('<h1 class="entry-title">', '</h1>');
    if ($subtitle) {  echo '<h2 class="subtitle">'. $subtitle.'</h2>'; }
}   
if ($query_term) {	
	if($repeatable_fields) {
      foreach ($repeatable_fields as $key => $value) {
	 
        if (in_array($query_term, $value) ) {
              echo '<h1 class="entry-title">'. $value['tag_h1'] . '</h1>';
              if ($value['tag_h2']) {  echo '<h2 class="subtitle">'. $value['tag_h2'].'</h2>'; }
           }
        }
    }
}
do_action( 'kadence_single_after_entry_title' );


