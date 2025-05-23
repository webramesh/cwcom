<?php

/**
 * Template part for displaying a post's content
 *
 * @package kadence
 */

namespace Kadence;

$request_tender_form_id = $tender_form_id = 0;
if (function_exists('get_form_id_by_name')) {
	$tender_form_id = get_form_id_by_name('Tender Form');
	$request_tender_form_id = get_form_id_by_name('Request info on Tender');
}
/* error_log('Request info on Tender'); error_log(print_r($request_tender_form_id,1)); error_log('Tender Form');error_log(print_r($tender_form_id,1)); */
$post_id = get_the_ID();
$tender_ref_number = get_post_meta(get_the_ID(), 'wpcf-tender-reference-number', true);
$product_country_string = strip_tags(get_the_term_list(get_the_ID(), 'tender-countries', '', ', '));
$tender_products_string = strip_tags(get_the_term_list(get_the_ID(), 'tender-products', '', ', '));
$tender_market_string = strip_tags(get_the_term_list(get_the_ID(), 'tender-market', '', ', '));
$tender_assortment = get_post_meta(get_the_ID(), 'wpcf-tender-assortment', true);
$tender_distribution = get_post_meta(get_the_ID(), 'wpcf-tender-distribution', true);
$tender_start_date = get_post_meta(get_the_ID(), 'wpcf-tender-start-date', true);
$tender_launch_plan = get_post_meta(get_the_ID(), 'wpcf-tender-launch-plan', true);
$tender_offer_deadline = get_post_meta(get_the_ID(), 'wpcf-tender-offer-deadline', true);
$tender_sample_deadline = get_post_meta(get_the_ID(), 'wpcf-tender-samples-deadline', true);
$tender_launch_date = get_post_meta(get_the_ID(), 'wpcf-tender-launch-date', true);
$tender_comment = get_post_meta(get_the_ID(), 'wpcf-tender-buyer-comments', true);
$tender_taste_style = get_post_meta(get_the_ID(), 'wpcf-tender-taste-style', true);

$tender_organic = get_post_meta(get_the_ID(), 'wpcf-tender-organic', true) == 1;
$tender_organic = ($tender_organic == 1) ? 'Yes' : '';
$tender_region = get_post_meta(get_the_ID(), 'wpcf-tender-region-classification', true);
$tender_grape_composition = get_post_meta(get_the_ID(), 'wpcf-tender-grape-composition', true);
$tender_vintage = get_post_meta(get_the_ID(), 'wpcf-tender-wine-vintage', true);

$tender_price = get_post_meta(get_the_ID(), 'wpcf-tender-wine-price', true);
$tender_container_size = get_post_meta(get_the_ID(), 'wpcf-tender-container-size', true);
$tender_container_type = get_post_meta(get_the_ID(), 'wpcf-tender-container-type', true);

$tender_bulk_price = get_post_meta(get_the_ID(), 'wpcf-tender-bulk-price-liter', true);
$tender_bottles_qty = get_post_meta(get_the_ID(), 'wpcf-tender-bottles-quantity', true);

$tender_est_volume = get_post_meta(get_the_ID(), 'wpcf-tender-estimated-volume', true);

$tender_wine_ageing = get_post_meta(get_the_ID(), 'wpcf-tender-wine-ageing', true);
$tender_bottle_closure = get_post_meta(get_the_ID(), 'wpcf-tender-bottle-closure', true);
$tender_alc_volume = get_post_meta(get_the_ID(), 'wpcf-tender-alcohol-volume', true);
$tender_sugar_lvl = get_post_meta(get_the_ID(), 'wpcf-tender-sugar-level', true);
$tender_image_required = get_post_meta(get_the_ID(), 'wpcf-tender-image-required', true);

$tender_other_requirements = get_post_meta(get_the_ID(), 'wpcf-tender-other-requirements-1', true);
$tender_other_requirements2 = get_post_meta(get_the_ID(), 'wpcf-tender-other-requirements-2', true);
$tender_other_requirements3 = get_post_meta(get_the_ID(), 'wpcf-tender-other-requirements-3', true);
$tender_other_requirements4 = get_post_meta(get_the_ID(), 'wpcf-tender-other-requirements-4', true);
$tender_other_requirements5 = get_post_meta(get_the_ID(), 'wpcf-tender-other-requirements-5', true);
$tender_other_requirements6 = get_post_meta(get_the_ID(), 'wpcf-tender-other-requirements-6', true);
$tender_other_requirements7 = get_post_meta(get_the_ID(), 'wpcf-tender-other-requirements-7', true);
$tender_archive_date = get_post_meta(get_the_ID(), 'wpcf-tender-archive-date', true);
$tender_winner = get_post_meta(get_the_ID(), 'wpcf-tender-winner', true);
$tender_winner_company = get_post_meta(get_the_ID(), 'wpcf-winner-company', true);
$tender_score = get_post_meta(get_the_ID(), 'wpcf-score', true);
$tender_launched_number = get_post_meta(get_the_ID(), 'wpcf-launched-number', true);
$tender_competition = get_post_meta(get_the_ID(), 'wpcf-tender-competition', true);
su_query_asset('js', array('popperjs', 'tippy'));

