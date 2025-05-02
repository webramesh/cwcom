<?php

/*pdf content*/
add_filter('the_content', 'meta_renderer', 1, 1);
function meta_renderer($content) {
if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action']=='catelog_singlepost') {
$post_id = $_GET['id'];
$tender_ref_number = get_post_meta( $post_id, 'wpcf-tender-reference-number', true);
$product_country = get_the_terms($post_id, 'tender-countries');
$tender_products = get_the_terms($post_id, 'tender-products');
$tender_market = get_the_terms($post_id, 'tender-market');

$product_country_string = strip_tags( get_the_term_list( $post_id, 'tender-countries', '', ', ') );
$tender_products_string = strip_tags( get_the_term_list( $post_id, 'tender-products', '', ', ') );
$tender_market_string = strip_tags( get_the_term_list( $post_id, 'tender-market', '', ', ') );

$tender_assortment = get_post_meta($post_id, 'wpcf-tender-assortment', true);
$tender_distribution = get_post_meta($post_id, 'wpcf-tender-distribution', true);
$tender_start_date = get_post_meta($post_id, 'wpcf-tender-start-date', true);
$tender_offer_deadline = get_post_meta($post_id, 'wpcf-tender-offer-deadline', true);
$tender_sample_deadline = get_post_meta($post_id, 'wpcf-tender-samples-deadline', true);
$tender_launch_date = get_post_meta($post_id, 'wpcf-tender-launch-date', true);
$tender_comment = get_post_meta($post_id, 'wpcf-tender-buyer-comments', true);
$tender_taste_style = get_post_meta($post_id, 'wpcf-tender-taste-style', true);

$tender_organic = get_post_meta($post_id, 'wpcf-tender-organic', true);
$tender_region = get_post_meta($post_id, 'wpcf-tender-region-classification', true);
$tender_grape_composition = get_post_meta($post_id, 'wpcf-tender-grape-composition', true);
$tender_vintage = get_post_meta($post_id, 'wpcf-tender-wine-vintage', true);

$tender_price = get_post_meta($post_id, 'wpcf-tender-wine-price', true);
$tender_container_size = get_post_meta($post_id, 'wpcf-tender-container-size', true);
$tender_container_type = get_post_meta($post_id, 'wpcf-tender-container-type', true);

$tender_bulk_price = get_post_meta($post_id, 'wpcf-tender-bulk-price-liter', true);
$tender_bottles_qty = get_post_meta($post_id, 'wpcf-tender-bottles-quantity', true);

$tender_est_volume = get_post_meta($post_id, 'wpcf-tender-estimated-volume', true);

$tender_wine_ageing = get_post_meta($post_id, 'wpcf-tender-wine-ageing', true);
$tender_bottle_closure = get_post_meta($post_id, 'wpcf-tender-bottle-closure', true);
$tender_alc_volume = get_post_meta($post_id, 'wpcf-tender-alcohol-volume', true);
$tender_sugar_lvl = get_post_meta($post_id, 'wpcf-tender-sugar-level', true);
$tender_image_required = get_post_meta($post_id, 'wpcf-tender-image-required', true);

$tender_other_requirements = get_post_meta($post_id, 'wpcf-tender-other-requirements-1', true);
$tender_other_requirements2 = get_post_meta($post_id, 'wpcf-tender-other-requirements-2', true);
$tender_other_requirements3 = get_post_meta($post_id, 'wpcf-tender-other-requirements-3', true);
$tender_other_requirements4 = get_post_meta($post_id, 'wpcf-tender-other-requirements-4', true);
$tender_other_requirements5 = get_post_meta($post_id, 'wpcf-tender-other-requirements-5', true);
$tender_other_requirements6 = get_post_meta($post_id, 'wpcf-tender-other-requirements-6', true);
$tender_other_requirements7 = get_post_meta($post_id, 'wpcf-tender-other-requirements-7', true);
$tender_archive_date = get_post_meta($post_id, 'wpcf-tender-archive-date', true);
$tender_winner = get_post_meta($post_id, 'wpcf-tender-winner', true);
$tender_competition = get_post_meta($post_id, 'wpcf-tender-competition', true);

$post_type = get_post_type($post_id);
 		if (get_post_type($post_id)=='tenders') {
             ?>
	<style>
		.tender-general-block, .pdfShowCont {font-size: 14px;}
        .tender-block__col{float:left;width:50%;}
		.tender-general__items {}
		.tender-item__label {float:left;width:35%;font-weight:bold;margin-bottom:5px;margin-right:10px;}
		.tender-item__value {}
		.tender-requirements-block {font-size: 14px;margin-top: 10px;margin-bottom: 10px;}
		.requirements-block__title {border-bottom: 1px solid #000;padding-bottom: 5px;display: block;}
        .no-float{float:none;}
        .dt-descr{
            display:block;
            font-weight: normal;
            font-size: 10px}
	</style>
    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/concealedwineslogotype_PDF.jpg" width="280" height="55">
	<div class="tender-general-block">
		<div class="tender-block__col">
               <b><?php echo get_the_title($post_id); ?></b>
		</div>
		<div class="tender-block__col float-right">
			Tender No. <?php echo $tender_ref_number; ?>
            <span class="dt-descr">The reference of the project, use it in communication with us.</span>
		</div>
        <div style="clear:both"></div>
            <hr>
		<dl class="tender-general__items">
			<?php if(!empty($tender_market_string)):?>
			<dt class="tender-item__label">
				<?php echo __('Monopoly:', 'kadence-child'); ?>
                <span class="dt-descr">Which monopoly distributor.</span>
			</dt>
			<dd class="tender-item__value">
				<?php echo $tender_market_string ?>
			</dd>
			<?php endif;?>
            <div style="clear:both"></div>
			<?php if(!empty($tender_assortment)):?>
			<dt class="tender-item__label">
				<?php echo __('Assortment:', 'kadence-child'); ?>
                <span class="dt-descr">Which type of initial contract.</span>
			</dt>
			<dd class="tender-item__value">
				<?php echo $tender_assortment ?>
			</dd>
			<?php endif;?>
            <div style="clear:both"></div>
			<?php if(!empty($tender_distribution)):?>
			<dt class="tender-item__label">
				<?php echo __('Distribution:', 'kadence-child'); ?>
                <span class="dt-descr">How many stores of distribution.</span>
			</dt>
			<dd class="tender-item__value">
				<?php echo $tender_distribution ?>
			</dd>
			<?php endif;?>
            <div style="clear:both"></div>
			<?php if(!empty($tender_start_date)):?>
			<dt class="tender-item__label">
				<?php echo __('Tender Start Date:', 'kadence-child'); ?>
                <span class="dt-descr">At this stage the tender will become official and more specified.</span>
			</dt>
			<dd class="tender-item__value">
				<?php echo wp_date( get_option( 'date_format' ), $tender_start_date); ?>
			</dd>
			<?php endif;?>
            <div style="clear:both"></div>
			<?php if(!empty($tender_offer_deadline)):?>
			<dt class="tender-item__label">
				<?php echo __('Deadline written offer:', 'kadence-child'); ?>
                <span class="dt-descr">Before this date you have to submit paperwork.</span>
			</dt>
			<dd class="tender-item__value">
				<?php echo wp_date( get_option( 'date_format' ), $tender_offer_deadline); ?>
			</dd>
			<?php endif;?>
            <div style="clear:both"></div>
			<?php if(!empty($tender_sample_deadline)):?>
			<dt class="tender-item__label">
				<?php echo __('Deadline Samples:', 'kadence-child'); ?>
                <span class="dt-descr">Before this date we will need to have samples in our Stockholm office.</span>
			</dt>
			<dd class="tender-item__value">
				<?php echo wp_date( get_option( 'date_format' ), $tender_sample_deadline); ?>
			</dd>
			<?php endif;?>
            <div style="clear:both"></div>
			<?php if(!empty($tender_launch_date)):?>
			<dt class="tender-item__label">
				<?php echo __('Launch Date:', 'kadence-child'); ?>
                <span class="dt-descr">Expected date the product will be launched in the market.</span>
			</dt>
			<dd class="tender-item__value">
				<?php echo wp_date( get_option( 'date_format' ), $tender_launch_date); ?>
			</dd>
			<?php endif;?>
            <div style="clear:both"></div>
			<?php if(!empty(get_the_content())):?>
			<dt class="tender-item__label">
				<?php echo __('Characteristics:', 'kadence-child'); ?>
                <span class="dt-descr">An explanation of style profile of the product.</span>
			</dt>
			<dd class="tender-item__value">
				<?php echo get_the_content(); ?>
			</dd>
			<?php endif;?>
            <div style="clear:both"></div>
			<?php if(!empty($tender_comment)):?>
			<dt class="tender-item__label">
				<?php echo __('Style/Buyer Comments:', 'kadence-child'); ?>
                <span class="dt-descr">A comment from the buyer on style profile of product.</span>
			</dt>
			<dd class="tender-item__value">
				<?php echo $tender_comment; ?>
			</dd>
			<?php endif;?>
            <div style="clear:both"></div>
			<?php if(!empty($tender_taste_style)):?>
			<dt class="tender-item__label">
				<?php echo __('Taste Style:', 'kadence-child'); ?>
				<a href="http://www.concealedwines.com/wpsysfiles/wp-content/uploads/2015/02/Tastestyleofwhite_ALKO.png" title="See Taste Styles Tables for White & Red Wines" style="font-size:smaller !important">(See Taste Styles Info)</a> <a href="http://www.concealedwines.com/wpsysfiles/wp-content/uploads/2015/02/Tastestyleofreds_ALKO.png" title="See Taste Styles Table for Red Wines" style="display:none !important">Red Wines Info</a>
                <span class="dt-descr">The style of what the buyer have in mind, see link for detailed description.</span>
                </dt>
			<dd class="tender-item__value">
				<?php echo $tender_taste_style; ?>
			</dd>
			<?php endif;?>
            <div style="clear:both"></div>
		</dl>
	</div>
	<div class="tender-requirements-block">
		<h3 class="requirements-block__title">Product Requirements</h3>
		<dl class="tender-general__items">
			<dt class="tender-item__label"><?php echo __('Country of Origin:', 'kadence-child'); ?>
                <span class="dt-descr">What Country / Countries the product is originating from.</span>
            </dt>
			<dd class="tender-item__value"><?php echo $product_country_string; ?> </dd>
            <div style="clear:both"></div>
			<dt class="tender-item__label"><?php echo __('Type of Product:', 'kadence-child'); ?>
                <span class="dt-descr">What type of product our client ask for.</span>
            </dt>
			<dd class="tender-item__value"><?php echo $tender_products_string; ?> </dd>
            <div style="clear:both"></div>
			<?php if(!empty($tender_organic)):?>
					<dt class="tender-item__label">
						<?php echo __('Organic:', 'kadence-tenders'); ?>
                        <span class="dt-descr">We ask for an organic certified product, we need documentation.</span>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_organic; ?>
					</dd>
				<?php endif; ?>
            <div style="clear:both"></div>
				<?php if(!empty($tender_region)):?>
					<dt class="tender-item__label">
						<?php echo __('Region (Classification):', 'kadence-tenders'); ?>
                        <span class="dt-descr">The region/classification of the product.</span>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_region; ?>
					</dd>
				<?php endif; ?>
<div style="clear:both"></div>
				<?php if(!empty($tender_grape_composition)):?>
					<dt class="tender-item__label">
						<?php echo __('Grapes:', 'kadence-tenders'); ?>
                        <span class="dt-descr">The grape composition of the product.</span>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_grape_composition; ?>
					</dd>
				<?php endif; ?>
<div style="clear:both"></div>
				<?php if(!empty($tender_vintage)):?>
					<dt class="tender-item__label">
						<?php echo __('Vintage:', 'kadence-tenders'); ?>
                        <span class="dt-descr">The vintage we ask for.</span>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_vintage; ?>
					</dd>
				<?php endif; ?>
<div style="clear:both"></div>
			<dt class="tender-item__label"><?php echo __('Ex. Cellar Price:', 'kadence-child'); ?>
            <span class="dt-descr">The net price we could pay per unit (not per case). Notice that we do not ask for any commission on top of this price!</span>
            </dt>

			<dd class="tender-item__value"><?php echo $tender_price . ' € per ' . $tender_container_size .' '. $tender_container_type; ?> </dd>
<div style="clear:both"></div>
			<?php if(!empty($tender_bulk_price)):?>
					<dt class="tender-item__label">
						<?php echo __('Bulk Price (per liter):', 'kadence-tenders'); ?>
                        <span class="dt-descr">The net price ex cellar per litre.</span>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_bulk_price; ?>
					</dd>
				<?php endif; ?>
<div style="clear:both"></div>
				<?php if(!empty($tender_bottles_qty)):?>
					<dt class="tender-item__label">
						<?php echo __('Minimum Volume (units):', 'kadence-tenders'); ?>
                        <span class="dt-descr">The minimum volume we have to state in the offer.</span>
					</dt>
					<dd class="tender-item__value">
					<?php echo $tender_bottles_qty . ' (Volume Unit ' . $tender_container_size .' '. $tender_container_type . ')'; ?>
					</dd>
				<?php endif; ?>
                <div style="clear:both"></div>
				<?php if(!empty($tender_est_volume)):?>
					<dt class="tender-item__label">
						<?php echo __('Estimated Volume (yearly):', 'kadence-tenders'); ?>
                        <span class="dt-descr">The estimated volume of the product on a yearly basis.</span>
					</dt>
					<dd class="tender-item__value">
					<?php echo $tender_est_volume . ' (Volume Unit ' . $tender_container_size .' '. $tender_container_type . ')'; ?>
					</dd>
				<?php endif; ?>
                <div style="clear:both"></div>
				<?php if(!empty($tender_container_type)):?>
					<?php echo do_shortcode('[simple_tooltip content="The type of container requested for the product."]'); ?>
					<dt class="tender-item__label">
						<?php echo __('Type of Container:', 'kadence-tenders'); ?>
                        <span class="dt-descr">The type of container requested for the product.</span>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_container_type; ?>
					</dd>
				<?php endif; ?>
                <div style="clear:both"></div>
				<?php if(!empty($tender_container_size)):?>
					<?php echo do_shortcode('[simple_tooltip content="The type of container requested for the product."]'); ?>
					<dt class="tender-item__label">
						<?php echo __('Container Size:', 'kadence-tenders'); ?>
                        <span class="dt-descr">The volume of container requested for the product.</span>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_container_size; ?>
					</dd>
				<?php endif; ?>
                <div style="clear:both"></div>
				<?php if(!empty($tender_wine_ageing)):?>
					<?php echo do_shortcode('[simple_tooltip content="The type of container requested for the product."]'); ?>
					<dt class="tender-item__label">
						<?php echo __('Ageing:', 'kadence-tenders'); ?>
                        <span class="dt-descr">The required ageing.</span>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_wine_ageing; ?>
					</dd>
				<?php endif; ?>
                <div style="clear:both"></div>
				<?php if(!empty($tender_bottle_closure)):?>
					<?php echo do_shortcode('[simple_tooltip content="The type of container requested for the product."]'); ?>
					<dt class="tender-item__label">
						<?php echo __('Closure:', 'kadence-tenders'); ?>
                        <span class="dt-descr">The type of closure on the bottle.</span>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_bottle_closure; ?>
					</dd>
				<?php endif; ?>
                <div style="clear:both"></div>
				<?php if(!empty($tender_alc_volume)):?>
					<?php echo do_shortcode('[simple_tooltip content="The type of container requested for the product."]'); ?>
					<dt class="tender-item__label">
						<?php echo __('Alcohol vol.:', 'kadence-tenders'); ?>
                        <span class="dt-descr">The alc. Vol. % of the product.</span>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_alc_volume . ' % alc. vol.'; ?>
					</dd>
				<?php endif; ?>
                <div style="clear:both"></div>
				<?php if(!empty($tender_sugar_lvl)):?>
					<?php echo do_shortcode('[simple_tooltip content="The type of container requested for the product."]'); ?>
					<dt class="tender-item__label">
						<?php echo __('Sugar level (g/l):', 'kadence-tenders'); ?>
                        <span class="dt-descr">The sugar lever in g/l of the product.</span>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_sugar_lvl . ' g/l'; ?>
					</dd>
				<?php endif; ?>
                <div style="clear:both"></div>
				<?php if(!empty($tender_image_required)):?>
					<?php echo do_shortcode('[simple_tooltip content="The type of container requested for the product."]'); ?>
					<dt class="tender-item__label">
						<?php echo __('Sample Image:', 'kadence-tenders'); ?>
                        <span class="dt-descr">If we have to submit an image to the offer or not.</span>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_image_required; ?>
					</dd>
				<?php endif; ?>
                <div style="clear:both"></div>
				<?php if(!empty($tender_other_requirements)):?>
					<?php echo do_shortcode('[simple_tooltip content="The type of container requested for the product."]'); ?>
					<dt class="tender-item__label no-float">
						<?php echo __('Other Requirements:', 'kadence-tenders'); ?>
                        <span class="dt-descr">Other criteria the product have to meet.</span>
					</dt>
					<dd class="tender-item__value">
						<ol>
							<li><?php echo $tender_other_requirements; ?></li>
							<?php if(!empty($tender_other_requirements2)): ?>
							<li><?php echo $tender_other_requirements2; ?></li>
							<?php endif; ?>
							<?php if(!empty($tender_other_requirements3)): ?>
							<li><?php echo $tender_other_requirements3; ?></li>
							<?php endif; ?>
							<?php if(!empty($tender_other_requirements4)): ?>
							<li><?php echo $tender_other_requirements4; ?></li>
							<?php endif; ?>
							<?php if(!empty($tender_other_requirements5)): ?>
							<li><?php echo $tender_other_requirements5; ?></li>
							<?php endif; ?>
							<?php if(!empty($tender_other_requirements6)): ?>
							<li><?php echo $tender_other_requirements6; ?></li>
							<?php endif; ?>
							<?php if(!empty($tender_other_requirements7)): ?>
							<li><?php echo $tender_other_requirements7; ?></li>
							<?php endif; ?>
						</ol>
					</dd>
				<?php endif; ?>
		</dl>
	</div>
	<?php $tender_archive_date = wp_date(get_option('date_format'), $tender_archive_date);
			// echo $tender_archive_date; ?>

<div class="pdfShowCont">
    	<?php if($tender_archive_date < wp_date(get_option('date_format'))):?>
		<div class="tender-stats">
			<h3>Tender Stats</h3>
			<dl>
				<dt>Winner:</dt>
				<dd><?php echo $tender_winner; ?></dd>
			</dl>
			<dl>
				<dt>Competition:</dt>
				<dd><?php echo $tender_competition; ?></dd>
			</dl>
		</div>

	<?php endif; ?>
    <br />
  <strong>To receive further information on this and other tenders feel free to contact us:</strong>
<br />
  <strong>Telephone:</strong> 08-41 02 44 34
<br />
info@concealedwines.com
<br />
<br />
<strong>ONLINE SUPPORT</strong>
<br />
<strong>Calle Nilsson (Skype ID: callenil) <br /> Email: calle.nilsson@concealedwines.com</strong>
</div>
</div>
<hr />
<p style="text-align:center;font-size:8pt !important;margin:0 !important;padding:0 !important">Concealed Wines AB - www.concealedwines.com - info@concealedwines.com</p>
<?php
		}
	}
	return $content;
}
