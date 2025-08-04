<?php
// Получаем список пользователей с подписками
global $wpdb;

$api = new LaTradeAPI();

// Получаем фильтры
$filter_status = isset($_GET['subscription']) ? sanitize_text_field($_GET['subscription']) : 'all';
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

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

// Получаем все продукты
$products = get_posts(array(
    'post_type' => 'product',
    'posts_per_page' => -1
));
?>

<div class="wrap">
    <h1>Управление пользователями и подписками</h1>
    
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
            <?php foreach($users as $user): 
                // Получаем данные пользователя из API
                $user_api_data = $api->getUserInfo($user->ID);
                $user_balance = VolumeFX_Payments::get_user_balance($user->ID);
                
                $has_active_subscription = false;
                $subscriptions = array();
                
                if($user_api_data && isset($user_api_data['data']['subscriptions'])) {
                    foreach($user_api_data['data']['subscriptions'] as $sub) {
                        $days_left = (strtotime($sub['date_end']) - time()) / 86400;
                        if($days_left > 0) {
                            $has_active_subscription = true;
                        }
                        $subscriptions[] = $sub;
                    }
                }
                
                // Применяем фильтр
                if($filter_status === 'active' && !$has_active_subscription) continue;
                if($filter_status === 'expired' && ($has_active_subscription || empty($subscriptions))) continue;
                if($filter_status === 'none' && !empty($subscriptions)) continue;
            ?>
            <tr>
                <td><?php echo $user->ID; ?></td>
                <td>
                    <strong><?php echo esc_html($user->display_name); ?></strong><br>
                    <small><?php echo esc_html($user->user_email); ?></small><br>
                    <?php if(get_user_meta($user->ID, 'phone', true)): ?>
                        <small>Тел: <?php echo esc_html(get_user_meta($user->ID, 'phone', true)); ?></small>
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?php echo number_format($user_balance, 2); ?> $</strong>
                    <?php if($user_api_data && isset($user_api_data['data']['balance'])): ?>
                        <br><small>API: <?php echo $user_api_data['data']['balance']; ?> дней</small>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if(!empty($subscriptions)): ?>
                        <?php foreach($subscriptions as $sub): 
                            $indicator_name = '';
                            foreach($products as $product) {
                                $slug = get_field('product_indicator_slug', $product->ID);
                                if($api->getIndicatorId($slug) == $sub['indicator_id']) {
                                    $indicator_name = $product->post_title;
                                    break;
                                }
                            }
                            
                            $days_left = (strtotime($sub['date_end']) - time()) / 86400;
                            $status_class = $days_left > 0 ? 'color: green;' : 'color: red;';
                        ?>
                        <div style="margin-bottom: 5px;">
                            <strong><?php echo esc_html($indicator_name); ?></strong><br>
                            <small>Счет: <?php echo esc_html($sub['account_number']); ?></small><br>
                            <small style="<?php echo $status_class; ?>">
                                До: <?php echo date('d.m.Y', strtotime($sub['date_end'])); ?>
                                (<?php echo $days_left > 0 ? 'осталось ' . ceil($days_left) . ' дн.' : 'истекла'; ?>)
                            </small>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span style="color: #999;">Нет подписок</span>
                    <?php endif; ?>
                </td>
                <td><?php echo date('d.m.Y H:i', strtotime($user->user_registered)); ?></td>
                <td>
                    <button class="button manage-user" data-user-id="<?php echo $user->ID; ?>">Управление</button>
                    <a href="?page=volumefx-payments&user_id=<?php echo $user->ID; ?>" class="button">Платежи</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Модальное окно управления пользователем -->
<div id="user-management-modal" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <h2>Управление подписками пользователя</h2>
        <form id="user-subscription-form">
            <input type="hidden" id="manage_user_id" name="user_id">
            
            <div class="user-info-section">
                <h3 id="user-name"></h3>
                <p id="user-email"></p>
            </div>
            
            <h4>Добавить/Продлить подписку</h4>
            
            <table class="form-table">
                <tr>
                    <th><label>Индикатор</label></th>
                    <td>
                        <select name="indicator_id" required>
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
                    <td><input type="text" name="account_number" required></td>
                </tr>
                <tr>
                    <th><label>Дата окончания</label></th>
                    <td><input type="date" name="date_end" required></td>
                </tr>
            </table>
            
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
    // Управление пользователем
    $('.manage-user').on('click', function() {
        var userId = $(this).data('user-id');
        var userName = $(this).closest('tr').find('strong').first().text();
        var userEmail = $(this).closest('tr').find('small').first().text();
        var currentBalance = $(this).closest('tr').find('td').eq(2).find('strong').text();
        
        $('#manage_user_id').val(userId);
        $('#user-name').text(userName);
        $('#user-email').text(userEmail);
        $('#current-balance').text(currentBalance);
        
        tb_show('Управление пользователем', '#TB_inline?inlineId=user-management-modal&width=600&height=600');
    });
    
    // Отправка формы управления подпиской
    $('#user-subscription-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        formData += '&action=admin_manage_user_subscription';
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
</style>