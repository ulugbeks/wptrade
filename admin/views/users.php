<?php

// Получаем список пользователей с подписками
global $wpdb;

$api = new LaTradeAPI();

// Получаем фильтры
$filter_status = isset($_GET['subscription']) ? sanitize_text_field($_GET['subscription']) : 'all';
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// Добавляем отладку для конкретного пользователя
$debug_user_id = isset($_GET['debug_user']) ? intval($_GET['debug_user']) : 0;

// Базовый запрос
$args = array(
    'orderby' => 'registered',
    'order' => 'DESC',
);

if($search) {
    $args['search'] = '*' . $search . '*';
    $args['search_columns'] = array('user_login', 'user_email', 'display_name');
}

$users = get_users($args);

// Создаем массив сопоставления indicator_id => product_name
$indicator_map = array(
    1 => 'Volatility Levels',
    2 => 'Fibo Musang',
    3 => 'Future Volume',
    4 => 'Options FX'
);

// Добавляем продукты из базы
$products = get_posts(array(
    'post_type' => 'product',
    'posts_per_page' => -1
));

foreach($products as $product) {
    $slug = get_field('product_indicator_slug', $product->ID);
    if($slug) {
        $indicator_id = $api->getIndicatorId($slug);
        if($indicator_id) {
            $indicator_map[$indicator_id] = $product->post_title;
        }
    }
}
?>

