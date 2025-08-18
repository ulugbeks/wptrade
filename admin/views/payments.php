<?php

// Получаем список платежей
global $wpdb;
$status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'pending';
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$type_filter = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '';

$query = "SELECT p.*, u.display_name, u.user_email 
          FROM {$wpdb->prefix}volumefx_payments p
          LEFT JOIN {$wpdb->users} u ON p.user_id = u.ID";

$where = array();
if($status !== 'all') {
    $where[] = $wpdb->prepare("p.status = %s", $status);
}
if($user_id) {
    $where[] = $wpdb->prepare("p.user_id = %d", $user_id);
}
if($type_filter) {
    $where[] = $wpdb->prepare("p.type = %s", $type_filter);
}

if(!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$query .= " ORDER BY p.created_at DESC";

$payments = $wpdb->get_results($query);
?>

<div class="wrap">
    <h1>Управление платежами и заказами</h1>
    
    <ul class="subsubsub">
        <li><a href="?page=volumefx-payments&status=pending" <?php echo $status === 'pending' ? 'class="current"' : ''; ?>>Ожидающие</a> |</li>
        <li><a href="?page=volumefx-payments&status=completed" <?php echo $status === 'completed' ? 'class="current"' : ''; ?>>Подтвержденные</a> |</li>
        <li><a href="?page=volumefx-payments&status=rejected" <?php echo $status === 'rejected' ? 'class="current"' : ''; ?>>Отклоненные</a> |</li>
        <li><a href="?page=volumefx-payments&status=all" <?php echo $status === 'all' ? 'class="current"' : ''; ?>>Все</a></li>
    </ul>
    
    <div class="tablenav top">
        <div class="alignleft actions">
            <select name="type_filter" onchange="window.location.href='?page=volumefx-payments&status=<?php echo $status; ?>&type=' + this.value">
                <option value="">Все типы</option>
                <option value="balance" <?php selected($type_filter, 'balance'); ?>>Пополнение баланса</option>
                <option value="subscription" <?php selected($type_filter, 'subscription'); ?>>Подписки</option>
                <option value="service" <?php selected($type_filter, 'service'); ?>>Заказы услуг</option>
            </select>
        </div>
    </div>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th>Пользователь</th>
                <th>Тип</th>
                <th>Описание</th>
                <th>Сумма</th>
                <th>Hash / Детали</th>
                <th>Статус</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($payments as $payment): ?>
            <tr>
                <td>#<?php echo $payment->id; ?></td>
                <td>
                    <?php if($payment->type === 'service'): 
                        // Для услуг показываем данные из метаданных
                        $client_name = get_post_meta($payment->product_id, 'order_' . $payment->id . '_client_name', true);
                        $client_email = get_post_meta($payment->product_id, 'order_' . $payment->id . '_client_email', true);
                    ?>
                        <strong><?php echo esc_html($client_name ?: $payment->display_name); ?></strong><br>
                        <small><?php echo esc_html($client_email ?: $payment->user_email); ?></small>
                    <?php else: ?>
                        <strong><?php echo esc_html($payment->display_name); ?></strong><br>
                        <small><?php echo esc_html($payment->user_email); ?></small>
                    <?php endif; ?>
                </td>
                <td>
                    <?php 
                    $type_labels = array(
                        'balance' => '<span style="color: #4CAF50;">Баланс</span>',
                        'subscription' => '<span style="color: #2196F3;">Подписка</span>',
                        'service' => '<span style="color: #FF9800;">Услуга</span>'
                    );
                    echo $type_labels[$payment->type] ?? $payment->type;
                    ?>
                </td>
                <td>
                    <?php 
                    if($payment->type === 'balance') {
                        echo 'Пополнение баланса';
                    } elseif($payment->type === 'service') {
                        $service = get_post($payment->product_id);
                        $tariff = get_post_meta($payment->product_id, 'order_' . $payment->id . '_tariff', true);
                        echo '<strong>' . ($service ? $service->post_title : 'N/A') . '</strong>';
                        if($tariff) {
                            echo '<br><small>Тариф: ' . esc_html($tariff) . '</small>';
                        }
                    } else {
                        $product = get_post($payment->product_id);
                        echo 'Подписка: ' . ($product ? $product->post_title : 'N/A');
                    }
                    ?>
                </td>
                <td><?php echo $payment->amount; ?> <?php echo $payment->currency; ?></td>
                <td>
                    <code style="font-size: 11px;"><?php echo $payment->transaction_hash ? substr($payment->transaction_hash, 0, 20) . '...' : '-'; ?></code>
                    
                    <?php if($payment->type === 'service'): 
                        $phone = get_post_meta($payment->product_id, 'order_' . $payment->id . '_client_phone', true);
                        $comment = get_post_meta($payment->product_id, 'order_' . $payment->id . '_comment', true);
                    ?>
                        <?php if($phone): ?>
                            <br><small><strong>Тел:</strong> <?php echo esc_html($phone); ?></small>
                        <?php endif; ?>
                        <?php if($comment): ?>
                            <br><small><strong>Комментарий:</strong> <?php echo esc_html(wp_trim_words($comment, 10)); ?></small>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php
                    $status_labels = array(
                        'pending' => '<span style="color: orange;">Ожидает</span>',
                        'completed' => '<span style="color: green;">Выполнен</span>',
                        'rejected' => '<span style="color: red;">Отклонен</span>'
                    );
                    echo $status_labels[$payment->status] ?? $payment->status;
                    ?>
                </td>
                <td><?php echo date('d.m.Y H:i', strtotime($payment->created_at)); ?></td>
                <td>
                    <?php if($payment->status === 'pending'): ?>
                        <button class="button button-primary approve-payment" 
                                data-id="<?php echo $payment->id; ?>"
                                data-type="<?php echo $payment->type; ?>">
                            Подтвердить
                        </button>
                        <button class="button reject-payment" 
                                data-id="<?php echo $payment->id; ?>"
                                data-type="<?php echo $payment->type; ?>">
                            Отклонить
                        </button>
                    <?php else: ?>
                        <button class="button view-details" 
                                data-id="<?php echo $payment->id; ?>"
                                data-type="<?php echo $payment->type; ?>">
                            Детали
                        </button>
                    <?php endif; ?>
                    
                    <?php if($payment->type === 'service'): ?>
                        <button class="button view-service-details" 
                                data-id="<?php echo $payment->id; ?>"
                                data-service-id="<?php echo $payment->product_id; ?>">
                            <span class="dashicons dashicons-visibility"></span>
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Модальное окно для подтверждения -->
<div id="payment-modal" style="display: none;">
    <div class="modal-content">
        <h3 id="modal-title">Подтвердить платеж</h3>
        <form id="payment-action-form">
            <input type="hidden" id="payment-id" name="payment_id">
            <input type="hidden" id="payment-action" name="action">
            <input type="hidden" id="payment-type" name="payment_type">
            
            <p>
                <label>Примечание администратора:</label>
                <textarea name="admin_note" rows="3" style="width: 100%;"></textarea>
            </p>
            
            <p>
                <button type="submit" class="button button-primary">Подтвердить</button>
                <button type="button" class="button" onclick="tb_remove();">Отмена</button>
            </p>
        </form>
    </div>
</div>

<!-- Модальное окно для деталей услуги -->
<div id="service-details-modal" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <h3>Детали заказа услуги</h3>
        <div id="service-details-content">
            <!-- Контент загружается через AJAX -->
        </div>
        <p>
            <button type="button" class="button" onclick="tb_remove();">Закрыть</button>
        </p>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Подтверждение платежа
    $('.approve-payment').on('click', function() {
        var paymentId = $(this).data('id');
        var paymentType = $(this).data('type');
        $('#payment-id').val(paymentId);
        $('#payment-type').val(paymentType);
        $('#payment-action').val('approve_payment');
        $('#modal-title').text('Подтвердить ' + (paymentType === 'service' ? 'заказ услуги' : 'платеж') + ' #' + paymentId);
        tb_show('Подтвердить', '#TB_inline?inlineId=payment-modal&width=400&height=300');
    });
    
    // Отклонение платежа
    $('.reject-payment').on('click', function() {
        var paymentId = $(this).data('id');
        var paymentType = $(this).data('type');
        $('#payment-id').val(paymentId);
        $('#payment-type').val(paymentType);
        $('#payment-action').val('reject_payment');
        $('#modal-title').text('Отклонить ' + (paymentType === 'service' ? 'заказ услуги' : 'платеж') + ' #' + paymentId);
        tb_show('Отклонить', '#TB_inline?inlineId=payment-modal&width=400&height=300');
    });
    
    // Просмотр деталей услуги
    $('.view-service-details').on('click', function() {
        var paymentId = $(this).data('id');
        var serviceId = $(this).data('service-id');
        
        // Загружаем детали через AJAX
        $.post(ajaxurl, {
            action: 'get_service_order_details',
            payment_id: paymentId,
            service_id: serviceId,
            nonce: '<?php echo wp_create_nonce("admin_service_nonce"); ?>'
        }, function(response) {
            if(response.success) {
                $('#service-details-content').html(response.data);
                tb_show('Детали заказа', '#TB_inline?inlineId=service-details-modal&width=600&height=500');
            }
        });
    });
    
    // Отправка формы
    $('#payment-action-form').on('submit', function(e) {
        e.preventDefault();
        
        var data = $(this).serialize();
        data += '&nonce=<?php echo wp_create_nonce("volumefx_admin_nonce"); ?>';
        
        $.post(ajaxurl, data, function(response) {
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
.view-service-details {
    padding: 2px 8px;
}
.view-service-details .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}
</style>