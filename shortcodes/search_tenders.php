<?php
if ($atts['class']) {
	$form_wrap_class = $atts['class'] . '"';
}
?>
 <div class="form-wrap <?php echo $atts['class'] ? $atts['class'] : ''; ?>">
   <form action="<?php echo admin_url('admin-post.php');; ?>" method="post" id="filter-shortcode">
      <div class="shortcode-filter-wrap">
<?php
/* tender markets list */
$markets = array();
$str = preg_replace('/\s+/', '', strip_tags($atts['market']));
$markets = explode(',', $str);

if (!$atts['market'] || count($markets) > 1) {
	$terms = get_terms_by_post_type('tender-market', 'tenders');
	if ($terms):
		?>
                   <select name="tender-market"><option value=""><?php echo __('All markets', 'kadence-child'); ?> </option>
                   <?php
		foreach ($terms as $term):
			if (!$atts['market'] || in_array($term->term_id, $markets)) {
				?>
                       <option value="<?php echo $term->term_id; ?>"><?php echo $term->name . '(' . $term->count . ')'; ?></option>
                   <?php }
		endforeach;
	endif; ?>
                    </select>
   <?php } elseif (count($markets) === 1) { ?>
             <input type="hidden" name="tender-market" value="<?php echo $atts['market']; ?>">
   <?php }

/* Country list */
$countries = array();
if ($atts['country']) {
	$str = preg_replace('/\s+/', '', strip_tags($atts['country']));
	$countries = explode(',', $str);
}

$terms = get_terms_by_post_type('tender-countries', 'tenders');
$chosen_countries = array();
if (empty($countries)) {
	$chosen_countries = get_theme_mod('filter-countries');
} elseif (count($countries) > 1) {
	$chosen_countries = $countries;
} else { ?> <input type="hidden" name="tender-countries" value="<?php echo $atts['country']; ?>"> <?php }

if (empty($countries) || count($countries) > 1) {
	if ($terms): ?>
                     <select name="tender-countries"><option value=""><?php echo __('All countries', 'kadence-child'); ?> </option>
                     <?php
		foreach ($terms as $term):
			if (empty($chosen_countries) || in_array($term->term_id, $chosen_countries)) {
				?>
                             <option value="<?php echo $term->term_id; ?>"> <?php echo $term->name . '(' . $term->count . ')'; ?></option>
                         <?php }
		endforeach;
	endif; ?>
                 </select>
             <?php }

/* product type list */
$products = array();
if ($atts['product']) {
	$str = preg_replace('/\s+/', '', strip_tags($atts['product']));
	$products = explode(',', $str);
}

$terms = get_terms_by_post_type('tender-products', 'tenders');
$chosen_products = array();
if (count($products) === 0) {
	$chosen_products = get_theme_mod('filter-products');
} elseif (count($products) > 1) {
	$chosen_products = $products;
} else { ?>  <input type="hidden" name="tender-products" value="<?php echo $atts['product']; ?>">
<?php }
if (count($products) === 0 || count($products) > 1) {
	if ($terms): ?>
                   <select name="tender-products"><option value=""><?php echo __('All products', 'kadence-child'); ?> </option>
                   <?php
		foreach ($terms as $term):
			if (empty($chosen_products) || in_array($term->term_id, $chosen_products)) {
				?>
                                <option value="<?php echo $term->term_id ?>"><?php echo $term->name . '(' . $term->count . ')'; ?> </option>
                            <?php }
		endforeach;
	endif; ?>
                    </select>
          <?php }
/* tender statuses list */
$statuses = array();
$str = preg_replace('/\s+/', '', strip_tags($atts['tender-status']));
$statuses = explode(',', $str);
if (!$atts['tender-status'] || count($statuses) > 1) {
	echo '<select name="tender-status"><option value="opened">' . __('Opened tenders', 'kadence-child') . '</option>';
	if (!$atts['tender-status'] || in_array('current', $statuses)) {
		echo '<option value="current">' . __('- current tenders', 'kadence-child') . '</option>';
	}
	if (!$atts['tender-status'] || in_array('upcoming', $statuses)) {
		echo '<option value="upcoming">' . __('- upcoming tenders', 'kadence-child') . '</option>';
	}
	if (!$atts['tender-status'] || in_array('closed', $statuses)) {
		echo '<option value="closed">' . __('Closed tenders', 'kadence-child') . '</option>';
	}
	echo '</select>';
} elseif (count($statuses) === 1) { ?>
             <input type="hidden" name="tender-status" value="<?php echo $atts['tender-status']; ?>">
   <?php
}
/* hidden fields and buttons */
?>

          <input type="hidden" name="archive-slug" value="<?php echo $atts['archive-slug']; ?>">
       </div>
       <div class="filter button-wrap">
           <button type ="submit" class="submit button"><?php echo $atts['btntext']; ?> </button>
       </div>
            <input type="hidden" name="action" value="filter_action">
   </form>
</div>