<div class="wrap">
    <h1>Управление пользователями и подписками</h1>
    
    <?php if($debug_user_id): ?>
    <div style="background: #f0f8ff; padding: 15px; margin: 20px 0; border: 1px solid #0073aa;">
        <h3>Отладка для пользователя ID: <?php echo $debug_user_id; ?></h3>
        <?php
        $debug_data = $api->getUserInfo($debug_user_id);
        echo '<pre>';
        print_r($debug_data);
        echo '</pre>';
        ?>
    </div>
    <?php endif; ?>
    
    <!-- Фильтры -->
    <div class="tablenav top">
        <div class="alignleft actions">
            <form method="get" action="">
                <input type="hidden" name="page" value="volumefx-users">
                
                <select name="subscription">
                    <option value="all" <?php selected($filter_status, 'all'); ?>>Все пользователи</option>
                    <option value="active" <?php selected($filter_status, 'active'); ?>>С активными подписками</option>
                    <option value="expired" <?php selected($filter_status, 'expired'); ?>>С истекшими подписками</option>
                    <option value="none" <?php selected($filter_status, 'none'); ?>>Без подписок</option>
                </select>
                
                <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="Поиск пользователей">
                
                <input type="submit" class="button" value="Фильтр">
            </form>
        </div>
    </div>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th>Пользователь</th>
                <th>Баланс</th>
                <th>Подписки</th>
                <th>Дата регистрации</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach($users as $user): 
                // Получаем данные пользователя из API
                $user_api_data = $api->getUserInfo($user->ID);
                $user_balance = VolumeFX_Payments::get_user_balance($user->ID);
                
                $has_active_subscription = false;
                $has_expired_subscription = false;
                $subscriptions = array();
                
                // ВАЖНО: Проверяем разные форматы ответа API
                if($user_api_data) {
                    // Проверяем успешный ответ
                    if(isset($user_api_data['code']) && $user_api_data['code'] == 200) {
                        // Проверяем наличие данных
                        if(isset($user_api_data['data'])) {
                            // Проверяем подписки - они могут быть в разных местах
                            $subs_data = null;
                            
                            // Вариант 1: subscriptions прямо в data
                            if(isset($user_api_data['data']['subscriptions'])) {
                                $subs_data = $user_api_data['data']['subscriptions'];
                            }
                            // Вариант 2: subscriptions в data->data
                            elseif(isset($user_api_data['data']['data']['subscriptions'])) {
                                $subs_data = $user_api_data['data']['data']['subscriptions'];
                            }
                            // Вариант 3: indicators вместо subscriptions
                            elseif(isset($user_api_data['data']['indicators'])) {
                                $subs_data = $user_api_data['data']['indicators'];
                            }
                            
                            if($subs_data && is_array($subs_data)) {
                                foreach($subs_data as $sub) {
                                    // Проверяем формат подписки
                                    $subscription = array();
                                    
                                    // Получаем ID индикатора
                                    if(isset($sub['indicator_id'])) {
                                        $subscription['indicator_id'] = $sub['indicator_id'];
                                    } elseif(isset($sub['indicator'])) {
                                        $subscription['indicator_id'] = $sub['indicator'];
                                    } elseif(isset($sub['id'])) {
                                        $subscription['indicator_id'] = $sub['id'];
                                    } else {
                                        continue; // Пропускаем если нет ID
                                    }
                                    
                                    // Получаем номер счета
                                    if(isset($sub['account_number'])) {
                                        $subscription['account_number'] = $sub['account_number'];
                                    } elseif(isset($sub['account'])) {
                                        $subscription['account_number'] = $sub['account'];
                                    } else {
                                        $subscription['account_number'] = 'N/A';
                                    }
                                    
                                    // Получаем дату окончания
                                    if(isset($sub['date_end'])) {
                                        $subscription['date_end'] = $sub['date_end'];
                                    } elseif(isset($sub['end_date'])) {
                                        $subscription['date_end'] = $sub['end_date'];
                                    } elseif(isset($sub['date'])) {
                                        $subscription['date_end'] = $sub['date'];
                                    } else {
                                        $subscription['date_end'] = date('Y-m-d'); // Текущая дата как заглушка
                                    }
                                    
                                    // Проверяем статус
                                    $days_left = (strtotime($subscription['date_end']) - time()) / 86400;
                                    if($days_left > 0) {
                                        $has_active_subscription = true;
                                    } else {
                                        $has_expired_subscription = true;
                                    }
                                    
                                    $subscriptions[] = $subscription;
                                }
                            }
                        }
                    }
                    // Если код ответа не 200, но есть данные
                    elseif(isset($user_api_data['data']) && is_array($user_api_data['data'])) {
                        // Пробуем парсить данные напрямую
                        if(isset($user_api_data['data']['subscriptions']) && is_array($user_api_data['data']['subscriptions'])) {
                            foreach($user_api_data['data']['subscriptions'] as $sub) {
                                if(isset($sub['indicator_id']) && isset($sub['date_end'])) {
                                    $days_left = (strtotime($sub['date_end']) - time()) / 86400;
                                    if($days_left > 0) {
                                        $has_active_subscription = true;
                                    } else {
                                        $has_expired_subscription = true;
                                    }
                                    $subscriptions[] = $sub;
                                }
                            }
                        }
                    }
                }
                
                // Применяем фильтр
                if($filter_status === 'active' && !$has_active_subscription) continue;
                if($filter_status === 'expired' && (!$has_expired_subscription || $has_active_subscription)) continue;
                if($filter_status === 'none' && !empty($subscriptions)) continue;
            ?>
            <tr>
                <td><?php echo $user->ID; ?></td>
                <td>
                    <strong><?php echo esc_html($user->display_name ?: $user->user_login); ?></strong><br>
                    <small><?php echo esc_html($user->user_email); ?></small><br>
                    <?php if(get_user_meta($user->ID, 'phone', true)): ?>
                        <small>Тел: <?php echo esc_html(get_user_meta($user->ID, 'phone', true)); ?></small>
                    <?php endif; ?>
                    <br>
                    <small><a href="?page=volumefx-users&debug_user=<?php echo $user->ID; ?>">Отладка API</a></small>
                </td>
                <td>
                    <strong><?php echo number_format($user_balance, 2); ?> $</strong>
                    <?php if($user_api_data && isset($user_api_data['data']['balance'])): ?>
                        <br><small>API баланс: <?php echo $user_api_data['data']['balance']; ?> дней</small>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if(!empty($subscriptions)): ?>
                        <?php foreach($subscriptions as $sub): 
                            // Получаем название индикатора
                            $indicator_id = $sub['indicator_id'];
                            $indicator_name = isset($indicator_map[$indicator_id]) 
                                ? $indicator_map[$indicator_id] 
                                : 'Индикатор #' . $indicator_id;
                            
                            $days_left = (strtotime($sub['date_end']) - time()) / 86400;
                            $status_class = $days_left > 0 ? 'color: green;' : 'color: red;';
                            $status_text = $days_left > 0 ? 'Активна' : 'Истекла';
                        ?>
                        <div style="margin-bottom: 10px; padding: 8px; border: 1px solid #e0e0e0; border-radius: 4px; background: #f9f9f9;">
                            <strong><?php echo esc_html($indicator_name); ?></strong><br>
                            <small>Счет: <?php echo esc_html($sub['account_number']); ?></small><br>
                            <small style="<?php echo $status_class; ?>">
                                <?php echo $status_text; ?> | До: <?php echo date('d.m.Y', strtotime($sub['date_end'])); ?>
                                (<?php echo $days_left > 0 ? 'осталось ' . ceil($days_left) . ' дн.' : 'истекла ' . abs(floor($days_left)) . ' дн. назад'; ?>)
                            </small><br>
                            
                            <div style="margin-top: 5px;">
                                <button class="button button-small edit-subscription" 
                                        data-user-id="<?php echo $user->ID; ?>"
                                        data-indicator-id="<?php echo $indicator_id; ?>"
                                        data-account="<?php echo esc_attr($sub['account_number']); ?>"
                                        data-date-end="<?php echo esc_attr($sub['date_end']); ?>">
                                    Изменить
                                </button>
                                
                                <?php if($days_left > 0): ?>
                                <button class="button button-small button-link-delete stop-subscription" 
                                        data-user-id="<?php echo $user->ID; ?>"
                                        data-indicator-id="<?php echo $indicator_id; ?>"
                                        data-account="<?php echo esc_attr($sub['account_number']); ?>"
                                        style="color: red;">
                                    Остановить
                                </button>
                                <?php else: ?>
                                <button class="button button-small reactivate-subscription" 
                                        data-user-id="<?php echo $user->ID; ?>"
                                        data-indicator-id="<?php echo $indicator_id; ?>"
                                        data-account="<?php echo esc_attr($sub['account_number']); ?>"
                                        style="color: green;">
                                    Активировать заново
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span style="color: #999;">Нет подписок</span>
                    <?php endif; ?>
                    
                    <button class="button button-small button-primary add-subscription" 
                            data-user-id="<?php echo $user->ID; ?>"
                            data-user-name="<?php echo esc_attr($user->display_name ?: $user->user_login); ?>"
                            style="margin-top: 5px;">
                        + Добавить подписку
                    </button>
                </td>
                <td><?php echo date('d.m.Y H:i', strtotime($user->user_registered)); ?></td>
                <td>
                    <button class="button manage-user" 
                            data-user-id="<?php echo $user->ID; ?>"
                            data-user-name="<?php echo esc_attr($user->display_name ?: $user->user_login); ?>"
                            data-user-email="<?php echo esc_attr($user->user_email); ?>"
                            data-user-balance="<?php echo $user_balance; ?>">
                        Управление
                    </button>
                    <a href="?page=volumefx-payments&user_id=<?php echo $user->ID; ?>" class="button">Платежи</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Модальное окно управления подпиской -->
