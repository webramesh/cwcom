<?php
require_once  get_stylesheet_directory() . '/inc/yoast-seo-sitemap.php';
require_once  get_stylesheet_directory() . '/inc/gravity-form.php';
require_once  get_stylesheet_directory() . '/inc/authorization-notifications.php';
require_once  get_stylesheet_directory() . '/inc/repeatable_meta_group.php';
require_once  get_stylesheet_directory() . '/shortcodes/shortcodes.php';
require_once  get_stylesheet_directory() . '/inc/filter-functions.php';
require_once  get_stylesheet_directory() . '/inc/pci-plugin.php';
require_once  get_stylesheet_directory() . '/inc/customizer.php';
require_once  get_stylesheet_directory() . '/inc/_term-meta-regions.php';
require_once  get_stylesheet_directory() . '/inc/SubscribeTenders/index.php';
require_once  get_stylesheet_directory() . '/inc/child-theme-functions.php';
require_once  get_stylesheet_directory() . '/inc/trackandemail.php';
require_once  get_stylesheet_directory() . '/inc/userdetails.php';
require_once  get_stylesheet_directory() . '/inc/usertendersubmit.php';

add_action('wp_ajax_loadmorebutton', 'kd_loadmore_ajax_handler');
add_action('wp_ajax_nopriv_loadmorebutton', 'kd_loadmore_ajax_handler');

function kd_loadmore_ajax_handler()
{

	// prepare our arguments for the query
	$params = json_decode(stripslashes($_POST['query']), true); // query_posts() takes care of the necessary sanitization
	$params['paged'] = $_POST['page'] + 1; // we need next page to be loaded
	// it is always better to use WP_Query but not here
	query_posts($params);
	global $wp_query;
	$wp_query->is_search = false;

	ob_start();

	get_template_part('template-parts/content/archive-article-loop', get_query_var('post_type'));
	$posts_html = ob_get_contents(); // we pass the posts to variable
	ob_end_clean(); // clear the buffer

	echo $posts_html;
	die; // here we exit the script and even no wp_reset_query() required!
}


/**
 * Ajax pagination function
 * Return load more button html string
 */
if (!function_exists('ajax_pagination')) {
	function ajax_pagination()
	{
		global $wp_query;
		su_query_asset('js', 'load-more-script');
		$paged = (get_query_var('paged')) ? get_query_var('paged') : ((get_query_var('page')) ? get_query_var('page') : 1);

		$translate['load-more'] =  __('Load more', 'kadence-child');

		if (empty($paged)) $paged = 1;
		$total = $wp_query->max_num_pages;
		if (!$total) $total = 1;

		$output = '';
		$style = '';
		if (!($total > 1) || !($paged < $total)) {
			$style = 'style="display:none;"';
		}
		// ajax load more -------------------------------------------------
		$output .= '<br><a id="kd_loadmore" class="button button_js" ' . $style . '>';
		$output .= '<span class="button_icon"><i class="icon-layout"></i></span>';
		$output .= '<span class="button_label">' . $translate['load-more'] . '</span>';
		$output .= '</a>';
		return $output;
	}
}
/*assign template past-tenders-template.php classis as arhive tenders has
when doing ajax get_post_type() return nothing but get_query_var( 'post_type' ) exists so
filtering classes becouse function et_archive_container_classes() based on get_post_type() func
*/
add_filter('kadence_archive_container_classes',  'archive_classes');

function archive_classes($classes)
{
	if (get_page_template_slug() === 'past-tenders-template.php' || is_post_type_archive('tenders')) {
		$classes   = array();
		$classes[] = 'content-wrap';
		$classes[] = 'grid-cols';
		if (Kadence\kadence()->option('tenders_archive_columns')) {
			$classes[] = 'tenders-archive';
			if ('1' === Kadence\kadence()->option('tenders_archive_columns')) {
				$placement = Kadence\kadence()->option('tenders_archive_item_image_placement', 'above');
				$classes[] = 'grid-sm-col-1';
				$classes[] = 'grid-lg-col-1';
				$classes[] = 'item-image-style-' . $placement;
			} elseif ('2' === Kadence\kadence()->option('tenders_archive_columns')) {
				$classes[] = 'grid-sm-col-2';
				$classes[] = 'grid-lg-col-2';
				$classes[] = 'item-image-style-above';
			} elseif ('4' === Kadence\kadence()->option('tenders_archive_columns')) {
				$classes[] = 'grid-sm-col-2';
				$classes[] = 'grid-lg-col-4';
				$classes[] = 'item-image-style-above';
			} else {
				$classes[] = 'grid-sm-col-2';
				$classes[] = 'grid-lg-col-3';
				$classes[] = 'item-image-style-above';
			}
		}
	}
	return $classes;
}
/* function return objects for terms shared taxonomy filtered by
$taxonomy - taxonomy slug
$post_type - $post_type slug
$term_ids -  comma separated string? example $term_ids= '45,67,69'
 */
