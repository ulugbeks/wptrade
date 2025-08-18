<?php


/**
 * Enqueue scripts and styles.
 */
function fxfortrader_scripts() {
    // Styles
    wp_enqueue_style('fxfortrader-flaticon', get_template_directory_uri() . '/assets/css/flaticon.css');
    wp_enqueue_style('fxfortrader-owl', get_template_directory_uri() . '/assets/css/owl.css');
    wp_enqueue_style('fxfortrader-bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.css');
    wp_enqueue_style('fxfortrader-fancybox', get_template_directory_uri() . '/assets/css/jquery.fancybox.min.css');
    wp_enqueue_style('fxfortrader-animate', get_template_directory_uri() . '/assets/css/animate.css');
    wp_enqueue_style('fxfortrader-nice-select', get_template_directory_uri() . '/assets/css/nice-select.css');
    wp_enqueue_style('fxfortrader-odometer', get_template_directory_uri() . '/assets/css/odometer.css');
    wp_enqueue_style('fxfortrader-elpath', get_template_directory_uri() . '/assets/css/elpath.css');
    wp_enqueue_style('fxfortrader-color', get_template_directory_uri() . '/assets/css/color.css');
    wp_enqueue_style('fxfortrader-rtl', get_template_directory_uri() . '/assets/css/rtl.css');
    wp_enqueue_style('fxfortrader-main', get_template_directory_uri() . '/assets/css/style.css');
    
    // Module CSS
    wp_enqueue_style('fxfortrader-header', get_template_directory_uri() . '/assets/css/module-css/header.css');
    wp_enqueue_style('fxfortrader-banner', get_template_directory_uri() . '/assets/css/module-css/banner.css');
    wp_enqueue_style('fxfortrader-clients', get_template_directory_uri() . '/assets/css/module-css/clients.css');
    wp_enqueue_style('fxfortrader-account', get_template_directory_uri() . '/assets/css/module-css/account.css');
    wp_enqueue_style('fxfortrader-about', get_template_directory_uri() . '/assets/css/module-css/about.css');
    wp_enqueue_style('fxfortrader-news', get_template_directory_uri() . '/assets/css/module-css/news.css');
    wp_enqueue_style('fxfortrader-pagetitle', get_template_directory_uri() . '/assets/css/module-css/page-title.css');
    wp_enqueue_style('fxfortrader-footer', get_template_directory_uri() . '/assets/css/module-css/footer.css');
    wp_enqueue_style('fxfortrader-responsive', get_template_directory_uri() . '/assets/css/responsive.css');
    
    // Scripts
    wp_enqueue_script('fxfortrader-bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), '', true);
    wp_enqueue_script('fxfortrader-owl', get_template_directory_uri() . '/assets/js/owl.js', array('jquery'), '', true);
    wp_enqueue_script('fxfortrader-wow', get_template_directory_uri() . '/assets/js/wow.js', array('jquery'), '', true);
    wp_enqueue_script('fxfortrader-fancybox', get_template_directory_uri() . '/assets/js/jquery.fancybox.js', array('jquery'), '', true);
    wp_enqueue_script('fxfortrader-appear', get_template_directory_uri() . '/assets/js/appear.js', array('jquery'), '', true);
    wp_enqueue_script('fxfortrader-isotope', get_template_directory_uri() . '/assets/js/isotope.js', array('jquery'), '', true);
    wp_enqueue_script('fxfortrader-parallax', get_template_directory_uri() . '/assets/js/parallax-scroll.js', array('jquery'), '', true);
    wp_enqueue_script('fxfortrader-nice-select', get_template_directory_uri() . '/assets/js/jquery.nice-select.min.js', array('jquery'), '', true);
    wp_enqueue_script('fxfortrader-scrolltop', get_template_directory_uri() . '/assets/js/scrolltop.min.js', array('jquery'), '', true);
    wp_enqueue_script('fxfortrader-odometer', get_template_directory_uri() . '/assets/js/odometer.js', array('jquery'), '', true);
    wp_enqueue_script('fxfortrader-script', get_template_directory_uri() . '/assets/js/script.js', array('jquery'), '', true);
    wp_enqueue_script('fxfortrader-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '', true);
}
add_action('wp_enqueue_scripts', 'fxfortrader_scripts');


// Подключение файлов
require get_template_directory() . '/inc/custom-post-types.php';
require get_template_directory() . '/inc/acf-fields.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/class-menu-walker.php';
require get_template_directory() . '/inc/theme-options.php';

require get_template_directory() . '/inc/class-volumefx-install.php';
require get_template_directory() . '/inc/class-latrade-api.php';
require get_template_directory() . '/inc/class-volumefx-payments.php';

// Подключаем админ панель
require get_template_directory() . '/admin/class-volumefx-admin.php';

register_activation_hook(__FILE__, array('VolumeFX_Install', 'install'));

// Загружаем текстовый домен после init
add_action('init', function() {
    load_theme_textdomain('volumefx', get_template_directory() . '/languages');
});

// Поддержка возможностей темы
function fxfortrader_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('menus');
    
    // Регистрация меню
    register_nav_menus(array(
        'primary' => 'Основное меню',
        'mobile' => 'Мобильное меню',
        'footer-about' => 'Меню футера - О нас',
    ));
}
add_action('after_setup_theme', 'fxfortrader_setup');

// Отключение админ-бара на фронтенде
add_filter('show_admin_bar', '__return_false');

// Хлебные крошки
function fxfortrader_breadcrumbs() {
    if (!is_home()) {
        echo '<a href="' . home_url() . '">Главная</a>';
        if (is_archive() || is_single()) {
            echo ' » ';
            if (is_single()) {
                $post_type = get_post_type();
                if ($post_type == 'product') {
                    echo '<a href="' . get_post_type_archive_link('product') . '">Продукты</a> » ';
                } elseif ($post_type == 'course') {
                    echo '<a href="' . get_post_type_archive_link('course') . '">Обучение и курсы</a> » ';
                }
                the_title();
            }
        }
    }
}


// Поддержка комментариев
add_theme_support('html5', array('comment-list', 'comment-form'));

// Настройка блога
function fxfortrader_blog_setup() {
    // Устанавливаем страницу блога
    update_option('show_on_front', 'page');
    // update_option('page_for_posts', $blog_page_id);
}

// Русификация дат
function russian_date($date_format) {
    $months = array(
        'January' => 'января',
        'February' => 'февраля',
        'March' => 'марта',
        'April' => 'апреля',
        'May' => 'мая',
        'June' => 'июня',
        'July' => 'июля',
        'August' => 'августа',
        'September' => 'сентября',
        'October' => 'октября',
        'November' => 'ноября',
        'December' => 'декабря',
    );
    
    return strtr($date_format, $months);
}
add_filter('get_the_date', 'russian_date');
add_filter('get_comment_date', 'russian_date');


// Отключаем админ-бар для всех кроме админов
add_action('after_setup_theme', 'fxfortrader_disable_admin_bar');
function fxfortrader_disable_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}

