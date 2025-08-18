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

// AJAX обработчик для управления подписками пользователя (админ)
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




// ACF поля для страницы с шаблоном "Страница услуг"
add_action('acf/init', 'volumefx_add_service_page_fields');
function volumefx_add_service_page_fields() {
    if(function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_service_page_fields',
            'title' => 'Настройки страницы услуг',
            'fields' => array(
                array(
                    'key' => 'field_service_page_price',
                    'label' => 'Цена услуги',
                    'name' => 'service_page_price',
                    'type' => 'number',
                    'default_value' => 100,
                    'min' => 0,
                    'prepend' => '$',
                    'instructions' => 'Укажите стоимость услуги в долларах',
                ),
                array(
                    'key' => 'field_service_page_period',
                    'label' => 'Период/Срок (опционально)',
                    'name' => 'service_page_period',
                    'type' => 'text',
                    'placeholder' => 'Например: единоразово, 1 месяц, 1 год',
                    'instructions' => 'Оставьте пустым, если не требуется',
                ),
                array(
                    'key' => 'field_service_page_sidebar_text',
                    'label' => 'Текст в сайдбаре',
                    'name' => 'service_page_sidebar_text',
                    'type' => 'wysiwyg',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                    'instructions' => 'Этот текст будет отображаться в сайдбаре над кнопкой покупки',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'page_template',
                        'operator' => '==',
                        'value' => 'page-services.php',
                    ),
                ),
            ),
        ));
    }
}

// AJAX обработчик для оплаты услуги со страницы
add_action('wp_ajax_submit_service_page_payment', 'volumefx_ajax_submit_service_page_payment');
function volumefx_ajax_submit_service_page_payment() {
    check_ajax_referer('service_payment_nonce', 'nonce');
    
    $user_id = get_current_user_id();
    if(!$user_id) {
        wp_send_json_error('Необходима авторизация');
    }
    
    // Создаем запись о платеже
    $payment_data = array(
        'user_id' => $user_id,
        'type' => 'service',
        'product_id' => intval($_POST['page_id']), // ID страницы
        'amount' => floatval($_POST['amount']),
        'crypto_amount' => floatval($_POST['amount']) . ' USDT',
        'transaction_hash' => sanitize_text_field($_POST['transaction_hash']),
        'status' => 'pending'
    );
    
    // Используем существующий класс для создания платежа
    $payment_id = VolumeFX_Payments::create_payment($payment_data);
    
    if($payment_id) {
        // Сохраняем название услуги
        update_post_meta($payment_data['product_id'], 'service_payment_name_' . $payment_id, sanitize_text_field($_POST['service_name']));
        
        // Отправляем уведомление администратору
        volumefx_notify_admin_service_page_payment($payment_id);
        
        wp_send_json_success('Платеж отправлен на проверку. Администратор свяжется с вами в ближайшее время.');
    } else {
        wp_send_json_error('Ошибка при создании платежа. Попробуйте еще раз.');
    }
}

// Уведомление админу о новой оплате услуги со страницы
function volumefx_notify_admin_service_page_payment($payment_id) {
    $payment = VolumeFX_Payments::get_payment($payment_id);
    $user = get_userdata($payment->user_id);
    $service_name = get_post_meta($payment->product_id, 'service_payment_name_' . $payment_id, true);
    
    $subject = 'Новая оплата услуги #' . $payment_id;
    $message = sprintf(
        "Новая оплата услуги от пользователя %s (%s)\n\n" .
        "Услуга: %s\n" .
        "Сумма: %s %s\n" .
        "Крипто: %s\n" .
        "Hash транзакции: %s\n\n" .
        "Телефон пользователя: %s\n\n" .
        "Перейти в админку: %s",
        $user->display_name,
        $user->user_email,
        $service_name,
        $payment->amount,
        $payment->currency,
        $payment->crypto_amount,
        $payment->transaction_hash,
        get_user_meta($user->ID, 'phone', true) ?: 'Не указан',
        admin_url('admin.php?page=volumefx-payments&status=pending')
    );
    
    wp_mail(get_option('admin_email'), $subject, $message);
}

// Добавляем глобальную настройку для адреса кошелька (если еще не добавлена)
add_action('acf/init', 'volumefx_add_crypto_wallet_settings');
function volumefx_add_crypto_wallet_settings() {
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
                        'default_value' => 'TQEQHJRLz1HUQcaJtfAgh7jWi1SiE2cpJT',
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

// Обновляем отображение типа платежа в админке
add_filter('volumefx_payment_type_display', 'volumefx_service_page_payment_type_display', 10, 2);
function volumefx_service_page_payment_type_display($display, $payment) {
    if($payment->type === 'service') {
        // Проверяем, является ли это страницей
        $post_type = get_post_type($payment->product_id);
        if($post_type === 'page') {
            $service_name = get_post_meta($payment->product_id, 'service_payment_name_' . $payment->id, true);
            return 'Услуга: ' . ($service_name ?: 'Услуга');
        }
    }
    return $display;
}