<?php
function current_tenders_sitemap_index($sitemap_index)
{
	global $wpseo_sitemaps;


	$sitemap_url    = home_url('current_tenders-sitemap.xml');
	$sitemap_date   = $wpseo_sitemaps->get_last_modified('tenders');
	$custom_sitemap = <<<SITEMAP_INDEX_ENTRY
<sitemap>
  <loc>%s</loc>
  <lastmod>%s</lastmod>
</sitemap>
SITEMAP_INDEX_ENTRY;
	$sitemap_index .= sprintf($custom_sitemap, $sitemap_url, $sitemap_date);
	return $sitemap_index;
}
add_filter('wpseo_sitemap_index', 'current_tenders_sitemap_index');



function current_tenders_sitemap_register()
{
	global $wpseo_sitemaps;
	if (isset($wpseo_sitemaps) && !empty($wpseo_sitemaps)) {
		$wpseo_sitemaps->register_sitemap('current_tenders', 'current_tenders_sitemap_generate');
	}
}
add_action('init', 'current_tenders_sitemap_register');

function current_tenders_sitemap_generate()
{
	global $wpseo_sitemaps;
	
    $thememetebox =new ThemeMetabox;
    $thememetebox->hhs_get_sample_options();
    $slugs = $thememetebox->slug;	
	$page   = get_page_by_path("current-tenders/tenders-for-country", OBJECT, 'page');
	$repeatable_fields = get_post_meta($page->ID, 'repeatable_fields', true);
    $urls = [];
	
	if ($repeatable_fields) :

    foreach ($repeatable_fields as $field) {
		
		$post_args = array(
			'posts_per_page' => 1,
			'post_type' => 'tenders', 
			'tax_query' => array(
			  'relation' => 'OR',
				array(
					'taxonomy' => 'tender-countries', 
					'field' => 'slug', 
					'terms' => $slugs[$field['select']],
				),
				array(
					'taxonomy' => 'tender-products',
					'field' => 'slug', 
					'terms' => $slugs[$field['select']],
				)
			),
			'orderby' => 'date',
			'order' => 'DESC',
		);

		$latest_cpt = get_posts($post_args);
	    if (explode(":", $field['select'])[0] === "tender-countries") {
			$url_end = 'tenders-for-country/?country='.$slugs[$field['select']];			
		}  
	    if (explode(":", $field['select'])[0] === "tender-products") {
			$url_end = 'tenders-for-product/?wpvtender-products='.$slugs[$field['select']];			
		} 		
	
	   $urls[] = $wpseo_sitemaps->renderer->sitemap_url([
	 	   'mod'    => $latest_cpt[0]->post_date_gmt,
		   'loc'    => home_url().'/current-tenders/'.$url_end ,
		   'images' => '0'
	    ]);
	}
	
    endif; 
	$sitemap_body = <<<SITEMAP_BODY
<urlset
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
  xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-image/1.1 http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd"
  xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
%s
</urlset>
SITEMAP_BODY;
	$sitemap = sprintf($sitemap_body, implode("\n", $urls));
	$wpseo_sitemaps->set_sitemap($sitemap);
}

/* seo metadata for country/product page with query string */
add_filter('wpseo_metadesc', 'add_custom_meta_desc', 10, 1);
function add_custom_meta_desc($wpseo_replace_vars)
{
	$query_term='';
	if (is_page('tenders-for-country') && !empty($_GET['country'])) {
        $country = $_GET['country']; 
	    $query_term = 'tender-countries:' . get_term_by('slug', $country, 'tender-countries')->term_id;
    } elseif (is_page('tenders-for-product') && !empty($_GET['wpvtender-products'])) {
	    $prod = $_GET['wpvtender-products']; 
	    $query_term = 'tender-products:' . get_term_by('slug', $prod, 'tender-products')->term_id;
	
    }
    if ($query_term) {	
        $page   = get_page_by_path("current-tenders/tenders-for-country", OBJECT, 'page');
		$repeatable_fields = get_post_meta($page->ID, 'repeatable_fields', true);
			
		 if($repeatable_fields) {
		    foreach ($repeatable_fields as $key => $value) {
			   if (in_array($query_term, $value)) {
			     	$wpseo_replace_vars = $value['meta_descr'];
			   }
		    }
		 }	
		 
    }	
   return $wpseo_replace_vars;
}

add_filter('wpseo_title', 'add_custom_meta_title', 10, 1);
function add_custom_meta_title($title)
{ 
	$query_term='';
	if (is_page('tenders-for-country') && !empty($_GET['country'])) {
        $country = $_GET['country']; 
	    $query_term = 'tender-countries:' . get_term_by('slug', $country, 'tender-countries')->term_id;
    } elseif (is_page('tenders-for-product') && !empty($_GET['wpvtender-products'])) {
	    $prod = $_GET['wpvtender-products']; 
	    $query_term = 'tender-products:' . get_term_by('slug', $prod, 'tender-products')->term_id;
	
    }
    if ($query_term) {	
        $page   = get_page_by_path("current-tenders/tenders-for-country", OBJECT, 'page');
		$repeatable_fields = get_post_meta($page->ID, 'repeatable_fields', true);
				
		 if($repeatable_fields) {
		    foreach ($repeatable_fields as $key => $value) {
			   if (in_array($query_term, $value)) {
			     	 $title = $value['meta_title'];
			   }
		    }
		 }	
    }	
	return $title;
}


function design_canonical($url)
{
	global $post;
	$country = $_GET['country'];
	$permalink = get_permalink($post->ID);

	if (!empty($country)) {
		return $permalink. '?country='. $country;
	} else {
		return $url;
	}
}
add_filter('wpseo_canonical', 'design_canonical');

?>