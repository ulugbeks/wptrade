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
    wp_enqueue_script('fxfortrader-main', get_template_directory_uri() . '/assets/js/script.js', array('jquery'), '', true);
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
    if(function_exists('acf_add_local_field')) {
        acf_add_local_field(array(
            'key' => 'field_product_indicator_slug',
            'label' => 'Slug индикатора для API',
            'name' => 'product_indicator_slug',
            'type' => 'select',
            'parent' => 'group_products',
            'choices' => array(
                'volatility_levels' => 'Volatility Levels',
                'fibo_musang' => 'Fibo Musang',
                'future_volume' => 'Future Volume',
                'options_fx' => 'Options FX',
            ),
        ));
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


