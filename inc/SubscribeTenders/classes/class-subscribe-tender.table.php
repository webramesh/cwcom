<?php
defined( 'ABSPATH' ) || exit;

class Tender_Subscribe_Table
{

    protected $prefix = 'h1p4m_';
    protected $table_logs = 'emailing_logs';

    /**
     * General Table Name
     * @var string
     */
    protected $table;

    /**
     * Tags Table Name
     * @var string
     */
    protected $table_tags;

    public function __construct($config)
    {

        $this->prefix = $config['prefix'];
        $this->table = $config['table'];
        $this->table_tags = $config['table_tags'];
        $this->table_logs = $config['table_logs'];

        $this->check_table();

    }

    /**
     * @return void
     */
    private function check_table()
    {
        global $wpdb;

        // Don't override custom prefix with WordPress prefix
        // Keep the h1p4m_ prefix
        // $this->prefix = $wpdb->prefix;
        
        $table = $this->prefix . $this->table;
        if($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) != $table) {
            $this->create_table();
        }

        $table_tags = $this->prefix.$this->table_tags;
        if($wpdb->get_var("SHOW TABLES LIKE '$table_tags'") != $table_tags)
        {
            $this->create_table_tags();
        }

        $table_logs = $this->prefix . $this->table_logs;
        if($wpdb->get_var("SHOW TABLES LIKE '$table_logs'") != $table_logs) {
            $this->create_table_logs();
        } else {
            $this->upgrade_table_logs();
        }
    }

    /**
     * Create Table
     * @return bool
     */
    private function create_table()
    {

        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `{$this->prefix}{$this->table}` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` INT(11) NOT NULL,
            `email` CHAR(100),
            `first_name` CHAR(100),
            `state` INT NOT NULL DEFAULT '0',
            `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE (email)
        ) $charset_collate;";

        return $this->create($sql);

    }

    /**
     * Create Table Tags
     * @return bool
     */
    private function create_table_tags()
    {

        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `{$this->prefix}{$this->table_tags}` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `emailing_id` INT(11) NOT NULL,
            `group_no` INT NOT NULL DEFAULT '0',
            `tag_taxonomy` CHAR(100),
            `tag_id` CHAR(200) NOT NULL,
            `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) $charset_collate;";

        return $this->create($sql);

    }

    /**
     * Create Table Logs
     * @return bool
     */
    private function create_table_logs()
    {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = 'h1p4m_emailing_logs'; // Use direct prefix

        $sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` INT(11) NOT NULL,
            `user_email` VARCHAR(100),
            `subscribe` VARCHAR(255),
            `message` TEXT,
            `code` INT,
            `email_title` TEXT,
            `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) $charset_collate;";

        return $this->create($sql);
    }

    /**
     * @param $sql
     * @return bool
     */
    private function create($sql)
    {

        global $wpdb;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $success = empty($wpdb->last_error);

        return $success;

    }

    /**
     * Upgrade table structure if needed
     */
    private function upgrade_table_logs() 
    {
        global $wpdb;
        $table_name = 'h1p4m_emailing_logs';

        // Check if user_email column exists
        $column_exists = $wpdb->get_results("SHOW COLUMNS FROM `{$table_name}` LIKE 'user_email'");
        
        if (empty($column_exists)) {
            // Add user_email column if it doesn't exist
            $wpdb->query("ALTER TABLE `{$table_name}` 
                         ADD COLUMN `user_email` VARCHAR(100) AFTER `user_id`");
        }
        
        // Check if email_title column exists
        $email_title_exists = $wpdb->get_results("SHOW COLUMNS FROM `{$table_name}` LIKE 'email_title'");
        
        if (empty($email_title_exists)) {
            // Add email_title column
            $wpdb->query("ALTER TABLE `{$table_name}` 
                         ADD COLUMN `email_title` TEXT AFTER `code`");
            
            // Copy message data to email_title for existing records
            $wpdb->query("UPDATE `{$table_name}` SET `email_title` = `message`");
            
            // Update message column to combine status and code
            $wpdb->query("UPDATE `{$table_name}` SET 
                         `message` = CONCAT(`status`, ' - ', IF(`code`=200, 'Success', CONCAT('Error (', `code`, ')')), ': ', `message`)");
        }
        
        // Check if status column still exists (after previous migration)
        $status_exists = $wpdb->get_results("SHOW COLUMNS FROM `{$table_name}` LIKE 'status'");
        
        if (!empty($status_exists)) {
            // Status column exists, we can remove it now that data is migrated
            $wpdb->query("ALTER TABLE `{$table_name}` DROP COLUMN `status`");
        }
    }

}
