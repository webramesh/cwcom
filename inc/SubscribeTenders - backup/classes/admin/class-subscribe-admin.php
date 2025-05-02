<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Tender_Subscribe_Admin' ) )
{

    class Tender_Subscribe_Admin
    {

        /**
         * @var
         */
        protected $config;
        /**
         * @var
         */
        protected $loader;

        public function __construct($config)
        {
            $this->config = $config;
            $this->load_class();

            add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
            add_action('admin_menu', array(&$this, 'create_page'));
        }

        /**
         * @return void
         */
        public function load_class()
        {
            if (!class_exists('WP_List_Table')) {
                require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
            }

            require_once get_stylesheet_directory() . '/inc/SubscribeTenders/classes/admin/class-subscribe-table.php';
            $this->loader['Tender_Subscribe_Admin_Table'] = new Tender_Subscribe_Admin_Table($this->config);

            /**
             * Admin Settings
             */
            require_once get_stylesheet_directory() . '/inc/SubscribeTenders/classes/admin/class-subscribe-settings.php';
            $this->loader['Tender_Subscribe_Admin_Settings'] = new Tender_Subscribe_Admin_Settings();

            /**
             * Admin Subscribes
             */
            require_once get_stylesheet_directory() . '/inc/SubscribeTenders/classes/admin/class-subscribe-subscribes.php';
            $this->loader['Tender_Subscribe_Admin_Subscribes'] = new Tender_Subscribe_Admin_Subscribes($this->config);

            /**
             * Admin Logs
             */
            require_once get_stylesheet_directory() . '/inc/SubscribeTenders/classes/admin/class-subscribe-logs.php';
            $this->loader['Tender_Subscribe_Admin_Logs'] = new Tender_Subscribe_Admin_Logs($this->config);

        }

        /**
         * @return void
         */
        public function admin_enqueue_scripts()
        {
            if (strpos(get_current_screen()->id, 'subscribe-admin') !== false) {
                wp_enqueue_style(
                    'subscribe-bootstrap',
                    'https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css'
                );
                wp_enqueue_style(
                    'subscribe-style',
                    get_stylesheet_directory_uri() . '/inc/SubscribeTenders/assets/admin/css/style.css'
                );
            }
        }

        /**
         * @return void
         */
        public function create_page()
        {
            global $admin_page_hooks;

            add_menu_page(
                __('Subscribe', 'kadence-child'),
                __('Subscribe', 'kadence-child'),
                'manage_options',
                'subscribe-admin',
                [$this->loader['Tender_Subscribe_Admin_Subscribes'], 'print_page'],
                'dashicons-email',
                3,
            );

            add_submenu_page(
                'subscribe-admin',
                __('Settings', 'kadence-child'),
                __('Settings', 'kadence-child'),
                'manage_options',
                'subscribe-admin-settings',
                [$this->loader['Tender_Subscribe_Admin_Settings'], 'print_settings_page'],
            );

            add_submenu_page(
                'subscribe-admin',
                __('Logs', 'kadence-child'),
                __('Logs', 'kadence-child'),
                'manage_options',
                'subscribe-admin-logs',
                [$this->loader['Tender_Subscribe_Admin_Logs'], 'print_page'],
            );

        }

    }

}
