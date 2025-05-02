<?php
/* authorization functional */

/* shortcodes  \kadence-child\shortcodes\shortcodes.php */
function wpdocs_log_me_shortcode_fn()
{

	$args = array(
		'echo'           => true,
		//			'redirect'       => site_url( $_SERVER['REQUEST_URI'] ),
		'form_id'        => 'login_form',
		'label_username' => __('Username:'),
		'label_password' => __('Password:'),
		'label_log_in'   => __('Login'),
		'id_username'    => 'user_login',
		'id_password'    => 'user_pass',
		'id_remember'    => 'rememberme',
		'id_submit'      => 'wp-submit',
		'remember'       => false,
		'value_username' => NULL,
		'value_remember' => false
	);

	return wp_login_form($args);
}
add_shortcode('wp_login_form', 'wpdocs_log_me_shortcode_fn');

add_action('wp_logout', 'auto_redirect_after_logout');

function auto_redirect_after_logout()
{
    if (key_exists('HTTP_REFERER', $_SERVER)) {		
	$curent_url = $_SERVER['HTTP_REFERER'];
	if (str_contains($curent_url, 'wp-login.php?action=logout')) { 
	wp_safe_redirect(home_url());	
	} else {		
		wp_safe_redirect($curent_url);
	}
	exit;
	}
}



function wph_noadmin()
{
	if (is_admin() && !current_user_can('administrator') && !wp_doing_ajax()) {
		wp_redirect(home_url());
		exit;
	}
}
add_action('init', 'wph_noadmin');

add_filter('login_redirect', 'my_login_redirect', 10, 3);
function my_login_redirect($redirect_to, $requested_redirect_to, $user)
{
	$curent_url = $_SERVER['HTTP_REFERER'];
	if (is_wp_error($user)) {
		$error_types = array_keys($user->errors);
		$error_type = 'both_empty';
		if (is_array($error_types) && !empty($error_types)) {
			$error_type = $error_types[0];
		}
		wp_redirect(get_permalink(get_page_by_path('client-area')) . "?login=failed&reason=" . $error_type);
		exit;
	} else {
		if (str_contains($requested_redirect_to, '/tenders/')) {
			$redirect_to = $curent_url . '/#apply-for-tender';
		}
	}
	return $redirect_to;
}

/* Remain tender offers when deleting user */
add_filter('post_types_to_delete_with_user', 'on_delete_user', 10, 2);
function on_delete_user($post_types_to_delete, $id)
{
	foreach ($post_types_to_delete as $key => $post_type_to_delete) {
		if ($post_type_to_delete === 'tender-offers') {
			unset($post_types_to_delete[$key]);
		}
	}
	return $post_types_to_delete;
}

/*lost password*/
add_action('login_form_lostpassword', 'redirect_to_custom_lostpassword');
function redirect_to_custom_lostpassword()
{
	if ('GET' == $_SERVER['REQUEST_METHOD']) {
		if (is_user_logged_in()) {
			return __('You are already signed in.', 'personalize-login');
			exit;
		}
		wp_redirect(home_url('member-password-lost')); //page slug where reset shortcode will be use
		exit;
	}
}


add_shortcode('custom-password-lost-form', 'render_password_lost_form');
function render_password_lost_form($attributes, $content = null)
{
	// Parse shortcode attributes
	$default_attributes = array('show_title' => false);
	$attributes = shortcode_atts($default_attributes, $attributes);


	if (is_user_logged_in()) {
		return __('You are already signed in.', 'personalize-login');
	} else {
		if (isset($_REQUEST['errors'])) {
			switch ($_REQUEST['errors']) {
				case 'empty_username':
					_e('You need to enter your email address to continue.', 'personalize-login');
				case 'invalid_email':
				case 'invalidcombo':
					_e('There are no users registered with this email address.', 'personalize-login');
			}
		}
		if (isset($_REQUEST['checkemail'])) {
			switch ($_REQUEST['checkemail']) {
				case 'confirm':
					_e('Password reset email has been sent.', 'personalize-login');
			}
			//			return;
		}
		if (isset($_POST['user_login'])) {
			var_dump($_POST['user_login']);
		}
		//		$link = get_the_permalink();
		//var_dump($link);
	?>
		<div id="password-lost-form" class="widecolumn">
			<?php if ($attributes['show_title']) : ?>
				<h3><?php _e('Forgot Your Password?', 'personalize-login'); ?></h3>
			<?php endif; ?>

			<p>
				<?php
				_e(
					"Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.",
					'personalize_login'
				);
				?>
			</p>

			<form id="lostpasswordform" action="<?php echo wp_lostpassword_url(); ?>" method="post">
				<p class="form-row">
					<label for="user_login"><?php _e('Email', 'personalize-login'); ?>
						<input type="text" name="user_login" id="user_login">
				</p>

				<p class="lostpassword-submit">
					<input type="submit" name="submit" class="lostpassword-button" value="<?php _e('Reset Password', 'personalize-login'); ?>" />
				</p>
			</form>
		</div>
		<?php
	}
}

