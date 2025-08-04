<?php

class VolumeFX_Payments {
    
    /**
     * Создать новый платеж
     */
    public static function create_payment($data) {
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->prefix . 'volumefx_payments',
            array(
                'user_id' => $data['user_id'],
                'type' => $data['type'],
                'product_id' => $data['product_id'] ?? null,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'USD',
                'crypto_amount' => $data['crypto_amount'] ?? null,
                'transaction_hash' => $data['transaction_hash'] ?? null,
                'status' => $data['status'] ?? 'pending'
            )
        );
        
        return $wpdb->insert_id;
    }
    
    /**
     * Обновить статус платежа
     */
    public static function update_payment_status($payment_id, $status, $admin_note = '') {
        global $wpdb;
        
        $wpdb->update(
            $wpdb->prefix . 'volumefx_payments',
            array(
                'status' => $status,
                'admin_note' => $admin_note,
                'updated_at' => current_time('mysql')
            ),
            array('id' => $payment_id)
        );
        
        // Если платеж подтвержден
        if($status === 'completed') {
            $payment = self::get_payment($payment_id);
            
            if($payment->type === 'balance') {
                // Пополнение баланса
                self::add_user_balance($payment->user_id, $payment->amount);
            }
        }
    }
    
    /**
     * Получить платеж
     */
    public static function get_payment($payment_id) {
        global $wpdb;
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}volumefx_payments WHERE id = %d",
            $payment_id
        ));
    }
    
    /**
     * Получить платежи пользователя
     */
    public static function get_user_payments($user_id, $status = null) {
        global $wpdb;
        
        $query = "SELECT * FROM {$wpdb->prefix}volumefx_payments WHERE user_id = %d";
        $params = array($user_id);
        
        if($status) {
            $query .= " AND status = %s";
            $params[] = $status;
        }
        
        $query .= " ORDER BY created_at DESC";
        
        return $wpdb->get_results($wpdb->prepare($query, $params));
    }
    
    /**
     * Добавить баланс пользователю
     */
    public static function add_user_balance($user_id, $amount) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'volumefx_user_balance';
        
        // Проверяем существует ли запись
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT user_id FROM $table WHERE user_id = %d",
            $user_id
        ));
        
        if($exists) {
            $wpdb->query($wpdb->prepare(
                "UPDATE $table SET 
                balance = balance + %f,
                total_deposited = total_deposited + %f
                WHERE user_id = %d",
                $amount, $amount, $user_id
            ));
        } else {
            $wpdb->insert($table, array(
                'user_id' => $user_id,
                'balance' => $amount,
                'total_deposited' => $amount
            ));
        }
    }
    
    /**
     * Получить баланс пользователя
     */
    public static function get_user_balance($user_id) {
        global $wpdb;
        
        $balance = $wpdb->get_var($wpdb->prepare(
            "SELECT balance FROM {$wpdb->prefix}volumefx_user_balance WHERE user_id = %d",
            $user_id
        ));
        
        return $balance ? floatval($balance) : 0;
    }
    
    /**
     * Списать с баланса
     */
    public static function deduct_balance($user_id, $amount) {
        global $wpdb;
        
        $current_balance = self::get_user_balance($user_id);
        
        if($current_balance >= $amount) {
            $wpdb->query($wpdb->prepare(
                "UPDATE {$wpdb->prefix}volumefx_user_balance SET 
                balance = balance - %f,
                total_spent = total_spent + %f
                WHERE user_id = %d",
                $amount, $amount, $user_id
            ));
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Активировать подписку
     */
    private static function activate_subscription($payment) {
        $api = new LaTradeAPI();
        
        // Получаем данные продукта
        $indicator_slug = get_field('product_indicator_slug', $payment->product_id);
        $indicator_id = $api->getIndicatorId($indicator_slug);
        
        // Получаем период подписки из метаданных платежа
        $period = get_post_meta($payment->product_id, 'payment_period_' . $payment->id, true) ?: 30;
        $account_number = get_post_meta($payment->product_id, 'payment_account_' . $payment->id, true);
        
        if($indicator_id && $account_number) {
            $end_date = date('Y-m-d', strtotime("+{$period} days"));
            
            $result = $api->updateUserSubscription(
                $payment->user_id,
                $indicator_id,
                $account_number,
                $end_date
            );
            
            return $result;
        }
        
        return false;
    }
    
    /**
     * Получить статистику платежей
     */
    public static function get_payments_stats() {
        global $wpdb;
        
        $stats = $wpdb->get_row("
            SELECT 
                COUNT(*) as total_payments,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
                SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_revenue
            FROM {$wpdb->prefix}volumefx_payments
        ");
        
        return $stats;
    }
}