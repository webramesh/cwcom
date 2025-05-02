<?php

// Track post visits
// Track post visits
function track_tender_visit() {
    if (is_singular('tenders') && is_user_logged_in()) {
        $post_id = get_the_ID();
        $user_id = get_current_user_id();
        
        $visits = get_user_meta($user_id, 'tender_visits', true);
        if (!is_array($visits)) {
            $visits = array();
        }
        
        // Add the current visit
        $visits[] = $post_id;
        update_user_meta($user_id, 'tender_visits', $visits);

        // Check if this is at least the third visit and email hasn't been sent yet
        $visit_count = array_count_values($visits)[$post_id];
        $email_sent = get_user_meta($user_id, "interest_email_sent_{$post_id}", true);
        
        // Check if the user is not an "Office User"
        $is_office_user = get_user_meta($user_id, 'wpcf-office-user', true);
        
        if ($visit_count >= 3 && !$email_sent && !$is_office_user) {
            send_interest_notification($user_id, $post_id);
            update_user_meta($user_id, "interest_email_sent_{$post_id}", true);
        }
    }
}
add_action('wp', 'track_tender_visit');

function send_interest_notification($user_id, $post_id) {
    $user = get_userdata($user_id);
    $tender_title = get_the_title($post_id);
    $tender_url = get_permalink($post_id);
    $tender_reference = get_post_meta($post_id, 'wpcf-tender-reference-number', true);
    $tender_start_date = get_post_meta($post_id, 'wpcf-tender-start-date', true);
    $tender_offer_deadline = get_post_meta($post_id, 'wpcf-tender-offer-deadline', true);

	// Desired date format
	$format = 'Y-m-d H:i:s'; 

	// Convert and format the dates
	 $formatted_tender_start_date = date($format, $tender_start_date);
	 $formatted_tender_offer_deadline = date($format, $tender_offer_deadline);

 	//$formatted_tender_start_date = !empty($tender_start_date) ? date($format, intval($tender_start_date)) : 'Not set';
    //$formatted_tender_offer_deadline = !empty($tender_offer_deadline) ? date($format, intval($tender_offer_deadline)) : 'Not set';

	
    $to = 'aakriti.adhikari@concealedwines.com';
    $subject = "User Interest Notification: {$tender_title} : {$tender_reference}";
    
    $user_name = !empty($user->display_name) ? $user->display_name : $user->user_login;
    $first_name = !empty($user->first_name) ? $user->first_name : $user_name;
	$email = !empty($user->user_email) ? $user->user_email : '';
    $phone = get_user_meta($user_id, 'wpcf-vendor-user-phone', true);
    $user_website = !empty($user->user_url) ? $user->user_url : 'Not provided';
    
    $visits = get_user_meta($user_id, 'tender_visits', true);
    $visit_count = is_array($visits) ? count(array_keys($visits, $post_id)) : 0;
    $last_visit_date = date('Y-m-d H:i:s');

    $message_html = "
    <html>
    <body>
        <p>Hello, User</p>
        <p>Notification user interest:</p>
        <h3>User details</h3>
        <p>First name of user: {$first_name}</p>
        <p>Email of user: {$user->user_email}</p>
        <p>Phone of user: {$phone}</p>
        <p>User Website: {$user_website}</p>
        <h3>Tender details</h3>
        <p>Reference tender: {$tender_reference}</p>
        <p>Title tender: {$tender_title}</p>
        <p>URL tender: <a href='{$tender_url}'>{$tender_url}</a></p>
        <p>Number of visits on the tender: {$visit_count}</p>
        <p>Start Date: {$formatted_tender_start_date}</p>
        <p>Tender Deadline: {$formatted_tender_offer_deadline}</p>
        <p>Last visit date on the tender: {$last_visit_date}</p>
        
        <p>Best regards,<br>Concealed Wines System</p>
    </body>
    </html>
    ";

    $message_plain = "Hello, User
Notification user interest:
User details
First name of user: {$first_name}
Email of user: {$user->user_email}
Phone of user: {$phone}
User Website: {$user_website}
Tender details
Reference tender: {$tender_reference}
Title tender: {$tender_title}
URL tender: {$tender_url}
Number of visits on the tender: {$visit_count}
Start Date: {$tender_start_date}
Tender Deadline: {$tender_offer_deadline}
Last visit date on the tender: {$last_visit_date}
Best regards,
Concealed Wines System";

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'X-SMTPAPI: {"category": "Tender Interest Notification"}'
    );

    $sent = wp_mail($to, $subject, $message_html, $headers);

    if ($sent) {
        error_log("Interest notification sent for user {$user_id} and tender {$post_id}");
    } else {
        error_log("Failed to send interest notification for user {$user_id} and tender {$post_id}");
    }
}

