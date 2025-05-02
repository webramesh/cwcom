<?php
/* Override shortcode post-category-image-with-grid-and-slider */
if (class_exists('Pciwgas_Script')) {
	add_action('wp_head', 'override_pci_cat_shortcodes');
	function override_pci_cat_shortcodes()
	{

		remove_shortcode('pci-cat-grid');
		remove_shortcode('pci-cat-slider');
		add_shortcode('pci-cat-grid', 'theme_pci_cat_grid_shortcode');
		'theme_pci_cat_slider_shortcode';
		add_shortcode('pci-cat-slider', 'theme_pci_cat_slider_shortcode');
	}

	function theme_pci_cat_grid_shortcode($atts, $content)
	{
		su_query_asset('css', 'theme-pciwgas-publlic-style');
		$base_country_link = get_theme_mod('base-country-link');
		$base_product_link = get_theme_mod('base-product-link');
		// SiteOrigin Page Builder Gutenberg Block Tweak - Do not Display Preview
		if (isset($_POST['action']) && ($_POST['action'] == 'so_panels_layout_block_preview' || $_POST['action'] == 'so_panels_builder_content_json')) {
			return '[pci-cat-grid]';
		}

		// Divi Frontend Builder - Do not Display Preview
		if (function_exists('et_core_is_fb_enabled') && isset($_POST['is_fb_preview']) && isset($_POST['shortcode'])) {
			return '<div class="pciwgas-builder-shrt-prev">
					<div class="pciwgas-builder-shrt-title"><span>' . esc_html__('Post Category Image Grid - Shortcode', 'post-category-image-with-grid-and-slider') . '</span></div>
					pci-cat-grid
				</div>';
		}

		// Fusion Builder Live Editor - Do not Display Preview
		if (class_exists('FusionBuilder') && ((isset($_GET['builder']) && $_GET['builder'] == 'true') || (isset($_POST['action']) && $_POST['action'] == 'get_shortcode_render'))) {
			return '<div class="pciwgas-builder-shrt-prev">
					<div class="pciwgas-builder-shrt-title"><span>' . esc_html__('Post Category Image Grid - Shortcode', 'post-category-image-with-grid-and-slider') . '</span></div>
					pci-cat-grid
				</div>';
		}

		// Shortcode Parameter
		$atts = extract(shortcode_atts(array(
			'size'    			=> 'full',
			'term_id' 			=> null,
			'taxonomy'          => 'category',
			'design'     		=> 'design-1',
			'orderby'    		=> 'name',
			'order'      		=> 'ASC',
			'columns'    		=> 3,
			'show_title' 		=> 'true',
			'show_count'		=> 'true',
			'show_desc'  		=> 'true',
			'hide_empty' 		=> 'true',
			'exclude_cat'		=> array(),
			'extra_class'		=> '',
			'className'			=> '',
			'align'				=> '',
			'post_type'         => 'tenders'
		), $atts, 'pci-cat-grid'));

		$size 		 	= !empty($size) 							? $size 						: 'full';
		$term_ids = $term_id;
		$term_id 		= !empty($term_id) 						? explode(',', $term_id) 		: '';
		$taxonomy 	 	= !empty($taxonomy) 						? $taxonomy 			    	: 'category';
		$design		 	= !empty($design) 						? $design 						: 'design-1';
		$show_title	 	= ($show_title == 'true') 				? true 							: false;
		$show_count	 	= ($show_count == 'true') 				? true 							: false;
		$show_desc	 	= ($show_desc == 'true') 					? true 							: false;
		$hide_empty  	= ($hide_empty == 'false') 				? false 						: true;
		$exclude_cat 	= !empty($exclude_cat)					? explode(',', $exclude_cat) 	: array();
		$columns 	 	= (!empty($columns) && $columns <= 4) 	? $columns 						: 3;
		$align			= !empty($align)							? 'align' . $align				: '';
		$extra_class	= $extra_class . ' ' . $align . ' ' . $className;
		$extra_class	= pciwgas_sanitize_html_classes($extra_class);
		$wrap__grid_class = "grid-cols grid-sm-col-2 grid-lg-col-" . $columns;
		$count 		 = 0;

		// get terms and workaround WP bug with parents/pad counts
		$args = array(
			'orderby'    => $orderby,
			'order'      => $order,
			'include'    => $term_id,
			'hide_empty' => $hide_empty,
			'exclude'	 => $exclude_cat,
		);

		/* $terms= get_terms( $taxonomy, $args );*/
		$post_categories = get_terms_by_post_type($taxonomy, $post_type, $term_ids, $orderby, $order);


		ob_start();

		if ($post_categories) { ?>
			<div class="pciwgas-cat-wrap  <?php echo $wrap__grid_class . ' ' . $extra_class; ?> pciwgas-<?php echo $design; ?>">
				<?php
				foreach ($post_categories as $category) {
					$category_image		= pciwgas_term_image($category->term_id, $size);
					$term_link 			= get_term_link($category, $taxonomy);
					$wrapper_cls		= "pciwgas-pdt-cat-grid";
					if ($category->taxonomy === 'tender-countries') $link = home_url() . $base_country_link . $category->slug;
					if ($category->taxonomy === 'tender-products') {
						$link = home_url() . $base_product_link . $category->slug;
					}
					$tax_img = get_term_meta($category->term_id, 'wpcf-category-image-preview', true);


					$query_args0 = array(
						'post_type'      => 'tenders',
						'post_status'    => 'publish',
						'posts_per_page' => -1,
						'order' => 'ASC',
						//'orderby' => 'meta_value',
						//'meta_key' => 'wpcf-tender-offer-deadline',
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key'     => 'wpcf-tender-archive-date',
								'value'   => time(),
								'compare' =>  '>=',
							),
							/*array(
								'key'     => 'wpcf-tender-launch-plan',
								'value'   => 0,
								'compare' =>  '=',
							),*/
						),
						'tax_query' => array(
							array(
								'taxonomy' => $taxonomy,
								'field'    => 'id',
								'terms'    => array($category->term_id)
							)
						),
						'no_found_rows'  => false
					);
					/*$tax_q2 = $tax_q = array();
					$query_args1 = $query_args0;
					$tax_q = array('relation' => 'AND');
					/*array_push($tax_q2, array(
						'taxonomy' => 'tender-market',
						'field' => 'slug',
						'terms' => 'sweden-systembolaget'
					));*/
					/*array_push($tax_q2, array(
						'taxonomy' => 'tender-products',
						'field'    => 'slug',
						'terms'    => array('spirits', 'beer', 'cider'),
						'operator' => 'NOT IN'
					));
						array_push($tax_q2, array(
							'taxonomy' => 'tender-countries',
							'field'    => 'id',
							'terms'    => array($category->term_id,)
						));
					
					array_push($tax_q, $tax_q2);
					$query_args1["tax_query"] = $tax_q;*/
					$postslist = get_posts($query_args0);
					//echo count($postslist);

				?>

					<div class="<?php echo $wrapper_cls; ?>">
						<a class="grid-item" href="<?php echo $link; /*$term_link*/ ?>">
							<div class="pciwgas-post-cat-inner">
								<div class="pciwgas-img-wrapper">
									<?php if (!empty($category_image)) { ?>
										<?php if (is_front_page()) { ?>
											<ul id="product-index-cat-list">
												<?php if (!empty($tax_img)) { ?>
													<img class="tax_cat cat_id_<?php echo $category->term_id; ?>" width="auto" height="auto" src="<?php echo ($tax_img); ?>" />
												<?php } ?>
											<?php } else { ?>
												<img src="<?php echo $category_image; ?>" class="pciwgas-cat-img" alt="" />
											<?php } ?>
										<?php } ?>
								</div>
								<div class="pciwgas-title">
									<?php if ($show_title && $category->name) { ?>
										<span class="cat-name"><?php echo $category->name; ?> </span>
									<?php }
									if ($show_count) { ?>
										<span class="pciwgas-cat-count emmm1">
											<?php echo count($postslist);//echo $category->count; ?>
										</span>
									<?php } ?>
								</div>
								<?php if ($show_desc && $category->description) { ?>
									<div class="pciwgas-description">
										<div class="pciwgas-cat-desc"><?php echo $category->description; ?></div>
									</div>
								<?php } ?>
								<?php if (!is_front_page()) { ?>
									<div class="pciwgas-more">
										<a class="pciwgas-more-link" href="<?php echo $link; ?>"><?php echo __('See tenders'); ?></a>
									</div>
								<?php } ?>
							</div>
						</a>
					</div>
				<?php $count++;
				} ?>
			</div>
		<?php
		}
		$content .= ob_get_clean();
		return $content;
	}

	function theme_pci_cat_slider_shortcode($atts, $content)
	{
		su_query_asset('css', 'theme-pciwgas-publlic-style');
		// SiteOrigin Page Builder Gutenberg Block Tweak - Do not Display Preview
		if (isset($_POST['action']) && ($_POST['action'] == 'so_panels_layout_block_preview' || $_POST['action'] == 'so_panels_builder_content_json')) {
			return '[pci-cat-slider]';
		}

		// Divi Frontend Builder - Do not Display Preview
		if (function_exists('et_core_is_fb_enabled') && isset($_POST['is_fb_preview']) && isset($_POST['shortcode'])) {
			return '<div class="pciwgas-builder-shrt-prev">
					<div class="pciwgas-builder-shrt-title"><span>' . esc_html__('Post Category Image Slider - Shortcode', 'post-category-image-with-grid-and-slider') . '</span></div>
					pci-cat-slider
				</div>';
		}

		// Fusion Builder Live Editor - Do not Display Preview
		if (class_exists('FusionBuilder') && ((isset($_GET['builder']) && $_GET['builder'] == 'true') || (isset($_POST['action']) && $_POST['action'] == 'get_shortcode_render'))) {
			return '<div class="pciwgas-builder-shrt-prev">
					<div class="pciwgas-builder-shrt-title"><span>' . esc_html__('Post Category Image Slider - Shortcode', 'post-category-image-with-grid-and-slider') . '</span></div>
					pci-cat-slider
				</div>';
		}

		// Shortcode Parameter
		$atts = extract(shortcode_atts(array(
			'size'    			=> 'full',
			'term_id' 			=> null,
			'taxonomy'          => 'category',
			'design'     		=> 'design-1',
			'orderby'    		=> 'name',
			'order'      		=> 'ASC',
			'show_title' 		=> 'true',
			'show_count'		=> 'true',
			'show_desc'  		=> 'true',
			'hide_empty' 		=> 'true',
			'slidestoshow' 		=> 3,
			'slidestoscroll' 	=> 1,
			'loop' 				=> 'true',
			'dots'     			=> 'true',
			'arrows'     		=> 'true',
			'autoplay'     		=> 'false',
			'autoplay_interval' => 3000,
			'speed'             => 300,
			'rtl'				=> '',
			'exclude_cat'		=> array(),
			'extra_class'		=> '',
			'className'			=> '',
			'align'				=> '',
		), $atts, 'pci-cat-slider'));

		$unique				 = pciwgas_get_unique();
		$size 				 = !empty($size) 				? $size 						: 'full';
		$term_id 	 		 = !empty($term_id) 				? explode(',', $term_id) 		: '';
		$design				 = !empty($design) 				? $design 						: 'design-1';
		$taxonomy 	 		 = !empty($taxonomy) 			? $taxonomy 			   		: 'category';
		$slidestoshow 		 = !empty($slidestoshow) 		? $slidestoshow 				: 3;
		$slidestoscroll 	 = !empty($slidestoscroll) 		? $slidestoscroll 				: 1;
		$autoplay_interval   = !empty($autoplay_interval)	? $autoplay_interval 			: 3000;
		$speed 				 = !empty($speed) 				? $speed 						: 300;
		$exclude_cat 		 = !empty($exclude_cat)			? explode(',', $exclude_cat)	: array();
		$show_title	 		 = ($show_title == 'true') 		? true 							: false;
		$show_count	 		 = ($show_count == 'true') 		? true 							: false;
		$show_desc			 = ($show_desc == 'true') 		? true 							: false;
		$hide_empty  		 = ($hide_empty == 'false') 		? false 						: true;
		$loop 				 = ($loop == 'false') 			? 'false' 						: 'true';
		$dots 				 = ($dots == 'false') 			? 'false' 						: 'true';
		$arrows 			 = ($arrows == 'false') 			? 'false' 						: 'true';
		$autoplay 			 = ($autoplay == 'false') 		? 'false' 						: 'true';
		$align				= !empty($align)					? 'align' . $align				: '';
		$extra_class		= $extra_class . ' ' . $align . ' ' . $className;
		$extra_class		= pciwgas_sanitize_html_classes($extra_class);

		// For RTL
		if (empty($rtl) && is_rtl()) {
			$rtl = 'true';
		} elseif ($rtl == 'true') {
			$rtl = 'true';
		} else {
			$rtl = 'false';
		}

		// Enqueue required script
		wp_enqueue_script('wpos-slick-jquery');
		wp_enqueue_script('pciwgas-public-script');

		// get terms and workaround WP bug with parents/pad counts
		$args = array(
			'orderby'    => $orderby,
			'order'      => $order,
			'include'    => $term_id,
			'hide_empty' => $hide_empty,
			'exclude'	 => $exclude_cat,
		);

		$post_categories = get_terms($taxonomy, $args);

		// Slider configuration
		$slider_conf = compact('slidestoshow', 'slidestoscroll', 'loop', 'dots', 'arrows', 'autoplay', 'autoplay_interval', 'speed', 'rtl');

		ob_start();

		if ($post_categories) { ?>
			<div class="pciwgas-cat-wrap pciwgas-cat-wrap-slider pciwgas-clearfix <?php echo $extra_class; ?> pciwgas-<?php echo $design; ?>">
				<div id="pciwgas-<?php echo $unique; ?>" class="pciwgas-cat-slider-main">
					<?php
					foreach ($post_categories as $category) {
						$category_image = pciwgas_term_image($category->term_id, $size);
						$term_link 		= get_term_link($category, $taxonomy);
					?>

						<div class="pciwgas-pdt-cat-slider">
							<div class="pciwgas-post-cat-inner">
								<div class="pciwgas-img-wrapper">
									<?php if (!empty($category_image)) {  ?>
										<a href="<?php echo $term_link; ?>"><img src="<?php echo $category_image; ?>" class="pciwgas-cat-img" alt="" /></a>
									<?php } ?>
								</div>
								<div class="pciwgas-title">
									<?php if ($show_title && $category->name) { ?>
										<a href="<?php echo $term_link; ?>"><?php echo $category->name; ?> </a>
									<?php }
									if ($show_count) { ?>
										<span class="pciwgas-cat-count"><?php echo $category->count; ?></span>
									<?php } ?>
								</div>
								<?php if ($show_desc && $category->description) { ?>
									<div class="pciwgas-description">
										<div class="pciwgas-cat-desc"><?php echo $category->description; ?></div>
									</div>
								<?php } ?>
							</div>
						</div>
					<?php } ?>
				</div>
				<div class="pciwgas-slider-conf" data-conf="<?php echo htmlspecialchars(json_encode($slider_conf)); ?>"></div>
			</div>
<?php
		}
		$content .= ob_get_clean();
		return $content;
	}
	global $pciwgas_script;
	remove_action('wp_enqueue_scripts', array($pciwgas_script, 'pciwgas_front_style'));

	add_filter('render_block', 'theme_register_pci_cat_grid', 10, 2);

	function theme_register_pci_cat_grid($block_content, $block)
	{
		if ("pciwgas/pci-cat-grid" == $block['blockName']) {
			$block_content = theme_pci_cat_grid_shortcode($block['attrs'], '');
			return $block_content;
		}
		if ("pciwgas/pci-cat-slider" == $block['blockName']) {
			$block_content = theme_pci_cat_slider_shortcode($block['attrs'], '');
			return $block_content;
		}
		return $block_content;
	}
}
?>