function get_terms_by_post_type($taxonomy, $post_type, $term_ids = null, $orderby = 'name', $order = 'ASC')
{
	switch ($orderby) {
		case 'name':
			$orderby = 't.name';
			break;
		case 'slug':
			$orderby = 't.slug';
			break;
		case 'term_id':
			$orderby = 't.term_id';
			break;
		case 'description':
			$orderby = 'tt.description';
			break;
		case 'term_group':
			$orderby = 't.term_group';
			break;
	}
	global $wpdb;
	$query_where = '';
	if (!is_null($term_ids)) {
		$query_where = "AND t.term_id IN(" . $term_ids . ")";
	}
	$ord = $orderby . ' ' . $order;
	$query = $wpdb->prepare(
		"SELECT t.*, COUNT(*) AS count, tt.description, tt.term_taxonomy_id, tt.taxonomy from h1p4m_terms AS t
	INNER JOIN h1p4m_term_taxonomy AS tt ON t.term_id = tt.term_id
	INNER JOIN h1p4m_term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id
	INNER JOIN h1p4m_posts AS p ON p.ID = r.object_id
	WHERE p.post_status='publish' " . $query_where . " AND p.post_type='%s' AND tt.taxonomy='%s'
	GROUP BY t.term_id ORDER BY " . $ord,
		$post_type,
		$taxonomy
	);
	$results = $wpdb->get_results($query);
	return $results;
}

/*rendering page content for archive pages such as blog  by page slug="blog"  */
add_action('kadence_before_main_content', 'blog_content_archive', 1);
function blog_content_archive()
{
	if (!is_singular() && get_post_type() == 'post') {;

		$page   = get_page_by_path("blog", OBJECT, 'page');	//TODO Change this to the ID of the page you want to use for the blog archive
		$output =  apply_filters('the_content', $page->post_content);
		echo "<div class='blog__intro'>" . $output . "</div>";
	}
}

/* filtering opened tenders with tender-archive-date > now in archive 'search-tenders' */
add_action('pre_get_posts', 'current_tenders');
function current_tenders($query)
{
	$tr_query = array();

	if (false !== get_transient('country')) {
		$tr_query['country'] = get_transient('country');
	}
	if (false !== get_transient('market')) {
		$tr_query['market'] = get_transient('market');
	}
	if (false !== get_transient('product')) {
		$tr_query['product'] = get_transient('product');
	}
	if (false !== get_transient('tender-status')) {
		$tr_query['tender-status'] = get_transient('tender-status');
	}

	if ($query->is_main_query() && !$query->is_admin) :
		$tax = '';
		if (is_object(get_queried_object())) {
			if (get_class(get_queried_object()) === 'WP_Term') {
				$tax = get_queried_object()->taxonomy;
			}
		}

		$tax_ar = array('tender-countries', 'tender-market', 'tender-products', 'tender-regions');

		if ((is_post_type_archive('tenders') && empty($tr_query)) || in_array($tax, $tax_ar)) :

			$meta_query = array(
				array(
					'key' => 'wpcf-tender-archive-date',
					'value' => time(),
					'compare' => '>'
				)
			);
			$query->set('meta_query', $meta_query);
			$query->set('orderby', 'ID');
			$query->set('order', 'DESC');

		elseif (is_post_type_archive('tenders') && !empty($tr_query)) :
			$filter_val = 0;
			$tax_q2 = array();
			$tax_q = array('relation' => 'AND');
			if (array_key_exists('country', $tr_query)) {
				$filter_val++;
				array_push($tax_q2, array(
					'taxonomy' => 'tender-countries',
					'field'    => 'term_id',
					'terms'    => $tr_query['country'],
				));
			}
			if (array_key_exists('market', $tr_query)) {
				$filter_val++;
				array_push($tax_q2, array(
					'taxonomy' => 'tender-market',
					'field' => 'id',
					'terms' => $tr_query['market']
				));
			}
			if (array_key_exists('product', $tr_query)) {
				$filter_val++;
				array_push($tax_q2, array(
					'taxonomy' => 'tender-products',
					'field'    => 'term_id',
					'terms'    => $tr_query['product'],
				));
			}
			if ($filter_val > 1) {
				array_push($tax_q, $tax_q2);
				$query->set('tax_query', $tax_q);
			} elseif ($filter_val > 0) {
				$query->set('tax_query', $tax_q2);
			}
			if (isset($tr_query['tender-status']) && $tr_query['tender-status']) {
				if ($tr_query['tender-status'] == 'upcoming') {
					$meta_query  = array(
						array(
							'key' => 'wpcf-tender-start-date',
							'value' => time(),
							'compare' => '>',
						)
					);
					$query->set('meta_query', $meta_query);
					$query->set('order', 'ASC');
					$query->set('orderby', 'meta_value');
					$query->set('meta_key', 'wpcf-tender-start-date');
				} elseif ($tr_query['tender-status'] == 'closed') {
					$meta_query  = array(
						array(
							'key' => 'wpcf-tender-archive-date',
							'value' => time(),
							'compare' => '<=',
						)
					);
					$query->set('meta_query', $meta_query);
					$query->set('order', 'DESC');
					$query->set('orderby', 'meta_value');
					$query->set('meta_key', 'wpcf-tender-archive-date');
				} elseif ($tr_query['tender-status'] == 'current') {
					$meta_query  = array(
						array(
							'key' => 'wpcf-tender-offer-deadline',
							'value' => time(),
							'compare' => '>=',
						)
					);
					$query->set('meta_query', $meta_query);
					$query->set('order', 'ASC');
					$query->set('orderby', 'meta_value');
					$query->set('meta_key', 'wpcf-tender-offer-deadline');
				} else {
					$meta_query  = array(
						array(
							'key' => 'wpcf-tender-archive-date',
							'value' => time(),
							'compare' => '>',
						)
					);
					$query->set('meta_query', $meta_query);
					$query->set('order', 'DESC');
					$query->set('orderby', 'meta_value');
					$query->set('meta_key', 'wpcf-tender-archive-date');
				}
			}

		endif;
	endif;
}

