
<?php
/**
 * Template Name: Личный кабинет
 * 
 * @package VolumeFX
 */

// Проверка авторизации
if (!is_user_logged_in()) {
    wp_redirect(home_url('/auth'));
    exit;
}

get_header();

$current_user = wp_get_current_user();
$user_meta = get_user_meta($current_user->ID);

// Получаем API данные
$api = new LaTradeAPI();
$user_api_data = $api->getUserInfo($current_user->ID);

// Получаем баланс пользователя
$user_balance = VolumeFX_Payments::get_user_balance($current_user->ID);
$pending_payments = VolumeFX_Payments::get_user_payments($current_user->ID, 'pending');

// Получаем все продукты (индикаторы)
$products = get_posts(array(
    'post_type' => 'product',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC'
));

// Обработка покупки за баланс
if (isset($_POST['action']) && $_POST['action'] === 'buy_with_balance') {
    check_ajax_referer('dashboard_nonce', 'nonce');
    
    $product_id = intval($_POST['product_id']);
    $price = floatval($_POST['price']);
    $account_number = sanitize_text_field($_POST['account_number']);
    $period = intval($_POST['period']);
    
    if ($user_balance >= $price) {
        // Списываем с баланса
        if (VolumeFX_Payments::deduct_balance($current_user->ID, $price)) {
            // Создаем запись о платеже
            $payment_id = VolumeFX_Payments::create_payment(array(
                'user_id' => $current_user->ID,
                'type' => 'subscription',
                'product_id' => $product_id,
                'amount' => $price,
                'status' => 'completed'
            ));
            
            // Сохраняем данные для активации
            update_post_meta($product_id, 'payment_account_' . $payment_id, $account_number);
            update_post_meta($product_id, 'payment_period_' . $payment_id, $period);
            
            // Активируем подписку через API
            $indicator_slug = get_field('product_indicator_slug', $product_id);
            $indicator_id = $api->getIndicatorId($indicator_slug);
            
            if ($indicator_id) {
                $end_date = date('Y-m-d', strtotime("+{$period} days"));
                
                $result = $api->updateUserSubscription(
                    $current_user->ID,
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
    exit;
}

// Обработка управления подпиской
if (isset($_POST['action']) && $_POST['action'] === 'update_subscription') {
    check_ajax_referer('dashboard_nonce', 'nonce');
    
    $indicator_id = sanitize_text_field($_POST['indicator_id']);
    $account_number = sanitize_text_field($_POST['account_number']);
    $subscription_end = sanitize_text_field($_POST['subscription_end']);
    
    $result = $api->updateUserSubscription(
        $current_user->ID,
        $indicator_id,
        $account_number,
        $subscription_end
    );
    
    wp_send_json($result);
    exit;
}

// Обработка обновления профиля
if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    check_ajax_referer('dashboard_nonce', 'nonce');
    
    wp_update_user(array(
        'ID' => $current_user->ID,
        'first_name' => sanitize_text_field($_POST['first_name']),
        'last_name' => sanitize_text_field($_POST['last_name']),
        'display_name' => sanitize_text_field($_POST['first_name']) . ' ' . sanitize_text_field($_POST['last_name'])
    ));
    
    update_user_meta($current_user->ID, 'phone', sanitize_text_field($_POST['phone']));
    
    wp_send_json_success('Профиль обновлен');
    exit;
}

// Проверяем уведомления
$show_payment_sent = isset($_GET['payment_sent']) && $_GET['payment_sent'] == '1';
$show_activated = isset($_GET['activated']) && $_GET['activated'] == '1';
?>

<!-- page-title -->
<section class="page-title centred pt_90 pb_0">
    <div class="pattern-layer rotate-me" style="background-image: url(<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-34.png);"></div>
    <div class="auto-container">
        <div class="content-box">
            <h1>Личный кабинет</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="<?php echo home_url(); ?>">Главная</a></li>
                <li>Личный кабинет</li>
            </ul>
        </div>
    </div>
</section>
<!-- page-title end -->

<!-- dashboard-section -->
<section class="dashboard-section pt_90 pb_100">
    <div class="auto-container">
        <?php if($show_payment_sent): ?>
        <div class="alert alert-info mb_30">
            <i class="fas fa-info-circle"></i> Ваш платеж отправлен на проверку. Администратор проверит его в ближайшее время.
        </div>
        <?php endif; ?>
        
        <?php if($show_activated): ?>
        <div class="alert alert-success mb_30">
            <i class="fas fa-check-circle"></i> Подписка успешно активирована!
        </div>
        <?php endif; ?>
        
        <div class="row clearfix">
            <!-- Sidebar -->
            <div class="col-lg-3 col-md-12 col-sm-12 sidebar-side">
                <div class="dashboard-sidebar">
                    <div class="user-info-box">
                        <div class="user-avatar">
                            <?php echo get_avatar($current_user->ID, 100); ?>
                        </div>
                        <h4><?php echo esc_html($current_user->display_name); ?></h4>
                        <p><?php echo esc_html($current_user->user_email); ?></p>
                    </div>
                    
                    <ul class="dashboard-menu">
                        <li class="active"><a href="#subscriptions" data-tab="subscriptions"><i class="fas fa-chart-line"></i> Мои подписки</a></li>
                        <li><a href="#balance" data-tab="balance"><i class="fas fa-wallet"></i> Баланс</a></li>
                        <li><a href="#profile" data-tab="profile"><i class="fas fa-user"></i> Профиль</a></li>
                        <li><a href="#history" data-tab="history"><i class="fas fa-history"></i> История</a></li>
                        <li><a href="<?php echo wp_logout_url(home_url()); ?>"><i class="fas fa-sign-out-alt"></i> Выйти</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Content -->
            <div class="col-lg-9 col-md-12 col-sm-12 content-side">
                <!-- Подписки -->
                <div id="subscriptions" class="dashboard-content active">
                    <div class="content-header">
                        <h3>Мои подписки на индикаторы</h3>
                        <div class="header-info">
                            <div class="balance-info">
                                <span>Баланс: <strong><?php echo number_format($user_balance, 2); ?> $</strong></span>
                            </div>
                            <?php if($user_api_data && $user_api_data['code'] == 200 && isset($user_api_data['data']['balance'])): ?>
                            <div class="api-balance-info">
                                <span>API баланс: <strong><?php echo $user_api_data['data']['balance']; ?> дней</strong></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if($pending_payments && count($pending_payments) > 0): ?>
                    <div class="pending-notice mb_30">
                        <i class="fas fa-info-circle"></i> 
                        У вас есть <?php echo count($pending_payments); ?> платеж(а) на проверке
                    </div>
                    <?php endif; ?>
                    
                    <div class="subscriptions-grid">
                        <?php foreach($products as $product): 
                            $product_meta = get_field('product_indicator_slug', $product->ID);
                            $indicator_id = $api->getIndicatorId($product_meta);
                            $price_options = get_field('product_price_options', $product->ID);
                            
                            // Получаем минимальную цену
                            $min_price = PHP_FLOAT_MAX;
                            if($price_options) {
                                foreach($price_options as $option) {
                                    if($option['price_amount'] < $min_price) {
                                        $min_price = $option['price_amount'];
                                    }
                                }
                            }
                            
                            // Проверяем активную подписку
                            $active_subscription = null;
                            if($user_api_data && isset($user_api_data['data']['subscriptions'])) {
                                foreach($user_api_data['data']['subscriptions'] as $sub) {
                                    if($sub['indicator_id'] == $indicator_id) {
                                        $active_subscription = $sub;
                                        break;
                                    }
                                }
                            }
                        ?>
                        <div class="subscription-card <?php echo $active_subscription ? 'active' : ''; ?>">
                            <div class="card-header">
                                <h4><?php echo get_the_title($product->ID); ?></h4>
                                <?php if($active_subscription): ?>
                                    <span class="status-badge active">Активна до <?php echo date('d.m.Y', strtotime($active_subscription['date_end'])); ?></span>
                                <?php else: ?>
                                    <span class="status-badge">Не активна</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body">
                                <?php if($active_subscription): ?>
                                    <div class="subscription-info">
                                        <p><strong>Номер счета:</strong> <?php echo esc_html($active_subscription['account_number']); ?></p>
                                        <p><strong>Активна до:</strong> <?php echo date('d.m.Y', strtotime($active_subscription['date_end'])); ?></p>
                                        <p><strong>Осталось дней:</strong> <?php 
                                            $days_left = (strtotime($active_subscription['date_end']) - time()) / 86400;
                                            echo max(0, ceil($days_left));
                                        ?></p>
                                    </div>
                                    
                                    <button class="theme-btn btn-small manage-subscription" 
                                            data-product-id="<?php echo $product->ID; ?>"
                                            data-indicator-id="<?php echo $indicator_id; ?>"
                                            data-account="<?php echo esc_attr($active_subscription['account_number']); ?>"
                                            data-date="<?php echo esc_attr($active_subscription['date_end']); ?>">
                                        Управление подпиской
                                    </button>
                                <?php else: ?>
                                    <div class="price-options">
                                        <?php if($price_options): ?>
                                            <?php foreach($price_options as $option): ?>
                                                <div class="price-option">
                                                    <span><?php echo $option['price_period']; ?></span>
                                                    <strong><?php echo $option['price_amount']; ?> $</strong>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="card-actions">
                                        <?php if($user_balance >= $min_price): ?>
                                            <button class="theme-btn btn-small buy-with-balance" 
                                                    data-product-id="<?php echo $product->ID; ?>"
                                                    data-product-name="<?php echo esc_attr(get_the_title($product->ID)); ?>"
                                                    data-indicator-id="<?php echo $indicator_id; ?>">
                                                Купить за баланс
                                            </button>
                                        <?php endif; ?>
                                        
                                        <a href="<?php echo home_url('/payment?type=subscription&product_id=' . $product->ID . '&amount=' . $min_price); ?>" 
                                           class="theme-btn btn-small btn-outline">
                                            Оплатить картой
                                        </a>
                                        
                                        <a href="<?php echo get_permalink($product->ID); ?>" class="link-btn">
                                            Подробнее →
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Баланс -->
                <div id="balance" class="dashboard-content">
                    <div class="content-header">
                        <h3>Управление балансом</h3>
                    </div>
                    
                    <div class="balance-main-card">
                        <div class="balance-amount-large">
                            <?php echo number_format($user_balance, 2); ?> <span>$</span>
                        </div>
                        <p>Текущий баланс вашего аккаунта</p>
                        
                        <div class="balance-actions">
                            <a href="<?php echo home_url('/payment?type=balance'); ?>" class="theme-btn btn-one">
                                <i class="fas fa-plus-circle"></i> Пополнить баланс
                            </a>
                        </div>
                    </div>
                    
                    <div class="balance-history mt_40">
                        <h4>История операций</h4>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Дата</th>
                                    <th>Операция</th>
                                    <th>Сумма</th>
                                    <th>Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $all_payments = VolumeFX_Payments::get_user_payments($current_user->ID);
                                if($all_payments):
                                    foreach($all_payments as $payment):
                                ?>
                                <tr>
                                    <td><?php echo date('d.m.Y H:i', strtotime($payment->created_at)); ?></td>
                                    <td>
                                        <?php 
                                        if($payment->type === 'balance') {
                                            echo 'Пополнение баланса';
                                        } else {
                                            $product = get_post($payment->product_id);
                                            echo 'Покупка: ' . ($product ? $product->post_title : 'N/A');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if($payment->type === 'balance'): ?>
                                            <span class="amount-plus">+<?php echo $payment->amount; ?> $</span>
                                        <?php else: ?>
                                            <span class="amount-minus">-<?php echo $payment->amount; ?> $</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status_classes = array(
                                            'pending' => 'status-pending',
                                            'completed' => 'status-success',
                                            'rejected' => 'status-danger'
                                        );
                                        $status_texts = array(
                                            'pending' => 'На проверке',
                                            'completed' => 'Выполнено',
                                            'rejected' => 'Отклонено'
                                        );
                                        ?>
                                        <span class="status-badge <?php echo $status_classes[$payment->status]; ?>">
                                            <?php echo $status_texts[$payment->status]; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php 
                                    endforeach;
                                else:
                                ?>
                                <tr>
                                    <td colspan="4" class="text-center">История операций пуста</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Профиль -->
                <div id="profile" class="dashboard-content">
                    <div class="content-header">
                        <h3>Настройки профиля</h3>
                    </div>
                    
                    <form id="profile-form" class="profile-form">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Имя</label>
                                    <input type="text" name="first_name" value="<?php echo esc_attr($current_user->first_name); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Фамилия</label>
                                    <input type="text" name="last_name" value="<?php echo esc_attr($current_user->last_name); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" value="<?php echo esc_attr($current_user->user_email); ?>" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Телефон</label>
                                    <input type="tel" name="phone" value="<?php echo esc_attr(get_user_meta($current_user->ID, 'phone', true)); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <button type="submit" class="theme-btn btn-one">Сохранить изменения</button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- История -->
                <div id="history" class="dashboard-content">
                    <div class="content-header">
                        <h3>История заказов</h3>
                    </div>
                    
                    <div class="history-table">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>№</th>
                                    <th>Дата</th>
                                    <th>Описание</th>
                                    <th>Сумма</th>
                                    <th>Статус</th>
                                    <th>Детали</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $orders = VolumeFX_Payments::get_user_payments($current_user->ID);
                                if($orders):
                                    foreach($orders as $order):
                                ?>
                                <tr>
                                    <td>#<?php echo $order->id; ?></td>
                                    <td><?php echo date('d.m.Y', strtotime($order->created_at)); ?></td>
                                    <td>
                                        <?php 
                                        if($order->type === 'balance') {
                                            echo 'Пополнение баланса';
                                        } else {
                                            $product = get_post($order->product_id);
                                            echo $product ? $product->post_title : 'Подписка';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo number_format($order->amount, 2); ?> $</td>
                                    <td>
                                        <?php
                                        $status_texts = array(
                                            'pending' => 'На проверке',
                                            'completed' => 'Выполнено',
                                            'rejected' => 'Отклонено'
                                        );
                                        ?>
                                        <span class="status-badge status-<?php echo $order->status; ?>">
                                            <?php echo $status_texts[$order->status]; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($order->transaction_hash): ?>
                                            <small>Hash: <?php echo substr($order->transaction_hash, 0, 10); ?>...</small>
                                        <?php endif; ?>
                                        <?php if($order->admin_note && $order->status === 'rejected'): ?>
                                            <br><small class="text-danger"><?php echo esc_html($order->admin_note); ?></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php 
                                    endforeach;
                                else:
                                ?>
                                <tr>
                                    <td colspan="6" class="text-center">История заказов пуста</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Модальное окно управления подпиской -->
<div id="subscription-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Управление подпиской</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <form id="subscription-form">
                <input type="hidden" id="indicator_id" name="indicator_id">
                
                <div class="form-group">
                    <label>Номер торгового счета</label>
                    <input type="text" id="account_number" name="account_number" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Дата окончания подписки</label>
                    <input type="date" id="subscription_end" name="subscription_end" class="form-control" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="theme-btn btn-one">Сохранить</button>
                    <button type="button" class="theme-btn btn-two cancel-btn">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно покупки за баланс -->
<div id="buy-balance-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Покупка подписки</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <p class="mb_20">Вы покупаете: <strong id="product-name"></strong></p>
            
            <form id="buy-balance-form">
                <input type="hidden" id="buy_product_id" name="product_id">
                <input type="hidden" id="buy_indicator_id" name="indicator_id">
                
                <div class="form-group">
                    <label>Номер торгового счета</label>
                    <input type="text" name="account_number" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Период подписки</label>
                    <select name="period" class="form-control" id="period-select" required>
                        <?php
                        // Здесь нужно динамически подгружать опции в зависимости от продукта
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Стоимость</label>
                    <div class="price-display">
                        <span id="selected-price">0</span> $
                    </div>
                </div>
                
                <div class="balance-info-modal">
                    <p>Ваш баланс: <strong><?php echo number_format($user_balance, 2); ?> $</strong></p>
                    <p id="balance-after">После покупки: <strong>0 $</strong></p>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="theme-btn btn-one">Купить</button>
                    <button type="button" class="theme-btn btn-two cancel-btn">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Переключение вкладок
    $('.dashboard-menu a').on('click', function(e) {
        var tab = $(this).data('tab');
        
        if(tab) {
            e.preventDefault();
            
            $('.dashboard-menu li').removeClass('active');
            $(this).parent().addClass('active');
            
            $('.dashboard-content').removeClass('active');
            $('#' + tab).addClass('active');
            
            // Обновляем URL без перезагрузки
            history.pushState(null, '', '#' + tab);
        }
    });
    
    // Проверяем hash при загрузке
    if(window.location.hash) {
        var hash = window.location.hash.substring(1);
        $('.dashboard-menu a[data-tab="' + hash + '"]').click();
    }
    
    // Управление подпиской
    $('.manage-subscription').on('click', function() {
        var indicatorId = $(this).data('indicator-id');
        var account = $(this).data('account');
        var date = $(this).data('date');
        
        $('#indicator_id').val(indicatorId);
        $('#account_number').val(account);
        $('#subscription_end').val(date);
        
        $('#subscription-modal').fadeIn();
    });
    
    // Покупка за баланс
    $('.buy-with-balance').on('click', function() {
        var productId = $(this).data('product-id');
        var productName = $(this).data('product-name');
        var indicatorId = $(this).data('indicator-id');
        
        $('#buy_product_id').val(productId);
        $('#buy_indicator_id').val(indicatorId);
        $('#product-name').text(productName);
        
        // Загружаем опции цен для продукта
        loadPriceOptions(productId);
        
        $('#buy-balance-modal').fadeIn();
    });
    
    // Загрузка опций цен
    function loadPriceOptions(productId) {
        // Здесь нужно сделать AJAX запрос для получения цен продукта
        // Пока используем данные из PHP
        var priceOptions = <?php 
            $all_price_options = array();
            foreach($products as $p) {
                $opts = get_field('product_price_options', $p->ID);
                if($opts) {
                    $all_price_options[$p->ID] = $opts;
                }
            }
            echo json_encode($all_price_options);
        ?>;
        
        if(priceOptions[productId]) {
            var select = $('#period-select');
            select.empty();
            
            $.each(priceOptions[productId], function(i, option) {
                var days = 30; // По умолчанию
                if(option.price_period.includes('3')) days = 90;
                if(option.price_period.includes('6')) days = 180;
                
                select.append($('<option>', {
                    value: days,
                    text: option.price_period + ' - ' + option.price_amount + ' $',
                    'data-price': option.price_amount
                }));
            });
            
            updateSelectedPrice();
        }
    }
    
    // Обновление выбранной цены
    $('#period-select').on('change', function() {
        updateSelectedPrice();
    });
    
    function updateSelectedPrice() {
        var selectedOption = $('#period-select option:selected');
        var price = parseFloat(selectedOption.data('price'));
        var currentBalance = <?php echo $user_balance; ?>;
        var afterBalance = currentBalance - price;
        
        $('#selected-price').text(price.toFixed(2));
        $('#balance-after').text(afterBalance.toFixed(2) + ' $');
        
        if(afterBalance < 0) {
            $('#balance-after').addClass('text-danger');
            $('#buy-balance-form button[type="submit"]').prop('disabled', true);
        } else {
            $('#balance-after').removeClass('text-danger');
            $('#buy-balance-form button[type="submit"]').prop('disabled', false);
        }
    }
    
    // Закрытие модальных окон
    $('.close, .cancel-btn').on('click', function() {
        $('.modal').fadeOut();
    });
    
    // Клик вне модального окна
    $(window).on('click', function(e) {
        if($(e.target).hasClass('modal')) {
            $('.modal').fadeOut();
        }
    });
    
    // Отправка формы управления подпиской
    $('#subscription-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        formData += '&action=update_subscription&nonce=<?php echo wp_create_nonce("dashboard_nonce"); ?>';
        
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: formData,
            success: function(response) {
                if(response.code === 200) {
                    alert('Подписка успешно обновлена!');
                    location.reload();
                } else {
                    alert('Ошибка при обновлении подписки');
                }
            }
        });
    });
    
    // Отправка формы покупки за баланс
    $('#buy-balance-form').on('submit', function(e) {
        e.preventDefault();
        
        var selectedOption = $('#period-select option:selected');
        var price = parseFloat(selectedOption.data('price'));
        
        var formData = $(this).serialize();
        formData += '&price=' + price;
        formData += '&action=buy_with_balance&nonce=<?php echo wp_create_nonce("dashboard_nonce"); ?>';
        
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    alert(response.data);
                    location.reload();
                } else {
                    alert(response.data || 'Произошла ошибка');
                }
            },
            error: function() {
                alert('Произошла ошибка при обработке запроса');
            }
        });
    });
    
    // Обновление профиля
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        formData += '&action=update_profile&nonce=<?php echo wp_create_nonce("dashboard_nonce"); ?>';
        
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    alert(response.data);
                } else {
                    alert('Ошибка при обновлении профиля');
                }
            }
        });
    });
});
</script>

