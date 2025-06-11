<div class="client-login-form" id="client-login-section">
    <h2><?php echo esc_html__('Client Area Login', 'kadence-child'); ?></h2>
    <p class="login-intro"><?php echo esc_html__('Please sign in to access your exclusive client dashboard.', 'kadence-child'); ?></p>
    
    <!-- Error message container for AJAX error responses -->
    <div class="login-error-message" style="display: none;"></div>

    <form name="client_loginform" id="client_login_form" class="cw-login-form">
        <div class="form-group">
            <label for="client_user_login"><?php echo esc_html__('Email', 'kadence-child'); ?></label>
            <div class="input-wrapper">
                <span class="input-icon"><i class="fas fa-envelope"></i></span>
                <input type="text" name="log" id="client_user_login" class="input" placeholder="<?php echo esc_attr__('Enter your email', 'kadence-child'); ?>" value="" size="20" autocapitalize="off" />
            </div>
        </div>

        <div class="form-group password-group">
            <label for="client_user_pass"><?php echo esc_html__('Password', 'kadence-child'); ?></label>
            <div class="password-field-wrapper">
                <span class="input-icon"><i class="fas fa-lock"></i></span>
                <input type="password" name="pwd" id="client_user_pass" class="input" placeholder="<?php echo esc_attr__('Enter your password', 'kadence-child'); ?>" value="" size="20" />
                <span class="password-toggle" id="password-toggle">
                    <i class="fas fa-eye" aria-hidden="true"></i>
                </span>
            </div>
        </div>

        <div class="form-group login-remember">
            <label>
                <input name="rememberme" type="checkbox" id="client_rememberme" value="forever" />
                <?php echo esc_html__('Remember Me', 'kadence-child'); ?>
            </label>
        </div>

        <div class="form-group login-submit">
            <input type="submit" name="wp-submit" id="client_wp-submit" class="button-primary" value="<?php echo esc_attr__('Sign In', 'kadence-child'); ?>" />
            <?php
            // Default to client area, but JavaScript will update this if on a tender page
            $redirect_to = get_permalink(get_page_by_path('client-area'));
            ?>
            <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>" />
            <?php 
            // Generate a fresh nonce that bypasses caching
            // Add timestamp to ensure uniqueness and prevent caching issues
            $fresh_nonce = wp_create_nonce('ajax-login-nonce');
            echo '<input type="hidden" name="login-security" value="' . esc_attr($fresh_nonce) . '" />';
            ?>
        </div>
    </form>
    
    <div class="social-login-separator">
        <span><?php echo esc_html__('Or sign in with', 'kadence-child'); ?></span>
    </div>
    
    <!-- Social Login using NextEnd Social Login -->
    <div class="wp-login-social-wrapper">
        <?php echo do_shortcode('[nextend_social_login provider="facebook,google"]'); ?>
    </div>
    
    <div class="client-login-links">
        <p class="recover_passwd"><a href="<?php echo wp_lostpassword_url(); ?>"><?php echo esc_html__('Forgot Password?', 'kadence-child'); ?></a></p>
        <p class="register_form_fancy_link2 reg_link"><?php echo esc_html__('Not registered yet?', 'kadence-child'); ?> <a href="#" class="switch-to-register"><?php echo esc_html__('Create an account', 'kadence-child'); ?></a></p>
    </div>
</div>