/* echo "tender archive date=".wp_date(get_option('date_format'), $tender_archive_date); */

$has_access = false;
if (isset($_GET['access'])) {
	$has_access = true;
}

?>

<div class="floating-gtranslate">
    <?php echo do_shortcode('[gtranslate]'); ?>
</div>

<div class="<?php echo esc_attr(apply_filters('kadence_entry_content_class', 'entry-content single-content')); ?>">
	<?php if ($tender_archive_date > time()): ?>
		<?php if (is_user_logged_in()) { ?>
			<div class="tender_buttons_top">
				<!-- <div class="tender_buttons_left">
					<?php echo do_shortcode('[gmptp_button post_id="' . $post_id . '"]'); ?>
					<?php if (!empty($tender_start_date) && !empty($tender_launch_plan)) { ?>
					<?php } else { ?>
						<div class="button-apply">
							<?php if ($tender_offer_deadline > time()): ?>
								<a href="#apply-for-tender" class="button type-transparent">Apply for tender</a>
							<?php else: ?>
								<a href="#" class="button type-transparent disabled" style="pointer-events:none;">Deadline Passed</a>
							<?php endif; ?>
						</div>
					<?php } ?>
				</div> -->
				<div class="tender_buttons_right">
					<div class="favorite_tender"><?php echo do_shortcode('[treaking-tender id="' . $post_id . '"]'); ?></div>
				</div>
			</div>
		<?php } else { ?>
			<!-- <div class="login-subscribe-apply-tender">
				<div class="subscribe_block">
					<a href="#" class="login-btn"><?php echo __('Log in to subscribe', 'kadence-tenders'); ?></a>
				</div>
				<div class="button-apply">
					<a href="#" class="login-btn button type-transparent">Apply for tender</a>
				</div>
			</div> -->

		<?php } ?>

	<?php endif; ?>
	<?php do_action('kadence_single_before_entry_content'); ?>  


	<h1 class="tender-general-block">
		<div class="tender-block__col">
			<span data-tippy-content="The reference of the project, use it in communication with us.">
				<?php the_title(); ?><?php if (!empty($tender_ref_number)) { ?> | <?php echo $tender_ref_number; ?><?php } ?>
			</span>

		</div>
	</h1>
	<dl class="tender-general__items">
		<div class="tender-general_title"><?php echo __('General tender details'); ?></div>
		<hr>
		<?php if (!empty($tender_market_string)): ?>
		 
        <div class="tenders_item">
           <dt class="tender-item__label tender_monopoly" data-tippy-content="Which monopoly distributor.">
				<?php echo __('Monopoly:', 'kadence-child'); ?>
			</dt>
			<dd class="tender-item__value">
				<?php echo $tender_market_string ?>
			</dd>
		</div>
		<?php endif; ?>
		<?php if (!empty($tender_assortment)): ?>
		<div class="tenders_item">
			<dt class="tender-item__label tender_assortment" data-tippy-content="Which type of initial contract.">
				<?php echo __('Assortment:', 'kadence-child'); ?>
			</dt>
			<dd class="tender-item__value">
				<?php echo $tender_assortment ?>
			</dd>
		</div>
		<?php endif; ?>
		<?php if (!empty($tender_distribution)): ?>
		<div class="tenders_item">
			<dt class="tender-item__label tender_distribution" data-tippy-content="How many stores of distribution.">
				<?php echo __('Distribution:', 'kadence-child'); ?>
			</dt>
			<dd class="tender-item__value">
				<?php echo $tender_distribution ?>
			</dd>
		</div>
		<?php endif; ?>
		<?php if (!empty($tender_start_date)): ?>
		<div class="tenders_item">
		    <dt class="tender-item__label tender_start_date" data-tippy-content="At this stage the tender will become official and more specified">
				<?php echo __('Tender Start Date:', 'kadence-child'); ?>
			</dt>
			<dd class="tender-item__value">
				<?php echo wp_date(get_option('date_format'), $tender_start_date); ?>
			</dd>
		</div>
		<?php endif; ?>
		<?php if (!empty($tender_offer_deadline)): ?>
		<div class="tenders_item">
			<dt class="tender-item__label tender_offer_deadline" data-tippy-content="Before this date you have to submit paperwork.">
				<?php echo __('Deadline written offer:', 'kadence-child'); ?>
			</dt>
			<dd class="tender-item__value">
				<?php echo wp_date(get_option('date_format'), $tender_offer_deadline); ?>
			</dd>
		</div>
		<?php endif; ?>
		<?php if (!empty($tender_sample_deadline)): ?>
		<div class="tenders_item">
			<dt class="tender-item__label tender_sample_deadline" data-tippy-content="Before this date we will need to have samples in our Stockholm office.">
				<?php echo __('Deadline Samples:', 'kadence-child'); ?>
			</dt>
			<dd class="tender-item__value">
				<?php echo wp_date(get_option('date_format'), $tender_sample_deadline); ?>
			</dd>
		</div>
		<?php endif; ?>
		<?php if (!empty($tender_launch_date)): ?>
		<div class="tenders_item">
		    <dt class="tender-item__label tender_launch_date" data-tippy-content="Expected date the product will be launched in the market.">
				<?php echo __('Launch Date:', 'kadence-child'); ?>
			</dt>
			<dd class="tender-item__value">
				<?php echo wp_date(get_option('date_format'), $tender_launch_date); ?>
			</dd>
		</div>
		<?php endif; ?>
		<?php if (!empty($tender_comment)): ?>
		<div class="tenders_item">
			<dt class="tender-item__label tender_comment" data-tippy-content="A comment from the buyer on style profile of product.">
				<?php echo __('Style/Buyer Comments:', 'kadence-child'); ?>
			</dt>
			<dd class="tender-item__value">
				<?php echo $tender_comment; ?>
			</dd>
		</div>
		<?php endif; ?>
		<?php if (!empty($tender_taste_style)): ?>
		<div class="tenders_item">
		    <dt class="tender-item__label tender_taste_style" data-tippy-content="The style of what the buyer have in mind, see link for detailed description.">
				<?php echo __('Taste Style:', 'kadence-child'); ?>
				<a href="/wp-content/uploads/2015/02/Tastestyleofwhite_ALKO.png" title="See Taste Styles Tables for White & Red Wines" style="font-size:smaller !important">(See Taste Styles Info)</a>
				<a href="/wp-content/uploads/2015/02/Tastestyleofreds_ALKO.png" title="See Taste Styles Table for Red Wines" style="display:none !important">Red Wines Info</a>
			</dt>
			<dd class="tender-item__value">
				<?php echo $tender_taste_style; ?>
			</dd>
		</div>
		<?php endif; ?>
	</dl>

	<?php if (!empty(get_the_content())): ?>
		<dl class="tender-general__items single_tender_content">
			<div class="tender-general_title"><?php echo __('Taste & Style description'); ?></div>
			<hr>
			<div class="tenders_item">
				<dt class="tender-item__label tender_content" data-tippy-content="An explanation of style profile of the product.">
					<?php echo __('Characteristics:', 'kadence-child'); ?>
				</dt>
				<dd class="tender-item__value">
					<?php echo get_the_content(); ?>
				</dd>
			</div>
		</dl>
	<?php endif; ?>

	<?php if (is_user_logged_in() || $has_access): ?>
		<div class="tender-requirements-block">
			<div class="tender-general_title"><?php echo __('Product Requirements'); ?></div>
			<hr>
			<dl class="tender-general__items">
			  <div class="tenders_item">
				<dt class="tender-item__label tenders_country" data-tippy-content="What Country / Countries the product is originating from."><?php echo __('Country of Origin:', 'kadence-child'); ?> </dt>
				<dd class="tender-item__value"><?php echo $product_country_string; ?> </dd>
			  </div>
			  <div class="tenders_item">
				<dt class="tender-item__label tenders_type" data-tippy-content="What type of product our client ask for."><?php echo __('Type of Product:', 'kadence-child'); ?> </dt>
				<dd class="tender-item__value"><?php echo $tender_products_string; ?> </dd>
			  </div>

				<?php if (!empty($tender_organic)): ?>
				<div class="tenders_item">
					<dt class="tender-item__label tender_organic" data-tippy-content="We ask for an organic certified product, we need documentation.">
						<?php echo __('Organic:', 'kadence-tenders'); ?>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_organic; ?>
					</dd>
				</div>
				<?php endif; ?>

				<?php if (!empty($tender_region)): ?>
				<div class="tenders_item">
					<dt class="tender-item__label tender_region" data-tippy-content="The region/classification of the product.">
						<?php echo __('Region (Classification):', 'kadence-tenders'); ?>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_region; ?>
					</dd>
				</div>
				<?php endif; ?>

				<?php if (!empty($tender_grape_composition)): ?>
				<div class="tenders_item">
					  <dt class="tender-item__label tender_grape_composition" data-tippy-content="The grape composition of the product.">
						<?php echo __('Grapes:', 'kadence-tenders'); ?>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_grape_composition; ?>
					</dd>
				</div>
				<?php endif; ?>

				<?php if (!empty($tender_vintage)): ?>
				<div class="tenders_item">
					<dt class="tender-item__label tender_vintage" data-tippy-content="The vintage we ask for.">
						<?php echo __('Vintage:', 'kadence-tenders'); ?>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_vintage; ?>
					</dd>
				</div>
				<?php endif; ?>

				<div class="tenders_item">
					<dt class="tender-item__label tender_price" data-tippy-content="The net price we could pay per unit (not per case). Notice that we do not ask for any commission on top of this price!">
						<?php
						// $harbour = array();
						$harbour = get_theme_mod('filter-countries-harbour');

						$terms = get_terms(array(
							'taxonomy' => array('tender-countries'),
							'orderby' => 'name',
							'order' => 'DESC',
							'hide_empty' => 0,
							'depth' => 3,
							'include' => $harbour,
							'hierarchical' => true
						));

						?>



						<?php

						$termss = wp_get_object_terms($post_id, 'tender-countries', array('orderby' => 'name', 'order' => 'ASC'));

						$global_terms = wp_list_pluck($terms, 'name');

						$curent_terms = get_the_terms($post_id, 'tender-countries');
						$curent_term = wp_list_pluck($curent_terms, 'name');

						$keys = 'N';
						foreach ($termss as $key => $value) {
							if (in_array($value->name, $global_terms)) {
								$keys = 'Y';
							} else {
								$keys = 'N';
							}
							$term_key = '';
							$term_key .= sprintf('%s', $keys);
						}
						// fob display instead of ex cellar for countries out of europe ashish 24/9/24
						$fob_countries = [
							'Africa (not South Africa)', 'Åland', 'All', 'Any Country', 'Any country (not Europé)', 'Argentina',
							'Armenia', 'Asia', 'Asia or European Country', 'Australia',
							'Barbados', 'Bolivia', 'Brasil', 'Brazil', 'California', 'Canada', 'Cananda', 'Canda', 'Caribbean',
							'Caribbean Islands', 'Central America', 'Carribean Islands',
							'central america', 'Chile', 'china', 'Cyprus', 'England',
							'Faeroe Islands', 'Faroe Islands', 'Great Britain', 'Georgia', 'Greenland', 'Greennland', 'Groenland',
							'Guadeloupe', 'Guatemala', 'Guyana', 'Iceland', 'India', 'International', 'Island', 'Israel',
							'Jamaica', 'Japan', 'Taiwan', 'Kosovo', 'Martinique', 'Mexico', 'Montenegro', 'New Zealand', 'No defined',
							'not defined', 'Not defined yet', 'Not Europé', 'not France', 'not japan or sweden', 'Not specified',
							'not yet defined', 'NZ', 'Other', 'Other Countries', 'Other country', 'Other than Europe', 'Others',
							'Peru', 'Republic of Macedonia', 'Sake', 'Scotland', 'Serbia', 'Several', 'Singapore', 'South Afirica',
							'South Africa', 'South America', 'Spirits', 'Sri Lanka', 'sweden or u.s.a', 'Thailand', 'Trinidad',
							'Tunisia', 'Turkey', 'U.S.A', 'UK', 'United Kingdom', 'United States', 'Uruguay', 'USA',
							'Venezuela', 'Wales', 'West Indies'
						];

						function isFobCountry($product_country_string, $fob_countries)
						{
							foreach ($fob_countries as $country) {
								if (strpos($product_country_string, $country) !== false) {
									return true;  // Return true as soon as any substring match is found
								}
							}
							return false;  // Return false if no substring matches are found
						}

						if ($keys != 'N' || isFobCountry($product_country_string, $fob_countries)) {
							echo __('FOB Price (Euro €):', 'kadence-child');  // FOB price if any condition is met
						} else {
							echo __('Ex. Cellar Price (Euro €):', 'kadence-child');  // Default cellar price
						}
						// if ($term_key != 'N') {
						// 	echo __('FOB terms (harbour):', 'kadence-child');
						// } else {
						// 	echo __('Ex. Cellar Price:', 'kadence-child');
						// }

						?>

					</dt>
					<dd class="tender-item__value"><?php echo $tender_price; ?> </dd>
				</div>

				<?php if (!empty($tender_bulk_price)): ?>
				<div class="tenders_item">
					<dt class="tender-item__label tender_bulk_price" data-tippy-content="The net price ex cellar per litre.">
						<?php echo __('Bulk Price (per liter):', 'kadence-tenders'); ?>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_bulk_price; ?>
					</dd>
				</div>
				<?php endif; ?>

				<?php if (!empty($tender_bottles_qty)): ?>
				<div class="tenders_item">
					<dt class="tender-item__label tender_bottles_qty" data-tippy-content="The minimum volume we have to state in the offer.">
						<?php echo __('Minimum Volume:', 'kadence-tenders'); ?>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_bottles_qty; ?>
					</dd>
				</div>
				<?php endif; ?>
				<?php if (!empty($tender_est_volume)): ?>
				<div class="tenders_item">
					<dt class="tender-item__label tender_est_volume" data-tippy-content="The estimated volume of the product on a yearly basis.">
						<?php echo __('Estimated Volume (yearly):', 'kadence-tenders'); ?>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_est_volume; ?>
					</dd>
				</div>
				<?php endif; ?>
				<?php if (!empty($tender_container_type)): ?>
				<div class="tenders_item">
					<dt class="tender-item__label tender_container_type" data-tippy-content="The type of container requested for the product.">
						<?php echo __('Type of Container:', 'kadence-tenders'); ?>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_container_type; ?>
					</dd>
				</div>
				<?php endif; ?>
				<?php if (!empty($tender_container_size)): ?>
				<div class="tenders_item">
				  <dt class="tender-item__label tender_container_size" data-tippy-content="The volume of container requested for the product.">
						<?php echo __('Container Size:', 'kadence-tenders'); ?>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_container_size; ?>
					</dd>
				</div>
				<?php endif; ?>
				<?php if (!empty($tender_wine_ageing)): ?>
				<div class="tenders_item">
					<dt class="tender-item__label tender_wine_ageing" data-tippy-content="The required ageing.">
						<?php echo __('Ageing:', 'kadence-tenders'); ?>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_wine_ageing; ?>
					</dd>
				</div>
				<?php endif; ?>
				<?php if (!empty($tender_bottle_closure)): ?>
				<div class="tenders_item">
					<dt class="tender-item__label tender_bottle_closure" data-tippy-content="The type of closure on the bottle.">
						<?php echo __('Closure:', 'kadence-tenders'); ?>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_bottle_closure; ?>
					</dd>
				</div>
				<?php endif; ?>
				<?php if (!empty($tender_alc_volume)): ?>
				<div class="tenders_item">
					<dt class="tender-item__label tender_alc_volume" data-tippy-content="The alc. Vol. % of the product.">
						<?php echo __('Alcohol vol.:', 'kadence-tenders'); ?>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_alc_volume . ' % alc. vol.'; ?>
					</dd>
				</div>
				<?php endif; ?>
				<?php if (!empty($tender_sugar_lvl)): ?>
				<div class="tenders_item">
					<dt class="tender-item__label tender_sugar_lvl" data-tippy-content="The sugar lever in g/l of the product.">
						<?php echo __('Sugar level (g/l):', 'kadence-tenders'); ?>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_sugar_lvl . ' g/l'; ?>
					</dd>
				</div>
				<?php endif; ?>
				<?php if (!empty($tender_image_required)): ?>
				<div class="tenders_item">
					<dt class="tender-item__label tender_image_required" data-tippy-content="If we have to submit an image to the offer or not.">
						<?php echo __('Sample Image:', 'kadence-tenders'); ?>
					</dt>
					<dd class="tender-item__value">
						<?php echo $tender_image_required; ?>
					</dd>
				</div>
				<?php endif; ?>
				<?php if (!empty($tender_other_requirements)): ?>
				<div class="tenders_item">
					<dt class="tender-item__label tender_other_requirements" data-tippy-content="Other criteria the product have to meet.">
						<?php echo __('Other Requirements:', 'kadence-tenders'); ?>
					</dt>
					<dd class="tender-item__value">
						<ol>
							<li><?php echo $tender_other_requirements; ?></li>
							<?php if (!empty($tender_other_requirements2)): ?>
								<li><?php echo $tender_other_requirements2; ?></li>
							<?php endif; ?>
							<?php if (!empty($tender_other_requirements3)): ?>
								<li><?php echo $tender_other_requirements3; ?></li>
							<?php endif; ?>
							<?php if (!empty($tender_other_requirements4)): ?>
								<li><?php echo $tender_other_requirements4; ?></li>
							<?php endif; ?>
							<?php if (!empty($tender_other_requirements5)): ?>
								<li><?php echo $tender_other_requirements5; ?></li>
							<?php endif; ?>
							<?php if (!empty($tender_other_requirements6)): ?>
								<li><?php echo $tender_other_requirements6; ?></li>
							<?php endif; ?>
							<?php if (!empty($tender_other_requirements7)): ?>
								<li><?php echo $tender_other_requirements7; ?></li>
							<?php endif; ?>
						</ol>
					</dd>
				</div>
				<?php endif; ?>
			</dl>
		</div>
		<div>
			<?php if (has_term('finland-alko', 'tender-market', null)): ?>
				<a href="#wow-modal-id-1" style="font-weight:bold">Click here to learn more about Alko's Green Choice</a>
				
			<?php endif; ?>
			<p>Read about Concealed Wines Code of conduct & CSR Standard <a href="<?php echo get_bloginfo('url'); ?>/summary-csr-strategy-concealed-wines/" target="_blank" rel="noopener noreferrer">here</a>.</p>
		</div>
		<div id="apply-for-tender">
			<?php if (!empty($tender_start_date) && !empty($tender_launch_plan)) { ?>
			<?php } else { ?>
				<?php echo do_shortcode('[tabby title="Apply for this Tender"]'); ?>

				<div class="tender-tab__container">
					<div class="tender-tab__col tender_form">
						<?php
						if ($tender_offer_deadline >= time()) {
							// Only show form if deadline hasn't passed
							$tender_market_terms = wp_get_post_terms('tender-market', array('fields' => 'ids '));
							$tender_market_terms = implode(',', $tender_market_terms);
							$tender_product_terms = wp_get_post_terms('tender-products', array('fields' => 'ids '));
							$tender_product_terms = implode(',', $tender_product_terms);

							echo do_shortcode('
								[gravityform
								id=' . $tender_form_id . '
								title="true"
								description="true"
								ajax="true"
								field_values="
										prnt-tender-id=' . $post_id . '
										&tender-offer-title=Offer for tender Ref. ' . $tender_ref_number . '
										&tender-ref-num=' . $tender_ref_number . '
										&tender-market-id=' . $tender_market_terms . '
										&tender-product-id=' . $tender_product_terms . '"
							]');
						} else {
							// Show message if deadline has passed
							echo '<div class="tender-expired-message">';
							echo '<p>Sorry, this tender is currently unavailable as the offer deadline has passed.</p>';
							echo '<p>If you are still interested, please submit the interest request form.</p>';
							echo '</div>';
						}
						?>
					</div>
				</div>
			<?php } ?>
			<?php if ($tender_archive_date > time()): ?>
				<?php echo do_shortcode('[tabby title="Submit interest request"]'); ?>
				<div class="tender-tab__container">
					<div class="tender-tab__col">
						<div class="tab_item">
							To receive further information on this and other tenders feel free to contact us:<br>
							<div class="tab_phone">Telephone: <a href="tel:08-41 02 44 34">08-41 02 44 34</a></div>
							<div class="tab_mail"><a href="mailto:<?php echo antispambot('info@concealedwines.com') ?>"><?php echo antispambot('info@concealedwines.com') ?></a></div>
						</div>
						<div class="tab_item">
							<strong>ONLINE SUPPORT Calle Nilsson</strong>
							<strong>(Teams ID: <a href="https://teams.live.com/l/invite/FEAmanssa6byZH14gU">callenil</a>) </strong>
							<div class="tab_mail">Email: <a href="mailto:<?php echo antispambot('calle.nilsson@concealedwines.com') ?>"><?php echo antispambot('calle.nilsson@concealedwines.com') ?></a></div>
							<div class="tab_skype"><a href="skype:callenil?chat" class="button"><?php echo __('Chat with me'); ?></a></div>
							<script type="text/javascript" src="https://teams.live.com/l/invite/FEAmanssa6byZH14gU"></script>
						</div>
					</div>
					<div class="tender-tab__col request_tender_form">
						<?php
						echo do_shortcode('[gravityform id=' . $request_tender_form_id . ' title="true" description="true" field_values="tender-reference=' . $tender_ref_number . '"]');
						?>
					</div>
				</div>

				<?php echo do_shortcode('[tabbyending]'); ?>
			<?php else: ?>
				<div class="tender-stats">
					
					<?php if (!empty($tender_winner)): ?>
						<h3>Tender Stats</h3>
						<dl>
							<dt>Winner:</dt>
							<dd><?php echo htmlspecialchars($tender_winner); ?></dd>
						</dl>
						<?php endif; ?>

					<?php if (!empty($tender_winner_company)): ?>
						<dl>
							<dt>Winner Company:</dt>
							<dd><?php echo htmlspecialchars($tender_winner_company); ?></dd>
						</dl>
					<?php endif; ?>

					<?php if (!empty($tender_score)): ?>
					<dl>
						<dt>Score:</dt>
						<dd><?php echo htmlspecialchars($tender_score) . ' points'; ?></dd>
					</dl>
					<?php endif; ?>

					<?php if (!empty($tender_competition)): ?>
					<dl>
						<dt>Competition:</dt>
						<dd><?php echo htmlspecialchars($tender_competition) . ' products'; ?></dd>
					</dl>
					<?php endif; ?>

					<?php if (!empty($tender_launched_number)): ?>
					<dl>
						<dt>Launched Number:</dt>
						<dd><?php echo htmlspecialchars($tender_launched_number); ?></dd>
					</dl>
					<?php endif; ?>

				</div>

			<?php endif; ?>
		</div>
		<?php wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__('Pages:', 'kadence'),
				'after' => '</div>',
			)
		);
		do_action('kadence_single_after_entry_content'); ?>
		<?php else: ?>
		<div class="tender_apply_footer"><span><span><?php echo __('Please <a href="#" class="login-btn">login/register</a> to see more info such as ex works price, volumes & other technical criteria. Once you are logged in you can also propose your products via only submission. It is no cost to register and takes only few minutes.', 'kadence-child'); ?></span></span></div>
		<div id="apply-for-tender">
			<?php if ($tender_archive_date > time()): ?>
				<?php echo do_shortcode('[tabby title="Submit interest request"]'); ?>
				<div class="tender-tab__container">
					<div class="tender-tab__col">
						<div class="tab_item">
							To receive further information on this and other tenders feel free to contact us:<br>
							<div class="tab_phone">Telephone: <a href="tel:08-41 02 44 34">08-41 02 44 34</a></div>
							<div class="tab_mail"><a href="mailto:<?php echo antispambot('info@concealedwines.com') ?>"><?php echo antispambot('info@concealedwines.com') ?></a></div>
						</div>
						<div class="tab_item">
							<strong>ONLINE SUPPORT Calle Nilsson</strong>
							<strong>(Teams ID: <a href="https://teams.live.com/l/invite/FEAmanssa6byZH14gU">callenil</a>) </strong>
							<div class="tab_mail">Email: <a href="mailto:<?php echo antispambot('calle.nilsson@concealedwines.com') ?>"><?php echo antispambot('calle.nilsson@concealedwines.com') ?></a></div>
							<div class="tab_skype"><a href="skype:callenil?chat" class="button"><?php echo __('Chat with me'); ?></a></div>
							<script type="text/javascript" src="https://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
						</div>
					</div>
					<div class="tender-tab__col request_tender_form">
						<?php
						echo do_shortcode('[gravityform id=' . $request_tender_form_id . ' title="true" description="true" field_values="tender-reference=' . $tender_ref_number . '"]');
						?>
					</div>
				</div>

				<?php echo do_shortcode('[tabbyending]'); ?>
				<?php else: ?>
				<div class="tender-stats">					
					<?php if (!empty($tender_winner)): ?>
						<h3>Tender Stats</h3>
					<dl>
						<dt>Winner:</dt>
						<dd><?php echo htmlspecialchars($tender_winner); ?></dd>
					</dl>
					<?php endif; ?>

					<?php if (!empty($tender_winner_company)): ?>
					<dl>
						<dt>Winner Company:</dt>
						<dd><?php echo htmlspecialchars($tender_winner_company); ?></dd>
					</dl>
					<?php endif; ?>

					<?php if (!empty($tender_score)): ?>
					<dl>
						<dt>Score:</dt>
						<dd><?php echo htmlspecialchars($tender_score) . ' points'; ?></dd>
					</dl>
					<?php endif; ?>

					<?php if (!empty($tender_competition)): ?>
					<dl>
						<dt>Competition:</dt>
						<dd><?php echo htmlspecialchars($tender_competition) . ' products'; ?></dd>
					</dl>
					<?php endif; ?>

					<?php if (!empty($tender_launched_number)): ?>
					<dl>
						<dt>Launched Number:</dt>
						<dd><?php echo htmlspecialchars($tender_launched_number); ?></dd>
					</dl>
					<?php endif; ?>

				</div>

			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php
	// Check if current URL contains '/tender/' and tender offer deadline hasn't passed
	$current_url = $_SERVER['REQUEST_URI'];
	$show_modal = (strpos($current_url, '/tenders/') !== false) && (!empty($tender_offer_deadline) && $tender_offer_deadline > time());
	?>
	
	<div class="cw-modal-overlay" id="tender-modal" style="display:none;" data-show-modal="<?php echo $show_modal ? 'true' : 'false'; ?>">
      <div class="cw-modal">
        <h2>Would you like to discuss this tender in person?</h2>
        <button class="cw-btn cw-btn-yes" id="tender-yes">Yes</button>
        <button class="cw-btn cw-btn-no"  id="tender-no">No</button>
      </div>
    </div>