// Перенаправление после выхода
add_action('wp_logout', 'fxfortrader_logout_redirect');
function fxfortrader_logout_redirect() {
    wp_redirect(home_url());
    exit;
}

// Создание страниц при активации темы
add_action('after_switch_theme', 'fxfortrader_create_auth_pages');
function fxfortrader_create_auth_pages() {
    // Страница входа
    if (!get_page_by_path('auth')) {
        wp_insert_post(array(
            'post_title' => 'Вход',
            'post_name' => 'auth',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-login.php',
        ));
    }
    
    // Страница регистрации
    if (!get_page_by_path('auth/register')) {
        $parent = get_page_by_path('auth');
        if ($parent) {
            wp_insert_post(array(
                'post_title' => 'Регистрация',
                'post_name' => 'register',
                'post_parent' => $parent->ID,
                'post_status' => 'publish',
                'post_type' => 'page',
                'page_template' => 'page-register.php',
            ));
        }
    }
    
    // Страница пополнения баланса
    if (!get_page_by_path('balance-topup')) {
        wp_insert_post(array(
            'post_title' => 'Пополнение баланса',
            'post_name' => 'balance-topup',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-balance-topup.php',
        ));
    }
}

// Кастомизация email при регистрации
add_filter('wp_new_user_notification_email', 'fxfortrader_custom_registration_email', 10, 3);
function fxfortrader_custom_registration_email($wp_new_user_notification_email, $user, $blogname) {
    $message = sprintf(__('Добро пожаловать на %s!'), $blogname) . "\r\n\r\n";
    $message .= __('Ваши данные для входа:') . "\r\n";
    $message .= sprintf(__('Email: %s'), $user->user_email) . "\r\n";
    $message .= __('Для входа перейдите по ссылке:') . "\r\n";
    $message .= home_url('/auth') . "\r\n";
    
    $wp_new_user_notification_email['message'] = $message;
    $wp_new_user_notification_email['subject'] = sprintf('[%s] Регистрация успешна', $blogname);
    
    return $wp_new_user_notification_email;
}

// Защита страниц личного кабинета
add_action('template_redirect', 'fxfortrader_protect_dashboard');
function fxfortrader_protect_dashboard() {
    if (is_page('dashboard') && !is_user_logged_in()) {
        wp_redirect(home_url('/auth?redirect=' . urlencode($_SERVER['REQUEST_URI'])));
        exit;
    }
}

// Добавьте в functions.php для красивых URL
add_action('init', 'fxfortrader_auth_rewrite_rules');
function fxfortrader_auth_rewrite_rules() {
    add_rewrite_rule(
        '^auth/register/?$',
        'index.php?pagename=auth/register',
        'top'
    );
}

// Сброс правил перезаписи при активации темы
add_action('after_switch_theme', 'fxfortrader_flush_rewrite_rules');
function fxfortrader_flush_rewrite_rules() {
    fxfortrader_auth_rewrite_rules();
    flush_rewrite_rules();
}


// Добавляем поле для indicator slug в продукты
add_action('acf/init', 'volumefx_add_indicator_field');
function volumefx_add_indicator_field() {
    if(function_exists('acf_add_local_field_group')) {
        // Группа полей для продуктов
        acf_add_local_field_group(array(
            'key' => 'group_products',
            'title' => 'Настройки продукта',
            'fields' => array(
                array(
                    'key' => 'field_product_indicator_slug',
                    'label' => 'Slug индикатора для API',
                    'name' => 'product_indicator_slug',
                    'type' => 'select',
                    'required' => 1,
                    'choices' => array(
                        'volatility_levels' => 'Volatility Levels',
                        'fibo_musang' => 'Fibo Musang',
                        'future_volume' => 'Future Volume',
                        'options_fx' => 'Options FX',
                    ),
                ),
                array(
                    'key' => 'field_product_price_options',
                    'label' => 'Варианты цен',
                    'name' => 'product_price_options',
                    'type' => 'repeater',
                    'required' => 1,
                    'layout' => 'table',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_price_period',
                            'label' => 'Период',
                            'name' => 'price_period',
                            'type' => 'text',
                            'default_value' => '1 месяц',
                        ),
                        array(
                            'key' => 'field_price_amount',
                            'label' => 'Цена',
                            'name' => 'price_amount',
                            'type' => 'number',
                            'default_value' => 199,
                            'min' => 0,
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'product',
                    ),
                ),
            ),
        ));
    }
}


