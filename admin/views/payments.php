<?php
// Получаем список платежей
global $wpdb;
$status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'pending';
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

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

if(!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$query .= " ORDER BY p.created_at DESC";

$payments = $wpdb->get_results($query);
?>

<div class="wrap">
    <h1>Управление платежами</h1>
    
    <ul class="subsubsub">
        <li><a href="?page=volumefx-payments&status=pending" <?php echo $status === 'pending' ? 'class="current"' : ''; ?>>Ожидающие</a> |</li>
        <li><a href="?page=volumefx-payments&status=completed" <?php echo $status === 'completed' ? 'class="current"' : ''; ?>>Подтвержденные</a> |</li>
        <li><a href="?page=volumefx-payments&status=rejected" <?php echo $status === 'rejected' ? 'class="current"' : ''; ?>>Отклоненные</a> |</li>
        <li><a href="?page=volumefx-payments&status=all" <?php echo $status === 'all' ? 'class="current"' : ''; ?>>Все</a></li>
    </ul>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Пользователь</th>
                <th>Тип</th>
                <th>Сумма</th>
                <th>Крипто</th>
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
                <td><?php echo $payment->amount; ?> <?php echo $payment->currency; ?></td>
                <td><?php echo $payment->crypto_amount ?: '-'; ?></td>
                <td>
                    <?php if($payment->type === 'balance'): ?>
                        <code style="font-size: 11px;"><?php echo $payment->transaction_hash ? substr($payment->transaction_hash, 0, 20) . '...' : '-'; ?></code>
                    <?php else: ?>
                        <code style="font-size: 11px;"><?php echo $payment->transaction_hash ? substr($payment->transaction_hash, 0, 20) . '...' : '-'; ?></code>
                        <?php 
                        $account = get_post_meta($payment->product_id, 'payment_account_' . $payment->id, true);
                        $period = get_post_meta($payment->product_id, 'payment_period_' . $payment->id, true);
                        if($account || $period): 
                        ?>
                        <br><small>
                            <?php if($account): ?>Счет: <?php echo esc_html($account); ?><br><?php endif; ?>
                            <?php if($period): ?>Период: <?php echo $period; ?> дней<?php endif; ?>
                        </small>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
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
                    <?php if($payment->status === 'pending'): ?>
                        <button class="button button-primary approve-payment" data-id="<?php echo $payment->id; ?>">Подтвердить</button>
                        <button class="button reject-payment" data-id="<?php echo $payment->id; ?>">Отклонить</button>
                    <?php else: ?>
                        <button class="button view-details" data-id="<?php echo $payment->id; ?>">Детали</button>
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

<script>
jQuery(document).ready(function($) {
    // Подтверждение платежа
    $('.approve-payment').on('click', function() {
        var paymentId = $(this).data('id');
        $('#payment-id').val(paymentId);
        $('#payment-action').val('approve_payment');
        $('#modal-title').text('Подтвердить платеж #' + paymentId);
        tb_show('Подтвердить платеж', '#TB_inline?inlineId=payment-modal&width=400&height=300');
    });
    
    // Отклонение платежа
    $('.reject-payment').on('click', function() {
        var paymentId = $(this).data('id');
        $('#payment-id').val(paymentId);
        $('#payment-action').val('reject_payment');
        $('#modal-title').text('Отклонить платеж #' + paymentId);
        tb_show('Отклонить платеж', '#TB_inline?inlineId=payment-modal&width=400&height=300');
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