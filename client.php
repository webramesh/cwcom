<?php
/**
 * Template Name: Client Area
 * 
 * A custom template for client area dashboard.
 * Displays login form for non-authenticated users and dashboard for logged-in clients.
 */
if (!defined('ABSPATH')) exit;
get_header(); 

// Get the actual registration form ID before doing anything
$register_form_id = null;
if (class_exists('GFAPI')) {
    $forms = GFAPI::get_forms();
    foreach ($forms as $form) {
        if ($form['title'] === 'Registration') {
            $register_form_id = $form['id'];
            break;
        }
    }
}

// Ensure Gravity Forms scripts are properly loaded on this page
if (class_exists('GFCommon') && $register_form_id) {
    // Use the actual form ID instead of 0
    gravity_form_enqueue_scripts($register_form_id, true);
    
    // Enqueue our custom client area script
    wp_enqueue_script(
        'client-area-js', 
        get_stylesheet_directory_uri() . '/modules/auth/client-area.js', 
        array('jquery', 'gform_gravityforms'), 
        '1.0.0', 
        true
    );
    
    // Pass the spinner URL and form ID to JavaScript
    wp_localize_script('client-area-js', 'gfClientVars', array(
        'spinnerUrl' => GFCommon::get_base_url() . '/images/spinner.svg',
        'spinnerAlt' => 'Loading',
        'formId' => $register_form_id
    ));
}
?>

<div id="primary" class="content-area">
	<div class="site-container">
		<div class="form-siderbar-content">
            <div class="client-main-content">
                <?php if (!is_user_logged_in()): ?>
                    <section class="client-login-container auth-modal-content">
                        <div class="client-form-container">
                            <!-- Login Form Section - Visible by default -->
                            <div id="client-login-section" style="display: block;">
                                <?php include(get_stylesheet_directory() . '/modules/auth/login-template.php'); ?>
                            </div>
                            
                            <!-- Registration Form Section - Hidden by default -->
                            <div id="client-register-section" style="display: none;">
                                <?php include(get_stylesheet_directory() . '/modules/auth/register-template.php'); ?>
                            </div>
                            
                            <!-- Make sure Gravity Forms global variables are available on this page -->
                            <script type="text/javascript">
                            document.addEventListener('DOMContentLoaded', function() {
                                if (typeof window.gform === 'undefined') {
                                    window.gform = {
                                        utils: {
                                            setupSpinner: function() {}
                                        }
                                    };
                                }
                                if (typeof window.gf_global === 'undefined') {
                                    window.gf_global = {
                                        "gfcaptcha": {},
                                        "spinnerUrl": "<?php echo GFCommon::get_base_url(); ?>/images/spinner.svg",
                                        "spinnerAlt": "Loading",
                                        "formId": <?php echo intval($register_form_id ?: 0); ?>,
                                    };
                                }
                                
                                // Enable Gravity Forms error display for embedded forms
                                jQuery(document).on('gform_post_render', function(event, formId) {
                                    // Remove loading state from submit buttons when form is re-rendered
                                    jQuery('.gform_button').prop('disabled', false).val('Register');
                                    
                                    // Make sure error containers are visible
                                    jQuery('.gform_validation_errors').css('display', 'block');
                                });
                            });
                            </script>
                        </div>
                    </section>
                    <div class="sign-up-information">
                    <h3>Sign in and access full tender details</h3> 
                        <p>As user you will be able to:</p>
                        <ul>
                            <li>See all details for each tender. (if not user, you can only see summary part for each tender)</li>
                            <li>Apply to tender online.</li>
                            <li>Subscribe to new tenders and track existing tenders.</li>
                        </ul>
                        <p>It is no cost to be registered and you can delete an account whenever you want. If you are already a user and lost password, you can recover it (link to recover page).</p>

                    </div>
                    <?php else: ?>
                        <?php
                            if (have_posts()) :
                                while (have_posts()) : the_post();
                                    the_content();
                                endwhile;
                            endif;
                            ?>
                        <?php endif; ?>
            </div>
            
            <!-- Sidebar Area (20%) -->
            <aside class="client-sidebar">
                <div class="sidebar-inner">
                    <div class="sidebar-widget">
                        <h3><?php echo esc_html__('About Concealed Wines', 'kadence-child'); ?></h3>
                        <?php 
                        if (is_active_sidebar('client-area-sidebar')) {
                            dynamic_sidebar('client-area-sidebar');
                        } else {
                            echo '<p>Concealed Wines business is to import and distribute wines and alcohol beverage products in the Swedish, Finnish and Norwegian market. Learn more about us and what we can offer you as our partner, by clicking on this link.</p>';
                        }
                        ?>
                    </div>
                    
                    <div class="sidebar-widget">
                        <h3><?php echo esc_html__('Latest News', 'kadence-child'); ?></h3>
                        <?php
                        $args = array(
                            'post_type' => 'post',
                            'posts_per_page' => 3,
                        );
                        $latest_posts = new WP_Query($args);
                        
                        if ($latest_posts->have_posts()) :
                            echo '<ul class="latest-posts-list">';
                            while ($latest_posts->have_posts()) : $latest_posts->the_post();
                                echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
                            endwhile;
                            echo '</ul>';
                            wp_reset_postdata();
                        else :
                            echo '<p>' . esc_html__('No news at this time.', 'kadence-child') . '</p>';
                        endif;
                        ?>
                    </div>
                    
                    <div class="sidebar-widget">
                        <h3><?php echo esc_html__('Contact Us', 'kadence-child'); ?></h3>
                        <div class="contact-info">
                            <p><i class="fas fa-envelope"></i> info@concealedwines.com</p>
                            <p><i class="fas fa-phone"></i> +46 8-410 244 34</p>
                        </div>
                    </div>

                    <?php if (!is_user_logged_in()): ?>

                    <div class="sidebar-widget">
                        
                        <?php 
                        if (is_active_sidebar('ewine-area-sidebar')) { ?>
                            <h3><?php echo esc_html__('E-Label offer', 'kadence-child'); ?></h3>
                            <?php dynamic_sidebar('ewine-area-sidebar');
                        } else {
                            echo '<p>Concealed Wines business is to import and distribute wines and alcohol beverage products in the Swedish, Finnish and Norwegian market. Learn more about us and what we can offer you as our partner, by clicking on this link.</p>';
                        }
                        ?>
                    </div>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </div>
</div>
<?php get_footer(); ?>