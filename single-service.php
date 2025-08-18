<?php
/**
 * The template for displaying single service
 *
 * @package VolumeFX
 */

get_header(); 

while(have_posts()): the_post();
    $service_price = get_field('service_price');
    $service_period = get_field('service_period');
    $sidebar_text = get_field('service_sidebar_text');
    
    // Адрес кошелька для оплаты
    $crypto_address = get_field('crypto_wallet_address', 'option') ?: 'TWVsrW7qEfFxzwt4xMVMWJwvQPn9ssTSxp';
?>

<!-- page-title -->
<section class="page-title centred pt_90 pb_0">
    <div class="pattern-layer rotate-me" style="background-image: url(<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-34.png);"></div>
    <div class="auto-container">
        <div class="content-box">
            <h1><?php the_title(); ?></h1>
            <ul class="bread-crumb clearfix">
                <li><a href="<?php echo home_url(); ?>">Главная</a></li>
                <li><?php the_title(); ?></li>
            </ul>
        </div>
    </div>
</section>
<!-- page-title end -->

<!-- service-details -->
<section class="education-details pt_90 pb_90">
    <div class="auto-container">
        <div class="row clearfix">
            <!-- Основной контент -->
            <div class="col-lg-8 col-md-12 col-sm-12 content-side">
                <div class="education-details-content">
                    <div class="text-box">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
            
            <!-- Сайдбар -->
            <div class="col-lg-4 col-md-12 col-sm-12 sidebar-side">
                <div class="education-sidebar ml_10">
                    <div class="sidebar-widget">
                        <?php if($service_price): ?>
                        <div class="price-box mb_25">
                            <h3 class="price"><?php echo esc_html($service_price); ?> $</h3>
                            <?php if($service_period): ?>
                            <span class="period"><?php echo esc_html($service_period); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($sidebar_text): ?>
                        <div class="sidebar-text mb_30">
                            <?php echo wp_kses_post($sidebar_text); ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="btn-box">
                            <?php if(is_user_logged_in()): ?>
                                <button class="theme-btn btn-one w-100" id="buy-service-btn" 
                                    data-service-id="<?php echo get_the_ID(); ?>"
                                    data-service-name="<?php echo esc_attr(get_the_title()); ?>"
                                    data-service-price="<?php echo esc_attr($service_price); ?>">
                                    Купить
                                </button>
                            <?php else: ?>
                                <a href="<?php echo home_url('/auth?redirect=' . urlencode(get_permalink())); ?>" 
                                   class="theme-btn btn-one w-100">Войти для покупки</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- service-details end -->

<!-- Модальное окно оплаты -->
<?php if(is_user_logged_in()): ?>
<div id="service-payment-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Оплата услуги</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <div class="service-info mb_20">
                <h4><?php the_title(); ?></h4>
                <p class="price-display">Стоимость: <strong><?php echo esc_html($service_price); ?> $</strong></p>
            </div>
            
            <form id="service-payment-form">
                <input type="hidden" name="service_id" value="<?php echo get_the_ID(); ?>">
                <input type="hidden" name="service_name" value="<?php echo esc_attr(get_the_title()); ?>">
                <input type="hidden" name="amount" value="<?php echo esc_attr($service_price); ?>">
                
                <div class="crypto-payment-info">
                    <h4>Оплата в USDT (TRC20)</h4>
                    <p>Отправьте <strong><?php echo esc_html($service_price); ?> USDT</strong> на адрес:</p>
                    
                    <div class="wallet-address-box">
                        <input type="text" id="crypto-wallet" value="<?php echo esc_attr($crypto_address); ?>" readonly>
                        <button type="button" class="copy-btn" onclick="copyWalletAddress()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Hash транзакции <span class="required">*</span></label>
                    <input type="text" name="transaction_hash" class="form-control" 
                           placeholder="Введите hash транзакции после оплаты" required>
                    <small>После перевода USDT введите hash транзакции</small>
                </div>
                
                <div class="payment-notice">
                    <p><i class="fas fa-info-circle"></i> После подтверждения платежа администратор свяжется с вами в течение 24 часов.</p>
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
    // Открытие модального окна
    $('#buy-service-btn').on('click', function(e) {
        e.preventDefault();
        $('#service-payment-modal').fadeIn();
    });
    
    // Закрытие модального окна
    $('.close, .cancel-btn').on('click', function() {
        $('#service-payment-modal').fadeOut();
    });
    
    // Клик вне модального окна
    $(window).on('click', function(e) {
        if ($(e.target).is('#service-payment-modal')) {
            $('#service-payment-modal').fadeOut();
        }
    });
    
    // Отправка формы оплаты
    $('#service-payment-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        formData += '&action=submit_service_payment';
        formData += '&nonce=<?php echo wp_create_nonce("service_payment_nonce"); ?>';
        
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.text();
        submitBtn.text('Обработка...').prop('disabled', true);
        
        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.data);
                    window.location.href = '<?php echo home_url("/dashboard?payment_sent=1"); ?>';
                } else {
                    alert(response.data || 'Произошла ошибка при отправке платежа');
                    submitBtn.text(originalText).prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                alert('Произошла ошибка при обработке запроса');
                submitBtn.text(originalText).prop('disabled', false);
            }
        });
    });
});

