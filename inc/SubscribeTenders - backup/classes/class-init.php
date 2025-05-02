<?php
defined( 'ABSPATH' ) || exit;

class Tender_Subscribe_Init
{

    /**
     * @var string[]
     */
    protected $config = [
        'prefix' => 'h1p4m_',  // Use custom prefix for subscription tables
        'table' => 'emailing',
        'table_tags' => 'emailing_tags',
        'table_logs' => 'emailing_logs',
        'taxonomies' => [
            'product' => 'tender-products',
            'country' => 'tender-countries',
            'market' => 'tender-market',
            'region' => 'tender-regions',
        ],
        'form' => [
            'button' => [
                'button_remove_group' => 'Remove Group',
                'button_submit' => 'Add Subscription'
            ],
            'input' => [
                'label' => [
                    'product' => 'Product',
                    'country' => 'Country',
                    'markets' => 'Market',
                    'regions' => 'Region',
                ]
            ],
        ],
        'messages' => [
            'succes_subscribe' => 'You Have Successfully Subscribed to the Newsletter!',
            'empty_subscribes' => 'You Don\'t Have any Subscriptions Yet',
            'empty_selected' => 'No Options Selected',
        ]
    ];

    /**
     * @var
     */
    protected $loader;

    public function __construct()
    {
        // Do not override the prefix with WordPress prefix
        // Keep using the custom h1p4m_ prefix
        
        $this->load_class();
    }

    /**
     * @return void
     */
    public function load_class()
    {
        /**
         * Check and Create Tables
         */
        require_once get_stylesheet_directory() . '/inc/SubscribeTenders/classes/class-subscribe-tender.table.php';
        $this->loader = new Tender_Subscribe_Table($this->config);

        /**
         * Admin Panel
         */
        if ( is_admin() )
        {
            /**
             * Admin
             */
            require_once get_stylesheet_directory() . '/inc/SubscribeTenders/classes/admin/class-subscribe-admin.php';
            $this->loader = new Tender_Subscribe_Admin($this->config);
        }

        /**
         * Shortcodes
         */
        require_once get_stylesheet_directory() . '/inc/SubscribeTenders/classes/class-subscribe-tender.shortcodes.php';
        $this->loader = new Tender_Subscribe_Shortcodes($this->config);

        /**
         * Shortcodes Handler
         */
        require_once get_stylesheet_directory() . '/inc/SubscribeTenders/classes/class-subscribe-tender.handler.php';
        $this->loader = new Tender_Subscribe_Handler($this->config);

        /**
         * Mail
         */
        require_once get_stylesheet_directory() . '/inc/SubscribeTenders/classes/class-subscribe-tender.mail.php';

        /**
         * Schedule
         */
        require_once get_stylesheet_directory() . '/inc/SubscribeTenders/classes/class-subscribe-tender.schedule.php';
        $this->loader = new Tender_Subscribe_Schedule($this->config);
    }
}