<?php
// Sticky Footer Bar
if ($tender_archive_date > time()):
    // Determine state for "Apply for Tender" button
    $can_apply_deadline = ($tender_offer_deadline > time());
    $show_sticky_apply_button = false;
    $apply_button_text = '';
    $apply_button_href = '#';
    $apply_button_classes = 'button'; // Base class
    $is_apply_action_disabled = false; // True if action is disabled (e.g. deadline passed)

    if (is_user_logged_in()) {
        // For logged-in users, show "Apply for Tender" sticky button
        // only if the non-gmptp "Apply for tender" link would have been shown at the top.
        if (empty($tender_start_date) || empty($tender_launch_plan)) {
            $show_sticky_apply_button = true;
            if ($can_apply_deadline) {
                $apply_button_text = __('Apply Tender', 'kadence-child');
                $apply_button_href = '#apply-for-tender';
            } else {
                $apply_button_text = __('Deadline Passed', 'kadence-child');
                $apply_button_classes .= ' disabled';
                $is_apply_action_disabled = true;
            }
        }
        // If $tender_start_date and $tender_launch_plan are both set,
        // the main "apply" action is via [gmptp_button], so we don't show this sticky "Apply for Tender".
    } else { // Not logged in
        $show_sticky_apply_button = true;
        if ($can_apply_deadline) {
            $apply_button_text = __('Apply Tender', 'kadence-child');
            $apply_button_href = '#'; // Will be a login trigger via class
            $apply_button_classes .= ' login-btn';
        } else {
            $apply_button_text = __('Deadline Passed', 'kadence-child');
            $apply_button_classes .= ' disabled';
            $is_apply_action_disabled = true;
        }
    }