// Поля для услуг
add_action('acf/init', 'volumefx_add_service_fields');
function volumefx_add_service_fields() {
    if(function_exists('acf_add_local_field_group')) {
        // Группа полей для услуг
        acf_add_local_field_group(array(
            'key' => 'group_services',
            'title' => 'Настройки услуги',
            'fields' => array(
                // Тип услуги
                array(
                    'key' => 'field_service_type',
                    'label' => 'Тип услуги',
                    'name' => 'service_type',
                    'type' => 'select',
                    'required' => 1,
                    'choices' => array(
                        'consulting' => 'Консультация',
                        'training' => 'Обучение',
                        'setup' => 'Настройка',
                        'support' => 'Поддержка',
                        'development' => 'Разработка',
                        'analytics' => 'Аналитика',
                        'other' => 'Другое',
                    ),
                    'default_value' => 'consulting',
                ),
                
                // Краткое описание
                array(
                    'key' => 'field_service_short_description',
                    'label' => 'Краткое описание',
                    'name' => 'service_short_description',
                    'type' => 'textarea',
                    'rows' => 3,
                    'instructions' => 'Краткое описание для карточки услуги',
                ),
                
                // Полное описание
                array(
                    'key' => 'field_service_description',
                    'label' => 'Полное описание',
                    'name' => 'service_description',
                    'type' => 'wysiwyg',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                ),
                
                // Варианты цен
                array(
                    'key' => 'field_service_price_options',
                    'label' => 'Варианты цен',
                    'name' => 'service_price_options',
                    'type' => 'repeater',
                    'layout' => 'table',
                    'button_label' => 'Добавить вариант цены',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_service_price_name',
                            'label' => 'Название тарифа',
                            'name' => 'price_name',
                            'type' => 'text',
                            'default_value' => 'Базовый',
                        ),
                        array(
                            'key' => 'field_service_price_period',
                            'label' => 'Период',
                            'name' => 'price_period',
                            'type' => 'text',
                            'default_value' => 'единоразово',
                        ),
                        array(
                            'key' => 'field_service_price_amount',
                            'label' => 'Цена ($)',
                            'name' => 'price_amount',
                            'type' => 'number',
                            'default_value' => 100,
                            'min' => 0,
                        ),
                        array(
                            'key' => 'field_service_price_description',
                            'label' => 'Описание тарифа',
                            'name' => 'price_description',
                            'type' => 'textarea',
                            'rows' => 2,
                        ),
                    ),
                ),
                
                // Что включено в услугу
                array(
                    'key' => 'field_service_features',
                    'label' => 'Что включено в услугу',
                    'name' => 'service_features',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'button_label' => 'Добавить пункт',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_service_feature_icon',
                            'label' => 'Иконка (FontAwesome класс)',
                            'name' => 'feature_icon',
                            'type' => 'text',
                            'default_value' => 'fas fa-check',
                        ),
                        array(
                            'key' => 'field_service_feature_text',
                            'label' => 'Текст',
                            'name' => 'feature_text',
                            'type' => 'text',
                        ),
                    ),
                ),
                
                // Процесс оказания услуги
                array(
                    'key' => 'field_service_process',
                    'label' => 'Процесс оказания услуги',
                    'name' => 'service_process',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'button_label' => 'Добавить этап',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_service_step_number',
                            'label' => 'Номер этапа',
                            'name' => 'step_number',
                            'type' => 'number',
                            'default_value' => 1,
                        ),
                        array(
                            'key' => 'field_service_step_title',
                            'label' => 'Название этапа',
                            'name' => 'step_title',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_service_step_description',
                            'label' => 'Описание этапа',
                            'name' => 'step_description',
                            'type' => 'textarea',
                            'rows' => 3,
                        ),
                    ),
                ),
                
                // FAQ
                array(
                    'key' => 'field_service_faqs',
                    'label' => 'Часто задаваемые вопросы',
                    'name' => 'service_faqs',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'button_label' => 'Добавить вопрос',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_service_faq_question',
                            'label' => 'Вопрос',
                            'name' => 'question',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_service_faq_answer',
                            'label' => 'Ответ',
                            'name' => 'answer',
                            'type' => 'textarea',
                            'rows' => 3,
                        ),
                    ),
                ),
                
                // Длительность услуги
                array(
                    'key' => 'field_service_duration',
                    'label' => 'Длительность',
                    'name' => 'service_duration',
                    'type' => 'text',
                    'instructions' => 'Например: 2 часа, 5 дней, 1 месяц',
                ),
                
                // Формат предоставления
                array(
                    'key' => 'field_service_format',
                    'label' => 'Формат предоставления',
                    'name' => 'service_format',
                    'type' => 'checkbox',
                    'choices' => array(
                        'online' => 'Онлайн',
                        'offline' => 'Офлайн',
                        'remote' => 'Удаленно',
                        'onsite' => 'На территории клиента',
                    ),
                    'layout' => 'horizontal',
                ),
                
                // Галерея
                array(
                    'key' => 'field_service_gallery',
                    'label' => 'Галерея',
                    'name' => 'service_gallery',
                    'type' => 'gallery',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ),
                
                // Видео
                array(
                    'key' => 'field_service_video',
                    'label' => 'Видео (YouTube URL)',
                    'name' => 'service_video',
                    'type' => 'url',
                ),
                
                // CTA кнопка
                array(
                    'key' => 'field_service_cta_text',
                    'label' => 'Текст кнопки действия',
                    'name' => 'service_cta_text',
                    'type' => 'text',
                    'default_value' => 'Заказать услугу',
                ),
                
                // Популярная услуга
                array(
                    'key' => 'field_service_is_featured',
                    'label' => 'Популярная услуга',
                    'name' => 'service_is_featured',
                    'type' => 'true_false',
                    'instructions' => 'Отметить как популярную/рекомендованную услугу',
                    'ui' => 1,
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
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ));
    }
}

