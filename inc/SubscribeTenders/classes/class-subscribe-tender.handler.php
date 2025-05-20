<?php
defined( 'ABSPATH' ) || exit;

class Tender_Subscribe_Handler
{

    /**
     * @var
     */
    protected $config = [
        'prefix' => 'h1p4m_',
        'table' => 'emailing',
        'table_tags' => 'emailing_tags', 
        'table_logs' => 'emailing_logs'
    ];

    public function __construct($config)
    {

        $this->config = $config;

        /**
         * Ajax Init
         */
        add_action( 'init', array( &$this, 'ajax_init' ) );

        /**
         * After User Registration
         */
        add_action( 'user_register', array( &$this, 'user_register_callback' ), 10, 2 );

        /**
         * After User Delete
         */
        add_action( 'delete_user', array( &$this, 'user_delete_callback' ), 10, 3 );

    }

    /**
     * @return void
     */
    public function ajax_init()
    {

        /**
         * Save Data Form Subscribe
         */
        add_action( 'wp_ajax_save_subscribe_form', array( &$this, 'save_subscribe_form' ) );
        add_action( 'wp_ajax_nopriv_save_subscribe_form', array( &$this, 'save_subscribe_form' ) );

        /**
         * Remove Group Subscribe
         */
        add_action( 'wp_ajax_remove_subscribe_group', array( &$this, 'remove_subscribe_group' ) );
        add_action( 'wp_ajax_nopriv_remove_subscribe_group', array( &$this, 'remove_subscribe_group' ) );

        /**
         * Save Data Form Treaking Tender
         */
        add_action( 'wp_ajax_save_treaking_form', array( &$this, 'save_treaking_form' ) );
        add_action( 'wp_ajax_nopriv_save_treaking_form', array( &$this, 'save_treaking_form' ) );

    }

    /**
     * Remove Subscribe Group
     * @return void
     */
    public function remove_subscribe_group()
    {

        $ids = $_POST['rows'];
        $arr_ids = explode(',', $_POST['rows']);

        if( !$arr_ids ) wp_send_json_error();

        $emailing = $this->get_emailing_by_tag_id($arr_ids[0]);

        if( !$emailing ) wp_send_json_error();

        $delete = $this->delete($ids);

        if( true === $delete['status'] )
        {

            wp_send_json_success([
                'html' => (new Tender_Subscribe_Shortcodes($this->config))->get_html_subscribes_user($emailing->email)
            ]);

        }else{

            wp_send_json_error([
                'message' => $delete['message']
            ]);

        }

    }

    /**
     * Get Emailing by Id Tag
     * @param $id
     * @return array|object|stdClass|void|null
     */
    protected function get_emailing_by_tag_id($id_tag = 0)
    {

        global $wpdb;

        if( $id_tag > 0 )
        {
            $table = $this->config['prefix'].$this->config['table_tags'];
            $tag = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = '" . $id_tag . "'" ) );

            if( $tag )
            {

                $table = $this->config['prefix'].$this->config['table'];
                $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = '" . $tag->emailing_id . "'" ) );

                return $result;

            }

        }

    }

    /**
     * @return void
     */
    public function save_subscribe_form()
    {

        if( ! is_user_logged_in() )
        {

            $first_name = sanitize_text_field($_POST['first_name']);
            $email = $_POST['email'];
            $user = 0;

        }else{

            $current_user = wp_get_current_user();
            $first_name = $current_user->user_firstname;
            $email = $current_user->user_email;
            $user = get_current_user_id();

        }

        $options = $_POST['options'];

        $insert = $this->insert($user, $first_name, $email, $options);

        if( $insert['status'] === true )
        {

            wp_send_json_success([
                'message' => $insert['message'],
                'html' => (new Tender_Subscribe_Shortcodes($this->config))->get_html_subscribes_user($email)
            ]);

        }else {

            wp_send_json_error([
                'message' => $insert['message']
            ]);

        }

    }

    /**
     * @return void
     */
    public function save_treaking_form()
    {

        $user = get_current_user_id();

        if( isset( $_POST['treaking_tender_status'] ) )
        {
            add_user_meta( $user, 'treaking_tender', absint($_POST['treaking_tender']) );
        }else{
            delete_user_meta( $user, 'treaking_tender', absint($_POST['treaking_tender']) );
        }

        wp_send_json_success([
            'message' => __( 'Data updated successfully', 'kadence-child' )
        ]);

    }