add_action('login_form_lostpassword', 'do_password_lost');
function do_password_lost()
{
	if ('POST' == $_SERVER['REQUEST_METHOD']) {
		$errors = retrieve_password();
		if (is_wp_error($errors)) {
			// Errors found
			$redirect_url = home_url('member-password-lost'); //page slug where reset shortcode will be use
			$redirect_url = add_query_arg('errors', join(',', $errors->get_error_codes()), $redirect_url);
		} else {
			// Email sent
			//			$link = get_the_permalink();
			//			var_dump($link);
			//			$redirect_url = home_url('signin');
			$redirect_url = home_url('member-password-lost'); //page slug where reset shortcode will be use
			$redirect_url = add_query_arg('checkemail', 'confirm', $redirect_url);
		}

		wp_redirect($redirect_url);
		exit;
	}
}

//After send Email
add_action('login_form_rp', 'redirect_to_custom_password_reset');
add_action('login_form_resetpass', 'redirect_to_custom_password_reset');
function redirect_to_custom_password_reset()
{
	if ('GET' == $_SERVER['REQUEST_METHOD']) {
		// Verify key / login combo
		$user = check_password_reset_key($_REQUEST['key'], $_REQUEST['login']);
		if (!$user || is_wp_error($user)) {
			if ($user && $user->get_error_code() === 'expired_key') {
				wp_redirect(home_url('member-login?login=expiredkey'));
			} else {
				wp_redirect(home_url('member-login?login=invalidkey'));
			}
			exit;
		}

		$redirect_url = home_url('member-password-reset');
		$redirect_url = add_query_arg('login', esc_attr($_REQUEST['login']), $redirect_url);
		$redirect_url = add_query_arg('key', esc_attr($_REQUEST['key']), $redirect_url);

		wp_redirect($redirect_url);
		exit;
	}
}


add_shortcode('custom-password-reset-form', 'render_password_reset_form');
function render_password_reset_form($attributes, $content = null)
{
	// Parse shortcode attributes
	$default_attributes = array('show_title' => false);
	$attributes = shortcode_atts($default_attributes, $attributes);

	if (is_user_logged_in()) {
		return __('You are already signed in.', 'personalize-login');
	} else {
		if (isset($_REQUEST['login']) && isset($_REQUEST['key'])) {
			$attributes['login'] = $_REQUEST['login'];
			$attributes['key'] = $_REQUEST['key'];

			// Error messages
			$errors = array();
			if (isset($_REQUEST['error'])) {
				$error_codes = explode(',', $_REQUEST['error']);
				$WP_Error = new WP_Error();

				foreach ($error_codes as $code) {
					$errors[] = $WP_Error->get_error_message($code);
				}
			}
			$attributes['errors'] = $errors;
		?>
			<div id="password-reset-form" class="widecolumn">
				<?php if ($attributes['show_title']) : ?>
					<h3><?php _e('Pick a New Password', 'personalize-login'); ?></h3>
				<?php endif; ?>

				<form name="resetpassform" id="resetpassform" action="<?php echo site_url('wp-login.php?action=resetpass'); ?>" method="post" autocomplete="off">
					<input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr($attributes['login']); ?>" autocomplete="off" />
					<input type="hidden" name="rp_key" value="<?php echo esc_attr($attributes['key']); ?>" />

					<?php if (count($attributes['errors']) > 0) : ?>
						<?php foreach ($attributes['errors'] as $error) : ?>
							<p>
								<?php echo $error; ?>
							</p>
						<?php endforeach; ?>
					<?php endif; ?>

					<p>
						<label for="pass1"><?php _e('New password', 'personalize-login') ?></label>
						<input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />
					</p>
					<p>
						<label for="pass2"><?php _e('Repeat new password', 'personalize-login') ?></label>
						<input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />
					</p>

					<p class="description"><?php echo wp_get_password_hint(); ?></p>

					<p class="resetpass-submit">
						<input type="submit" name="submit" id="resetpass-button" class="button" value="<?php _e('Reset Password', 'personalize-login'); ?>" />
					</p>
				</form>
			</div>
		<?php
		} else {
			return __('Invalid password reset link.', 'personalize-login');
		}
	}
}

