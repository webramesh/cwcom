<?php
defined( 'ABSPATH' ) || exit;

class Tender_Subscribe_Subscribes
{

    /**
     * @var
     */
    protected $config;

    public function __construct($config)
    {

        $this->config = $config;

        //add_action('init', function () {
        $this->emailing_send();
        //}, 99);

    }

    /**
     * @return void
     */
    public function emailing_send()
    {

        $this->subscribes_tenders();
        $this->tender_tracking();

    }

    /**
     * @return void
     */
    protected function subscribes_tenders() {
        global $wpdb;
        $table = $this->config['prefix'].$this->config['table'];
        $table_tag = $this->config['prefix'].$this->config['table_tags'];
    
        // Get all users with subscriptions
        $users = $wpdb->get_results("SELECT DISTINCT id, email, first_name FROM {$table} WHERE 1=1");
    
        if ($users) {
            foreach ($users as $user) {
                // Get all tags for this user
                $query_tags = $wpdb->get_results($wpdb->prepare(
                    "SELECT * FROM {$table_tag} WHERE emailing_id=%d",
                    $user->id
                ));
    
                if ($query_tags) {
                    $all_matching_tenders = [];
                    $tags = $this->group_tags($query_tags);
    
                    // Collect all matching tenders for all tag groups
                    foreach ($tags as $tag) {
                        $tenders = $this->get_tenders($tag);
                        if ($tenders) {
                            $all_matching_tenders = array_merge($all_matching_tenders, $tenders);
                        }
                    }
    
                    // Remove duplicate tenders
                    $all_matching_tenders = array_unique($all_matching_tenders, SORT_REGULAR);
    
                    // If we found any matching tenders, send one consolidated email
                    if (!empty($all_matching_tenders)) {
                        $this->tender_tags_modified($user, $all_matching_tenders);
                    }
                }
            }
        }
    }

    /**
     * @param $tags
     * @return void
     */
    protected function group_tags($tags)
    {

        $items = [];

        foreach ( $tags as $tag )
        {
            $items[$tag->group_no][] = $tag;
        }

        return $items;

    }

    /**
     * @param $tag
     * @return void
     */
    protected function get_tenders($tag)
    {
        $result = [];

        $args = [
            'post_type' => 'tenders',
            'posts_per_page' => -1,
            'date_query' => [ // ADDED: Fetches tenders posted in the last 24 hours
                [
                    'after'     => '24 hours ago',
                    'inclusive' => true,
                ],
            ],
        ];

        $tax_query = [];
        foreach ( $tag as $child )
        {
            if( !empty($child->tag_id ))
            {
                $tax_query[] = [
                    'taxonomy' => $child->tag_taxonomy,
                    'field'    => 'id',
                    'terms'    => ($child->tag_taxonomy == $this->config['taxonomies']['region']) ? explode(',', $child->tag_id) : [$child->tag_id],
                ];
            }
        }

        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }

        /**
         * Get Tenders
         */
        $query_instance = new WP_Query();
        $fetched_tenders = $query_instance->query( $args ); // $fetched_tenders is an array of posts

        if ( $fetched_tenders )
        {
            // MODIFIED: Assign all fetched tenders directly, as WP_Query now handles date filtering.
            // The previous foreach loop with manual date checking has been removed.
            $result = $fetched_tenders;
        }