<div id="subscription-management-modal" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <h2 id="subscription-modal-title">Управление подпиской</h2>
        <form id="subscription-management-form">
            <input type="hidden" id="sub_user_id" name="user_id">
            <input type="hidden" id="sub_action" name="sub_action" value="update">
            
            <table class="form-table">
                <tr>
                    <th><label>Индикатор</label></th>
                    <td>
                        <select name="indicator_id" id="sub_indicator_id" required>
                            <option value="">Выберите индикатор</option>
                            <option value="1">Volatility Levels</option>
                            <option value="2">Fibo Musang</option>
                            <option value="3">Future Volume</option>
                            <option value="4">Options FX</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label>Номер счета</label></th>
                    <td><input type="text" name="account_number" id="sub_account_number" required></td>
                </tr>
                <tr>
                    <th><label>Дата окончания</label></th>
                    <td><input type="date" name="date_end" id="sub_date_end" required></td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">Сохранить</button>
                <button type="button" class="button" onclick="tb_remove();">Отмена</button>
            </p>
        </form>
    </div>
</div>

<!-- Модальное окно управления пользователем -->
<div id="user-management-modal" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <h2>Управление пользователем</h2>
        <form id="user-management-form">
            <input type="hidden" id="manage_user_id" name="user_id">
            
            <div class="user-info-section">
                <h3 id="user-name"></h3>
                <p id="user-email"></p>
            </div>
            
            <h4>Управление балансом</h4>
            
            <table class="form-table">
                <tr>
                    <th><label>Текущий баланс</label></th>
                    <td><span id="current-balance">0.00</span> $</td>
                </tr>
                <tr>
                    <th><label>Добавить/Вычесть</label></th>
                    <td>
                        <input type="number" name="balance_change" step="0.01" placeholder="Например: 100 или -50">
                        <p class="description">Положительное число для пополнения, отрицательное для списания</p>
                    </td>
                </tr>
                <tr>
                    <th><label>Примечание</label></th>
                    <td><textarea name="admin_note" rows="3" style="width: 100%;"></textarea></td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">Сохранить изменения</button>
                <button type="button" class="button" onclick="tb_remove();">Отмена</button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Добавить подписку
    $('.add-subscription').on('click', function() {
        var userId = $(this).data('user-id');
        var userName = $(this).data('user-name');
        
        $('#sub_user_id').val(userId);
        $('#sub_action').val('add');
        $('#subscription-modal-title').text('Добавить подписку для ' + userName);
        
        // Очищаем форму
        $('#sub_indicator_id').val('');
        $('#sub_account_number').val('');
        $('#sub_date_end').val('');
        
        tb_show('Добавить подписку', '#TB_inline?inlineId=subscription-management-modal&width=600&height=400');
    });
    
    // Изменить подписку
    $('.edit-subscription').on('click', function() {
        var userId = $(this).data('user-id');
        var indicatorId = $(this).data('indicator-id');
        var account = $(this).data('account');
        var dateEnd = $(this).data('date-end');
        
        $('#sub_user_id').val(userId);
        $('#sub_action').val('update');
        $('#sub_indicator_id').val(indicatorId);
        $('#sub_account_number').val(account);
        $('#sub_date_end').val(dateEnd);
        $('#subscription-modal-title').text('Изменить подписку');
        
        tb_show('Изменить подписку', '#TB_inline?inlineId=subscription-management-modal&width=600&height=400');
    });
    
    // Остановить подписку
    $('.stop-subscription').on('click', function() {
        if(!confirm('Вы уверены, что хотите остановить эту подписку? Дата окончания будет установлена на вчерашний день.')) {
            return;
        }
        
        var userId = $(this).data('user-id');
        var indicatorId = $(this).data('indicator-id');
        var accountNumber = $(this).data('account');
        
        $.post(ajaxurl, {
            action: 'admin_stop_subscription',
            user_id: userId,
            indicator_id: indicatorId,
            account_number: accountNumber,
            nonce: '<?php echo wp_create_nonce("volumefx_admin_nonce"); ?>'
        }, function(response) {
            if(response.success) {
                alert(response.data);
                location.reload();
            } else {
                alert('Ошибка: ' + response.data);
            }
        });
    });
    
    // Реактивировать подписку
    $('.reactivate-subscription').on('click', function() {
        var userId = $(this).data('user-id');
        var indicatorId = $(this).data('indicator-id');
        var account = $(this).data('account');
        
        // Устанавливаем дату на 30 дней вперед
        var futureDate = new Date();
        futureDate.setDate(futureDate.getDate() + 30);
        var dateString = futureDate.toISOString().split('T')[0];
        
        $('#sub_user_id').val(userId);
        $('#sub_action').val('update');
        $('#sub_indicator_id').val(indicatorId);
        $('#sub_account_number').val(account);
        $('#sub_date_end').val(dateString);
        $('#subscription-modal-title').text('Реактивировать подписку');
        
        tb_show('Реактивировать подписку', '#TB_inline?inlineId=subscription-management-modal&width=600&height=400');
    });
    
    // Отправка формы управления подпиской
    $('#subscription-management-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        formData += '&action=admin_manage_subscription';
        formData += '&nonce=<?php echo wp_create_nonce("volumefx_admin_nonce"); ?>';
        
        $.post(ajaxurl, formData, function(response) {
            if(response.success) {
                alert(response.data);
                location.reload();
            } else {
                alert('Ошибка: ' + response.data);
            }
        });
    });
    
    // Управление пользователем
    $('.manage-user').on('click', function() {
        var userId = $(this).data('user-id');
        var userName = $(this).data('user-name');
        var userEmail = $(this).data('user-email');
        var currentBalance = $(this).data('user-balance');
        
        $('#manage_user_id').val(userId);
        $('#user-name').text(userName);
        $('#user-email').text(userEmail);
        $('#current-balance').text(currentBalance.toFixed(2));
        
        tb_show('Управление пользователем', '#TB_inline?inlineId=user-management-modal&width=600&height=500');
    });
    
    // Отправка формы управления балансом
    $('#user-management-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        formData += '&action=admin_manage_user_balance';
        formData += '&nonce=<?php echo wp_create_nonce("volumefx_admin_nonce"); ?>';
        
        $.post(ajaxurl, formData, function(response) {
            if(response.success) {
                alert(response.data);
                location.reload();
            } else {
                alert('Ошибка: ' + response.data);
            }
        });
    });
});
</script>

<style>
.modal-content {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
}
.user-info-section {
    background: #f5f5f5;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
}
.user-info-section h3 {
    margin: 0 0 5px 0;
}
.user-info-section p {
    margin: 0;
    color: #666;
}
.button-link-delete {
    color: #d63638 !important;
}
.button-link-delete:hover {
    color: #a02222 !important;
}
</style>