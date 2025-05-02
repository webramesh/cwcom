<?php
defined( 'ABSPATH' ) || exit;

class Tender_Subscribe_Mail
{

    /**
     * @var
     */
    protected $config;

    public function __construct($config)
    {

        $this->config = $config;
        $this->check_table_status();

    }

    /**
     * @param $arr
     * @return void
     */
    public function send($arr = [])
    {
        extract($arr);

        $subject = $this->placeholders(get_option($template . '_subject'), $placeholders);
        $template_html = get_option($template);
        $template_html = $this->placeholders($template_html, $placeholders);
        $headers = array('Content-Type: text/html; charset=UTF-8');

        if (!empty($template_html)) {
            // Store subscribe data for use in callbacks
            $subscribe_data = array(
                'to' => $to,
                'user_id' => isset($subscribe['user_id']) ? $subscribe['user_id'] : 0,
                'tender_title' => isset($subscribe['tender_title']) ? $subscribe['tender_title'] : '',
                'template' => $template, // Pass the template name
                'subject' => $subject    // Pass the actual subject
            );

            // Remove any existing hooks
            remove_all_filters('wp_mail_succeeded');
            remove_all_filters('wp_mail_failed');

            // Add our hooks with explicit priority
            add_action('wp_mail_succeeded', function($mail_data) use ($subscribe_data) {
                $this->mail_success($mail_data, $subscribe_data);
            }, 10, 1);

            add_action('wp_mail_failed', function($error) use ($subscribe_data) {
                $this->mail_failed($error, $subscribe_data);
            }, 10, 1);

            // Send mail and capture result
            $sent = wp_mail($to, $subject, $template_html, $headers);

            // If mail function returns false, log failure
            if (!$sent) {
                $this->log(422, 'Failed to send email', $subscribe_data);
            }

            return $sent;
        }

        return false;
    }

    /**
     * @param $template
     * @param $data
     * @return string
     */
    protected function placeholders($template, $data)
    {

        foreach ($data as $key => $value) {
            $template = preg_replace('/\[\['.$key.'\]\]/i', $value, $template);
        }

        return $template;

    }

    /**
     * @param $mail_data
     * @param $subscribe
     * @return void
     */
    protected function mail_success($mail_data, $subscribe) {
        // Get the actual email subject from the subscribe data
        if (isset($subscribe['subject']) && !empty($subscribe['subject'])) {
            // Use the actual email subject that was sent
            $subject = $subscribe['subject'];
        } else {
            // Fallback to getting subject from template name if available
            $template_type = isset($subscribe['template']) ? $subscribe['template'] : '';
            
            if ($template_type === 'subscribe_settings_template_one') {
                $subject = get_option('subscribe_settings_template_one_subject');
            } elseif ($template_type === 'subscribe_settings_template_two') {
                $subject = get_option('subscribe_settings_template_two_subject');
            } elseif ($template_type === 'subscribe_settings_template_three') {
                $subject = get_option('subscribe_settings_template_three_subject');
            } elseif ($template_type === 'subscribe_settings_template_four') {
                $subject = get_option('subscribe_settings_template_four_subject');
            } else {
                $subject = 'Email sent successfully';
            }
        }

        // Default message for status reporting
        $message = 'Email sent successfully';
        
        // Pass the actual email subject as email_title
        $this->log(200, $message, $subscribe, $subject);
    }

    /**
     * @param $error
     * @param $subscribe
     * @return void
     */
    protected function mail_failed($error, $subscribe) {
        $this->log(422, $error->get_error_message(), $subscribe);
    }

    /**
     * @param $code
     * @param $message
     * @param $subscribe
     * @param $email_title - Optional parameter for email subject
     * @return void
     */
    protected function log($code, $message, $subscribe, $email_title = '') {
        global $wpdb;
        
        try {
            $table = 'h1p4m_emailing_logs';
            
            // Format status message with code
            $status_text = ($code === 200) ? 'Success' : 'Error';
            $formatted_message = $status_text . ' - ' . 
                                ($code === 200 ? 'Success' : 'Error (' . $code . ')') . 
                                ': ' . (is_wp_error($message) ? $message->get_error_message() : $message);
                                
            // If email_title is not provided, use the message
            if (empty($email_title)) {
                $email_title = is_wp_error($message) ? $message->get_error_message() : $message;
            }
            
            $data = array(
                'user_id' => isset($subscribe['user_id']) ? $subscribe['user_id'] : 0,
                'user_email' => isset($subscribe['to']) ? $subscribe['to'] : '',
                'subscribe' => isset($subscribe['tender_title']) ? $subscribe['tender_title'] : '',
                'message' => $formatted_message,
                'code' => $code,
                'email_title' => $email_title,
                'date' => current_time('mysql')
            );
            
            error_log('Attempting to insert log: ' . print_r($data, true));
            $result = $wpdb->insert(
                $table,
                $data,
                array(
                    '%d',  // user_id
                    '%s',  // user_email
                    '%s',  // subscribe
                    '%s',  // message
                    '%d',  // code
                    '%s',  // email_title
                    '%s'   // date
                )
            );
            if ($result === false) {
                error_log('Failed to insert email log: ' . $wpdb->last_error);
                error_log('Last query: ' . $wpdb->last_query);
            } else {
                error_log('Successfully inserted log with ID: ' . $wpdb->insert_id);
            }
        } catch (Exception $e) {
            error_log('Exception while logging email: ' . $e->getMessage());
        }
    }

    /**
     * Debug method to check table status
     */
    private function check_table_status() {
        global $wpdb;
        // Use h1p4m_ prefix directly
        $table = 'h1p4m_emailing_logs';
        
        // Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'");
        error_log("Table $table exists: " . ($table_exists ? 'yes' : 'no'));
        
        if ($table_exists) {
            // Check table structure
            $columns = $wpdb->get_results("SHOW COLUMNS FROM $table");
            error_log("Table columns: " . print_r($columns, true));
        }
    }

}