        return $result;
    }

    /**
     * @return void
     */
    protected function tender_tracking()
    {

        $arr = [
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => 'treaking_tender',
                    'compare' => 'EXISTS'
                ],
            ]
        ];
        $users = new WP_User_Query($arr);

        if( ! empty($users->results) )
        {

            foreach ( $users->results as $user )
            {

                $tenders = get_user_meta( $user->ID, 'treaking_tender' );

                if( $tenders )
                {

                    foreach ( $tenders as $tender_id )
                    {

                        $tender = get_post($tender_id);

                        if( $tender )
                        {

                            $curent_time = strtotime(current_time('Y-m-d H:i:s'));
                            $tender_start_date = get_post_meta($tender->ID, 'wpcf-tender-start-date', true);
                            $tender_end_date = get_post_meta($tender->ID, 'wpcf-tender-offer-deadline', true);
                            $date5 = strtotime("+5 day", $curent_time);
							$date6 = strtotime("+6 day", $curent_time);
							$date1 = strtotime("-1 day", $curent_time);

                            /**
                             * Send Mail Start Date
                             */
                            if( $tender_start_date <= $curent_time && $tender_start_date > $date1 && !empty($tender_start_date) )
                            {
                                    $this->tender_tracking_start_date($user, $tender, $tender_start_date);

                            }

                            /**
                             * Send Mail if Tender 5 days Dedline
                             */
                            $days = preg_replace("/[^0-9]/", '', human_time_diff($curent_time, $tender_end_date));
                            if(  $tender_end_date >= $date5 && $tender_end_date < $date6 && !empty($tender_end_date) )
                            {
                                $this->tender_tracking_end_date($user, $tender, $tender_end_date, $days);
                            }

                            /**
                             * if Tender Modified
                             */
                            $modified = strtotime(date('Y-m-d', strtotime($tender->post_modified)));

                            if($modified <= $curent_time &&  $modified > $date1)
                            {
                                $this->tender_tracking_modified($user, $tender);
                            }

                        }

                    }

                }

            }

        }

    }

    /**
     * @param $tender
     * @return false|string|WP_Error|WP_Term[]
     */
    protected function get_tender_products($tender)
    {

        $terms = get_the_term_list($tender, $this->config['taxonomies']['product']);

        return $terms;

    }

    /**
     * @param $tender
     * @return false|string|WP_Error|WP_Term[]
     */
    protected function get_tender_market($tender)
    {

        $terms = get_the_term_list($tender, $this->config['taxonomies']['market']);

        return $terms;

    }

    /**
     * @param $tender
     * @return false|string|WP_Error|WP_Term[]
     */
    protected function get_tender_regions($tender)
    {

        $terms = get_the_term_list($tender, $this->config['taxonomies']['region']);

        return $terms;

    }

    /**
     * @param $tender
     * @return false|string|WP_Error|WP_Term[]
     */
    protected function get_tender_countries($tender)
    {

        $terms = get_the_term_list($tender, $this->config['taxonomies']['country']);

        return $terms;

    }

    /**
     * @param $tender
     * @return mixed
     */
    protected function get_tender_price($tender)
    {

        $price = get_post_meta( $tender, 'wpcf-tender-wine-price', true);

        return $price;

    }

    /**
     * @param $tender
     * @return mixed
     */
    protected function get_tender_volume($tender)
    {

        $volume = get_post_meta( $tender, 'wpcf-tender-alcohol-volume', true);

        return $volume;

    }

    /**
     * @param $tender
     * @return mixed
     */
    protected function get_tender_classification($tender)
    {

        $classification = get_post_meta( $tender, 'wpcf-tender-region-classification', true);

        return $classification;

    }

    /**
     * @param $tender
     * @return mixed
     */
    protected function get_tender_referenc($tender)
    {

        $referenc = get_post_meta( $tender, 'wpcf-tender-reference-number', true);

        return $referenc;

    }

    /**
     * @param $user
     * @param $tender
     * @param $start_date
     * @return void
     */
    protected function tender_tracking_start_date($user, $tender, $start_date)
    {

        $market = $this->get_tender_market($tender->ID);
        $regions = $this->get_tender_regions($tender->ID);
		$countries = $this->get_tender_countries($tender->ID);
        $price = $this->get_tender_price($tender->ID);
        $volume = $this->get_tender_volume($tender->ID);
        $classification = $this->get_tender_classification($tender->ID);
        $referenc = $this->get_tender_referenc($tender->ID);
        //$products = $this->get_tender_products($tender->ID);

        $mail = new Tender_Subscribe_Mail($this->config);
        $mail->send([
            'to' => $user->user_email,
            'template' => 'subscribe_settings_template_three',
            'placeholders' => [
                'first_name' => $user->first_name,
                'tender_title' => $tender->post_title,
                'tender_referenc' => $referenc,
                'market' => $market,
				'country' => $countries,
                'price' => $price,
                'volume' => $volume,
                'classification' => $classification,
                'start_date' => wp_date( 'j M Y', $start_date ),
                'region' => $regions,
				'link_tender' => get_the_permalink($tender->ID),
                //'products' => $products,
            ],
            'subscribe' => [
                'user_id' => $user->ID,
                'tender_title' => $tender->post_title,
            ]
        ]);

    }

    /**
     * @param $user
     * @param $tender
     * @param $start_date
     * @param $days
     * @return void
     */
    protected function tender_tracking_end_date($user, $tender, $tender_end_date, $days = 0)
    {

        $market = $this->get_tender_market($tender->ID);
        $regions = $this->get_tender_regions($tender->ID);
		$countries = $this->get_tender_countries($tender->ID);
        $price = $this->get_tender_price($tender->ID);
        $volume = $this->get_tender_volume($tender->ID);
        $classification = $this->get_tender_classification($tender->ID);
        $referenc = $this->get_tender_referenc($tender->ID);
        //$products = $this->get_tender_products($tender->ID);

        $mail = new Tender_Subscribe_Mail($this->config);
        $mail->send([
            'to' => $user->user_email,
            'template' => 'subscribe_settings_template_two',
            'placeholders' => [
                'first_name' => $user->first_name,
                'tender_title' => $tender->post_title,
                'tender_referenc' => $referenc,
                'market' => $market,
				'country' => $countries,
                'price' => $price,
                'volume' => $volume,
                'classification' => $classification,
                'deadline_date' => wp_date( 'j M Y', $tender_end_date ),
                'days' => $days,
                'region' => $regions,
				'link_tender' => get_the_permalink($tender->ID),
                //'products' => $products,
            ],
            'subscribe' => [
                'user_id' => $user->ID,
                'tender_title' => $tender->post_title,
            ]
        ]);

    }

    /**
     * @param $user
     * @param $tender
     * @return void
     */
    protected function tender_tracking_modified($user, $tender)
    {

        $market = $this->get_tender_market($tender->ID);
        $regions = $this->get_tender_regions($tender->ID);
		$countries = $this->get_tender_countries($tender->ID);
        $price = $this->get_tender_price($tender->ID);
        $volume = $this->get_tender_volume($tender->ID);
        $classification = $this->get_tender_classification($tender->ID);
        $referenc = $this->get_tender_referenc($tender->ID);
        $products = $this->get_tender_products($tender->ID);

        $mail = new Tender_Subscribe_Mail($this->config);
        $mail->send([
            'to' => $user->user_email,
            'template' => 'subscribe_settings_template_four',
            'placeholders' => [
                'first_name' => $user->first_name,
                'tender_title' => $tender->post_title,
                'tender_referenc' => $referenc,
                'market' => $market,
				'country' => $countries,
                'price' => $price,
                'volume' => $volume,
                'classification' => $classification,
                'region' => $regions,
				'link_tender' => get_the_permalink($tender->ID),
                'products' => $products,
            ],
            'subscribe' => [
                'user_id' => $user->ID,
                'tender_title' => $tender->post_title,
            ]
        ]);

    }

    /**
     * @param $user
     * @param $tender
     * @return void
     */
    protected function tender_tags_modified($user, $tenders)
{
    $curent_time = strtotime(current_time('Y-m-d'));
    $mail = new Tender_Subscribe_Mail($this->config);
    
    // Prepare consolidated tender data
    $tender_data = [];
    foreach ($tenders as $tender) {
        $tender_data[] = [
            'title' => $tender->post_title,
            'referenc' => $this->get_tender_referenc($tender->ID),
            'market' => $this->get_tender_market($tender->ID),
            'countries' => $this->get_tender_countries($tender->ID),
            'price' => $this->get_tender_price($tender->ID),
            'volume' => $this->get_tender_volume($tender->ID),
            'classification' => $this->get_tender_classification($tender->ID),
            'link' => add_query_arg('access', '', get_permalink($tender->ID)),
            'days' => preg_replace("/[^0-9]/", '', human_time_diff($curent_time, get_post_meta($tender->ID, 'wpcf-tender-offer-deadline', true)))
        ];
    }
    
    // Build the tender rows HTML
    $tender_rows = '';
    foreach ($tender_data as $tender) {
        $tender_rows .= "
        <tr>
            <td colspan='2'><hr></td>
        </tr>
        <tr>
            <td>Tender Reference: </td>
            <td><strong>{$tender['referenc']}</strong></td>
        </tr>
        <tr>
            <td>Tender Title: </td>
            <td><strong>{$tender['title']}</strong></td>
        </tr>
        <tr>
            <td>Link: </td>
            <td><strong>{$tender['link']}</strong></td>
        </tr>";
    }

    // Get all tender titles for subject line
    $tender_titles = array_map(function($tender) {
        return $tender['title'];
    }, $tender_data);

    // Create a comma-separated list of tender titles
    $tender_titles_string = implode(', ', $tender_titles);
    
    $mail->send([
        'to' => $user->email,
        'template' => 'subscribe_settings_template_one',
        'placeholders' => [
            'first_name' => $user->first_name,
            'tender_rows' => $tender_rows,
            'tender_count' => count($tender_data),
        ],
        'subscribe' => [
            'user_id' => isset($user->ID) ? $user->ID : 0, // Added fallback
            'tender_title' => $tender_titles_string,
        ]
    ]);
}

}