?>
<div class="sticky-footer-bar-container">
    <div class="sticky-footer-bar">
		<div class="sticky-footer-left">
			<?php if (is_user_logged_in()): ?>
				<?php echo do_shortcode('[gmptp_button post_id="' . $post_id . '"]'); ?>
				<div class="favorite_tender">
					<?php echo do_shortcode('[treaking-tender id="' . $post_id . '"]'); ?>
				</div>
			<?php endif; ?>

			<?php if (!is_user_logged_in()): ?>
			<p><?php echo __('Please <a href="#" class="login-btn">login/register</a> to see more info and more features.There is no cost to register and takes only few minutes.', 'kadence-child'); ?></p>
			<?php endif; ?>
		</div>
		
        <div class="sticky-footer-right">
            <?php if ($show_sticky_apply_button): ?>
                <a href="<?php echo $is_apply_action_disabled ? '#' : esc_url($apply_button_href); ?>"
                   class="<?php echo esc_attr($apply_button_classes); ?>"
                   <?php if ($is_apply_action_disabled) echo 'aria-disabled="true" style="pointer-events:none;"'; ?>>
                    <?php echo esc_html($apply_button_text); ?>
                </a>
            <?php endif; ?>            

            <a href="#apply-for-tender" class="button">
                <?php echo __('Submit Interest', 'kadence-child'); ?>
            </a>

        </div>
    </div>
</div>

<?php endif; // end of ($tender_archive_date > time()) ?>

</div><!-- .entry-content -->