// Функция копирования адреса кошелька
function copyWalletAddress() {
    var copyText = document.getElementById("crypto-wallet");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    
    var btn = event.target.closest('.copy-btn');
    var originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i>';
    btn.style.background = '#4caf50';
    
    setTimeout(function() {
        btn.innerHTML = originalHTML;
        btn.style.background = '';
    }, 2000);
}
</script>

<style>
/* Стили для страницы услуги */
.sidebar-widget {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 30px;
}

.price-box {
    text-align: center;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.price-box .price {
    font-size: 36px;
    color: #1a73e8;
    margin: 0;
    font-weight: 700;
}

.price-box .period {
    display: block;
    margin-top: 10px;
    color: #666;
    font-size: 16px;
}

.sidebar-text {
    color: #555;
    line-height: 1.8;
}

/* Модальное окно */
.modal {
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    overflow-y: auto;
}

.modal-content {
    background-color: #fff;
    margin: 50px auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 30px;
    border-bottom: 1px solid #eee;
    background: #f8f9fa;
    border-radius: 12px 12px 0 0;
}

.modal-header h3 {
    margin: 0;
    color: #333;
}

.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
}

.close:hover {
    color: #000;
}

.modal-body {
    padding: 30px;
}

.service-info {
    background: #f0f8ff;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #d0e5ff;
}

.service-info h4 {
    margin: 0 0 10px 0;
    color: #333;
}

.price-display {
    margin: 0;
    font-size: 18px;
}

.crypto-payment-info {
    background: #fff8e1;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    border: 1px solid #ffe082;
}

.crypto-payment-info h4 {
    margin: 0 0 10px 0;
    color: #f57c00;
}

.wallet-address-box {
    display: flex;
    gap: 10px;
    margin: 15px 0;
}

.wallet-address-box input {
    flex: 1;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-family: monospace;
    font-size: 12px;
    background: #f8f9fa;
}

.copy-btn {
    padding: 12px 20px;
    background: #1a73e8;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
}

.copy-btn:hover {
    background: #1557b0;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
}

.form-group .required {
    color: #dc3545;
}

.form-group input {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 12px;
}

.payment-notice {
    background: #e3f2fd;
    padding: 15px;
    border-radius: 8px;
    margin: 20px 0;
}

.payment-notice p {
    margin: 0;
    color: #0d47a1;
    font-size: 14px;
}

.payment-notice i {
    color: #1976d2;
    margin-right: 8px;
}

.form-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 25px;
}

.w-100 {
    width: 100%;
}

/* Адаптивность */
@media (max-width: 768px) {
    .modal-content {
        margin: 20px auto;
        width: 95%;
    }
    
    .wallet-address-box {
        flex-direction: column;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions button {
        width: 100%;
    }
}
</style>
<?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>