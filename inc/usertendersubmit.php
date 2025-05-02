<?php
// Add the dashboard widget
function add_tender_offers_dashboard_widget() {
    wp_add_dashboard_widget(
        'tender_offers_dashboard_widget',
        'Submitted Tender Offers',
        'render_tender_offers_dashboard_widget'
    );
}
add_action('wp_dashboard_setup', 'add_tender_offers_dashboard_widget');

// Render the dashboard widget content
function render_tender_offers_dashboard_widget() {
    $args = array(
        'post_type' => 'tender-offers',
        'posts_per_page' => 20,
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $tender_offers = new WP_Query($args);

    if ($tender_offers->have_posts()) {
        echo '<table class="widefat fixed" cellspacing="0">';
        echo '<thead><tr>
                <th>Email</th>
                <th>Author</th>
                <th>Reference Number</th>
                <th>Submitted Date</th>
              </tr></thead><tbody>';

        while ($tender_offers->have_posts()) {
            $tender_offers->the_post();
            $post_id = get_the_ID();
            $author_id = get_post_field('post_author', $post_id);
            $author = get_userdata($author_id);
            $email = $author->user_email;
            $author_name = $author->display_name;
            $reference_number = get_post_meta($post_id, 'wpcf-tender-offer-reference', true);
            $submitted_date = get_the_date('Y-m-d');

            echo '<tr>';
            echo '<td>' . esc_html($email) . '</td>';
            echo '<td>' . esc_html($author_name) . '</td>';
            echo '<td><a href="' . esc_url(site_url('/tender/' . $reference_number)) . '" target="_blank" rel="noopener noreferrer">' . esc_html($reference_number) . '</a></td>';
            echo '<td>' . esc_html($submitted_date) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
        wp_reset_postdata();
    } else {
        echo 'No tender offers found.';
    }
}



// Add custom styles for the widget
function tender_offers_dashboard_widget_styles() {
    echo '<style>
        #tender_offers_dashboard_widget .inside {
            padding: 0;
            margin: 0;
        }
        #tender_offers_dashboard_widget table {
            border-collapse: collapse;
            width: 100%;
        }
        #tender_offers_dashboard_widget th,
        #tender_offers_dashboard_widget td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        #tender_offers_dashboard_widget th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>';
}
add_action('admin_head', 'tender_offers_dashboard_widget_styles');