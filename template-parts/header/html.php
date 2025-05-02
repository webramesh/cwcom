<?php

/**
 * Template part for displaying the header HTML Modual
 *
 * @package kadence
 */

namespace Kadence;

?>
<div class="site-header-item site-header-focus-item" data-section="kadence_customizer_header_html">
	<?php
	/**
	 * Kadence Header HTML
	 *
	 * Hooked Kadence\header_html
	 */
	do_action('kadence_header_html');
	?>
	<?php if (is_user_logged_in()) { ?>
		<div class="header-html header_logout">
			<a href="/client-area/">
				<span class="kt-info-svg-icon kt-info-svg-icon-fe_userCheck">
					<svg style="display:inline-block;vertical-align:middle" viewBox="0 0 24 24" height="30" width="30" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
						<circle cx="8.5" cy="7" r="4"></circle>
						<polyline points="17 11 19 13 23 9"></polyline>
					</svg></span>
			</a>
			<a href="<?php echo wp_logout_url(); ?>"><?php echo esc_html__('Logout', 'kadence-child'); ?></a>
		</div>
	<?php } else { ?>
		<div class="header-html header_login">
			<a href="/client-area/">
				<span class="kt-info-svg-icon kt-info-svg-icon-fe_user">
					<svg style="display:inline-block;vertical-align:middle" viewBox="0 0 24 24" height="30" width="30" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
						<circle cx="8.5" cy="7" r="4"></circle>
					</svg></span>
			</a>
			<span class="login_box"><a href="#" class="login-btn"><?php echo esc_html__('Sign in', 'kadence-child'); ?></a></span>
			<span class="header_separatop">|</span>
			<span class="signup_box"><a href="#" class="register-btn"><?php echo esc_html__('Sign up', 'kadence-child'); ?></a></span>
		</div>
	<?php } ?>
</div><!-- data-section="header_html" -->