// Добавьте эти функции в functions.php

// AJAX обработчик для заказа услуги
add_action('wp_ajax_submit_service_order', 'volumefx_ajax_submit_service_order');
add_action('wp_ajax_nopriv_submit_service_order', 'volumefx_ajax_submit_service_order');
function volumefx_ajax_submit_service_order() {
    check_ajax_referer('service_order_nonce', 'nonce');
    
    // Получаем ID пользователя (0 если не авторизован)
    $user_id = get_current_user_id();
    
    // Собираем данные заказа
    $service_id = intval($_POST['service_id']);
    $service_name = sanitize_text_field($_POST['service_name']);
    $price_amount = floatval($_POST['price_amount']);
    $price_name = sanitize_text_field($_POST['price_name']);
    $price_period = sanitize_text_field($_POST['price_period']);
    
    // Контактные данные
    $client_name = sanitize_text_field($_POST['client_name']);
    $client_email = sanitize_email($_POST['client_email']);
    $client_phone = sanitize_text_field($_POST['client_phone']);
    $order_comment = sanitize_textarea_field($_POST['order_comment']);
    
    // Данные транзакции
    $transaction_hash = sanitize_text_field($_POST['transaction_hash']);
    
    // Если пользователь не авторизован, пытаемся найти его по email
    if(!$user_id) {
        $user = get_user_by('email', $client_email);
        if($user) {
            $user_id = $user->ID;
        } else {
            // Создаем нового пользователя
            $random_password = wp_generate_password(12, false);
            $user_id = wp_create_user($client_email, $random_password, $client_email);
            
            if(!is_wp_error($user_id)) {
                // Обновляем имя пользователя
                wp_update_user(array(
                    'ID' => $user_id,
                    'display_name' => $client_name,
                    'first_name' => $client_name,
                ));
                
                // Сохраняем телефон
                if($client_phone) {
                    update_user_meta($user_id, 'phone', $client_phone);
                }
                
                // Отправляем письмо с паролем
                wp_new_user_notification($user_id, null, 'both');
            } else {
                // Если не удалось создать пользователя, используем 0
                $user_id = 0;
            }
        }
    }
    
    // Создаем запись о платеже/заказе
    $payment_data = array(
        'user_id' => $user_id,
        'type' => 'service', // Новый тип для услуг
        'product_id' => $service_id,
        'amount' => $price_amount,
        'crypto_amount' => $price_amount . ' USDT',
        'transaction_hash' => $transaction_hash,
        'status' => 'pending'
    );
    
    $payment_id = VolumeFX_Payments::create_payment($payment_data);
    
    if($payment_id) {
        // Сохраняем дополнительные данные заказа
        add_post_meta($service_id, 'order_' . $payment_id . '_client_name', $client_name);
        add_post_meta($service_id, 'order_' . $payment_id . '_client_email', $client_email);
        add_post_meta($service_id, 'order_' . $payment_id . '_client_phone', $client_phone);
        add_post_meta($service_id, 'order_' . $payment_id . '_comment', $order_comment);
        add_post_meta($service_id, 'order_' . $payment_id . '_tariff', $price_name);
        add_post_meta($service_id, 'order_' . $payment_id . '_period', $price_period);
        
        // Отправляем уведомление админу
        volumefx_notify_admin_new_service_order($payment_id);
        
        // Отправляем уведомление клиенту
        volumefx_notify_client_service_order($payment_id, $client_email, $client_name);
        
        // Возвращаем успешный ответ
        wp_send_json_success(array(
            'message' => 'Ваш заказ успешно отправлен! Администратор свяжется с вами после проверки платежа.',
            'redirect' => $user_id ? home_url('/dashboard?service_ordered=1') : null
        ));
    } else {
        wp_send_json_error('Ошибка при создании заказа. Попробуйте еще раз.');
    }
}

// Уведомление админу о новом заказе услуги
function volumefx_notify_admin_new_service_order($payment_id) {
    $payment = VolumeFX_Payments::get_payment($payment_id);
    $service = get_post($payment->product_id);
    
    // Получаем дополнительные данные
    $client_name = get_post_meta($payment->product_id, 'order_' . $payment_id . '_client_name', true);
    $client_email = get_post_meta($payment->product_id, 'order_' . $payment_id . '_client_email', true);
    $client_phone = get_post_meta($payment->product_id, 'order_' . $payment_id . '_client_phone', true);
    $comment = get_post_meta($payment->product_id, 'order_' . $payment_id . '_comment', true);
    $tariff = get_post_meta($payment->product_id, 'order_' . $payment_id . '_tariff', true);
    
    $subject = 'Новый заказ услуги #' . $payment_id;
    $message = "Поступил новый заказ услуги!\n\n";
    $message .= "=== ИНФОРМАЦИЯ О ЗАКАЗЕ ===\n";
    $message .= "Номер заказа: #" . $payment_id . "\n";
    $message .= "Услуга: " . $service->post_title . "\n";
    $message .= "Тариф: " . $tariff . "\n";
    $message .= "Сумма: " . $payment->amount . " $\n\n";
    
    $message .= "=== ДАННЫЕ КЛИЕНТА ===\n";
    $message .= "Имя: " . $client_name . "\n";
    $message .= "Email: " . $client_email . "\n";
    $message .= "Телефон: " . ($client_phone ?: 'Не указан') . "\n\n";
    
    if($comment) {
        $message .= "=== КОММЕНТАРИЙ К ЗАКАЗУ ===\n";
        $message .= $comment . "\n\n";
    }
    
    $message .= "=== ДАННЫЕ ПЛАТЕЖА ===\n";
    $message .= "Метод: USDT TRC20\n";
    $message .= "Hash транзакции: " . $payment->transaction_hash . "\n\n";
    
    $message .= "Перейти в админку для проверки:\n";
    $message .= admin_url('admin.php?page=volumefx-payments&status=pending');
    
    wp_mail(get_option('admin_email'), $subject, $message);
}

