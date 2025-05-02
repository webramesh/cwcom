<?php
defined( 'ABSPATH' ) || exit;

class Tender_Subscribe_Shortcodes
{

    /**
     * @var
     */
    protected $config;

    public function __construct($config)
    {

        $this->config = $config;
        /**
         * Ajax Init
         */
        $this->ajax_init();

        /**
         * Scripts
         */
        add_action( 'wp_enqueue_scripts', array( &$this, 'register_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );

        /**
         * Set Shortcode
         */
        $this->set_shortcode();

    }

    /**
     * @return void
     */
    public function register_scripts()
    {

        wp_register_style( 'subscribe-tender-multiselect', get_stylesheet_directory_uri() . '/inc/SubscribeTenders/assets/js/multiselect/jquery.multiselect.css', [] );
        wp_register_style( 'subscribe-tender-bundle', get_stylesheet_directory_uri() . '/inc/SubscribeTenders/assets/css/style.css', [] );

        wp_register_script( 'subscribe-tender-multiselect', get_stylesheet_directory_uri() . '/inc/SubscribeTenders/assets/js/multiselect/jquery.multiselect.js', ['jquery']);
        wp_register_script( 'subscribe-tender-bundle', get_stylesheet_directory_uri() . '/inc/SubscribeTenders/assets/js/script.js', ['jquery']);
        wp_register_script( 'subscribe-tender-shortcodes', get_stylesheet_directory_uri() . '/inc/SubscribeTenders/assets/js/shortcodes.js', ['jquery']);

    }

    /**
     * @return void
     */
    public function enqueue_scripts()
    {

        wp_enqueue_style('subscribe-tender-multiselect');
        wp_enqueue_style('subscribe-tender-bundle');

        wp_enqueue_script('subscribe-tender-multiselect');
        wp_enqueue_script('subscribe-tender-bundle');
        wp_enqueue_script('subscribe-tender-shortcodes');

        wp_localize_script( 'jquery', 'subscribe_tender',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
            )
        );

    }

    /**
     * Set Shortcode
     * @return void
     */
    private function set_shortcode()
    {

        add_shortcode( 'subscribe-tender', [ $this, 'subscribe_tender_form_shortcode' ] );
        add_shortcode( 'treaking-tender', [ $this, 'treaking_tender_form_shortcode' ] );

    }

    /**
     * @return void
     */
    public function ajax_init()
    {

        /**
         * Select Tender Region
         */
        add_action( 'wp_ajax_select_tender_region', array( &$this, 'select_tender_region' ) );
        add_action( 'wp_ajax_nopriv_select_tender_region', array( &$this, 'select_tender_region' ) );

        /**
         * Get Subscribes by Email
         */
        add_action( 'wp_ajax_subscribes_by_email', array( &$this, 'subscribes_by_email' ) );
        add_action( 'wp_ajax_nopriv_subscribes_by_email', array( &$this, 'subscribes_by_email' ) );

    }

    /**
     * Render Shortcode Treaking Tender Form
     * @param $atts
     * @param $content
     * @return void
     */
    function treaking_tender_form_shortcode($atts, $content)
    {

        extract( shortcode_atts( [
            'id' => '',
            'title' => 'Update Treaking',
            'width' => '100%',
        ], $atts ) );

        if( empty($id) || !is_user_logged_in() ) return false;

        $meta = get_user_meta( get_current_user_id(), 'treaking_tender' );
        $checked = in_array($id, $meta) ? 'checked="checked"' : '';

        /**
         * Html
         */
        $html = '<div class="treaking-tender-form">';
        $html .= !empty($title) ? '<h2 class="treaking-tender-form__title">' . __( $title, 'kadence-child' ) . '</h2>' : '';
        $html .= '<form method="post" class="treakingTenderForm">';

        /**
         * CheckBox
         */
        $html .= '<div class="treaking-form__group checkbox">';
        $html .= '<label data-tippy-content="Mark to follow">&#9733;<input name="treaking_tender_status" type="checkbox" id="treaking-form-tender" value="1" '. $checked .' class="treaking-form__group__checkbox" /></label>';
        $html .= '<input name="treaking_tender" type="hidden" value="' . $id . '" />';
        $html .= '</div>';

        $html .= '<input name="action" type="hidden" value="save_treaking_form" />';
        $html .= '<div class="subscribe-form__message message"></div>';
        $html .= '</form>';
        $html .= '</div>';
		
		su_query_asset('js', array('popperjs', 'tippy'));


        return $html;

    }

    /**
     * Render Shortcode Subscribe Form
     * @param $atts
     * @param $content
     * @return string
     */
    function subscribe_tender_form_shortcode( $atts, $content )
    {

        extract( shortcode_atts( [
            'title' => '',
            'subtitle' => '',
            'width' => '100%',
        ], $atts ) );

        /**
         * Html
         */
        $html = '<div class="subscribe-form" id="subscribe_tender_main">';
        $html .= !empty($title) ? '<h2 class="subscribe-form__title">' . __( $title, 'kadence-child' ) . '</h2>' : '';
        $html .= '<form method="post" class="subscribeTenderForm">';

        if( ! is_user_logged_in() )
        {

            $html .= '<div class="subscribe-form__register_group">';

            /**
             * First Name
             */
            $html .= '<div class="subscribe-form__group">';
            $html .= '<label for="subscribe-form-input-firstname">' . __( 'First Name:', 'kadence-child' ) . '</label>';
            $html .= '<input name="first_name" type="text" id="subscribe-form-input-firstname" value="" class="subscribe-form__group__input" required pattern="[a-zA-Z]{3,20}" />';
            $html .= '</div>';

            /**
             * Email
             */
            $html .= '<div class="subscribe-form__group">';
            $html .= '<label for="subscribe-form-input-email">' . __( 'E-mail:', 'kadence-child' ) . '</label>';
            $html .= '<input name="email" type="email" id="subscribe-form-input-email" value="" class="subscribe-form__group__input subscribeTenderFormEmail" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" />';
            $html .= '</div>';

            $html .= '</div>';

        }

        /****************************************************
         * Group Line
         ***************************************************/
        $html .= '<div class="groupRow">';

        /**
         * Tender Product
         */
        $tenders = $this->get_tender_products();

        $html .= $this->html_select([
            'id' => 'product',
            'terms' => $tenders,
            'select_class' => 'subscribeTenderProduct subscribeTenderGetRegion',
            'multiselect' => false
        ]);

        /**
         * Countries
         */
        $countries = $this->get_tender_countries();

        $html .= $this->html_select([
            'id' => 'country',
            'terms' => $countries,
            'select_class' => 'subscribeTenderCountry subscribeTenderGetRegion',
            'multiselect' => false
        ]);

        /**
         * Regions
         */
        $html .= '<div class="ajaxTenderRegions mr-0 ml-0"></div>';

        /**
         * Markets
         */
        $markets = $this->get_tender_markets();

        $html .= $this->html_select([
            'id' => 'markets',
            'terms' => $markets,
            'select_class' => '',
            'multiselect' => false
        ]);

        $html .= '</div>';
        /****************************************************
         * End Group Line
         ***************************************************/

        /**
         * Submit
         */
        $html .= '<div class="subscribe-form__group">';
        $html .= '<button type="submit">' . __( $this->config['form']['button']['button_submit'], 'kadence-child' ) . '</button>';
        $html .= '</div>';

        $html .= '<input name="action" type="hidden" value="save_subscribe_form" />';
        $html .= '<div class="subscribe-form__message message"></div>';
        $html .= '</form>';

        /************************
         * Subscribes User List
         ***********************/
        $html .= '<div class="subscribe-form__subscribes">';
        $html .= !empty($subtitle) ? '<h2 class="subscribe-form__title">' . __( $subtitle, 'kadence-child' ) . '</h2>' : '';
        $html .= '<div class="ajaxSubscribesUser">';
        $html .= $this->get_html_subscribes_user();
        $html .= '</div>';
        $html .= '</div>';
        /************************
         * End Subscribes User List
         ***********************/

        $html .= '</div>';

        return $html;

    }

    /**
     * Tender Products
     * @return WP_Term_Query
     */
    private function get_tender_products($parent = 0)
    {

        $options = get_theme_mod('filter-products');

        $args = [
            'taxonomy' => $this->config['taxonomies']['product'],
            'get' => 'all',
            'include' => $options,
            'hide_empty' => false,
            'fields' => 'all',
            'parent' => $parent,
            'hierarchical' => false,
        ];

        $terms = new WP_Term_Query($args);

        return $terms;

    }

    /**
     * Tender Countries
     * @return WP_Term_Query
     */
    private function get_tender_countries($parent = 0)
    {

        $options = get_theme_mod('filter-countries');

        $args = [
            'taxonomy' => $this->config['taxonomies']['country'],
            'get' => 'all',
            'hide_empty' => false,
            'parent' => $parent,
            'include' => $options,
        ];

        $terms = new WP_Term_Query($args);

        return $terms;

    }

    /**
     * Tender Regions
     * @return WP_Term_Query
     */
    private function get_tender_regions($regions = [], $parent = 0)
    {

        $args = [
            'taxonomy' => $this->config['taxonomies']['region'],
            'get' => 'all',
            'hide_empty' => false,
            'include' => $regions,
            'parent' => $parent
        ];

        $terms = new WP_Term_Query($args);

        return $terms;

    }

    /**
     * Tender Markets
     * @return WP_Term_Query
     */
    private function get_tender_markets($parent = 0)
    {

        $args = [
            'taxonomy' => $this->config['taxonomies']['market'],
            'get' => 'all',
            'hide_empty' => false,
            'parent' => $parent
        ];

        $terms = new WP_Term_Query($args);

        return $terms;

    }

    /**
     * Get Select Tender Region
     * @return void
     */
    public function select_tender_region()
    {

        $html = null;
        $product = absint($_POST['product']);
        $country = absint($_POST['country']);

        if( $product === 0 || $country === 0 ) wp_send_json_error();

        /**
         * Get Term Product
         */
        $wine = get_term_by( 'id', $product, $this->config['taxonomies']['product'] );

        if( !$wine ) wp_send_json_error();

        if( stristr($wine->slug, 'wine') === FALSE ) wp_send_json_error();

        /**
         * Regions by Country
         */
        $regions = get_term_meta( $country, '_term_tender_regions', true );

        if( !$regions ) wp_send_json_error();

        $regions = $this->get_tender_regions($regions);

        $html .= $this->html_select([
            'id' => 'regions',
            'terms' => $regions,
            'select_class' => '',
            'multiselect' => true
        ]);

        if ( is_wp_error( $regions ) )
        {
            wp_send_json_error();
        }

        wp_send_json_success([
            'html' => $html
        ]);

    }

    /**
     * Get Subscribe for User
     * @param $email
     * @return string|void
     */
    protected function get_user_active_subscribe($email)
    {

        global $wpdb;

        $result = [];
        $table = $this->config['prefix'].$this->config['table'];
        $table_tag = $this->config['prefix'].$this->config['table_tags'];

        $query = "SELECT 
                   t1.id,
                   t1.emailing_id,
                   t1.group_no,
                   t1.tag_taxonomy,
                   t1.tag_id,
                   t2.email 
                  FROM {$table_tag} AS t1
                  INNER JOIN {$table} AS t2 ON t2.id = t1.emailing_id
                  WHERE t2.email='{$email}'";

        $query = $wpdb->get_results( $wpdb->prepare($query) );

        if( $query )
        {

            foreach ( $query as $key => $item )
            {
                $result[$item->group_no][] = $query[$key];
            }

        }

        return $result;

    }

    /**
     * Generate Html Select
     * @param $args
     * @return string|null
     */
    protected function html_select($args = [])
    {

        $html = null;

        if( $args['terms']->terms )
        {

            $multiselect = $args['multiselect'] ? 'multiple' : '';
            $html .= '<div class="subscribe-form__group">';
            $html .= '<label for="subscribe-form-input-' . $args['id'] . '">' . __( $this->config['form']['input']['label'][$args['id']], 'kadence-child' ) . '</label>';
            $html .= '<select name="options[' . $args['id'] . '][]" id="subscribe-form-input-' . $args['id'] . '" class="' . $args['select_class'] . '" ' . $multiselect . '>';

            if( !$multiselect )
            {
                $html .= '<option value="none"></option>';
                $html .= '<option value="0">' . __( 'All', 'kadence-child' ) . '</option>';
            }

            foreach ( $args['terms']->terms as $item )
            {

                $html .= '<option value="' . $item->term_id . '">' . $item->name . '</option>';

                if( 'product' === $args['id'] )
                {

                    $children = $this->get_tender_products($item->term_id);

//                    if( $children )
//                    {
//
//                        foreach ( $children->terms as $child )
//                        {
//
//                            $html .= '<option value="' . $child->term_id . '">- ' . $child->name . '</option>';
//
//                        }
//
//                    }

                }

            }

            $html .= '</select>';
            $html .= '</div>';

        }

        return $html;

    }

    /**
     * Get Html Subscribes by User
     * @param $user_email
     * @return string|null
     */
    public function get_html_subscribes_user($user_email = null)
    {

        $html = null;
        $message_error = '<p class="subscribe-form__subscribes-excerpt">' . __( $this->config['messages']['empty_subscribes'], 'kadence-child' ) . '</p>';

        if( is_user_logged_in() )
        {

            $current_user = wp_get_current_user();
            $user_email = $current_user->user_email;

        }

        if( empty($user_email) ) return $message_error;

        $subscribes = $this->get_user_active_subscribe($user_email);

        /**
         * is Check Subscribes
         */
        if( $subscribes )
        {

            /****************************************************
             * Active User Subscribes
             ***************************************************/
            foreach ( $subscribes as $subscribe )
            {

                $ids = [];
                $html .= '<div class="groupRow">';

                foreach ( $subscribe as $item )
                {

                    $key = null;
                    $region_arr = false;

                    switch ($item->tag_taxonomy) {
                        case 'tender-countries':
                            $key = 'country';
                            break;
                        case 'tender-market':
                            $key = 'markets';
                            break;
                        case 'tender-products':
                            $key = 'product';
                            break;
                        case 'tender-regions':
                            $key = 'regions';
                            $region_arr = true;
                            break;
                    }

                    if( $region_arr )
                    {

                        $term = get_terms( array(
                            'hide_empty'  => 0,
                            'taxonomy'    => $item->tag_taxonomy,
                            'include' => explode(',', $item->tag_id),
                        ) );

                        if( $term )
                        {
                            $labels = [];
                            foreach ( $term as $value )
                            {
                                $labels[] = $value->name;
                            }
                            $label = implode(', ', $labels);
                        }

                    }else{
                        $term = get_term_by( 'id', $item->tag_id, $item->tag_taxonomy );
                        $label = __( 'All', 'kadence-child' );
                        if( $term )
                        {
                            $label = $term->name;
                        }
                    }

                    $html .= '<div class="subscribe-form__group">';
                    $html .= '<label for="subscribe-form-input-' . $item->tag_taxonomy . '">' . __( $this->config['form']['input']['label'][$key], 'kadence-child' ) . '</label>';
                    $html .= '<div class="subscribe-form__group-value">' . $label . '</div>';
                    $html .= '</div>';

                    array_push($ids, $item->id);
                }


                $html .= '<button type="button" class="subscribe-form__remove_line__button subscribeTenderRemoveGroup" data-tippy-content="Delete subscription filter" data-rows="' . implode(',', $ids) . '">' . __( $this->config['form']['button']['button_remove_group'], 'kadence-child' ) . '</button>';

                $html .= '</div>';

            }

            /****************************************************
             * End Active User Subscribes
             ***************************************************/

        }else{

            $html = $message_error;

        }

        return $html;

    }

    /**
     * Get User Subscribes by Email
     * @return void
     */
    public function subscribes_by_email()
    {

        $email = $_POST['email'];

        if( !$email ) wp_send_json_error();

        $html = $this->get_html_subscribes_user($email);

        wp_send_json_success([
            'html' => $html
        ]);

    }

}