<style>
/* Дополнительные стили для личного кабинета */
.header-info {
    display: flex;
    gap: 20px;
    align-items: center;
}

.api-balance-info {
    background: #e3f2fd;
    padding: 10px 20px;
    border-radius: 8px;
    color: #1976d2;
}

.pending-notice {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
    padding: 15px 20px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.balance-main-card {
    background: #fff;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.balance-amount-large {
    font-size: 48px;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.balance-amount-large span {
    font-size: 24px;
    font-weight: 400;
}

.balance-actions {
    margin-top: 30px;
}

.dashboard-table {
    width: 100%;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
}

.dashboard-table th {
    background: #f5f7fa;
    padding: 15px;
    font-weight: 600;
    text-align: left;
}

.dashboard-table td {
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.amount-plus {
    color: #4caf50;
    font-weight: 600;
}

.amount-minus {
    color: #f44336;
    font-weight: 600;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-success,
.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-danger,
.status-rejected {
    background: #f8d7da;
    color: #721c24;
}

.card-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.theme-btn.btn-outline {
    background: transparent;
    border: 2px solid #1a73e8;
    color: #1a73e8;
}

.theme-btn.btn-outline:hover {
    background: #1a73e8;
    color: #fff;
}

.link-btn {
    text-align: center;
    color: #1a73e8;
    text-decoration: none;
    font-size: 14px;
}

.link-btn:hover {
    text-decoration: underline;
}

.price-display {
    font-size: 24px;
    font-weight: 700;
    color: #333;
}

.balance-info-modal {
    background: #f5f7fa;
    padding: 15px;
    border-radius: 8px;
    margin: 20px 0;
}

.balance-info-modal p {
    margin: 5px 0;
}

#balance-after.text-danger {
    color: #dc3545 !important;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-info {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
</style>

<?php get_footer(); ?>