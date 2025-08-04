<?php
/**
 * Template Name: Пополнение баланса
 * 
 * @package VolumeFX
 */

if (!is_user_logged_in()) {
    wp_redirect(home_url('/auth'));
    exit;
}

get_header();

$current_user = wp_get_current_user();
$crypto_address = 'TWVsrW7qEfFxzwt4xMVMWJwvQPn9ssTSxp';

// Обработка отправки формы
if(isset($_POST['submit_payment'])) {
    $payment_data = array(
        'user_id' => $current_user->ID,
        'type' => 'balance',
        'amount' => floatval($_POST['amount']),
        'crypto_amount' => floatval($_POST['amount']) . ' USDT',
        'transaction_hash' => sanitize_text_field($_POST['transaction_hash'])
    );
    
    $payment_id = VolumeFX_Payments::create_payment($payment_data);
    
    if($payment_id) {
        // Отправляем уведомление админу
        volumefx_notify_admin_new_payment($payment_id);
        
        wp_redirect(home_url('/dashboard?payment_sent=1#balance'));
        exit;
    }
}
?>

<!-- page-title -->
<section class="page-title centred pt_90 pb_0">
    <div class="pattern-layer rotate-me" style="background-image: url(<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-34.png);"></div>
    <div class="auto-container">
        <div class="content-box">
            <h1>Пополнение баланса</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="<?php echo home_url(); ?>">Главная</a></li>
                <li><a href="<?php echo home_url('/dashboard#balance'); ?>">Личный кабинет</a></li>
                <li>Пополнение баланса</li>
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
                        <h3>Пополнение баланса</h3>
                        <p>Выберите сумму пополнения или введите свою</p>
                    </div>
                    
                    <div class="payment-form">
                        <form method="POST" id="balance-payment-form">
                            <!-- Быстрые суммы -->
                            <div class="quick-amounts">
                                <button type="button" class="amount-btn" data-amount="50">50 $</button>
                                <button type="button" class="amount-btn" data-amount="100">100 $</button>
                                <button type="button" class="amount-btn" data-amount="200">200 $</button>
                                <button type="button" class="amount-btn" data-amount="500">500 $</button>
                            </div>
                            
                            <div class="form-group">
                                <label>Сумма пополнения ($)</label>
                                <input type="number" name="amount" id="amount" class="form-control" min="10" step="1" value="100" required>
                            </div>
                            
                            <div class="crypto-info-box">
                                <h4>Оплата в USDT (TRC20)</h4>
                                <p>Отправьте <strong><span id="crypto-amount">100</span> USDT</strong> на адрес:</p>
                                <div class="wallet-address">
                                    <input type="text" id="crypto-address" value="<?php echo $crypto_address; ?>" readonly>
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
                            
                            <div class="payment-note">
                                <p><strong>Баланс будет пополнен после подтверждения администратором.</strong></p>
                            </div>
                            
                            <button type="submit" name="submit_payment" class="theme-btn btn-one w-100">
                                Я оплатил
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
jQuery(document).ready(function($) {
    // Быстрые суммы
    $('.amount-btn').on('click', function() {
        var amount = $(this).data('amount');
        $('#amount').val(amount);
        updateCryptoAmount();
        
        $('.amount-btn').removeClass('active');
        $(this).addClass('active');
    });
    
    // Обновление суммы крипто при изменении
    $('#amount').on('input', function() {
        updateCryptoAmount();
        $('.amount-btn').removeClass('active');
    });
    
    function updateCryptoAmount() {
        var amount = $('#amount').val();
        $('#crypto-amount').text(amount);
    }
});

function copyAddress() {
    var copyText = document.getElementById("crypto-address");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert("Адрес скопирован: " + copyText.value);
}
</script>

<style>
.payment-box {
    background: #fff;
    border-radius: 12px;
    padding: 40px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.payment-header {
    text-align: center;
    margin-bottom: 30px;
}

.payment-header h3 {
    margin-bottom: 10px;
}

.quick-amounts {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    justify-content: center;
}

.amount-btn {
    padding: 10px 25px;
    border: 2px solid #e0e0e0;
    background: #fff;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.amount-btn:hover,
.amount-btn.active {
    border-color: #1a73e8;
    background: #f0f7ff;
    color: #1a73e8;
}

.crypto-info-box {
    background: #f0f8ff;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    border: 1px solid #d0e5ff;
}

.crypto-info-box h4 {
    margin-bottom: 10px;
    color: #1a73e8;
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
    background: #f8f9fa;
}

.copy-btn {
    padding: 10px 15px;
    background: #1a73e8;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.copy-btn:hover {
    background: #1557b0;
}

.payment-note {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    padding: 15px;
    border-radius: 8px;
    margin: 20px 0;
}

.w-100 {
    width: 100%;
}
</style>

<?php get_footer(); ?>