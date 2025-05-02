<?php	
/*Shortcode [related_tenders_by_country]*/
add_shortcode( 'related_tenders_by_country', 'related_tenders_by_country_func');
function related_tenders_by_country_func( $atts ){
    ob_start();
	
    include(realpath( dirname(__FILE__) ).'/related_tenders_by_country_layout.php');
    return ob_get_clean();	
}
/*Shortcode [current_tenders_by_country country='' launch-plan ='']
country = term ids taxomony tender-countries comma separated
launch-plan = 0 or 1
*/
add_shortcode( 'current_tenders_by_country', 'current_tenders_by_country_func');
function current_tenders_by_country_func( $atts ){
    $atts = shortcode_atts(array(
        'country' => '',
        'launch-plan'=>'',
    ), $atts, 'current_tenders_by_country' );
    ob_start();

    include(realpath( dirname(__FILE__) ).'/current_tenders_by_country_layout.php');
    return ob_get_clean();
}
/*Shortcode  [safe_email]your_email@gmail.com[/safe_email]  echo antispambot('john.doe@mysite.com');*/
add_shortcode( 'safe_email', 'safe_email_func');
function safe_email_func( $atts, $content = null ){
    return antispambot($content);
}
/*Shortcode [search_tenders country='' market='' tender-status='' archive-slug='' class='' btntext='']
if you input value for parameter filter by this parameter not rendered because it has one value.
Parameters:
- country: term id for taxonomy 'tender-countries'/ empty for all countries
   / to render filter by country live empty or input more then one value comma separeted
- market: term id for taxonomy 'tender-market'/ empty for all markets
   / to render filter by markets live empty or input more then one value comma separeted
- tender-status: 'opened', 'current', 'upcoming', 'closed' /
   / to render filter by statuses live empty or input more then one value comma separeted
- archive-link: link to tender archive page without domain name.
*/
add_shortcode( 'search_tenders', 'search_tenders_func');
function search_tenders_func( $atts ){
    $atts = shortcode_atts(array(
        'class' => '',
        'btntext' =>  __( 'Apply filter', 'kadence-child' ),
        'country' => '',
        'market'=>'',
        'product' => '',
        'tender-status' => '',
        'archive-slug' => 'search-tenders'
    ), $atts, 'search_tenders' );
    su_query_asset ('css', 'shortcode-filter');
    ob_start();
    include(realpath( dirname(__FILE__) ).'/search_tenders.php');
    return ob_get_clean();
}
/* action for the form rendered by shortcode [search_tenders] */
add_action( 'admin_post_filter_action', 'filter_action_func' );
add_action( 'admin_post_nopriv_filter_action', 'filter_action_func' );

function filter_action_func() {   $data = array();
     if (isset($_POST['tender-countries']) && $_POST['tender-countries']) {  set_transient("country", $_POST['tender-countries']); }
     if (isset($_POST['tender-market']) && $_POST['tender-market']){  set_transient("market", $_POST['tender-market']);}
     if (isset($_POST['tender-products']) && $_POST['tender-products']) {  set_transient("product", $_POST['tender-products']); }
     if (isset($_POST['tender-status']) && $_POST['tender-status']) { set_transient("tender-status", $_POST['tender-status']); }
    /* Handle request then generate response using echo or leaving PHP and using HTML */
    if (isset($_POST['archive-slug']) && $_POST['archive-slug']) { wp_redirect(home_url().'/'.$_POST['archive-slug'].'/');
    } else { wp_redirect(home_url().'/search-tenders');}
    exit();
}

function encodeQueryData($data) {
   $str = '?'; $i = 1;
    foreach ($data as $k=>$d) {
        $str = $str . $k . '=' . trim($d);
       if ($i!==count($data)) {
           $str = $str .'&';
       }
        $i++;
    }
   return $str;
}

