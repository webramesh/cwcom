<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Tender_Subscribe_Admin_Logs' ) )
{

    class Tender_Subscribe_Admin_Logs
    {

        /**
         * @var
         */
        protected $config;

        public function __construct($config)
        {

            $this->config = $config;

            add_action( 'admin_init', array( &$this, 'clear' ) );

        }

        /**
         * @return void
         */
        public function print_page()
        {
            $url = "?page=subscribe-admin-logs&logs=clear";
            $url = admin_url( "admin.php{$url}" );
            ?>
            <!-- wrap -->
            <div class="wrap subscribe-wrap">

                <!-- content -->
                <div id="wrap" class="subscribe__settings-content">

                    <!-- container -->
                    <div class="container-fluid">

                        <!-- row -->
                        <div class="row">

                            <!-- col -->
                            <div class="col-lg-12">

                                <!-- Title -->
                                <div class=" subscribe__settings-titlepage">
                                    <h1 class="wp-heading-inline subscribe__settings-titlepage">
                                        <?= get_admin_page_title() ?>
                                    </h1>

                                    <?php
                                    if( $this->count() > 0 )
                                    {
                                    ?>
                                        <a href="<?= $url ?>" class="button">
                                            <?php _e( 'Clear Logs', 'kadence-child' ); ?>
                                        </a>
                                    <?php } ?>

                                </div>
                                <!-- End Title -->

                                <!-- Table -->
                                <div class="subscribe__table">
                                    <?php
                                    $table = new Tender_Subscribe_Admin_Table($this->config);
                                    $table->prepare_items_logs();
                                    $table->display();
                                    ?>
                                </div>
                                <!-- End Table -->

                            </div>
                            <!-- end col -->

                        </div>
                        <!-- end row -->

                    </div>
                    <!-- end container -->

                </div>
                <!-- end content -->

            </div>
            <!-- end wrap -->

            <?php
        }

        /**
         * Clear Log
         * @return void
         */
        public function clear()
        {

            global $pagenow, $wpdb;;

            if ( ('admin.php' === $pagenow) && ($_GET['page'] == 'subscribe-admin-logs') && isset($_GET['logs']) && ($_GET['logs'] == 'clear') )
            {

                $table = $this->config['prefix'].$this->config['table_logs'];
                $delete = $wpdb->query("TRUNCATE TABLE $table");

            }

        }

        /**
         * @return string|null
         */
        protected function count()
        {
            global $wpdb;

            $table = $this->config['prefix'].$this->config['table_logs'];

            $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM {$table}");

            return $rowcount;

        }

    }

}
