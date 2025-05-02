<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Tender_Subscribe_Admin_Table' ) )
{

    class Tender_Subscribe_Admin_Table extends WP_List_Table
    {

        /**
         * Stores the value returned by ->get_column_info().
         *
         * @since 4.1.0
         * @var array
         */
        protected $_column_headers;

        /**
         * @var array[][]
         */
        protected $config = [
            'columns_sortable'  => [
                'id' => array( 'ID', true ),
            ]
        ];

        public function __construct($config)
        {

            $this->config['global'] = $config;

            parent::__construct( array(
                'singular' => 'subscribe', //singular name of the listed records
                'plural'   => 'subscribe', //plural name of the listed records
                'ajax'     => false //does this table support ajax?
            ) );

        }

        /**
         * Print default column content
         *
         * @param $item        mixed Item of the row
         * @param $column_name string Column name
         *
         * @return string Column content
         * @since 1.0.0
         */
        public function column_default( $item, $column_name )
        {

            if ( isset( $item[$column_name] ) ) :

                return esc_html( $item[$column_name] );

            else:

                return print_r( $item, true ); //Show the whole array for troubleshooting purposes

            endif;

        }

        /**
         * Print column with subscribe ID
         *
         * @param $item mixed Current item row
         *
         * @return string Column content
         * @since 1.0.0
         */
        public function column_id( $item )
        {
            $column = sprintf( '<strong>#%s</strong>', $item['id'] );
            return $column;
        }

        /**
         * Print column with subscribe Display Name
         *
         * @param $item mixed Current item row
         *
         * @return string Column content
         * @since 1.0.0
         */
        public function column_display_name( $item )
        {
            $column = $item['display_name'];
            return $column;
        }

        /**
         * Print column with subscribe First Name
         *
         * @param $item mixed Current item row
         *
         * @return string Column content
         * @since 1.0.0
         */
        public function column_first_name( $item )
        {
            $column = $item['first_name'];
            return $column;
        }

        /**
         * Print column with subscribe User ID
         *
         * @param $item mixed Current item row
         *
         * @return string Column content
         * @since 1.0.0
         */
        public function column_user_id( $item )
        {
            $column = $item['user_id'] <> 0 ? __( 'Yes', 'kadence-child' ) : __( 'No', 'kadence-child' );
            return $column;
        }

        /**
         * Print column with subscribe Message
         *
         * @param $item mixed Current item row
         *
         * @return string Column content
         * @since 1.0.0
         */
        public function column_msg( $item )
        {
            // Get the template type from the log message if available
            if (strpos($item['message'], 'template_one') !== false) {
                $subject = get_option('subscribe_settings_template_one_subject');
            } 
            else if (strpos($item['message'], 'template_two') !== false) {
                $subject = get_option('subscribe_settings_template_two_subject');
            }
            else if (strpos($item['message'], 'template_three') !== false) {
                $subject = get_option('subscribe_settings_template_three_subject');
            }
            else if (strpos($item['message'], 'template_four') !== false) {
                $subject = get_option('subscribe_settings_template_four_subject');
            }
            else {
                // Clean up message by removing redundant "Success - " prefix
                $message = $item['message'];
                if (strpos($message, 'Success - ') === 0) {
                    $message = substr($message, 10); // Remove "Success - " prefix (10 characters)
                }
                return esc_html($message);
            }

            return esc_html($subject);
        }

        /**
         * Print column with subscribe Selections
         *
         * @param $item mixed Current item row
         *
         * @return string Column content
         * @since 1.0.0
         */
        public function column_selections($item) {
            if (empty($item['selections'])) {
                return '-';
            }

            $selections = explode('||', $item['selections']);
            $grouped = [];
            
            foreach ($selections as $selection) {
                if (empty($selection)) continue;
                
                list($taxonomy, $values) = explode('::', $selection);
                $type = str_replace('tender-', '', $taxonomy);
                $type = ucfirst($type); // Capitalize first letter
                
                // Handle "All" selection (when tag_id is 0)
                if ($values == '0') {
                    $values = 'All';
                }

                if (!isset($grouped[$type])) {
                    $grouped[$type] = [];
                }
                $grouped[$type][] = $values;
            }
            
            $output = [];
            foreach ($grouped as $type => $values) {
                $unique_values = array_unique($values);
                $output[] = "<strong>{$type}:</strong> " . implode(', ', $unique_values);
            }

            return implode('<br>', $output);
        }

        /**
         * Print column with subscribe User
         *
         * @param $item mixed Current item row
         *
         * @return string Column content
         * @since 1.0.0
         */
        public function column_user($item) {
            if (!empty($item['user_email'])) {
                // Get user by email
                $user = get_user_by('email', $item['user_email']);
                if ($user) {
                    return sprintf('%s (%s)', 
                        esc_html($user->display_name), 
                        esc_html($item['user_email'])
                    );
                }
                // If no user found, just show email
                return esc_html($item['user_email']);
            }
            return '-';
        }

        /**
         * Print column with subscribe Status (now displaying Email title)
         *
         * @param $item mixed Current item row
         * @return string Column content
         */
        public function column_status($item) {
            // First, try to get the template type from email_title if it exists
            // or from the message field if email_title is not available
            $content = isset($item['email_title']) && !empty($item['email_title']) 
                       ? $item['email_title'] 
                       : (isset($item['message']) ? $item['message'] : '');
            
            // Check for template identifiers in the content
            if (strpos($content, 'template_one') !== false) {
                $subject = get_option('subscribe_settings_template_one_subject');
                return esc_html($subject);
            } 
            elseif (strpos($content, 'template_two') !== false) {
                $subject = get_option('subscribe_settings_template_two_subject');
                return esc_html($subject);
            }
            elseif (strpos($content, 'template_three') !== false) {
                $subject = get_option('subscribe_settings_template_three_subject');
                return esc_html($subject);
            }
            elseif (strpos($content, 'template_four') !== false) {
                $subject = get_option('subscribe_settings_template_four_subject');
                return esc_html($subject);
            }
            
            // For data that doesn't contain template information or has been migrated:
            if (isset($item['email_title']) && !empty($item['email_title'])) {
                return esc_html($item['email_title']);
            }
            
            // Fallback for old data without email_title and no template info
            $status = isset($item['status']) ? $item['status'] : '';
            $code = isset($item['code']) ? $item['code'] : '';
            
            if ($status && $code) {
                return sprintf('%s (%d)', 
                    esc_html($status), 
                    esc_html($code)
                );
            }
            
            return '—'; // Em dash for empty values
        }

        /**
         * Print column with subscribe Date
         *
         * @param $item mixed Current item row
         * @return string Column content
         */
        public function column_date($item) {
            // Check if we're on the logs page
            $page = isset($_GET['page']) ? $_GET['page'] : '';
            
            if ($page === 'subscribe-admin-logs') {
                // For logs table
                return date('M/d/Y : h:i A', strtotime($item['date']));
            } else {
                // For subscribe table
                return date('M/d/Y : h:i A', strtotime($item['date']));
            }
        }

        /**
         * Returns hidden columns for current table
         *
         * @return mixed Array of hidden columns
         * @since 1.0.0
         */
        public function get_hidden_columns()
        {
            return get_hidden_columns( get_current_screen() );
        }

        /**
         * Gets the number of items to display on a single page.
         *
         * @since 1.0.0
         *
         * @param string $option
         * @param int    $default
         * @return int
         */
        protected function get_items_per_page( $option, $default = 20 )
        {
            $per_page = (int) get_user_option( $option );
            if ( empty( $per_page ) || $per_page < 1 ) {
                $per_page = $default;
            }

            /**
             * Filters the number of items to be displayed on each page of the list table.
             *
             * The dynamic hook name, `$option`, refers to the `per_page` option depending
             * on the type of list table in use. Possible filter names include:
             *
             *  - `edit_comments_per_page`
             *  - `sites_network_per_page`
             *  - `site_themes_network_per_page`
             *  - `themes_network_per_page'`
             *  - `users_network_per_page`
             *  - `edit_post_per_page`
             *  - `edit_page_per_page'`
             *  - `edit_{$post_type}_per_page`
             *  - `edit_post_tag_per_page`
             *  - `edit_category_per_page`
             *  - `edit_{$taxonomy}_per_page`
             *  - `site_users_network_per_page`
             *  - `users_per_page`
             *
             * @since 2.9.0
             *
             * @param int $per_page Number of items to be displayed. Default 20.
             */
            return (int) apply_filters( "{$option}", $per_page );
        }

        /**
         * Returns column to be sortable in table
         *
         * @return array Array of sortable columns
         * @since 1.0.0
         */
        public function get_sortable_columns()
        {

            $sortable_columns = $this->config['columns_sortable'];
            return $sortable_columns;
        }

        /**
         * Returns columns available in table
         *
         * @return array Array of columns of the table
         * @since 1.0.0
         */
        public function get_columns()
        {
            // Check if we're on the logs page
            $page = isset($_GET['page']) ? $_GET['page'] : '';
            
            if ($page === 'subscribe-admin-logs') {
                // Columns for Logs table
                return [
                    'user' => __('User', 'kadence-child'),
                    'status' => __('Email title', 'kadence-child'),
                    'msg' => __('Message', 'kadence-child'),
                    'date' => __('Date', 'kadence-child')
                ];
            } else {
                // Columns for Subscribe table (original columns)
                return [
                    'id' => __('ID', 'kadence-child'),
                    'email' => __('E-mail', 'kadence-child'),
                    'display_name' => __('Display Name', 'kadence-child'), 
                    'date' => __('Subscribe Date', 'kadence-child'),
                    'user_id' => __('Registration', 'kadence-child'),
                    'selections' => __('Selections', 'kadence-child')
                ];
            }
        }

        /**
         * Print table views
         *
         * @return array Array with available views
         * @since 1.0.0
         */
        public function get_views()
        {
            $current   = isset( $_GET['status'] ) ? $_GET['status'] : 'all';
            $query_arg = array();

            if ( ! empty( $_REQUEST['s'] ) && $_REQUEST['s'] != '' ) {
                $query_arg['s'] = $_REQUEST['s'];
            }

            return $query_arg;
        }

        /**
         *
         * @return int Number of counted subscribes
         * @use   Affilates_Handler::get_subscribes()
         * @since 1.0.0
         *
         * @param array $args
         * @return int
         *
         */
        public function count_subscribe( $args = [] )
        {

            $defaults = array(
                'ID' => true
            );

            $args = wp_parse_args( $args, $defaults );

            return count( $this->get_subscribe( $args ) );
        }

        /**
         *
         * @return int Number of counted subscribes
         * @use   Affilates_Handler::get_subscribes()
         * @since 1.0.0
         *
         * @param array $args
         * @return int
         *
         */
        public function count_logs( $args = [] )
        {

            $defaults = array(
                'ID' => true
            );

            $args = wp_parse_args( $args, $defaults );

            return count( $this->get_logs( $args ) );
        }

        /**
         * Get Items Logs
         * @param array $args
         * @return array|object|null
         */
        public function get_logs($args = array()) {
            global $wpdb;

            $defaults = array(
                'ID' => false,
                'order' => 'DESC',
                'orderby' => 'ID',
                'limit' => 0,
                'offset' => 0,
                'search' => isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : ''
            );

            $args = wp_parse_args($args, $defaults);
            
            $table = $this->config['global']['prefix'] . $this->config['global']['table_logs'];
            
            $query = "SELECT * FROM {$table}";

            // Add search condition
            if (!empty($args['search'])) {
                $search = '%' . $wpdb->esc_like($args['search']) . '%';
                $query .= $wpdb->prepare(" WHERE 
                    user_email LIKE %s OR 
                    message LIKE %s OR
                    email_title LIKE %s", // Added email_title to search
                    $search, $search, $search
                );
            }
            
            $query .= " ORDER BY id DESC";
            
            if (!empty($args['limit'])) {
                $query .= $wpdb->prepare(" LIMIT %d, %d", $args['offset'], $args['limit']);
            }

            return $wpdb->get_results($query, ARRAY_A);
        }

        /**
         * Get Items Subscribes
         * @param array $args
         * @return array|object|null
         */
        public function get_subscribe() {
            global $wpdb;
            
            $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';
            
            $query = "
                SELECT DISTINCT
                    sb.id,
                    sb.user_id,
                    sb.email,
                    sb.first_name, 
                    sb.date,
                    u.user_email,
                    u.display_name,
                    u.ID as user_id,
                    GROUP_CONCAT(
                        DISTINCT CONCAT(
                            et.tag_taxonomy, '::',
                            CASE 
                                WHEN et.tag_id = '0' THEN 'All'
                                WHEN et.tag_taxonomy = 'tender-regions' AND et.tag_id LIKE '%,%' THEN 
                                    (SELECT GROUP_CONCAT(t2.name ORDER BY t2.name ASC SEPARATOR ', ')
                                     FROM {$wpdb->terms} t2 
                                     WHERE FIND_IN_SET(t2.term_id, et.tag_id))
                                ELSE (
                                    SELECT GROUP_CONCAT(t2.name ORDER BY t2.name ASC SEPARATOR ', ')
                                    FROM {$wpdb->terms} t2
                                    WHERE t2.term_id = et.tag_id
                                )
                            END
                        ) ORDER BY et.tag_taxonomy
                        SEPARATOR '||'
                    ) as selections
                FROM {$this->config['global']['prefix']}{$this->config['global']['table']} sb
                LEFT JOIN {$wpdb->users} u ON u.ID = sb.user_id
                LEFT JOIN {$this->config['global']['prefix']}{$this->config['global']['table_tags']} et 
                    ON et.emailing_id = sb.id
                LEFT JOIN {$wpdb->terms} t ON FIND_IN_SET(t.term_id, et.tag_id)
                WHERE et.tag_id IS NOT NULL";

            // Add search condition
            if (!empty($search)) {
                $like = '%' . $wpdb->esc_like($search) . '%';
                $query .= $wpdb->prepare(" AND (
                    sb.email LIKE %s OR
                    sb.first_name LIKE %s OR
                    u.display_name LIKE %s OR
                    u.user_email LIKE %s
                )", $like, $like, $like, $like);
            }

            $query .= " GROUP BY sb.email
                        HAVING selections IS NOT NULL
                        ORDER BY sb.id DESC";

            $results = $wpdb->get_results($query, ARRAY_A);
            
            return array_filter($results, function($row) {
                return !empty($row['selections']);
            });
        }

        /**
         * Prepare items for table
         * @verify_subscribe
         *
         * @return void
         * @since 1.0.0
         */
        public function prepare_items_subscribe() {
            // Get columns
            $columns = $this->get_columns();
            $hidden = $this->get_hidden_columns();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array($columns, $hidden, $sortable);
            
            // Get items
            $items = $this->get_subscribe();
            $total_items = count($items);
            
            // Pagination
            $per_page = $this->get_items_per_page('edit_subscribes_per_page', 20);
            $current_page = $this->get_pagenum();
            
            // Slice data for current page
            $this->items = array_slice($items, (($current_page-1) * $per_page), $per_page);
            
            // Set pagination args
            $this->set_pagination_args([
                'total_items' => $total_items,
                'per_page' => $per_page,
                'total_pages' => ceil($total_items / $per_page)
            ]);

            // Add search box before the table
            echo '<form method="get">';
            echo '<input type="hidden" name="page" value="' . esc_attr($_REQUEST['page']) . '" />';
            $this->search_box('Search Subscribers', 'search_subscribers');
        }

        /**
         * Prepare items for table
         * @verify_subscribe
         *
         * @return void
         * @since 1.0.0
         */
        public function prepare_items_logs()
        {
            $query_arg = [];

            $columns = [
                'user' => __('User', 'kadence-child'),
                'status' => __('Email title', 'kadence-child'), // Changed from 'Status' to 'Email title'
                'msg' => __('Message', 'kadence-child'),
                'date' => __('Date', 'kadence-child'),
            ];

            // sets pagination arguments
            $per_page       = $this->get_items_per_page( 'edit_subscribes_per_page' );
            $current_page   = $this->get_pagenum();
            $total_items    = $this->count_logs( $query_arg );
            $logs     = $this->get_logs( array_merge(
                array(
                    'limit'   => $per_page,
                    'offset'  => ( ( $current_page - 1 ) * $per_page ),
                    'orderby' => isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : 'ID',
                    'order'   => isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'DESC',
                ),
                $query_arg
            ) );

            // sets columns headers
            $hidden                = $this->get_hidden_columns();
            $sortable              = $this->get_sortable_columns();
            $this->_column_headers = array( $columns, $hidden, $sortable );

            // retrieve data for table
            $this->items = $logs;

            // sets pagination args
            $this->set_pagination_args( array(
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil( $total_items / $per_page )
            ) );

            // Add search box before the table
            echo '<form method="get">';
            echo '<input type="hidden" name="page" value="' . esc_attr($_REQUEST['page']) . '" />';
            $this->search_box('Search Logs', 'search_logs');
        }

        /**
         * Display the search box
         * @param string $text The search button text
         * @param string $input_id The search input id
         */
        public function search_box($text, $input_id) {
            $search_value = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';
            ?>
            <p class="search-box">
                <label class="screen-reader-text" for="<?php echo esc_attr($input_id); ?>"><?php echo esc_html($text); ?>:</label>
                <input type="search" id="<?php echo esc_attr($input_id); ?>" name="s" value="<?php echo esc_attr($search_value); ?>" />
                <?php submit_button($text, '', '', false, array('id' => 'search-submit')); ?>
            </p>
            <?php
        }

    }

}
