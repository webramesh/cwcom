<?php
// Add "Login as User" link in users list
add_filter('user_row_actions', 'add_login_as_user_link', 10, 2);
function add_login_as_user_link($actions, $user) {
    if (current_user_can('administrator') && $user->ID != get_current_user_id()) {
        $login_url = wp_nonce_url(
            admin_url('admin-ajax.php?action=login_as_user&user_id=' . $user->ID),
            'login_as_user_' . $user->ID
        );
        $actions['login_as'] = sprintf(
            '<a href="%s" target="_blank">Login as user</a>',
            esc_url($login_url)
        );
    }
    return $actions;
}

// Handle the login as user action
add_action('wp_ajax_login_as_user', 'handle_login_as_user');
function handle_login_as_user() {
    if (!current_user_can('administrator')) {
        wp_die('Permission denied');
    }

    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    check_admin_referer('login_as_user_' . $user_id);

    maybe_start_session();
    $_SESSION['admin_original_user_id'] = get_current_user_id();

    wp_clear_auth_cookie();
    wp_set_auth_cookie($user_id, false);
    wp_set_current_user($user_id);

    wp_redirect(home_url());
    exit;
}

// Handle return to admin action
add_action('wp_ajax_return_to_admin', 'handle_return_to_admin');
function handle_return_to_admin() {
    maybe_start_session();
    check_admin_referer('return_to_admin');

    if (isset($_SESSION['admin_original_user_id'])) {
        $admin_id = $_SESSION['admin_original_user_id'];
        unset($_SESSION['admin_original_user_id']);

        wp_clear_auth_cookie();
        wp_set_auth_cookie($admin_id, false);
        wp_set_current_user($admin_id);
    }

    // Redirect to Users page instead of Dashboard
    wp_redirect(admin_url('users.php'));
    exit;
}

// Add both footer hooks with high priority (999) to ensure they run last
add_action('wp_footer', 'add_fixed_return_button', 999);
add_action('admin_footer', 'add_fixed_return_button', 999);

function add_fixed_return_button() {
    maybe_start_session();

    if (isset($_SESSION['admin_original_user_id'])) {
        $return_url = wp_nonce_url(
            admin_url('admin-ajax.php?action=return_to_admin'),
            'return_to_admin'
        );
        ?>
        <div id="return-to-admin-fixed">
            <a href="<?php echo esc_url($return_url); ?>" class="return-to-admin-button">
                <span class="dashicons dashicons-admin-users"></span>
                Return to Admin 
            <?php 
                $admin_user = get_user_by('id', $_SESSION['admin_original_user_id']);
                echo ' (' . $admin_user->user_login . ')'; 
            ?>
            </a>
        </div>
        <?php
    }
}

// Add style hooks
add_action('wp_head', 'add_return_button_styles');
add_action('admin_head', 'add_return_button_styles');

function add_return_button_styles() {
    ?>
    <style>
        #return-to-admin-fixed {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 999999;
        }
        .return-to-admin-button {
            display: flex !important;
            align-items: center;
            background-color: #dc3545 !important;
            color: white !important;
            padding: 10px 20px !important;
            border-radius: 5px !important;
            text-decoration: none !important;
            font-weight: bold !important;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2) !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif !important;
            font-size: 14px !important;
            transition: all 0.3s ease !important;
        }
        .return-to-admin-button:hover {
            background-color: #c82333 !important;
            color: white !important;
            text-decoration: none !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3) !important;
        }
        .return-to-admin-button .dashicons {
            margin-right: 8px;
            font-size: 20px !important;
            width: 20px !important;
            height: 20px !important;
        }
    </style>
    <?php
}

// Persist session across all pages
function maybe_start_session() {
    if (!session_id() && !headers_sent()) {
        session_start();
        if (!isset($_SESSION)) {
            $_SESSION = array();
        }
    }
}