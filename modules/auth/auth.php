<?php
if (!defined('ABSPATH')) exit;

class CW_Auth {
    private static $instance = null;
    private $register_form_id = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Get the registration form ID first
        $this->register_form_id = $this->get_form_id_by_name('Registration');

        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'render_auth_modal'));
        add_shortcode('register-form', array($this, 'reg_forms'));
        
        // AJAX login handling
        add_action('wp_ajax_nopriv_ajax_login', array($this, 'ajax_login'));

        // Initialize Gravity Forms globally
        add_action('wp_footer', array($this, 'initialize_gravity_forms'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style('client-auth-fontawesome', get_stylesheet_directory_uri() . '/assets/css/all.css', array(), '1.0.0');
        wp_enqueue_style('client-auth', get_stylesheet_directory_uri() . '/modules/auth/auth.css', array(), '1.0.0');
        
        // Force Gravity Forms to enqueue its scripts & styles on all pages
        if (class_exists('GFCommon') && $this->register_form_id) {
            GFCommon::get_base_url();
            GFCommon::maybe_output_gf_vars();
            
            // Use actual form ID instead of 0
            gravity_form_enqueue_scripts($this->register_form_id, true);
        }
        
        wp_enqueue_script('cw-auth', get_stylesheet_directory_uri() . '/modules/auth/auth.js', array('jquery', 'gform_gravityforms'), '1.0.2', true);
        
        wp_localize_script('cw-auth', 'cwAuth', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cw-auth-nonce'),
            'loginNonce' => wp_create_nonce('ajax-login-nonce'),
            'clientAreaUrl' => get_permalink(get_page_by_path('client-area')),
            'adminUrl' => admin_url(),
            'formId' => $this->register_form_id
        ));
    }

    // Add method to initialize Gravity Forms global variable
    public function initialize_gravity_forms() {
        if (class_exists('GFCommon')) {
            ?>
            <script type="text/javascript">
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
                    "formId": <?php echo intval($this->register_form_id ?: 0); ?>,
                };
            }
            </script>
            <?php
        }
    }

    // AJAX login handler
    public function ajax_login() {
        // Check the nonce
        check_ajax_referer('ajax-login-nonce', 'security');
        
        // Get login credentials
        $username = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $remember = isset($_POST['remember']) && $_POST['remember'] === 'true';
        
        // Basic validation
        if (empty($username) || empty($password)) {
            wp_send_json_error(array(
                'message' => __('Username and password are required.', 'kadence-child')
            ));
            exit;
        }
        
        // Attempt to log the user in
        $user = wp_signon(array(
            'user_login' => $username,
            'user_password' => $password,
            'remember' => $remember
        ), is_ssl());
        
        // Check for errors
        if (is_wp_error($user)) {
            wp_send_json_error(array(
                'message' => $user->get_error_message()
            ));
            exit;
        } else {
            // Get user roles
            $user_id = $user->ID;
            $user_info = get_userdata($user_id);
            $user_roles = $user_info->roles;
            
            // Set default redirect to client area
            $redirect_url = get_permalink(get_page_by_path('client-area'));
            
            // Check if user is an administrator and redirect to admin dashboard
            if (in_array('administrator', $user_roles)) {
                $redirect_url = admin_url();
            } else {
                // For non-admin users, get the redirect URL (if provided)
                if (!empty($_POST['redirect']) && filter_var($_POST['redirect'], FILTER_VALIDATE_URL)) {
                    $redirect_url = esc_url_raw($_POST['redirect']);
                    
                    // Make sure it's a URL within our site for security
                    $home_url = home_url();
                    if (strpos($redirect_url, $home_url) !== 0) {
                        // If not on our site, default to client area
                        $redirect_url = get_permalink(get_page_by_path('client-area'));
                    }
                }
            }
            
            wp_send_json_success(array(
                'redirect' => $redirect_url,
                'user_id' => $user_id,
                'roles' => $user_roles
            ));
            exit;
        }
    }

    public function reg_forms() {
        $register_form_id = '';
        if (class_exists('GFAPI')) {
            $register_form_id = $this->get_form_id_by_name('Registration');
        }
        
        $forms_content = '
        <div id="fancybox_form">
            <div class="register_form_fancy">
                <div class="form_title">' . esc_html__('Sign up', 'kadence-child') . '</div>
                <div class="register_desc">' . esc_html__("It is no cost to sign up and directly after you click Register you will get an activation account link to your inbox..", 'kadence-child') . '</div>
                ' . do_shortcode('[gravityform id="' . $register_form_id . '" title="false" description="false" ajax="true" tabindex="0"]') . 
                do_shortcode('[nextend_social_login provider="facebook,google"]') . '
            </div>
            <div class="register_form_fancy_link2 log_link"><a href="#" class="switch-to-login" data-tab="login">' . esc_html__('Already registered? Sign in', 'kadence-child') . '</a></div>
            <div class="recover_passwd"><a href="/client-area/member-password-lost/">Recover password</a></div>
        </div>';
        return $forms_content;
    }

    public function login_forms() {
        $forms_content = '
        <div id="fancybox_form">
            <div class="login_form_box">
                <div class="form_title">' . esc_html__('Sign in', 'kadence-child') . '</div>' . 
                do_shortcode('[loginform]') . '
            </div>
            <div class="register_form_fancy_link2 reg_link"><a href="#" class="switch-to-register" data-tab="register">' . esc_html__('New User Registration', 'kadence-child') . '</a></div>
            <div class="recover_passwd"><a href="/client-area/member-password-lost/">Recover password</a></div>
        </div>';
        return $forms_content;
    }

    public function get_form_id_by_name($name) {
        if (class_exists('GFAPI')) {
            $forms = GFAPI::get_forms();
            foreach ($forms as $form) {
                if ($form['title'] === $name) {
                    return $form['id'];
                }
            }
        }
        return '';
    }

    public function render_auth_modal() {
        ob_start();
        ?>
        <div class="auth-modal">
            <div class="auth-modal-content">
                <span class="auth-modal-close">&times;</span>
                <div class="auth-tabs">
                    <div class="auth-tab" data-tab="login">Sign In</div>
                    <div class="auth-tab" data-tab="register">Sign Up</div>
                </div>
                <div class="auth-content">
                    <div class="auth-login-content" id="auth-login" style="position: absolute; display: none;">
                        <?php include(get_stylesheet_directory() . '/modules/auth/login-template.php'); ?>
                    </div>
                    <div class="auth-register-content" id="auth-register" style="position: absolute; display: none;">
                       <?php include(get_stylesheet_directory() . '/modules/auth/register-template.php'); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        echo ob_get_clean();
    }
}

// Initialize the auth module
add_action('init', function() {
    CW_Auth::get_instance();
});


function client_area_sidebar() {
    register_sidebar(array(
        'name'          => __('Client Area Sidebar', 'kadence-child'),
        'id'            => 'client-area-sidebar',
        'description'   => __('Widgets in this area will be shown on the client area page.', 'kadence-child'),
        'before_widget' => '<div class="">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Ewine Area Sidebar', 'kadence-child'),
        'id'            => 'ewine-area-sidebar',
        'description'   => __('Widgets in this area will be shown on the client area page.', 'kadence-child'),
        'before_widget' => '<div class="">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'client_area_sidebar');