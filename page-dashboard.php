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
                            if($price_options && is_array($price_options)) {
                                foreach($price_options as $option) {
                                    if(isset($option['price_amount']) && $option['price_amount'] < $min_price) {
                                        $min_price = $option['price_amount'];
                                    }
                                }
                            }
                            
                            // Если цены не настроены, используем дефолтную
                            if($min_price == PHP_FLOAT_MAX) {
                                $min_price = 199;
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
                                        
                                        <button class="theme-btn btn-small btn-outline buy-with-crypto" 
                                                data-product-id="<?php echo $product->ID; ?>"
                                                data-product-name="<?php echo esc_attr(get_the_title($product->ID)); ?>"
                                                data-indicator-id="<?php echo $indicator_id; ?>">
                                            Оплатить криптой
                                        </button>
                                        
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
                            <a href="<?php echo home_url('/balance-topup'); ?>" class="theme-btn btn-one">
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
                        <!-- Опции загружаются динамически -->
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

<!-- Модальное окно оплаты криптовалютой -->
<div id="crypto-payment-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Оплата криптовалютой</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <p class="mb_20">Вы покупаете: <strong id="crypto-product-name"></strong></p>
            
            <form id="crypto-payment-form">
                <input type="hidden" id="crypto_product_id" name="product_id">
                <input type="hidden" id="crypto_indicator_id" name="indicator_id">
                <input type="hidden" id="crypto_price" name="price">
                
                <div class="form-group">
                    <label>Номер торгового счета</label>
                    <input type="text" name="account_number" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Период подписки</label>
                    <select name="period" class="form-control" id="crypto-period-select" required>
                        <option value="30" data-price="199">1 месяц - 199 $</option>
                        <option value="90" data-price="299">3 месяца - 299 $</option>
                        <option value="180" data-price="499">6 месяцев - 499 $</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Стоимость</label>
                    <div class="price-display">
                        <span id="crypto-selected-price">0</span> $
                    </div>
                </div>
                
                <div class="crypto-info-box">
                    <h4>Оплата в USDT (TRC20)</h4>
                    <p>Отправьте <strong><span id="crypto-amount">0</span> USDT</strong> на адрес:</p>
                    <div class="wallet-address">
                        <input type="text" id="crypto-address" value="TQEQHJRLz1HUQcaJtfAgh7jWi1SiE2cpJT" readonly>
                        <button type="button" class="copy-btn" onclick="copyAddress()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Hash транзакции</label>
                    <input type="text" name="transaction_hash" class="form-control" placeholder="Введите hash транзакции после оплаты" required>
                    <small>После перевода введите hash транзакции</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="theme-btn btn-one">Я оплатил</button>
                    <button type="button" class="theme-btn btn-two cancel-btn">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    console.log('Dashboard script loaded');
    
    // Переключение вкладок
    $(document).on('click', '.dashboard-menu a[data-tab]', function(e) {
        e.preventDefault();
        console.log('Tab clicked:', $(this).data('tab'));
        
        var tab = $(this).data('tab');
        
        // Убираем активный класс со всех элементов
        $('.dashboard-menu li').removeClass('active');
        $('.dashboard-content').removeClass('active');
        
        // Добавляем активный класс нужным элементам
        $(this).parent('li').addClass('active');
        $('#' + tab).addClass('active');
        
        // Обновляем URL
        if(history.pushState) {
            history.pushState(null, null, '#' + tab);
        }
    });
    
    // Проверяем hash при загрузке
    if(window.location.hash) {
        var hash = window.location.hash.substring(1);
        console.log('Hash found:', hash);
        $('.dashboard-menu a[data-tab="' + hash + '"]').trigger('click');
    }
    
    // Управление подпиской
    $(document).on('click', '.manage-subscription', function(e) {
        e.preventDefault();
        console.log('Manage subscription clicked');
        
        $('#indicator_id').val($(this).data('indicator-id'));
        $('#account_number').val($(this).data('account'));
        $('#subscription_end').val($(this).data('date'));
        
        $('#subscription-modal').fadeIn();
    });
    
    // Покупка за баланс
    $(document).on('click', '.buy-with-balance', function(e) {
        e.preventDefault();
        console.log('Buy with balance clicked');
        
        var productId = $(this).data('product-id');
        var productName = $(this).data('product-name');
        
        $('#buy_product_id').val(productId);
        $('#product-name').text(productName);
        
        // Добавляем дефолтные опции
        $('#period-select').html(`
            <option value="30" data-price="199">1 месяц - 199 $</option>
            <option value="90" data-price="299">3 месяца - 299 $</option>
            <option value="180" data-price="499">6 месяцев - 499 $</option>
        `);
        
        updateSelectedPrice();
        $('#buy-balance-modal').fadeIn();
    });
    
    // Покупка за криптовалюту
    $(document).on('click', '.buy-with-crypto', function(e) {
        e.preventDefault();
        console.log('Buy with crypto clicked');
        
        var productId = $(this).data('product-id');
        var productName = $(this).data('product-name');
        
        $('#crypto_product_id').val(productId);
        $('#crypto-product-name').text(productName);
        
        // Добавляем дефолтные опции
        $('#crypto-period-select').html(`
            <option value="30" data-price="199">1 месяц - 199 $</option>
            <option value="90" data-price="299">3 месяца - 299 $</option>
            <option value="180" data-price="499">6 месяцев - 499 $</option>
        `);
        
        updateCryptoPrice();
        $('#crypto-payment-modal').fadeIn();
    });
    
    // Обновление выбранной цены
    $(document).on('change', '#period-select', function() {
        updateSelectedPrice();
    });
    
    function updateSelectedPrice() {
        var selectedOption = $('#period-select option:selected');
        var price = parseFloat(selectedOption.attr('data-price')) || 199;
        var currentBalance = <?php echo isset($user_balance) ? $user_balance : 0; ?>;
        var afterBalance = currentBalance - price;
        
        $('#selected-price').text(price.toFixed(2));
        $('#balance-after').html('После покупки: <strong>' + afterBalance.toFixed(2) + ' $</strong>');
        
        if(afterBalance < 0) {
            $('#balance-after').addClass('text-danger');
            $('#buy-balance-form button[type="submit"]').prop('disabled', true);
        } else {
            $('#balance-after').removeClass('text-danger');
            $('#buy-balance-form button[type="submit"]').prop('disabled', false);
        }
    }
    
    // Обновление цены крипто
    $(document).on('change', '#crypto-period-select', function() {
        updateCryptoPrice();
    });
    
    function updateCryptoPrice() {
        var selectedOption = $('#crypto-period-select option:selected');
        var price = parseFloat(selectedOption.attr('data-price')) || 199;
        
        $('#crypto-selected-price').text(price.toFixed(2));
        $('#crypto-amount').text(price.toFixed(2));
        $('#crypto_price').val(price);
    }
    
    // Копирование адреса
    window.copyAddress = function() {
        var copyText = document.getElementById("crypto-address");
        if(copyText) {
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
            alert("Адрес скопирован: " + copyText.value);
        }
    }
    
    // Закрытие модальных окон
    $(document).on('click', '.close, .cancel-btn', function() {
        $('.modal').fadeOut();
    });
    
    // Клик вне модального окна
    $(document).on('click', function(e) {
        if($(e.target).hasClass('modal')) {
            $('.modal').fadeOut();
        }
    });
    
    // Отправка форм
    $('#buy-balance-form').on('submit', function(e) {
        e.preventDefault();
        console.log('Buy balance form submitted');
        
        var selectedOption = $('#period-select option:selected');
        var price = parseFloat(selectedOption.attr('data-price')) || 199;
        
        var formData = $(this).serialize();
        formData += '&price=' + price;
        formData += '&action=buy_with_balance&nonce=<?php echo wp_create_nonce("dashboard_nonce"); ?>';
        
        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
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
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                alert('Произошла ошибка при обработке запроса');
            }
        });
    });
    
    $('#crypto-payment-form').on('submit', function(e) {
        e.preventDefault();
        console.log('Crypto payment form submitted');
        
        var formData = $(this).serialize();
        formData += '&action=submit_crypto_payment&nonce=<?php echo wp_create_nonce("dashboard_nonce"); ?>';
        
        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    alert(response.data);
                    window.location.href = '<?php echo home_url("/dashboard?payment_sent=1"); ?>';
                } else {
                    alert(response.data || 'Произошла ошибка');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                alert('Произошла ошибка при обработке запроса');
            }
        });
    });
    
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        console.log('Profile form submitted');
        
        var formData = $(this).serialize();
        formData += '&action=update_user_profile&nonce=<?php echo wp_create_nonce("dashboard_nonce"); ?>';
        
        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    alert(response.data);
                } else {
                    alert('Ошибка при обновлении профиля');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                alert('Ошибка при обработке запроса');
            }
        });
    });
    
    // Проверка загрузки элементов
    console.log('Dashboard menu items:', $('.dashboard-menu a[data-tab]').length);
    console.log('Subscription cards:', $('.subscription-card').length);
    console.log('Buy buttons:', $('.buy-with-balance, .buy-with-crypto').length);
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

.subscription-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    transition: all 0.3s ease;
}

.subscription-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.subscription-card.active {
    border-color: #4caf50;
    background: #f8fdf9;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.card-header h4 {
    margin: 0;
    font-size: 18px;
}

.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 5px 30px rgba(0,0,0,0.3);
    animation: modalFadeIn 0.3s ease;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 30px;
    border-bottom: 1px solid #eee;
}

.modal-body {
    padding: 30px;
}

.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: #000;
}

.crypto-info-box {
    background: #f0f8ff;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    border: 1px solid #d0e5ff;
}

.wallet-address {
    display: flex;
    gap: 10px;
    margin: 15px 0;
}

.wallet-address input {
    flex: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: monospace;
    font-size: 12px;
}

.copy-btn {
    padding: 10px 15px;
    background: #1a73e8;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.theme-btn.btn-outline {
    background: transparent;
    border: 2px solid #1a73e8;
    color: #1a73e8!important;
}

.theme-btn.btn-outline:hover {
    background: #1a73e8;
    color: #fff!important;
}
</style>

<?php get_footer(); ?>