add_action('login_form_rp', 'do_password_reset');
add_action('login_form_resetpass', 'do_password_reset');
function do_password_reset()
{
	if ('POST' == $_SERVER['REQUEST_METHOD']) {
		$rp_key = $_REQUEST['rp_key'];
		$rp_login = $_REQUEST['rp_login'];

		$user = check_password_reset_key($rp_key, $rp_login);

		if (!$user || is_wp_error($user)) {
			if ($user && $user->get_error_code() === 'expired_key') {
				wp_redirect(home_url('client-area/sign-up?login=expiredkey'));
			} else {
				wp_redirect(home_url('client-area/sign-up?login=invalidkey'));
			}
			exit;
		}

		if (isset($_POST['pass1'])) {
			if ($_POST['pass1'] != $_POST['pass2']) {
				// Passwords don't match
				$redirect_url = home_url('member-password-reset');

				$redirect_url = add_query_arg('key', $rp_key, $redirect_url);
				$redirect_url = add_query_arg('login', $rp_login, $redirect_url);
				$redirect_url = add_query_arg('error', 'password_reset_mismatch', $redirect_url);

				wp_redirect($redirect_url);
				exit;
			}

			if (empty($_POST['pass1'])) {
				// Password is empty
				$redirect_url = home_url('member-password-reset'); //page slug where reset shortcode will be use

				$redirect_url = add_query_arg('key', $rp_key, $redirect_url);
				$redirect_url = add_query_arg('login', $rp_login, $redirect_url);
				$redirect_url = add_query_arg('error', 'password_reset_empty', $redirect_url);

				wp_redirect($redirect_url);
				exit;
			}

			// Parameter checks OK, reset password
			reset_password($user, $_POST['pass1']);
			wp_redirect(home_url('client-area?password=changed')); //page slug where signin shortcode will be use
		} else {
			echo "Invalid request.";
		}

		exit;
	}
}
/*End authorization_functions*/

/* Email notifications */

/* Dismiss change password notofication*/
add_filter('send_password_change_email', '__return_false');

/* Admin email notifications */
add_action('user_register',    'inform_admin_user_registration');
function inform_admin_user_registration($user_id)
{
	$user = get_userdata($user_id);

	$email_context = 'New user ' . $user->first_name . ' email:' . $user->user_email . ' registered at the site ' . get_bloginfo('name');
	$subject = 'New user registered';
	$headers = 'From: ' . get_bloginfo('name') . ' ' . "\r\n";
	// get the admin email
	$admin_email = get_option('admin_email');

	// send the email
	wp_mail($admin_email, $subject, $email_context,  $headers);
}

add_action('submited_user',    'inform_admin_user_subscription');
function inform_admin_user_subscription($email, $first_name)
{
	$email_context = 'New user ' . $first_name . ' email:' . $email . ' subscribed for the tenders notifications at the site ' . get_bloginfo('name');
	$subject = 'New user subscription';
	$headers = 'From: ' . get_bloginfo('name') . ' ' . "\r\n";

	// get the admin email
	$admin_email = get_option('admin_email');

	// send the email
	wp_mail($admin_email, $subject, $email_context,  $headers);
}
/*User notifications about deleting account*/
add_action('delete_user', 'wpdocs_delete_user');
function wpdocs_delete_user($user_id)
{
	add_filter('wp_mail_content_type', function ($content_type) {
		return 'text/html';
	});
	$user_data = get_userdata($user_id);
	$admin_email = get_option('admin_email');
	$headers = 'From: ' . get_bloginfo('name') . ' ' . "\r\n";
	$subject = 'Your account is deleted';
	$message = 'Your account on ' . get_bloginfo('name') . ' is now deleted.<br><br>
<div class="footer" style="border-top: 1px solid #ddd; padding: 20px 0; clear: both; text-align: left;">
<h5>Best Regards</h5><br>
<div>
<small style="font-size: 11px;">' . home_url() . '</small><br>
<small style="font-size: 11px;">Sweden | Norway | Finland</small><br>
<small style="font-size: 11px;"><a href="' . home_url() . '">' . home_url() . '</a></small>
</div>
<div>
<br>
<small style="font-size: 11px;"><b>Contact details:</b><br>
Telephone <a href="tel:+46841024434">+46 841 024 434</a><br>
Office address:<br>
Bo Bergmansgata 14<br>
115 50 Stockholm<br>
Sweden
</small>
</div>
</div>';
	wp_mail($user_data->user_email, $subject, $message, $headers);
}
/* End Email notifications */

