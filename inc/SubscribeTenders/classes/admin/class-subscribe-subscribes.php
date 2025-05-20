<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Tender_Subscribe_Admin_Subscribes' ) )
{

    class Tender_Subscribe_Admin_Subscribes
    {

        /**
         * @var
         */
        protected $config;

        public function __construct($config)
        {
            $this->config = $config;
            
            // Add action to handle CSV export
            add_action('admin_init', array($this, 'export_subscribers_to_csv'));
        }

        /**
         * Export subscribers data to CSV
         */
        public function export_subscribers_to_csv() {
            if (isset($_GET['action']) && $_GET['action'] === 'export_csv' && isset($_GET['page']) && $_GET['page'] === 'subscribe-admin') {
                global $wpdb;

                // Check nonce for security
                if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'export_subscribers_csv')) {
                    wp_die('Security check failed');
                }
                
                // Run the same query as in get_subscribe() but without pagination
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
                    FROM {$this->config['prefix']}{$this->config['table']} sb
                    LEFT JOIN {$wpdb->users} u ON u.ID = sb.user_id
                    LEFT JOIN {$this->config['prefix']}{$this->config['table_tags']} et 
                        ON et.emailing_id = sb.id
                    LEFT JOIN {$wpdb->terms} t ON FIND_IN_SET(t.term_id, et.tag_id)
                    WHERE et.tag_id IS NOT NULL
                    GROUP BY sb.email
                    HAVING selections IS NOT NULL
                    ORDER BY sb.id DESC";
                
                $results = $wpdb->get_results($query, ARRAY_A);
                
                // Filter out any rows with empty selections
                $results = array_filter($results, function($row) {
                    return !empty($row['selections']);
                });
                
                // Set headers for CSV download
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="subscribers-export-' . date('Y-m-d') . '.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                // Create CSV output
                $output = fopen('php://output', 'w');
                
                // Add headers
                $headers = array(
                    'ID',
                    'User ID',
                    'Email',
                    'First Name',
                    'Date',
                    'User Email',
                    'Display Name',
                    'Selections'
                );
                
                fputcsv($output, $headers);
                
                // Process each data row
                foreach ($results as $row) {
                    // Format the selections field
                    $selections_data = $row['selections'];
                    
                    // Replace the || separator with something more CSV friendly
                    $selections_data = str_replace('||', ' | ', $selections_data);
                    
                    // Build the row for CSV
                    $csv_row = array(
                        $row['id'],
                        $row['user_id'],
                        $row['email'],
                        $row['first_name'],
                        $row['date'],
                        $row['user_email'],
                        $row['display_name'],
                        $selections_data
                    );
                    
                    fputcsv($output, $csv_row);
                }
                
                fclose($output);
                exit;
            }
        }

        /**
         * @return void
         */
        public function print_page()
        {
            // Create export CSV button URL with nonce
            $export_url = wp_nonce_url(
                add_query_arg(
                    array(
                        'action' => 'export_csv',
                        'page' => 'subscribe-admin'
                    ),
                    admin_url('admin.php')
                ),
                'export_subscribers_csv'
            );
            ?>
            <div class="wrap subscribe-wrap">
                <div id="wrap" class="subscribe__settings-content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <h1 class="wp-heading-inline"><?php echo get_admin_page_title(); ?></h1>
                                <!-- Add Export CSV Button -->
                                <a href="<?php echo esc_url($export_url); ?>" class="page-title-action">Export to CSV</a>
                                <div class="subscribe__table">
                                    <?php
                                    $table = new Tender_Subscribe_Admin_Table($this->config);
                                    $table->prepare_items_subscribe();
                                    $table->display();
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

    }

}
