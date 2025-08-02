<?php

class VolumeFX_Install {
    
    public static function install() {
        self::create_tables();
    }
    
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Таблица платежей
        $table_payments = $wpdb->prefix . 'volumefx_payments';
        
        $sql_payments = "CREATE TABLE $table_payments (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            type varchar(20) NOT NULL, -- balance, subscription
            product_id bigint(20) DEFAULT NULL,
            amount decimal(10,2) NOT NULL,
            currency varchar(10) DEFAULT 'USD',
            crypto_amount varchar(50) DEFAULT NULL,
            transaction_hash varchar(255) DEFAULT NULL,
            status varchar(20) DEFAULT 'pending',
            admin_note text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY status (status)
        ) $charset_collate;";
        
        // Таблица баланса пользователей
        $table_balance = $wpdb->prefix . 'volumefx_user_balance';
        
        $sql_balance = "CREATE TABLE $table_balance (
            user_id bigint(20) NOT NULL,
            balance decimal(10,2) DEFAULT 0,
            total_deposited decimal(10,2) DEFAULT 0,
            total_spent decimal(10,2) DEFAULT 0,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (user_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_payments);
        dbDelta($sql_balance);
    }
}

// Активация при включении темы
add_action('after_switch_theme', array('VolumeFX_Install', 'install'));