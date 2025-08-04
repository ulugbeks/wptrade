<?php
/**
 * Главная страница админки VolumeFX
 */

// Получаем статистику
$stats = VolumeFX_Payments::get_payments_stats();
$api = new LaTradeAPI();
$api_info = $api->getInfo();

// Получаем последние платежи
global $wpdb;
$recent_payments = $wpdb->get_results("
    SELECT p.*, u.display_name, u.user_email 
    FROM {$wpdb->prefix}volumefx_payments p
    LEFT JOIN {$wpdb->users} u ON p.user_id = u.ID
    ORDER BY p.created_at DESC
    LIMIT 5
");

// Получаем активные подписки
$active_subscriptions = 0;
$total_users = count_users();
$users_with_subscriptions = 0;

$users = get_users(array('number' => -1));
foreach($users as $user) {
    $user_api_data = $api->getUserInfo($user->ID);
    if($user_api_data && isset($user_api_data['data']['subscriptions'])) {
        $has_active = false;
        foreach($user_api_data['data']['subscriptions'] as $sub) {
            $days_left = (strtotime($sub['date_end']) - time()) / 86400;
            if($days_left > 0) {
                $active_subscriptions++;
                $has_active = true;
            }
        }
        if($has_active) {
            $users_with_subscriptions++;
        }
    }
}
?>

<div class="wrap">
    <h1>VolumeFX - Панель управления</h1>
    
    <!-- Статистика -->
    <div class="dashboard-stats">
        <div class="stat-box">
            <div class="stat-number"><?php echo number_format($stats->total_revenue, 2); ?> $</div>
            <div class="stat-label">Общая выручка</div>
        </div>
        
        <div class="stat-box">
            <div class="stat-number"><?php echo $stats->pending_count; ?></div>
            <div class="stat-label">Платежей на проверке</div>
        </div>
        
        <div class="stat-box">
            <div class="stat-number"><?php echo $active_subscriptions; ?></div>
            <div class="stat-label">Активных подписок</div>
        </div>
        
        <div class="stat-box">
            <div class="stat-number"><?php echo $total_users['total_users']; ?></div>
            <div class="stat-label">Всего пользователей</div>
        </div>
    </div>
    
    <!-- API информация -->
    <?php if($api_info && $api_info['code'] == 200): ?>
    <div class="api-info-box">
        <h3>Информация API LA-Trade</h3>
        <div class="api-stats">
            <?php if(isset($api_info['data']['balance'])): ?>
            <div class="api-stat">
                <strong>Баланс API:</strong> <?php echo $api_info['data']['balance']; ?> дней
            </div>
            <?php endif; ?>
            <div class="api-stat">
                <strong>Статус API:</strong> <span style="color: green;">Активен</span>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="dashboard-content">
        <!-- Последние платежи -->
        <div class="dashboard-section">
            <h2>Последние платежи</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Пользователь</th>
                        <th>Тип</th>
                        <th>Сумма</th>
                        <th>Статус</th>
                        <th>Дата</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($recent_payments): ?>
                        <?php foreach($recent_payments as $payment): ?>
                        <tr>
                            <td>#<?php echo $payment->id; ?></td>
                            <td>
                                <strong><?php echo esc_html($payment->display_name); ?></strong><br>
                                <small><?php echo esc_html($payment->user_email); ?></small>
                            </td>
                            <td>
                                <?php 
                                if($payment->type === 'balance') {
                                    echo 'Пополнение баланса';
                                } else {
                                    $product = get_post($payment->product_id);
                                    echo 'Подписка: ' . ($product ? $product->post_title : 'N/A');
                                }
                                ?>
                            </td>
                            <td><?php echo $payment->amount; ?> $</td>
                            <td>
                                <?php
                                $status_labels = array(
                                    'pending' => '<span style="color: orange;">Ожидает</span>',
                                    'completed' => '<span style="color: green;">Подтвержден</span>',
                                    'rejected' => '<span style="color: red;">Отклонен</span>'
                                );
                                echo $status_labels[$payment->status] ?? $payment->status;
                                ?>
                            </td>
                            <td><?php echo date('d.m.Y H:i', strtotime($payment->created_at)); ?></td>
                            <td>
                                <a href="?page=volumefx-payments&status=<?php echo $payment->status; ?>" class="button button-small">Подробнее</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">Платежи не найдены</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <p><a href="?page=volumefx-payments" class="button">Все платежи</a></p>
        </div>
        
        <!-- Быстрые действия -->
        <div class="dashboard-section">
            <h2>Быстрые действия</h2>
            <div class="quick-actions">
                <a href="?page=volumefx-payments&status=pending" class="button button-primary">
                    <span class="dashicons dashicons-clock"></span> Проверить платежи
                </a>
                <a href="?page=volumefx-users" class="button">
                    <span class="dashicons dashicons-admin-users"></span> Управление пользователями
                </a>
                <a href="<?php echo admin_url('post-new.php?post_type=product'); ?>" class="button">
                    <span class="dashicons dashicons-plus"></span> Добавить продукт
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stat-box {
    background: #fff;
    border: 1px solid #ccd0d4;
    padding: 20px;
    text-align: center;
    border-radius: 4px;
}

.stat-number {
    font-size: 32px;
    font-weight: 600;
    color: #2271b1;
    margin-bottom: 5px;
}

.stat-label {
    color: #666;
    font-size: 14px;
}

.api-info-box {
    background: #f0f8ff;
    border: 1px solid #2271b1;
    padding: 15px;
    margin: 20px 0;
    border-radius: 4px;
}

.api-info-box h3 {
    margin-top: 0;
}

.api-stats {
    display: flex;
    gap: 30px;
}

.dashboard-content {
    display: grid;
    grid-template-columns: 1fr;
    gap: 30px;
    margin-top: 30px;
}

.dashboard-section {
    background: #fff;
    padding: 20px;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
}

.dashboard-section h2 {
    margin-top: 0;
}

.quick-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.quick-actions .button {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.quick-actions .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}
</style>