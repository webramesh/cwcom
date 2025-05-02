<?php

//login user counts

// Add custom column to user list
function add_login_count_column($columns) {
    $columns['login_count'] = 'Login Count';
    return $columns;
}
add_filter('manage_users_columns', 'add_login_count_column');

// Populate custom column with login count data
function populate_login_count_column($value, $column_name, $user_id) {
    if ($column_name == 'login_count') {
        $login_count = get_user_meta($user_id, 'login_count', true);
        return $login_count ? $login_count : '0';
    }
    return $value;
}
add_action('manage_users_custom_column', 'populate_login_count_column', 10, 3);

// Make the column sortable
function make_login_count_column_sortable($columns) {
    $columns['login_count'] = 'login_count';
    return $columns;
}
add_filter('manage_users_sortable_columns', 'make_login_count_column_sortable');

// Handle sorting
function login_count_column_orderby($query) {
    if (!is_admin()) {
        return;
    }

    $orderby = $query->get('orderby');

    if ('login_count' == $orderby) {
        $query->set('meta_key', 'login_count');
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_users', 'login_count_column_orderby');

// Track user logins
function track_user_login($user_login, $user) {
    $login_count = (int)get_user_meta($user->ID, 'login_count', true);
    update_user_meta($user->ID, 'login_count', $login_count + 1);
}
add_action('wp_login', 'track_user_login', 10, 2);