// Уведомление клиенту о получении заказа
function volumefx_notify_client_service_order($payment_id, $client_email, $client_name) {
    $payment = VolumeFX_Payments::get_payment($payment_id);
    $service = get_post($payment->product_id);
    $tariff = get_post_meta($payment->product_id, 'order_' . $payment_id . '_tariff', true);
    
    $subject = 'Ваш заказ услуги принят #' . $payment_id;
    $message = "Здравствуйте, " . $client_name . "!\n\n";
    $message .= "Спасибо за ваш заказ. Мы получили вашу заявку и платеж.\n\n";
    
    $message .= "ДЕТАЛИ ЗАКАЗА:\n";
    $message .= "==================\n";
    $message .= "Номер заказа: #" . $payment_id . "\n";
    $message .= "Услуга: " . $service->post_title . "\n";
    $message .= "Тариф: " . $tariff . "\n";
    $message .= "Сумма: " . $payment->amount . " $\n\n";
    
    $message .= "СТАТУС: Проверка платежа\n\n";
    
    $message .= "Наш менеджер проверит ваш платеж и свяжется с вами в ближайшее время ";
    $message .= "для уточнения деталей и начала выполнения услуги.\n\n";
    
    $message .= "Если у вас есть вопросы, пожалуйста, свяжитесь с нами.\n\n";
    
    $message .= "С уважением,\n";
    $message .= get_bloginfo('name');
    
    wp_mail($client_email, $subject, $message);
}

// Добавляем тип "service" в систему платежей
add_filter('volumefx_payment_types', 'add_service_payment_type');
function add_service_payment_type($types) {
    $types['service'] = 'Заказ услуги';
    return $types;
}


// AJAX обработчик для получения деталей заказа услуги
add_action('wp_ajax_get_service_order_details', 'volumefx_ajax_get_service_order_details');
function volumefx_ajax_get_service_order_details() {
    check_ajax_referer('admin_service_nonce', 'nonce');
    
    if(!current_user_can('manage_options')) {
        wp_send_json_error('Недостаточно прав');
    }
    
    $payment_id = intval($_POST['payment_id']);
    $service_id = intval($_POST['service_id']);
    
    // Получаем данные платежа
    $payment = VolumeFX_Payments::get_payment($payment_id);
    $service = get_post($service_id);
    
    // Получаем метаданные заказа
    $client_name = get_post_meta($service_id, 'order_' . $payment_id . '_client_name', true);
    $client_email = get_post_meta($service_id, 'order_' . $payment_id . '_client_email', true);
    $client_phone = get_post_meta($service_id, 'order_' . $payment_id . '_client_phone', true);
    $comment = get_post_meta($service_id, 'order_' . $payment_id . '_comment', true);
    $tariff = get_post_meta($service_id, 'order_' . $payment_id . '_tariff', true);
    $period = get_post_meta($service_id, 'order_' . $payment_id . '_period', true);
    
    // Формируем HTML
    ob_start();
    ?>
    <div class="service-order-details">
        <h4>Информация о заказе #<?php echo $payment_id; ?></h4>
        
        <table class="widefat">
            <tr>
                <th>Услуга:</th>
                <td><?php echo esc_html($service->post_title); ?></td>
            </tr>
            <tr>
                <th>Тариф:</th>
                <td><?php echo esc_html($tariff); ?> <?php if($period): ?>(<?php echo esc_html($period); ?>)<?php endif; ?></td>
            </tr>
            <tr>
                <th>Сумма:</th>
                <td><?php echo $payment->amount; ?> $ (<?php echo $payment->crypto_amount; ?>)</td>
            </tr>
            <tr>
                <th>Статус:</th>
                <td>
                    <?php
                    $status_labels = array(
                        'pending' => '<span style="color: orange;">Ожидает проверки</span>',
                        'completed' => '<span style="color: green;">Выполнен</span>',
                        'rejected' => '<span style="color: red;">Отклонен</span>'
                    );
                    echo $status_labels[$payment->status] ?? $payment->status;
                    ?>
                </td>
            </tr>
        </table>
        
        <h4 style="margin-top: 20px;">Данные клиента</h4>
        <table class="widefat">
            <tr>
                <th>Имя:</th>
                <td><?php echo esc_html($client_name); ?></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><a href="mailto:<?php echo esc_attr($client_email); ?>"><?php echo esc_html($client_email); ?></a></td>
            </tr>
            <tr>
                <th>Телефон:</th>
                <td>
                    <?php if($client_phone): ?>
                        <a href="tel:<?php echo esc_attr($client_phone); ?>"><?php echo esc_html($client_phone); ?></a>
                    <?php else: ?>
                        <em>Не указан</em>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        
        <?php if($comment): ?>
        <h4 style="margin-top: 20px;">Комментарий к заказу</h4>
        <div style="background: #f5f5f5; padding: 10px; border-radius: 4px;">
            <?php echo nl2br(esc_html($comment)); ?>
        </div>
        <?php endif; ?>
        
        <h4 style="margin-top: 20px;">Данные платежа</h4>
        <table class="widefat">
            <tr>
                <th>Метод оплаты:</th>
                <td>USDT TRC20</td>
            </tr>
            <tr>
                <th>Hash транзакции:</th>
                <td style="word-break: break-all;">
                    <code><?php echo esc_html($payment->transaction_hash); ?></code>
                </td>
            </tr>
            <tr>
                <th>Дата заказа:</th>
                <td><?php echo date('d.m.Y H:i', strtotime($payment->created_at)); ?></td>
            </tr>
        </table>
        
        <?php if($payment->admin_note): ?>
        <h4 style="margin-top: 20px;">Примечание администратора</h4>
        <div style="background: #fff3cd; padding: 10px; border-radius: 4px; border: 1px solid #ffeaa7;">
            <?php echo nl2br(esc_html($payment->admin_note)); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
    $html = ob_get_clean();
    
    wp_send_json_success($html);
}

