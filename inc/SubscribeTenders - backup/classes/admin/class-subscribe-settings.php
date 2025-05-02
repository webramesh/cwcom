<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Tender_Subscribe_Admin_Settings' ) )
{

    class Tender_Subscribe_Admin_Settings
    {

        public function __construct()
        {

            add_action( 'admin_init', array( &$this, 'register_settings' ) );
            add_action( 'admin_init', array( &$this, 'add_fields' ) );

            add_action('update_option_subscribe_settings_general_period_time', array( &$this, 'callback_save_option_time' ), 10, 2);
            add_action('update_option_subscribe_settings_general_period', array( &$this, 'callback_save_option_period' ), 10, 2);

        }

        /**
         * @return void
         */
        public function register_settings()
        {

            register_setting(
                'subscribe_settings',
                'subscribe_settings_options',
                [
                    &$this,
                    'options_validate'
                ]
            );

        }

        /**
         * @param $input
         * @return mixed
         */
        public function options_validate( $input )
        {

            return $input;

        }

        /**
         * @return void
         */
        public function add_fields()
        {

            /**
             * Add Section Settings General
             */
            add_settings_section(
                'subscribe_settings_general',
                __( 'Settings', 'kadence-child' ),
                function () {},
                'subscribe_settings'
            );

            /**
             * Add Section Settings Template
             */
            add_settings_section(
                'subscribe_settings_template',
                __( 'Templates', 'kadence-child' ),
                function () {},
                'subscribe_settings'
            );

            /**
             * Register Period
             * @email_template
             */
            register_setting(
                'subscribe_settings_general',
                'subscribe_settings_general_period'
            );
            add_settings_field(
                'subscribe_settings_general_period',
                '',
                function ($val){
                    $option_name  = 'subscribe_settings_general_period';
                    ?>
                    <!-- Section -->
                    <div class="" style="margin-bottom: 15px">

                        <table>
                            <tbody>
                            <tr>
                                <td width="150">
                                    <label for="<?= $option_name ?>">
                                        <?php _e( 'Sending Period', 'kadence-child' ); ?>
                                    </label>
                                </td>
                                <td width="150">
                                    <!-- Input Group -->
                                    <div class="subscribe__settings-inputgroup">
                                        <select name="<?= $option_name ?>" id="<?= $option_name ?>" style="width: 100%">
                                            <option value="twicedaily" <?= (get_option($option_name) == 'twicedaily') ? 'selected' : '' ?> ><?php _e( 'Twice a Day', 'kadence-child' ); ?></option>
                                            <option value="daily" <?= (get_option($option_name) == 'daily') ? 'selected' : '' ?> ><?php _e( 'Daily', 'kadence-child' ); ?></option>
                                        </select>
                                    </div>
                                    <!-- End Input Group -->
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                    <!-- End Section -->
                    <?php
                },
                "subscribe_settings",
                "subscribe_settings_general",
                []
            );

            /**
             * Register Time
             * @email_template
             */
            register_setting(
                'subscribe_settings_general',
                'subscribe_settings_general_period_time'
            );
            add_settings_field(
                'subscribe_settings_general_period_time',
                '',
                function ($val){
                    $option_name  = 'subscribe_settings_general_period_time';
                    ?>
                    <!-- Section -->
                    <div class="">

                        <table>
                            <tbody>
                            <tr>
                                <td width="150">
                                    <label for="<?= $option_name ?>">
                                        <?php _e( 'Time', 'kadence-child' ); ?>
                                    </label>
                                </td>
                                <td width="150">
                                    <!-- Input Group -->
                                    <div class="subscribe__settings-inputgroup">
                                        <input type="text" name="<?= $option_name ?>" id="<?= $option_name ?>" value="<?= get_option($option_name) ?>">
                                    </div>
                                    <!-- End Input Group -->
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                    <!-- End Section -->
                    <?php
                },
                "subscribe_settings",
                "subscribe_settings_general",
                []
            );

            /*******************************************
             * Template and Subject
             * Для рассылки тендерв отобранным по тегам
             ******************************************/
            register_setting(
                'subscribe_settings_template',
                'subscribe_settings_template_one_subject'
            );
            add_settings_field(
                'subscribe_settings_template_one_subject',
                '',
                function ($val){
                    $option_name  = 'subscribe_settings_template_one_subject';
                    ?>

                    <!-- row -->
                    <div class="subscribe__settings-tabs--content---body-row">

                        <h3>
                            <?php _e( 'To Send Tenders Selected by Tags', 'kadence-child' ); ?>
                        </h3>

                        <label for="<?= $option_name ?>">
                            <?php _e( 'Subject', 'kadence-child' ); ?>
                        </label>

                        <!-- Input Group -->
                        <div class="subscribe__settings-inputgroup">
                            <input type="text" id="<?= $option_name ?>" name="<?= $option_name ?>" value="<?= get_option($option_name) ?>">
                        </div>
                        <!-- End Input Group -->

                    </div>
                    <!-- end row -->

                    <?php
                },
                "subscribe_settings",
                "subscribe_settings_template",
                []
            );

            register_setting(
                'subscribe_settings_template',
                'subscribe_settings_template_one'
            );
            add_settings_field(
                'subscribe_settings_template_one',
                '',
                function ($val){
                    $option_name  = 'subscribe_settings_template_one';
                    ?>

                    <!-- row -->
                    <div class="subscribe__settings-tabs--content---body-row">

                        <label for="<?= $option_name ?>">
                            <?php _e( 'Template', 'kadence-child' ); ?>
                        </label>

                        <!-- Input Group -->
                        <div class="subscribe__settings-inputgroup">
                            <textarea name="<?= $option_name ?>" id="<?= $option_name ?>" rows="15"><?= get_option($option_name) ?></textarea>
                        </div>
                        <!-- End Input Group -->

                    </div>
                    <!-- end row -->

                    <?php
                },
                "subscribe_settings",
                "subscribe_settings_template",
                []
            );

            /*******************************************
             * Template and Subject
             * Для рассылки напоминаний об окончании deadline date
             ******************************************/
            register_setting(
                'subscribe_settings_template',
                'subscribe_settings_template_two_subject'
            );
            add_settings_field(
                'subscribe_settings_template_two_subject',
                '',
                function ($val){
                    $option_name  = 'subscribe_settings_template_two_subject';
                    ?>

                    <!-- row -->
                    <div class="subscribe__settings-tabs--content---body-row">

                        <h3>
                            <?php _e( 'To Send Reminders About the End of the Deadline Date', 'kadence-child' ); ?>
                        </h3>

                        <label for="<?= $option_name ?>">
                            <?php _e( 'Subject', 'kadence-child' ); ?>
                        </label>

                        <!-- Input Group -->
                        <div class="subscribe__settings-inputgroup">
                            <input type="text" id="<?= $option_name ?>" name="<?= $option_name ?>" value="<?= get_option($option_name) ?>">
                        </div>
                        <!-- End Input Group -->

                    </div>
                    <!-- end row -->

                    <?php
                },
                "subscribe_settings",
                "subscribe_settings_template",
                []
            );

            register_setting(
                'subscribe_settings_template',
                'subscribe_settings_template_two'
            );
            add_settings_field(
                'subscribe_settings_template_two',
                '',
                function ($val){
                    $option_name  = 'subscribe_settings_template_two';
                    ?>

                    <!-- row -->
                    <div class="subscribe__settings-tabs--content---body-row">

                        <label for="<?= $option_name ?>">
                            <?php _e( 'Template', 'kadence-child' ); ?>
                        </label>

                        <!-- Input Group -->
                        <div class="subscribe__settings-inputgroup">
                            <textarea
                                    name="<?= $option_name ?>"
                                    id="<?= $option_name ?>"
                                    rows="15"
                            ><?= get_option($option_name) ?></textarea>
                        </div>
                        <!-- End Input Group -->

                    </div>
                    <!-- end row -->

                    <?php
                },
                "subscribe_settings",
                "subscribe_settings_template",
                []
            );

            /*******************************************
             * Template and Subject
             * Для рассылки напоминаний об скором наступлении start date
             ******************************************/
            register_setting(
                'subscribe_settings_template',
                'subscribe_settings_template_three_subject'
            );
            add_settings_field(
                'subscribe_settings_template_three_subject',
                '',
                function ($val){
                    $option_name  = 'subscribe_settings_template_three_subject';
                    ?>

                    <!-- row -->
                    <div class="subscribe__settings-tabs--content---body-row">

                        <h3>
                            <?php _e( 'To Send Reminders About the Imminent Start Date', 'kadence-child' ); ?>
                        </h3>

                        <label for="<?= $option_name ?>">
                            <?php _e( 'Subject', 'kadence-child' ); ?>
                        </label>

                        <!-- Input Group -->
                        <div class="subscribe__settings-inputgroup">
                            <input type="text" id="<?= $option_name ?>" name="<?= $option_name ?>" value="<?= get_option($option_name) ?>">
                        </div>
                        <!-- End Input Group -->

                    </div>
                    <!-- end row -->

                    <?php
                },
                "subscribe_settings",
                "subscribe_settings_template",
                []
            );

            register_setting(
                'subscribe_settings_template',
                'subscribe_settings_template_three'
            );
            add_settings_field(
                'subscribe_settings_template_three',
                '',
                function ($val){
                    $option_name  = 'subscribe_settings_template_three';
                    ?>

                    <!-- row -->
                    <div class="subscribe__settings-tabs--content---body-row">

                        <label for="<?= $option_name ?>">
                            <?php _e( 'Template', 'kadence-child' ); ?>
                        </label>

                        <!-- Input Group -->
                        <div class="subscribe__settings-inputgroup">
                            <textarea name="<?= $option_name ?>" id="<?= $option_name ?>" rows="15"><?= get_option($option_name) ?></textarea>
                        </div>
                        <!-- End Input Group -->

                    </div>
                    <!-- end row -->

                    <?php
                },
                "subscribe_settings",
                "subscribe_settings_template",
                []
            );

            /*******************************************
             * Template and Subject
             * Для рассылки, извещающей об изменениях(модификации информации) для отслеживаемых(помеченных звездочкой) тендерах
             ******************************************/
            register_setting(
                'subscribe_settings_template',
                'subscribe_settings_template_four_subject'
            );
            add_settings_field(
                'subscribe_settings_template_four_subject',
                '',
                function ($val){
                    $option_name  = 'subscribe_settings_template_four_subject';
                    ?>

                    <!-- row -->
                    <div class="subscribe__settings-tabs--content---body-row">

                        <h3>
                            <?php _e( 'For a Mailing List Announcing Changes to Monitored Tenders', 'kadence-child' ); ?>
                        </h3>

                        <label for="<?= $option_name ?>">
                            <?php _e( 'Subject', 'kadence-child' ); ?>
                        </label>

                        <!-- Input Group -->
                        <div class="subscribe__settings-inputgroup">
                            <input type="text" id="<?= $option_name ?>" name="<?= $option_name ?>" value="<?= get_option($option_name) ?>">
                        </div>
                        <!-- End Input Group -->

                    </div>
                    <!-- end row -->

                    <?php
                },
                "subscribe_settings",
                "subscribe_settings_template",
                []
            );

            register_setting(
                'subscribe_settings_template',
                'subscribe_settings_template_four'
            );
            add_settings_field(
                'subscribe_settings_template_four',
                '',
                function ($val){
                    $option_name  = 'subscribe_settings_template_four';
                    ?>

                    <!-- row -->
                    <div class="subscribe__settings-tabs--content---body-row">

                        <label for="<?= $option_name ?>">
                            <?php _e( 'Template', 'kadence-child' ); ?>
                        </label>

                        <!-- Input Group -->
                        <div class="subscribe__settings-inputgroup">
                            <textarea name="<?= $option_name ?>" id="<?= $option_name ?>" rows="15"><?= get_option($option_name) ?></textarea>
                        </div>
                        <!-- End Input Group -->

                    </div>
                    <!-- end row -->

                    <?php
                },
                "subscribe_settings",
                "subscribe_settings_template",
                []
            );

        }

        /**
         * @return void
         */
        public function message()
        {

            $message = [
                'element_exists'   => $this->get_message( '<strong>' . __( 'The element you have entered already exists.', 'kadence-child' ) . '</strong>', 'error', false ),
                'saved'            => $this->get_message( '<strong>' . __( 'Settings saved', 'kadence-child' ) . '.</strong>', 'updated', false ),
                'reset'            => $this->get_message( '<strong>' . __( 'Settings reset', 'kadence-child' ) . '.</strong>', 'updated', false ),
                'delete'           => $this->get_message( '<strong>' . __( 'Element deleted correctly.', 'kadence-child' ) . '</strong>', 'updated', false ),
                'updated'          => $this->get_message( '<strong>' . __( 'Element updated correctly.', 'kadence-child' ) . '</strong>', 'updated', false ),
                'settings-updated' => $this->get_message( '<strong>' . __( 'Element updated correctly.', 'kadence-child' ) . '</strong>', 'updated', false ),
                'imported'         => $this->get_message( '<strong>' . __( 'Database imported correctly.', 'kadence-child' ) . '</strong>', 'updated', false ),
                'no-imported'      => $this->get_message( '<strong>' . __( 'An error has occurred during import. Please try again.', 'kadence-child' ) . '</strong>', 'error', false ),
                'file-not-valid'   => $this->get_message( '<strong>' . __( 'The added file is not valid.', 'kadence-child' ) . '</strong>', 'error', false ),
                'cant-import'      => $this->get_message( '<strong>' . __( 'Sorry, import is disabled.', 'kadence-child' ) . '</strong>', 'error', false ),
                'ord'              => $this->get_message( '<strong>' . __( 'Sorting successful.', 'kadence-child' ) . '</strong>', 'updated', false ),
            ];

            foreach ( $message as $key => $value ) :
                if ( isset( $_GET[ $key ] ) ) :
                    echo $message[ $key ];
                endif;
            endforeach;

        }

        /**
         * @param $message
         * @param $type
         * @param $echo
         * @return string
         */
        public function get_message( $message, $type = 'error', $echo = true )
        {
            $message = '<div id="message" class="' . $type . ' fade"><p>' . $message . '</p></div>';
            if ( $echo ) :
                echo $message;
            endif;
            return $message;
        }

        /**
         * @return void
         */
        public function print_settings_page()
        {
            ?>
            <!-- wrap -->
            <div class="wrap subscribe-wrap settings">

                <!-- content -->
                <div id="wrap" class="subscribe__settings-content">

                    <!-- container -->
                    <div class="container-fluid">

                        <!-- row -->
                        <div class="row">

                            <!-- col -->
                            <div class="col-lg-12">

                                <!-- Title -->
                                <div>
                                    <h1 class="wp-heading-inline subscribe__settings-titlepage">
                                        <?= get_admin_page_title() ?>
                                    </h1>
                                </div>
                                <!-- End Title -->

                                <?php $this->message(); ?>

                                <!-- Content -->
                                <div class="subscribe__settings-tabs">

                                    <!-- Tabs List -->
                                    <ul class="subscribe__settings-tabs--list subscribeAdminTab">
                                        <li>
                                            <a href="#subscribe__settingsGeneral" class="nav-tab nav-tab-active subscribe__settings-tabsTb">
                                                General
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#subscribe__settingsTemplates" class="nav-tab subscribe__settings-tabsTb">
                                                Templates
                                            </a>
                                        </li>
                                    </ul>
                                    <script>
                                    jQuery(document).ready(function($) {
                                        $('.subscribe__settings-tabsTb').click(function(e) {
                                            e.preventDefault();
                                            $('.subscribe__settings-tabsTb').removeClass('nav-tab-active');
                                            $(this).addClass('nav-tab-active');
                                            $('.subscribe__settings-tabs--content---body').removeClass('active');
                                            $($(this).attr('href')).addClass('active');
                                        });
                                    });
                                    </script>
                                    <!-- End Tabs List -->

                                    <div class="clear"></div>

                                    <!-- Tabs Content -->
                                    <div class="subscribe__settings-tabs--content subscribeAdminTabs">

                                        <!-- Tabs Content > General -->
                                        <div id="subscribe__settingsGeneral" class="active subscribe__settings-tabs--content---body">

                                            <form method="post" action="options.php">
                                                <?php settings_fields( 'subscribe_settings_general' ); ?>
                                                <?php do_settings_fields( 'subscribe_settings', 'subscribe_settings_general' ); ?>
                                                <?php submit_button(); ?>
                                            </form>

                                        </div>
                                        <!-- End Tabs Content > General -->

                                        <!-- Tabs Content > Mail -->
                                        <div id="subscribe__settingsTemplates" class="subscribe__settings-tabs--content---body">

                                            <form method="post" action="options.php">
                                                <?php settings_fields( 'subscribe_settings_template' ); ?>
                                                <?php do_settings_fields( 'subscribe_settings', 'subscribe_settings_template' ); ?>
                                                <?php submit_button(); ?>
                                            </form>

                                        </div>
                                        <!-- End Tabs Content > Mail -->

                                    </div>
                                    <!-- End Tabs Content -->

                                </div>
                                <!-- End Content -->

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
         * @param $old_value
         * @param $value
         * @return void
         */
        public function callback_save_option_time($old_value, $value)
        {

            wp_clear_scheduled_hook( 'subscribe_emailing' );
            $period = get_option('subscribe_settings_general_period');

            $value = date_i18n('H:i', strtotime($value));

            wp_schedule_event( strtotime("{$value}"), "{$period}", 'subscribe_emailing' );

        }

        /**
         * @param $old_value
         * @param $value
         * @return void
         */
        public function callback_save_option_period($old_value, $value)
        {

            wp_clear_scheduled_hook( 'subscribe_emailing' );
            $time = get_option('subscribe_settings_general_period_time');

            $time = date_i18n('H:i', strtotime($time));

            wp_schedule_event( strtotime("{$time}"), "{$value}", 'subscribe_emailing' );

        }

    }

}
