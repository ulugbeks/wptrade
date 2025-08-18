<?php
/**
 * Добавьте этот код в файл functions.php
 * Регистрация услуг и обработчики
 */

// Регистрация типа записи "Услуги" (без архива)
function volumefx_register_service_post_type() {
    register_post_type('service', array(
        'labels' => array(
            'name' => 'Услуги',
            'singular_name' => 'Услуга',
            'add_new' => 'Добавить услугу',
            'add_new_item' => 'Добавить новую услугу',
            'edit_item' => 'Редактировать услугу',
            'new_item' => 'Новая услуга',
            'view_item' => 'Просмотреть услугу',
            'search_items' => 'Поиск услуг',
            'not_found' => 'Услуги не найдены',
            'not_found_in_trash' => 'В корзине услуги не найдены',
            'menu_name' => 'Услуги',
        ),
        'public' => true,
        'has_archive' => false, // Отключаем архив
        'rewrite' => array('slug' => 'service'),
        'supports' => array('title', 'editor'),
        'menu_icon' => 'dashicons-admin-tools',
        'show_in_menu' => true,
        'menu_position' => 5,
    ));
}
add_action('init', 'volumefx_register_service_post_type');

// ACF поля для услуг (упрощенные)
add_action('acf/init', 'volumefx_add_service_fields');
function volumefx_add_service_fields() {
    if(function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_service_fields',
            'title' => 'Настройки услуги',
            'fields' => array(
                array(
                    'key' => 'field_service_price',
                    'label' => 'Цена услуги',
                    'name' => 'service_price',
                    'type' => 'number',
                    'required' => 1,
                    'default_value' => 100,
                    'min' => 0,
                    'prepend' => '$',
                    'instructions' => 'Укажите стоимость услуги в долларах',
                ),
                array(
                    'key' => 'field_service_period',
                    'label' => 'Период/Срок (опционально)',
                    'name' => 'service_period',
                    'type' => 'text',
                    'placeholder' => 'Например: единоразово, 1 месяц, 1 год',
                    'instructions' => 'Оставьте пустым, если не требуется',
                ),
                array(
                    'key' => 'field_service_sidebar_text',
                    'label' => 'Текст в сайдбаре',
                    'name' => 'service_sidebar_text',
                    'type' => 'wysiwyg',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                    'instructions' => 'Этот текст будет отображаться в сайдбаре над кнопкой покупки',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'service',
                    ),
                ),
            ),
        ));
    }
}

// AJAX обработчик для оплаты услуги
add_action('wp_ajax_submit_service_payment', 'volumefx_ajax_submit_service_payment');
function volumefx_ajax_submit_service_payment() {
    check_ajax_referer('service_payment_nonce', 'nonce');
    
    $user_id = get_current_user_id();
    if(!$user_id) {
        wp_send_json_error('Необходима авторизация');
    }
    
    // Создаем запись о платеже
    $payment_data = array(
        'user_id' => $user_id,
        'type' => 'service',
        'product_id' => intval($_POST['service_id']),
        'amount' => floatval($_POST['amount']),
        'crypto_amount' => floatval($_POST['amount']) . ' USDT',
        'transaction_hash' => sanitize_text_field($_POST['transaction_hash']),
        'status' => 'pending'
    );
    
    // Используем существующий класс для создания платежа
    $payment_id = VolumeFX_Payments::create_payment($payment_data);
    
    if($payment_id) {
        // Отправляем уведомление администратору
        volumefx_notify_admin_service_payment($payment_id);
        
        wp_send_json_success('Платеж отправлен на проверку. Администратор свяжется с вами в ближайшее время.');
    } else {
        wp_send_json_error('Ошибка при создании платежа. Попробуйте еще раз.');
    }
}

// Уведомление админу о новой оплате услуги
function volumefx_notify_admin_service_payment($payment_id) {
    $payment = VolumeFX_Payments::get_payment($payment_id);
    $user = get_userdata($payment->user_id);
    $service = get_post($payment->product_id);
    
    $subject = 'Новая оплата услуги #' . $payment_id;
    $message = sprintf(
        "Новая оплата услуги от пользователя %s (%s)\n\n" .
        "Услуга: %s\n" .
        "Сумма: %s %s\n" .
        "Крипто: %s\n" .
        "Hash транзакции: %s\n\n" .
        "Перейти в админку: %s",
        $user->display_name,
        $user->user_email,
        $service->post_title,
        $payment->amount,
        $payment->currency,
        $payment->crypto_amount,
        $payment->transaction_hash,
        admin_url('admin.php?page=volumefx-payments&status=pending')
    );
    
    wp_mail(get_option('admin_email'), $subject, $message);
}

// Добавляем глобальную настройку для адреса кошелька (если еще не добавлена)
add_action('acf/init', 'volumefx_add_global_crypto_settings');
function volumefx_add_global_crypto_settings() {
    if(function_exists('acf_add_local_field_group')) {
        // Проверяем, существует ли уже группа
        if(!acf_get_field_group('group_crypto_settings')) {
            acf_add_local_field_group(array(
                'key' => 'group_crypto_settings',
                'title' => 'Настройки криптовалюты',
                'fields' => array(
                    array(
                        'key' => 'field_crypto_wallet_address',
                        'label' => 'Адрес кошелька USDT TRC20',
                        'name' => 'crypto_wallet_address',
                        'type' => 'text',
                        'default_value' => 'TWVsrW7qEfFxzwt4xMVMWJwvQPn9ssTSxp',
                        'instructions' => 'Укажите адрес кошелька для приема платежей в USDT TRC20',
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'theme-general-settings',
                        ),
                    ),
                ),
            ));
        }
    }
}

// Обновляем обработку платежей в админке для поддержки услуг
add_filter('volumefx_payment_type_display', 'volumefx_service_payment_type_display', 10, 2);
function volumefx_service_payment_type_display($display, $payment) {
    if($payment->type === 'service') {
        $service = get_post($payment->product_id);
        return 'Услуга: ' . ($service ? $service->post_title : 'N/A');
    }
    return $display;
}