// AJAX обработчики для личного кабинета
add_action('wp_ajax_buy_with_balance', 'volumefx_ajax_buy_with_balance');
function volumefx_ajax_buy_with_balance() {
    check_ajax_referer('dashboard_nonce', 'nonce');
    
    $user_id = get_current_user_id();
    if(!$user_id) {
        wp_send_json_error('Необходима авторизация');
    }
    
    $product_id = intval($_POST['product_id']);
    $price = floatval($_POST['price']);
    $account_number = sanitize_text_field($_POST['account_number']);
    $period = intval($_POST['period']);
    
    $user_balance = VolumeFX_Payments::get_user_balance($user_id);
    
    if ($user_balance >= $price) {
        // Списываем с баланса
        if (VolumeFX_Payments::deduct_balance($user_id, $price)) {
            // Создаем запись о платеже
            $payment_id = VolumeFX_Payments::create_payment(array(
                'user_id' => $user_id,
                'type' => 'subscription',
                'product_id' => $product_id,
                'amount' => $price,
                'status' => 'completed'
            ));
            
            // Сохраняем данные для активации
            update_post_meta($product_id, 'payment_account_' . $payment_id, $account_number);
            update_post_meta($product_id, 'payment_period_' . $payment_id, $period);
            
            // Активируем подписку через API
            $api = new LaTradeAPI();
            $indicator_slug = get_field('product_indicator_slug', $product_id);
            $indicator_id = $api->getIndicatorId($indicator_slug);
            
            if ($indicator_id) {
                $end_date = date('Y-m-d', strtotime("+{$period} days"));
                
                $result = $api->updateUserSubscription(
                    $user_id,
                    $indicator_id,
                    $account_number,
                    $end_date
                );
                
                wp_send_json_success('Подписка успешно активирована');
            }
        } else {
            wp_send_json_error('Недостаточно средств на балансе');
        }
    } else {
        wp_send_json_error('Недостаточно средств на балансе');
    }
}

add_action('wp_ajax_submit_crypto_payment', 'volumefx_ajax_submit_crypto_payment');
function volumefx_ajax_submit_crypto_payment() {
    check_ajax_referer('dashboard_nonce', 'nonce');
    
    $user_id = get_current_user_id();
    if(!$user_id) {
        wp_send_json_error('Необходима авторизация');
    }
    
    $payment_data = array(
        'user_id' => $user_id,
        'type' => 'subscription',
        'product_id' => intval($_POST['product_id']),
        'amount' => floatval($_POST['price']),
        'crypto_amount' => floatval($_POST['price']) . ' USDT',
        'transaction_hash' => sanitize_text_field($_POST['transaction_hash'])
    );
    
    $payment_id = VolumeFX_Payments::create_payment($payment_data);
    
    if($payment_id) {
        // Сохраняем данные для будущей активации
        update_post_meta($payment_data['product_id'], 'payment_account_' . $payment_id, sanitize_text_field($_POST['account_number']));
        update_post_meta($payment_data['product_id'], 'payment_period_' . $payment_id, intval($_POST['period']));
        
        // Отправляем уведомление админу
        volumefx_notify_admin_new_payment($payment_id);
        
        wp_send_json_success('Платеж отправлен на проверку. Администратор проверит его в ближайшее время.');
    } else {
        wp_send_json_error('Ошибка при создании платежа');
    }
}

// AJAX обработчик для обновления профиля
add_action('wp_ajax_update_user_profile', 'volumefx_update_user_profile');
function volumefx_update_user_profile() {
    check_ajax_referer('dashboard_nonce', 'nonce');
    
    $user_id = get_current_user_id();
    
    if($user_id) {
        wp_update_user(array(
            'ID' => $user_id,
            'first_name' => sanitize_text_field($_POST['first_name']),
            'last_name' => sanitize_text_field($_POST['last_name']),
        ));
        
        update_user_meta($user_id, 'phone', sanitize_text_field($_POST['phone']));
        
        wp_send_json_success('Профиль обновлен');
    }
    
    wp_send_json_error('Ошибка обновления');
}

// Сохранение истории заказов
function volumefx_save_order_history($user_id, $order_data) {
    $history = get_user_meta($user_id, 'order_history', true);
    if(!is_array($history)) {
        $history = array();
    }
    
    $history[] = array(
        'date' => current_time('mysql'),
        'product' => $order_data['product'],
        'period' => $order_data['period'],
        'amount' => $order_data['amount'],
        'status' => 'completed',
        'status_text' => 'Оплачено'
    );
    
    update_user_meta($user_id, 'order_history', $history);
}