    /**
     * @param $user
     * @param $first_name
     * @param $email
     * @param $options
     * @return array
     */
    protected function insert($user, $first_name, $email, $options) {
        global $wpdb;
        
        $table = $this->config['prefix'].$this->config['table'];
        $table_tags = $this->config['prefix'].$this->config['table_tags'];
        
        // Validate options
        if (!$options || empty($options)) {
            return [
                'status' => false,
                'message' => __('No options selected', 'kadence-child')
            ];
        }
    
        // Start transaction
        $wpdb->query('START TRANSACTION');
        
        try {
            // Check if user already exists
            $existing_user = $wpdb->get_row($wpdb->prepare(
                "SELECT id FROM {$table} WHERE email = %s",
                $email
            ));

            $emailing_id = 0;
            
            if ($existing_user) {
                // Use existing user's ID
                $emailing_id = $existing_user->id;
            } else {
                // Insert new user
                $wpdb->insert($table, [
                    'user_id' => $user,
                    'first_name' => $first_name,
                    'email' => $email,
                    'state' => 1
                ]);
                
                $emailing_id = $wpdb->insert_id;
            }
    
            // Get max group number for this user
            $max_group = $wpdb->get_var($wpdb->prepare(
                "SELECT MAX(group_no) FROM {$table_tags} WHERE emailing_id = %d",
                $emailing_id
            ));
            
            $new_group = (int)$max_group + 1;
    
            // Insert selections with new group number
            foreach ($options as $key => $option) {
                if (!$option || $option[0] == 'none') {
                    continue;
                }
    
                $taxonomy = null;
                switch ($key) {
                    case 'product':
                        $taxonomy = $this->config['taxonomies']['product'];
                        break;
                    case 'country':
                        $taxonomy = $this->config['taxonomies']['country'];
                        break;
                    case 'regions':
                        $taxonomy = $this->config['taxonomies']['region'];
                        if ($option && $option[0] !== '0') { // MODIFIED: Keep implode for multiple regions, but not for "All"
                            $option[0] = implode(',', $option);
                        }
                        break;
                    case 'markets':
                        $taxonomy = $this->config['taxonomies']['market'];
                        break;
                }
    
                if ($taxonomy && isset($option[0])) { // MODIFIED: Check if $option[0] is set
                    $tag_id_to_store = $option[0]; // Use $option[0] directly, which could be '0' for "All"

                    $inserted = $wpdb->insert($table_tags, [
                        'emailing_id' => $emailing_id,
                        'group_no' => $new_group,
                        'tag_taxonomy' => $taxonomy,
                        'tag_id' => $tag_id_to_store 
                    ]);
    
                    if (!$inserted) {
                        throw new Exception($wpdb->last_error);
                    }
                }
            }
    
            $wpdb->query('COMMIT');
            
            return [
                'status' => true,
                'message' => __($this->config['messages']['succes_subscribe'], 'kadence-child')
            ];
    
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @param $id
     * @return bool|int|mysqli_result|resource|null
     */
    protected function delete($ids) {
        global $wpdb;
        
        $table_tags = $this->config['prefix'].$this->config['table_tags'];
        $table = $this->config['prefix'].$this->config['table'];

        // First get the emailing_id before deleting tags
        if (is_array($ids)) {
            $ids = implode(',', array_map('intval', $ids));
        }

        $emailing_id = $wpdb->get_var($wpdb->prepare(
            "SELECT DISTINCT emailing_id FROM {$table_tags} WHERE id IN ($ids)"
        ));

        if (!$emailing_id) {
            return [
                'status' => false,
                'message' => 'Could not find subscription'
            ];
        }

        // Delete the selected tags
        $delete_tags = $wpdb->query(
            "DELETE FROM {$table_tags} WHERE id IN ($ids)"
        );

        // Check if any selections remain for this subscription
        $remaining_tags = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_tags} WHERE emailing_id = %d",
            $emailing_id
        ));

        // If no selections remain, delete the entire subscription
        if ($remaining_tags == 0) {
            $wpdb->query($wpdb->prepare(
                "DELETE FROM {$table} WHERE id = %d",
                $emailing_id
            ));
        }

        if ($delete_tags !== false) {
            return [
                'status' => true,
                'message' => __('Success!', 'kadence-child')
            ];
        } else {
            return [
                'status' => false,
                'message' => $wpdb->last_error
            ];
        }
    }

    /**
     * @param $user_id
     * @param $userdata
     * @return bool|int|mysqli_result|resource|null
     */
    protected function update($user_id, $userdata)
    {

        global $wpdb;

        $table = $this->config['prefix'].$this->config['table'];

        $first_name = $userdata['first_name'];
        $email = $userdata['user_email'];

        $query = "UPDATE {$table} 
                  SET user_id = '{$user_id}', first_name = '{$first_name}' 
	              WHERE email = '{$email}'";

        $res = $wpdb->query( $query );

        return $res;

    }

    /**
     * Uodate User ID in Emailing Table
     * @param $user_id
     * @param $userdata
     * @return void
     */
    public function user_register_callback($user_id, $userdata)
    {

        global $wpdb;

        $email = $userdata['user_email'];

        $table = $this->config['prefix'].$this->config['table'];

        $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE email = '" . $email . "'" ) );

        if( $result )
        {
            $upd = $this->update($user_id, $userdata);
        }

    }

    /**
     * Remove Emailing All by User ID
     * @param $id
     * @param $reassign
     * @param $user
     * @return void
     */
    public function user_delete_callback($id, $reassign, $user)
    {

        global $wpdb;

        $table = $this->config['prefix'].$this->config['table'];
        $emailing = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = '" . $id . "'" ) );

        if( $emailing )
        {

            $delete_emailing = $wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE id = '" . $emailing->id . "'" ));

            $table = $this->config['prefix'].$this->config['table_tags'];
            $delete_tags = $wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE emailing_id = '" . $emailing->id . "'" ));

        }





    }

}