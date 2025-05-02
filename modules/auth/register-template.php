<?php
// Get registration form ID - using the method from the parent class to avoid duplicates
$register_form_id = '';
if (class_exists('GFAPI')) {
    // Check if we can access the auth instance
    if (class_exists('CW_Auth')) {
        global $cw_auth;
        $cw_auth = CW_Auth::get_instance();
        if (method_exists($cw_auth, 'get_form_id_by_name')) {
            $register_form_id = $cw_auth->get_form_id_by_name('Registration');
        }
    } else {
        // Fallback to direct form lookup if auth class not available
        $forms = GFAPI::get_forms();
        foreach ($forms as $form) {
            if ($form['title'] === 'Registration') {
                $register_form_id = $form['id'];
                break;
            }
        }
    }
}
?>

<div class="client-register-form" id="client-register-section">
    <h2><?php echo esc_html__('Client Registration', 'kadence-child'); ?></h2>
    <p class="register-intro"><?php echo esc_html__('Create your account to access exclusive client features.', 'kadence-child'); ?></p>
    
    <div class="register-form-container">
        <?php 
        if (!empty($register_form_id)) {
            echo do_shortcode('[gravityform id="' . $register_form_id . '" title="false" description="false" ajax="true"]'); 
        } else {
            echo '<p class="error-message">Registration form not found. Please contact the administrator.</p>';
        }
        ?>
    </div>
    
    <div class="social-login-separator">
        <span><?php echo esc_html__('Or register with', 'kadence-child'); ?></span>
    </div>
    
    <!-- Social Login for Registration -->
    <div class="wp-login-social-wrapper">
        <?php echo do_shortcode('[nextend_social_login provider="facebook,google"]'); ?>
    </div>
    
    <div class="client-login-links">
        <p class="register_form_fancy_link2 log_link"><?php echo esc_html__('Already have an account?', 'kadence-child'); ?> <a href="#" class="switch-to-login"><?php echo esc_html__('Sign in', 'kadence-child'); ?></a></p>
    </div>
</div>