// send mail to the user
function send_interest_email($user_id, $post_id) {
    $user = get_userdata($user_id);
    $tender_title = get_the_title($post_id);
    $tender_url = get_permalink($post_id);
    $to = $user->user_email;
    $subject = "Interest in Tender: " . $tender_title;

	$user_name = !empty($user->display_name) ? $user->display_name : $user->user_login;
    
    // HTML message with linked tender title
    $message_html = "
    <html>
    <body>
        <p>Dear {$user_name},</p>
        <p>It seems that you are interested in the tender <a href='{$tender_url}'>\"{$tender_title}\"</a>.</p>
        <p>Do you need any help from Concealed Wines? If yes, feel free to contact us.</p>
        <p>Thank you.</p>
        <p>Best regards,<br>Concealed Wines Team</p>
    </body>
    </html>
    ";

    // Plain text version for email clients that don't support HTML
     $message_plain = "Dear {$user_name},

It seems that you are interested in the tender \"{$tender_title}\".
You can view the tender here: {$tender_url}

Do you need any help from Concealed Wines? If yes, feel free to contact us.

Thank you.

Best regards,
Concealed Wines Team";

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'X-SMTPAPI: {"category": "Tender Interest"}'
    );

    // Use wp_mail with HTML content
    $sent = wp_mail($to, $subject, $message_html, $headers);

    // Optionally, log if the email was sent successfully
    if ($sent) {
        error_log("Interest email sent to user {$user_id} for tender {$post_id}");
    } else {
        error_log("Failed to send interest email to user {$user_id} for tender {$post_id}");
    }
}

//Get visit count for a user
function get_tender_visit_count($user_id) {
    $visits = get_user_meta($user_id, 'tender_visits', true);
    return is_array($visits) ? count($visits) : 0;
}

// Display visit count in admin user profile
function display_tender_visits_in_profile($user) {
    $visits = get_user_meta($user->ID, 'tender_visits', true);
    $visit_count = is_array($visits) ? count($visits) : 0;
    ?>
    <h3>Tender Visits</h3>
    <table class="form-table">
        <tr>
            <th><label for="tender_visits">Total Tender Visits</label></th>
            <td><?php echo $visit_count; ?></td>
        </tr>
        <tr>
            <th><label for="tender_visits_details">Visited Tenders</label></th>
            <td>
                <?php
                if (is_array($visits) && !empty($visits)) {
                    $visit_counts = array_count_values($visits);
                    arsort($visit_counts);  // Sort by visit count, descending
                    echo '<ul id="tender-visits-list">';
                    foreach ($visit_counts as $post_id => $count) {
                        $post_title = get_the_title($post_id);
                        $post_url = get_permalink($post_id);
                        echo "<li data-post-id='$post_id'>";
                        echo "<a href='$post_url' target='_blank'>$post_title</a> (Visits: $count)";
                        echo "</li>";
                    }
                    echo '</ul>';
                } else {
                    echo 'No tenders visited yet.';
                }
                ?>
            </td>
        </tr>
    </table>
    <style>
        #tender-visits-list {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
            list-style-type: none;
        }
        #tender-visits-list li {
            margin-bottom: 5px;
        }
        #tender-visits-list a {
            text-decoration: none;
        }
        #tender-visits-list a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
    jQuery(document).ready(function($) {
        $('#tender-visits-list li').click(function(e) {
            if (!$(e.target).is('a')) {
                var link = $(this).find('a').attr('href');
                window.open(link, '_blank');
            }
        });
    });
    </script>
    <?php
}
add_action('show_user_profile', 'display_tender_visits_in_profile');
add_action('edit_user_profile', 'display_tender_visits_in_profile');

// Add custom column to users list
function add_tender_visits_column($columns) {
    $columns['tender_visits'] = 'Tender Visits';
    return $columns;
}
add_filter('manage_users_columns', 'add_tender_visits_column');

// Make the column sortable
function make_tender_visits_column_sortable($columns) {
    $columns['tender_visits'] = 'tender_visits';
    return $columns;
}
add_filter('manage_users_sortable_columns', 'make_tender_visits_column_sortable');

function sort_users_by_tender_visits($query) {
    if (!is_admin()) {
        return;
    }

    $orderby = $query->get('orderby');

    if ('tender_visits' == $orderby) {
        $query->set('meta_key', 'tender_visits_count');
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_users', 'sort_users_by_tender_visits');



// Display visit count in the custom column
function display_tender_visits_column_content($value, $column_name, $user_id) {
    if ($column_name === 'tender_visits') {
        $visit_count = get_tender_visit_count($user_id);
        update_user_meta($user_id, 'tender_visits_count', $visit_count);
        return $visit_count;
    }
    return $value;
}
add_action('manage_users_custom_column', 'display_tender_visits_column_content', 10, 3);