function wporg_block_wrapper($block_content, $block)
{
	if (preg_match('#^/current-tenders#', $_SERVER['REQUEST_URI']) && empty($_GET['country'])) {
		if ($block['blockName'] === 'kadence/accordion') {
			return '<div class="faq_block_tenders"><div class="faq_title has-text-align-center"> '. __('Questions and Answers about tenders for ', 'kadence-child') . get_the_title() . '</div>' . $block_content;
		}
	} else {
		return $block_content;
	}
	return $block_content;
}

add_filter('render_block', 'wporg_block_wrapper', 10, 2);


// change password by login users Ramesh Dhakal

add_shortcode('custom-change-password-form', 'render_change_password_form');

function render_change_password_form($attributes, $content = null)
{
    // Parse shortcode attributes
    $default_attributes = array('show_title' => false);
    $attributes = shortcode_atts($default_attributes, $attributes);

    if (!is_user_logged_in()) {
        return __('You must be logged in to change your password.', 'personalize-login');
    }

    // Error messages
    $errors = array();
    if (isset($_POST['submit'])) {
        $user = wp_get_current_user();
        
        if (!wp_check_password($_POST['current_password'], $user->user_pass, $user->ID)) {
            $errors[] = __('The current password is incorrect.', 'personalize-login');
        }

        if (empty($_POST['new_password'])) {
            $errors[] = __('The new password field is empty.', 'personalize-login');
        }

        if ($_POST['new_password'] != $_POST['confirm_new_password']) {
            $errors[] = __('The new passwords do not match.', 'personalize-login');
        }

        if (empty($errors)) {
            wp_set_password($_POST['new_password'], $user->ID);
            wp_password_change_notification($user);
            return __('Your password has been successfully changed.', 'personalize-login');
        }
    }

    ob_start();
  ?>
   
        

    <div class="change-password-form">
        <?php if ($attributes['show_title']) : ?>
            <h3><?php _e('Change Your Password', 'personalize-login'); ?></h3>
        <?php endif; ?>

        <?php if (!empty($errors)) : ?>
            <?php foreach ($errors as $error) : ?>
                <p class="error">
                    <?php echo $error; ?>
                </p>
            <?php endforeach; ?>
        <?php endif; ?>

        <form name="changepassform" id="changepassform" action="" method="post" autocomplete="off">
            <div class="form-row">
                <label for="current_password"><?php _e('Current password', 'personalize-login') ?></label>
                <input type="password" name="current_password" id="current_password" class="input" value="" autocomplete="off" required />
            </div>
            <div class="form-row">
                <label for="new_password"><?php _e('New password', 'personalize-login') ?></label>
                <input type="password" name="new_password" id="new_password" class="input" value="" autocomplete="off" required />
            </div>
            <div class="form-row">
                <label for="confirm_new_password"><?php _e('Confirm new password', 'personalize-login') ?></label>
                <input type="password" name="confirm_new_password" id="confirm_new_password" class="input" value="" autocomplete="off" required />
            </div>
            <p class="description"><?php echo wp_get_password_hint(); ?></p>
            <p class="change-password-submit">
                <input type="submit" name="submit" id="changepass-button" class="button" value="<?php _e('Change Password', 'personalize-login'); ?>" />
            </p>
        </form>
    </div>
    <?php
    return ob_get_clean();
}


