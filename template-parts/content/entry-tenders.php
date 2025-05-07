<?php

/**
 * Template part for displaying a post
 *
 * @package kadence
 */

namespace Kadence;

$current_date = time();
$archive = get_post_meta(get_the_ID(), 'wpcf-tender-archive-date', true);
su_query_asset('js', array('popperjs', 'tippy'));
?>

<article <?php post_class('tender-item entry content-bg loop-entry'); ?><?php if ($current_date >= $archive) { ?> id="archive_tender" <?php } else { ?><?php } ?>>
	<?php
	/**
	 * Hook for entry thumbnail.
	 *
	 * @hooked Kadence\loop_entry_thumbnail
	 */
	do_action('kadence_loop_entry_thumbnail');
	$ref_number = get_post_meta(get_the_ID(), 'wpcf-tender-reference-number', true);
	$markets = get_the_terms(get_the_ID(), 'tender-market');

	?>
	<div class="favorite_tender">
		<?php if (!is_user_logged_in()) { ?>
			<div class="subscribe_block"><a href="#" class="login-btn"><?php echo __('Log in to subscribe', 'kadence-tenders'); ?></a></div>
		<?php } ?>
		<?php echo do_shortcode('[treaking-tender id="' . get_the_ID() . '"]'); ?>
	</div>
	<a href="<?php echo get_permalink(); ?>" class="entry-content-wrap">

		<?php echo '<h2 class="tender-item__title">' . get_the_title() . ' — ' . $ref_number . '</h2>'; ?>
		<div class="tender-item__content">
		    
			<dl class="tender-item__col">
				<?php

				$region = get_post_meta(get_the_ID(), 'wpcf-tender-region-classification', true);
				$composition = get_post_meta(get_the_ID(), 'wpcf-tender-grape-composition', true);
				$vintage = get_post_meta(get_the_ID(), 'wpcf-tender-wine-vintage', true);
				$product_type = get_the_terms(get_the_ID(), 'tender-products');
				$product_type_string = strip_tags(get_the_term_list(get_the_ID(), 'tender-products', '', ', '));
				/* $product_type_string = join(', ', wp_list_pluck($product_type, 'name')); */
				$product_country = get_the_terms(get_the_ID(), 'tender-countries');
				$product_country_string = strip_tags(get_the_term_list(get_the_ID(), 'tender-countries', '', ', '));
				/*$product_country_string = join(', ', wp_list_pluck($product_country, 'name'));*/

				if (!empty($region)) : ?>
					<div class="tenders_item">
						<dt class="tender-item__label tender_region">
							<?php echo __('Region:', 'kadence-tenders'); ?>
						</dt>
						<dd class="tender-item__value">
							<?php echo $region; ?>
						</dd>
					</div>
				<?php endif; ?>

				<?php if (!empty($composition)) : ?>
					<div class="tenders_item">
						<dt class="tender-item__label tender_grape_composition">
							<?php echo __('Grapes:', 'kadence-tenders'); ?>
						</dt>
						<dd class="tender-item__value">
							<?php echo $composition; ?>
						</dd>
					</div>
				<?php endif; ?>
				<?php if (!empty($product_country_string)) : ?>
					<div class="tenders_item">
						<dt class="tender-item__label tenders_country">
							<?php echo __('Country:', 'kadence-tenders'); ?>
						</dt>
						<dd class="tender-item__value">
							<?php echo $product_country_string; ?>
						</dd>
					</div>
				<?php endif; ?>
				<?php if (!empty($vintage)) : ?>
					<div class="tenders_item">
						<dt class="tender-item__label tender_vintage">
							<?php echo __('Vintage:', 'kadence-tenders'); ?>
						</dt>
						<dd class="tender-item__value">
							<?php echo $vintage; ?>
						</dd>
					</div>
				<?php endif; ?>
				<?php if (!empty($product_type_string)) : ?>
					<div class="tenders_item">
						<dt class="tender-item__label tenders_type">
							<?php echo __('Product Type:', 'kadence-tenders'); ?>
						</dt>
						<dd class="tender-item__value">
							<?php echo $product_type_string; ?>
						</dd>
					</div>
				<?php endif; ?>

			</dl>

			<dl class="tender-item__col">
				<?php
				$price = get_post_meta(get_the_ID(), 'wpcf-tender-wine-price', true);
				$quantity = get_post_meta(get_the_ID(), 'wpcf-tender-bottles-quantity', true);
				$estimated_volume = get_post_meta(get_the_ID(), 'wpcf-tender-estimated-volume', true);
				$deadline = get_post_meta(get_the_ID(), 'wpcf-tender-offer-deadline', true);
				$startdate = get_post_meta(get_the_ID(), 'wpcf-tender-start-date', true);
				$tender_bulk_price = get_post_meta(get_the_ID(), 'wpcf-tender-bulk-price-liter', true);
				if (!empty($price)) : ?>
					<div class="tenders_item">
						<dt class="tender-item__label tender_price">
							<?php echo __('Price:', 'kadence-tenders'); ?>
						</dt>
						<dd class="tender-item__value">
							<?php if (is_user_logged_in()) { ?>
								<?php echo $price; ?>
							<?php } else { ?>
								<span class="login_span login-btn" aria-haspopup="dialog" data-tippy-content="No cost to register/sign in"><?php _e('Log in'); ?></span>
							<?php } ?>
						</dd>
					</div>
				<?php endif; ?>

				<?php if (!empty($tender_bulk_price)) : ?>
					<div class="tenders_item">
						<dt class="tender-item__label tender_bulk_price" data-tippy-content="The net price ex cellar per litre.">
							<?php echo __('Bulk Price (per liter):', 'kadence-tenders'); ?>
						</dt>
						<dd class="tender-item__value">
							<?php if (is_user_logged_in()) { ?>
								<?php echo $tender_bulk_price; ?>
							<?php } else { ?>
								<span class="login_span login-btn" aria-haspopup="dialog" data-tippy-content="No cost to register/sign in"><?php _e('Log in'); ?></span>
							<?php } ?>
						</dd>
					</div>
				<?php endif; ?>

				<?php if (!empty($quantity)) : ?>
					<div class="tenders_item">
						<dt class="tender-item__label tender_bottles_qty">
							<?php echo __('Volume:', 'kadence-tenders'); ?>
						</dt>
						<dd class="tender-item__value">
							<?php echo $quantity; ?>
						</dd>
					</div>
				<?php endif; ?>
				
				<?php if (!empty($estimated_volume)) : ?>
                        <div class="tenders_item">
                            <dt class="tender-item__label tender_est_volume">
                                <?php echo __('Estimated Volume (yearly):', 'kadence-tenders'); ?>
                            </dt>
                            <dd class="tender-item__value">
                                <?php echo $estimated_volume; ?>
                            </dd>
                        </div>
                    <?php endif; ?>

				<?php if (!empty($deadline)) : ?>
					<div class="tenders_item">
						<dt class="tender-item__label tender_offer_deadline">
							<?php echo __('Deadline:', 'kadence-tenders'); ?>
						</dt>
						<dd class="tender-item__value">
							<?php echo wp_date(get_option('date_format'), $deadline); ?>
						</dd>
					</div>
				<?php endif; ?>
				<?php if (!empty($startdate)) : ?>
					<div class="tenders_item">
						<dt class="tender-item__label tender_start_date">
							<?php echo __('Start date:', 'kadence-tenders'); ?>
						</dt>
						<dd class="tender-item__value">
							<?php echo wp_date(get_option('date_format'), $startdate); ?>
						</dd>
					</div>
				<?php endif; ?>
			</dl>
		</div>
		</a>
        <?php
            // custom fields to show icons.
            $bulk_price = get_post_meta(get_the_ID(), 'wpcf-tender-bulk-price-liter', true);
            $organic    = get_post_meta(get_the_ID(), 'wpcf-tender-organic', true);
            $deadline   = get_post_meta(get_the_ID(), 'wpcf-tender-offer-deadline', true);
            
            // Get publish date
            $publish_date_unix = get_the_date('U', get_the_ID());
            
            // check if tender is recent
            $days_ago_30 = time() - (30 * DAY_IN_SECONDS);
            $is_recent   = ($publish_date_unix >= $days_ago_30); 
            ?>
            <div class="tender-alerticons">
                <div class="tender-icons">
                <?php // For a checkbox, typically '1' or 'checked' indicates it's set. Adjust if needed.
                if (!empty($organic)) : ?>
                        
                    <img src="<?php echo get_stylesheet_directory_uri() . '/images/organic.png'; ?>" width="32" height="32" alt="Organic Product"
                    data-tippy-content="<?php esc_attr_e('Organic product.', 'kadence-tenders'); ?>">
                <?php endif; ?>
            
                <?php if ($is_recent) : ?>
                    <img src="<?php echo get_stylesheet_directory_uri() . '/images/new.png'; ?>" width="32" height="32" alt="Recently Published"
                    data-tippy-content="<?php esc_attr_e('This tender is recently uploaded. If it matches your product, add it to your interest list for further notifications.', 'kadence-tenders'); ?>">
                <?php endif; ?>
                
                <?php if (!empty($bulk_price)) : ?>
                    <img src="<?php echo get_stylesheet_directory_uri() . '/images/bulk.png'; ?>" width="32" height="32" alt="Bulk Price Available"
                    data-tippy-content="<?php esc_attr_e('Bulk wine can be offered.', 'kadence-tenders'); ?>">
                    <?php
                        // Output a quick test message
                        echo '<div style="color:grey; font-weight:bold; padding-left:5px; align-content:end;">'
                           . __('Bulk wine can be offered.', 'kadence-tenders')
                           . '</div>';
                        ?>
                <?php endif; ?>
                </div>
                
                <a href="<?php echo get_permalink(); ?>" class="tender-item__link">More Details</a>
            </div>
            
            <?php
            // $deadline = get_post_meta(get_the_ID(), 'wpcf-tender-offer-deadline', true);
            // $startdate = get_post_meta(get_the_ID(), 'wpcf-tender-start-date', true);
            
            if (!empty($deadline)) {
                // Calculate deadline-based countdown
                $days_remaining = floor(($deadline - time()) / 86400); // seconds to days
                if ($days_remaining > 0 && $days_remaining <= 10) {
                    echo '<div style="color:red; font-weight:bold; padding-left:10px;">'
                       . sprintf(__('Only %s days remaining to submission deadline.', 'kadence-tenders'), $days_remaining)
                       . '</div>';
                }
            } elseif (!empty($startdate)) {
                // Calculate start-date-based countdown
                $days_until_start = floor((($startdate - time()) / 86400) + 1);
                if ($days_until_start > 0 && $days_until_start <= 10) {
                    echo '<div style="color:red; font-weight:bold; padding-left:10px;">'
                       . sprintf(__('Submission for this tender starts in %s days. Click the star button to add it to your interest list.', 'kadence-tenders'), $days_until_start)
                       . '</div>';
                }
            }
            ?>
            
		
	
</article>