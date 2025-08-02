<?php
/**
 * Template Name: Оплата
 * 
 * @package VolumeFX
 */

if (!is_user_logged_in()) {
    wp_redirect(home_url('/auth'));
    exit;
}

get_header();

$current_user = wp_get_current_user();
$type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '';
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 0;

// Обработка отправки формы
if(isset($_POST['submit_payment'])) {
    $payment_data = array(
        'user_id' => $current_user->ID,
        'type' => sanitize_text_field($_POST['type']),
        'product_id' => intval($_POST['product_id']),
        'amount' => floatval($_POST['amount']),
        'crypto_amount' => sanitize_text_field($_POST['crypto_amount'])
    );
    
    if($_POST['type'] === 'subscription') {
        // Сохраняем дополнительные данные для подписки
        update_post_meta($payment_data['product_id'], 'payment_account_' . $payment_id, sanitize_text_field($_POST['account_number']));
        update_post_meta($payment_data['product_id'], 'payment_period_' . $payment_id, intval($_POST['period']));
    }
    
    $payment_id = VolumeFX_Payments::create_payment($payment_data);
    
    if($payment_id) {
        // Отправляем уведомление админу
        volumefx_notify_admin_new_payment($payment_id);
        
        wp_redirect(home_url('/dashboard?payment_sent=1'));
        exit;
    }
}

// Криптовалютный адрес
$crypto_address = 'TWVsrW7qEfFxzwt4xMVMWJwvQPn9ssTSxp';
?>

<!-- page-title -->
<section class="page-title centred pt_90 pb_0">
    <div class="pattern-layer rotate-me" style="background-image: url(<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-34.png);"></div>
    <div class="auto-container">
        <div class="content-box">
            <h1>Оплата</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="<?php echo home_url(); ?>">Главная</a></li>
                <li>Оплата</li>
            </ul>
        </div>
    </div>
</section>

<!-- payment-section -->
<section class="payment-section pt_90 pb_100">
    <div class="auto-container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="payment-box">
                    <div class="payment-header">
                        <h3><?php echo $type === 'balance' ? 'Пополнение баланса' : 'Оплата подписки'; ?></h3>
                    </div>
                    
                    <div class="payment-info">
                        <div class="crypto-info">
                            <h4>Оплата в USDT Tether TRC20</h4>
                            <p>Без комиссии, подтверждение платежа не требуется.</p>
                            <p><strong>Сумма к оплате: $<?php echo $amount; ?></strong></p>
                        </div>
                        
                        <div class="payment-methods">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment-methods.png" alt="Payment Methods">
                        </div>
                        
                        <div class="wallet-info">
                            <p>Для оплаты в USDT Tether TRC 20, отправьте <strong><?php echo $amount; ?> USDT</strong> на кошелек:</p>
                            <div class="wallet-address">
                                <input type="text" id="crypto-address" value="<?php echo $crypto_address; ?>" readonly>
                                <button type="button" class="copy-btn" onclick="copyAddress()">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        
                        <form method="POST" id="payment-form">
                            <input type="hidden" name="type" value="<?php echo esc_attr($type); ?>">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <input type="hidden" name="amount" value="<?php echo $amount; ?>">
                            <input type="hidden" name="crypto_amount" value="<?php echo $amount; ?> USDT">
                            
                            <?php if($type === 'subscription' && $product_id): ?>
                            <div class="form-group">
                                <label>Номер торгового счета</label>
                                <input type="text" name="account_number" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Период подписки</label>
                                <select name="period" class="form-control" required>
                                    <option value="30">1 месяц</option>
                                    <option value="90">3 месяца</option>
                                    <option value="180">6 месяцев</option>
                                </select>
                            </div>
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label>ID/HASH транзакции</label>
                                <input type="text" name="transaction_hash" class="form-control" placeholder="Введите ID/HASH транзакции после оплаты">
                                <small>После перевода введите в поле ниже ID/HASH транзакции</small>
                            </div>
                            
                            <div class="payment-note">
                                <p><strong>Подписка автоматически активируется/продлится после подтверждения сети Tether TRC-20.</strong></p>
                            </div>
                            
                            <button type="submit" name="submit_payment" class="theme-btn btn-one w-100">
                                Я оплатил
                            </button>
                        </form>
                        
                        <div class="payment-help">
                            <a href="#" onclick="showAlternatives()">Ни один из вариантов не подходит?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function copyAddress() {
    var copyText = document.getElementById("crypto-address");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert("Адрес скопирован: " + copyText.value);
}

function showAlternatives() {
    alert("Свяжитесь с поддержкой для альтернативных способов оплаты");
}
</script>

<?php get_footer(); ?>