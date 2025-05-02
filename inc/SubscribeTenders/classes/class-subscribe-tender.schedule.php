<?php
defined( 'ABSPATH' ) || exit;

class Tender_Subscribe_Schedule
{

    /**
     * @var
     */
    protected $config;

    public function __construct($config)
    {

        $this->config = $config;

        add_action('init', function (){
            $this->subscribe_schdelue();
        }, 99);

    }

    /**
     * @return void
     */
    public function subscribe_schdelue()
    {

        add_action( 'subscribe_emailing', array( $this, 'init_subscribes' ) );

        if ( ! wp_next_scheduled( 'subscribe_emailing' ) )
        {
            $period = get_option('subscribe_settings_general_period');
            $time = get_option('subscribe_settings_general_period_time');
            $time = !empty($time) ? $time : '22:30:00';
            $time = date_i18n('H:i', strtotime($time));

            wp_schedule_event( strtotime("{$time}"), "{$period}", 'subscribe_emailing' );
        }

    }

    /**
     * @return void
     */
    public function init_subscribes()
    {
        require_once get_stylesheet_directory() . '/inc/SubscribeTenders/classes/class-subscribe-tender.subscribes.php';
        $sender = new Tender_Subscribe_Subscribes($this->config);
    }

}