// Обработка платежа и активация подписки
add_action('init', 'volumefx_process_payment');
function volumefx_process_payment() {
    if(isset($_GET['payment_success']) && $_GET['payment_success'] == '1') {
        if(!is_user_logged_in()) return;
        
        $user_id = get_current_user_id();
        $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
        $period = isset($_GET['period']) ? intval($_GET['period']) : 30;
        $account_number = isset($_GET['account']) ? sanitize_text_field($_GET['account']) : '';
        
        if($product_id && $account_number) {
            $api = new LaTradeAPI();
            $indicator_slug = get_field('product_indicator_slug', $product_id);
            $indicator_id = $api->getIndicatorId($indicator_slug);
            
            if($indicator_id) {
                $end_date = date('Y-m-d', strtotime("+{$period} days"));
                
                $result = $api->updateUserSubscription(
                    $user_id,
                    $indicator_id,
                    $account_number,
                    $end_date
                );
                
                if($result && $result['code'] == 200) {
                    // Сохраняем в историю
                    volumefx_save_order_history($user_id, array(
                        'product' => get_the_title($product_id),
                        'period' => $period . ' дней',
                        'amount' => $_GET['amount'] ?? 0
                    ));
                    
                    // Перенаправляем в личный кабинет
                    wp_redirect(home_url('/dashboard?activated=1'));
                    exit;
                }
            }
        }
    }
}






// Уведомление админу о новом платеже
function volumefx_notify_admin_new_payment($payment_id) {
    $payment = VolumeFX_Payments::get_payment($payment_id);
    $user = get_userdata($payment->user_id);
    
    $subject = 'Новый платеж #' . $payment_id;
    $message = sprintf(
        "Новый платеж от пользователя %s (%s)\n\n" .
        "Тип: %s\n" .
        "Сумма: %s %s\n" .
        "Крипто: %s\n" .
        "Hash: %s\n\n" .
        "Перейти в админку: %s",
        $user->display_name,
        $user->user_email,
        $payment->type,
        $payment->amount,
        $payment->currency,
        $payment->crypto_amount,
        $payment->transaction_hash,
        admin_url('admin.php?page=volumefx-payments&status=pending')
    );
    
    wp_mail(get_option('admin_email'), $subject, $message);
}

// Уведомление пользователю об одобрении
function volumefx_notify_user_payment_approved($payment) {
    $user = get_userdata($payment->user_id);
    
    $subject = 'Ваш платеж подтвержден';
    $message = sprintf(
        "Здравствуйте, %s!\n\n" .
        "Ваш платеж #%d на сумму %s %s успешно подтвержден.\n\n" .
        "Спасибо за покупку!",
        $user->display_name,
        $payment->id,
        $payment->amount,
        $payment->currency
    );
    
    wp_mail($user->user_email, $subject, $message);
}

// Уведомление пользователю об отклонении
function volumefx_notify_user_payment_rejected($payment) {
    $user = get_userdata($payment->user_id);
    
    $subject = 'Ваш платеж отклонен';
    $message = sprintf(
        "Здравствуйте, %s!\n\n" .
        "Ваш платеж #%d на сумму %s %s был отклонен.\n\n" .
        "Причина: %s\n\n" .
        "Если у вас есть вопросы, пожалуйста, свяжитесь с поддержкой.",
        $user->display_name,
        $payment->id,
        $payment->amount,
        $payment->currency,
        $payment->admin_note ?: 'Не указана'
    );
    
    wp_mail($user->user_email, $subject, $message);
}

// AJAX обработчик для управления подписками пользователя (админ) - СТАРАЯ ВЕРСИЯ
add_action('wp_ajax_admin_manage_user_subscription', 'volumefx_admin_manage_user_subscription');
function volumefx_admin_manage_user_subscription() {
    check_ajax_referer('volumefx_admin_nonce', 'nonce');
    
    if(!current_user_can('manage_options')) {
        wp_send_json_error('Недостаточно прав');
    }
    
    $user_id = intval($_POST['user_id']);
    $indicator_id = intval($_POST['indicator_id']);
    $account_number = sanitize_text_field($_POST['account_number']);
    $date_end = sanitize_text_field($_POST['date_end']);
    $balance_change = floatval($_POST['balance_change']);
    $admin_note = sanitize_textarea_field($_POST['admin_note']);
    
    $success = true;
    $messages = array();
    
    // Обновляем подписку через API
    if($indicator_id && $account_number && $date_end) {
        $api = new LaTradeAPI();
        $result = $api->updateUserSubscription($user_id, $indicator_id, $account_number, $date_end);
        
        if($result && $result['code'] == 200) {
            $messages[] = 'Подписка успешно обновлена';
        } else {
            $success = false;
            $messages[] = 'Ошибка при обновлении подписки через API';
        }
    }
    
    // Изменяем баланс
    if($balance_change != 0) {
        if($balance_change > 0) {
            VolumeFX_Payments::add_user_balance($user_id, $balance_change);
            $messages[] = 'Баланс пополнен на ' . $balance_change . ' $';
            
            // Создаем запись о пополнении
            VolumeFX_Payments::create_payment(array(
                'user_id' => $user_id,
                'type' => 'balance',
                'amount' => $balance_change,
                'status' => 'completed',
                'admin_note' => 'Ручное пополнение администратором. ' . $admin_note
            ));
        } else {
            $deducted = VolumeFX_Payments::deduct_balance($user_id, abs($balance_change));
            if($deducted) {
                $messages[] = 'С баланса списано ' . abs($balance_change) . ' $';
            } else {
                $success = false;
                $messages[] = 'Недостаточно средств для списания';
            }
        }
    }
    
    if($success) {
        wp_send_json_success(implode('. ', $messages));
    } else {
        wp_send_json_error(implode('. ', $messages));
    }
}

// ============= НОВЫЕ AJAX ОБРАБОТЧИКИ ДЛЯ АДМИНКИ =============

