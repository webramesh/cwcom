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

        }

        /**
         * @return void
         */
        public function print_page()
        {
            ?>
            <div class="wrap subscribe-wrap">
                <div id="wrap" class="subscribe__settings-content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <h1 class="wp-heading-inline"><?php echo get_admin_page_title(); ?></h1>
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
