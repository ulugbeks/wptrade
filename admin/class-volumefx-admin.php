<?php

class VolumeFX_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_approve_payment', array($this, 'ajax_approve_payment'));
        add_action('wp_ajax_reject_payment', array($this, 'ajax_reject_payment'));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            'Volume-FX',
            'Volume-FX',
            'manage_options',
            'volumefx',
            array($this, 'admin_page'),
            'dashicons-chart-line',
            30
        );
        
        add_submenu_page(
            'volumefx',
            'Платежи',
            'Платежи',
            'manage_options',
            'volumefx-payments',
            array($this, 'payments_page')
        );
        
        add_submenu_page(
            'volumefx',
            'Пользователи',
            'Пользователи',
            'manage_options',
            'volumefx-users',
            array($this, 'users_page')
        );
    }
    
    public function admin_page() {
        // Главная страница админки
        include get_template_directory() . '/admin/views/dashboard.php';
    }
    
    public function payments_page() {
        // Страница платежей
        include get_template_directory() . '/admin/views/payments.php';
    }
    
    public function users_page() {
        // Страница пользователей
        include get_template_directory() . '/admin/views/users.php';
    }
    
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'volumefx') !== false) {
            wp_enqueue_script('thickbox');
            wp_enqueue_style('thickbox');
        }
    }
    
    public function ajax_approve_payment() {
        check_ajax_referer('volumefx_admin_nonce', 'nonce');
        
        if(!current_user_can('manage_options')) {
            wp_send_json_error('Недостаточно прав');
        }
        
        $payment_id = intval($_POST['payment_id']);
        $admin_note = sanitize_textarea_field($_POST['admin_note']);
        
        // Получаем информацию о платеже
        $payment = VolumeFX_Payments::get_payment($payment_id);
        
        if($payment && $payment->type === 'subscription') {
            // Получаем сохраненные данные
            $account_number = get_post_meta($payment->product_id, 'payment_account_' . $payment_id, true);
            $period = get_post_meta($payment->product_id, 'payment_period_' . $payment_id, true);
            
            if($account_number && $period) {
                // Активируем подписку через API
                $api = new LaTradeAPI();
                $indicator_slug = get_field('product_indicator_slug', $payment->product_id);
                
                // Проверяем, является ли это кастомным индикатором
                if($indicator_slug === 'custom') {
                    $indicator_id = 'custom_' . $payment->product_id;
                } else {
                    $indicator_id = $api->getIndicatorId($indicator_slug);
                }
                
                if($indicator_id) {
                    $end_date = date('Y-m-d', strtotime("+{$period} days"));
                    
                    $result = $api->updateUserSubscription(
                        $payment->user_id,
                        $indicator_id,
                        $account_number,
                        $end_date
                    );
                    
                    if($result && $result['code'] == 200) {
                        // Обновляем статус платежа
                        VolumeFX_Payments::update_payment_status($payment_id, 'completed', $admin_note);
                        
                        // Отправляем уведомление пользователю
                        volumefx_notify_user_payment_approved($payment);
                        
                        // Удаляем временные метаданные
                        delete_post_meta($payment->product_id, 'payment_account_' . $payment_id);
                        delete_post_meta($payment->product_id, 'payment_period_' . $payment_id);
                        
                        wp_send_json_success('Платеж подтвержден и подписка активирована');
                    } else {
                        wp_send_json_error('Ошибка при активации подписки через API');
                    }
                } else {
                    wp_send_json_error('Не найден индикатор для продукта');
                }
            } else {
                wp_send_json_error('Не найдены данные для активации подписки');
            }
        } else {
            // Для пополнения баланса просто обновляем статус
            VolumeFX_Payments::update_payment_status($payment_id, 'completed', $admin_note);
            volumefx_notify_user_payment_approved($payment);
            wp_send_json_success('Платеж подтвержден');
        }
    }
    
    public function ajax_reject_payment() {
        check_ajax_referer('volumefx_admin_nonce', 'nonce');
        
        $payment_id = intval($_POST['payment_id']);
        $admin_note = sanitize_textarea_field($_POST['admin_note']);
        
        VolumeFX_Payments::update_payment_status($payment_id, 'rejected', $admin_note);
        
        // Отправляем уведомление пользователю
        $payment = VolumeFX_Payments::get_payment($payment_id);
        volumefx_notify_user_payment_rejected($payment);
        
        wp_send_json_success('Платеж отклонен');
    }
}

new VolumeFX_Admin();