// AJAX обработчик для управления подписками (добавление/изменение)
add_action('wp_ajax_admin_manage_subscription', 'volumefx_admin_manage_subscription');
function volumefx_admin_manage_subscription() {
    check_ajax_referer('volumefx_admin_nonce', 'nonce');
    
    if(!current_user_can('manage_options')) {
        wp_send_json_error('Недостаточно прав');
    }
    
    $user_id = intval($_POST['user_id']);
    $indicator_id = intval($_POST['indicator_id']);
    $account_number = sanitize_text_field($_POST['account_number']);
    $date_end = sanitize_text_field($_POST['date_end']);
    $action_type = sanitize_text_field($_POST['sub_action']);
    
    if(!$user_id || !$indicator_id || !$account_number || !$date_end) {
        wp_send_json_error('Заполните все обязательные поля');
    }
    
    $api = new LaTradeAPI();
    
    // Обновляем или добавляем подписку через API
    $result = $api->updateUserSubscription($user_id, $indicator_id, $account_number, $date_end);
    
    if($result && $result['code'] == 200) {
        $message = $action_type === 'add' ? 'Подписка успешно добавлена' : 'Подписка успешно обновлена';
        
        // Создаем запись в истории платежей для отслеживания
        if($action_type === 'add') {
            VolumeFX_Payments::create_payment(array(
                'user_id' => $user_id,
                'type' => 'subscription',
                'amount' => 0,
                'status' => 'completed',
                'admin_note' => 'Подписка добавлена администратором. Индикатор: ' . $indicator_id . ', Счет: ' . $account_number
            ));
        }
        
        wp_send_json_success($message);
    } else {
        $error_message = 'Ошибка при обновлении подписки через API';
        if($result && isset($result['data']['message'])) {
            $error_message .= ': ' . $result['data']['message'];
        }
        wp_send_json_error($error_message);
    }
}


// AJAX обработчик для остановки подписки
add_action('wp_ajax_admin_stop_subscription', 'volumefx_admin_stop_subscription');
function volumefx_admin_stop_subscription() {
    check_ajax_referer('volumefx_admin_nonce', 'nonce');
    
    if(!current_user_can('manage_options')) {
        wp_send_json_error('Недостаточно прав');
    }
    
    $user_id = intval($_POST['user_id']);
    $indicator_id = intval($_POST['indicator_id']);
    $account_number = isset($_POST['account_number']) ? sanitize_text_field($_POST['account_number']) : '';
    
    if(!$user_id || !$indicator_id) {
        wp_send_json_error('Недостаточно данных');
    }
    
    $api = new LaTradeAPI();
    
    // Если номер счета не передан, получаем его из API
    if(empty($account_number)) {
        $user_data = $api->getUserInfo($user_id);
        
        if($user_data && isset($user_data['data']['subscriptions'])) {
            foreach($user_data['data']['subscriptions'] as $sub) {
                if($sub['indicator_id'] == $indicator_id) {
                    $account_number = $sub['account_number'];
                    break;
                }
            }
        }
        
        if(empty($account_number)) {
            wp_send_json_error('Не удалось найти номер счета для данной подписки');
        }
    }
    
    // Устанавливаем дату окончания на вчера
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    // Обновляем подписку через API
    $result = $api->updateUserSubscription($user_id, $indicator_id, $account_number, $yesterday);
    
    if($result && $result['code'] == 200) {
        // Создаем запись в истории
        VolumeFX_Payments::create_payment(array(
            'user_id' => $user_id,
            'type' => 'subscription',
            'amount' => 0,
            'status' => 'completed',
            'admin_note' => 'Подписка остановлена администратором. Индикатор ID: ' . $indicator_id . ', Счет: ' . $account_number
        ));
        
        wp_send_json_success('Подписка успешно остановлена. Дата окончания установлена на ' . $yesterday);
    } else {
        $error_msg = 'Ошибка при остановке подписки через API';
        if($result && isset($result['data']) && is_array($result['data'])) {
            if(isset($result['data']['message'])) {
                $error_msg .= ': ' . $result['data']['message'];
            } elseif(isset($result['data']['error'])) {
                $error_msg .= ': ' . $result['data']['error'];
            }
        }
        wp_send_json_error($error_msg);
    }
}

// AJAX обработчик для управления балансом
add_action('wp_ajax_admin_manage_user_balance', 'volumefx_admin_manage_user_balance');
function volumefx_admin_manage_user_balance() {
    check_ajax_referer('volumefx_admin_nonce', 'nonce');
    
    if(!current_user_can('manage_options')) {
        wp_send_json_error('Недостаточно прав');
    }
    
    $user_id = intval($_POST['user_id']);
    $balance_change = floatval($_POST['balance_change']);
    $admin_note = sanitize_textarea_field($_POST['admin_note']);
    
    if(!$user_id || $balance_change == 0) {
        wp_send_json_error('Недостаточно данных');
    }
    
    $messages = array();
    
    if($balance_change > 0) {
        // Пополнение баланса
        VolumeFX_Payments::add_user_balance($user_id, $balance_change);
        $messages[] = 'Баланс пополнен на ' . $balance_change . ' $';
        
        // Создаем запись о пополнении
        VolumeFX_Payments::create_payment(array(
            'user_id' => $user_id,
            'type' => 'balance',
            'amount' => $balance_change,
            'status' => 'completed',
            'admin_note' => 'Ручное пополнение администратором. ' . $admin_note
        ));
    } else {
        // Списание с баланса
        $deducted = VolumeFX_Payments::deduct_balance($user_id, abs($balance_change));
        if($deducted) {
            $messages[] = 'С баланса списано ' . abs($balance_change) . ' $';
            
            // Создаем запись о списании
            VolumeFX_Payments::create_payment(array(
                'user_id' => $user_id,
                'type' => 'balance',
                'amount' => -abs($balance_change),
                'status' => 'completed',
                'admin_note' => 'Ручное списание администратором. ' . $admin_note
            ));
        } else {
            wp_send_json_error('Недостаточно средств для списания');
        }
    }
    
    wp_send_json_success(implode('. ', $messages));
}