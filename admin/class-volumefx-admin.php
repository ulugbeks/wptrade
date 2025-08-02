<?php

// admin/class-volumefx-admin.php
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
    
    public function ajax_approve_payment() {
        check_ajax_referer('volumefx_admin_nonce', 'nonce');
        
        $payment_id = intval($_POST['payment_id']);
        $admin_note = sanitize_textarea_field($_POST['admin_note']);
        
        VolumeFX_Payments::update_payment_status($payment_id, 'completed', $admin_note);
        
        // Отправляем уведомление пользователю
        $payment = VolumeFX_Payments::get_payment($payment_id);
        volumefx_notify_user_payment_approved($payment);
        
        wp_send_json_success('Платеж подтвержден');
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