/**
 * Registers the sidebars.
 */
function kadence_child_action_register_sidebars()
{
	$widgets = array(
		'sidebar-lang1' => __('Sidebar for language 1', 'kadence-child'),
		'sidebar-lang2' => __('Sidebar for language 2', 'kadence-child'),
		'sidebar-lang3' => __('Sidebar for language 3', 'kadence-child'),
		'sidebar-lang4' => __('Sidebar for language 4', 'kadence-child'),
		'sidebar-lang5' => __('Sidebar for language 5', 'kadence-child'),
		'sidebar3'      => __('Sidebar 3', 'kadence-child'),
		'sidebar4'      => __('Sidebar 4', 'kadence-child'),
		'hero_image'      => __('Hero header for Current Tenders', 'kadence-child'),
		'tenders_bottom'      => __('Tender category bottom block', 'kadence-child'),
    	'social_sidebar'      => __('Sidebar without social', 'kadence-child'),
	);

	foreach ($widgets as $id => $name) {
		register_sidebar(
			array(
				'name'          => $name,
				'id'            => $id,
				'description'   => esc_html__('Add widgets here.', 'kadence'),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
}
add_action('widgets_init', 'kadence_child_action_register_sidebars', 100);
add_filter('body_class', 'my_body_classes');
function my_body_classes($classes)
{
	$classes[] = 'kadence-child';
	return $classes;
}

require_once locate_template('inc/page-to-pdf.php');
add_action('page_top', 'top_page_renderer', 10, 1);
function top_page_renderer($post)
{
    if (get_post_type($post->ID) == 'tenders') {
        // Path to your image file
        $image_path = '/var/www/concealedwines.com/html/wpsysfiles/wp-content/themes/kadence-child/images/concealedwinespdflogo.jpg';

        
        // Read image file
        $image_data = file_get_contents($image_path);
        
        // Base64 encode the image data
        $base64_image = base64_encode($image_data);
        
        // Determine MIME type (adjust if your image is not a JPEG)
        $mime_type = 'image/jpeg';
        
        // Output the image tag with base64 encoded data
        echo '<div class="logo">';
        echo '<img src="data:' . $mime_type . ';base64,' . $base64_image . '" width="280" height="55">';
        echo '<span class="header-text"></span></div><hr>';
    }
}

/* remove empty <p></p> in shortcode gutenberg block*/
function do_shortcode_in_gut($block_content, $block)
{
	if ($block['blockName'] === 'core/shortcode') {
		$block_content = $block['innerContent'][0];
	}
	return $block_content;
}
add_filter('render_block', 'do_shortcode_in_gut', 99, 2);

/*Avoid empty value in metadata*/
add_filter("add_term_metadata",    'func_no_add_emty_val', 10, 4);
add_filter("add_post_metadata",    'func_no_add_emty_val', 10, 4);
add_filter("add_user_metadata",    'func_no_add_emty_val', 10, 4);
add_filter("add_comment_metadata", 'func_no_add_emty_val', 10, 4);
add_filter("update_comment_metadata", 'func_del_if_emty_val', 1000, 5);
add_filter("update_post_metadata",    'func_del_if_emty_val', 1000, 5);
add_filter("update_term_metadata",    'func_del_if_emty_val', 1000, 5);
add_filter("update_user_metadata",    'func_del_if_emty_val', 1000, 5);
function func_no_add_emty_val($check, $object_id, $meta_key, $meta_value)
{
	if ($meta_key !== 'wpcf-tender-launch-plan') {
		if (empty($meta_value) || is_null($meta_value)) {
			return true;
		}
	}
	return $check;
}
function func_del_if_emty_val($check, $object_id, $meta_key, $meta_value, $prev_value)
{
	if ($meta_key !== 'wpcf-tender-launch-plan') {
		if ($meta_value == '' || is_null($meta_value)) {
			delete_post_meta($object_id, $meta_key, $prev_value);
			return true;
		}
	}
	return $check;
}


/*???*/
add_action('init', 'userID');

function userID()
{

	$user_ID = get_current_user_id();
}

/* Add body classes */
function add_page_class_to_the_body($classes)
{
	global $wp_query;
	$bg_class = '';

	if (array_key_exists('gfur_activation', $_GET)) {
		foreach ($classes as $k => $class) {
			if ($class === 'transparent-header' || $class === 'mobile-transparent-header') {
				unset($classes[$k]);
			}
		}
		$classes[] = 'additional-main';
	}
	if (isset($wp_query)) {
		$theid = intval($wp_query->queried_object_id);
		$page_class = get_post_meta($theid, 'page_class', true);
		if ($wp_query->is_page() && $page_class) {
			$classes[] = $page_class;
		}
		$classes[] = 'cwcom';
	}
	return $classes;
}
add_filter('body_class', 'add_page_class_to_the_body', 100);

add_filter('kadence_archive_post_type_slug', 'func_archive_post_type_slug', 10);
function func_archive_post_type_slug($slug)
{
	global $wp_query;
	if (empty($slug) && !is_search()) {
		$slug = $wp_query->query_vars['post_type'];
	}
	return $slug;
}

/*add_action('kadence_after_footer', 'func_kadence_after_footer');
function func_kadence_after_footer()
{ ?>
	<div style="display:none;">
		<?php login_reg_forms(); ?>
	</div>
	<?php  }
	*/

add_action('kadence_before_main_content', 'func_output_ligin_reg_fom');
function func_output_ligin_reg_fom()
{
	global $post;
	if (!is_user_logged_in() &&  $post->post_name == 'client-area') {
?>
		<div class="login_form_wrapper">
			<div class="login_form_container">
				<? //php login_reg_forms(); 
				?>
				<?php echo do_shortcode('[login-form]'); ?>
			</div>
		</div>
	<?php
	}
}



function login_form_shortcode($atts, $content = null)
{

	extract(shortcode_atts(array(
		'form_id' => '',
		'label_username' => '',
		'label_password' => '',
		'label_log_in' => ''
	), $atts));

//	if (!is_user_logged_in()) {
		$id = 'login_form';
		$uname = 'Email';
		$pass = 'Password';
		$login = 'Login';
		$form = wp_login_form(array('echo' => false, 'form_id' => $id, 'label_username' => $uname, 'label_password' => $pass, 'label_log_in' => $login));
//	}
	return $form;
}
add_shortcode('loginform', 'login_form_shortcode');


function login_forms()
{	?>
<?php $forms_content = '
	<div id="fancybox_form" style="width:320px;">
		<div class="login_form_box">
			<div class="form_title">' . esc_html__('Sign in', 'kadence-child') . '</div>' . do_shortcode('[loginform]') . '
		</div>
		<div class="register_form_fancy_link2 reg_link"><a href="#">' . esc_html__('New User Registration', 'kadence-child') . '</a></div>
		<div class="recover_passwd"><a href="/client-area/member-password-lost/">Recover password</a> </div>
	</div>';
	return $forms_content;
}
add_shortcode('login-form', 'login_forms');


function reg_forms()
{  $register_form_id='';
if (class_exists('GFAPI')) {
	$register_form_id = get_form_id_by_name('Registration');
}
?>
<?php $forms_content = '
	<div id="fancybox_form" style="width:320px;">
		<div class="register_form_fancy">
			<div class="form_title">' . esc_html__('Sign up', 'kadence-child') . '</div>
			<div class="register_desc">' . esc_html__("It is no cost to sign up and directly after you click Register you will get an activation account link to your inbox..", 'kadence-child') . '</div>
			' . do_shortcode('[gravityform id="' . $register_form_id . '" title="false" description="false"]') . do_shortcode('[nextend_social_login provider="facebook,google"]') . '
		</div>
		<div class="register_form_fancy_link2 log_link"><a href="#boxzilla-23028">' . esc_html__('Already registered? Sign in', 'kadence-child') . '</a></div>
		<div class="recover_passwd"><a href="/client-area/member-password-lost/">Recover password</a> </div>
	</div>';
	return $forms_content;
}
add_shortcode('register-form', 'reg_forms');

