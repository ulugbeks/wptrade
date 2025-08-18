<?php
/**
 * The template for displaying single service
 *
 * @package FXForTrader
 */

get_header(); 

while(have_posts()): the_post();
    $service_type = get_field('service_type');
    $description = get_field('service_description');
    $price_options = get_field('service_price_options');
    $features = get_field('service_features');
    $process = get_field('service_process');
    $faqs = get_field('service_faqs');
    $duration = get_field('service_duration');
    $format = get_field('service_format');
    $gallery = get_field('service_gallery');
    $video_url = get_field('service_video');
    $cta_text = get_field('service_cta_text') ?: 'Заказать услугу';
?>

<!-- page-title -->
<section class="page-title centred pt_90 pb_0">
    <div class="pattern-layer rotate-me" style="background-image: url(<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-34.png);"></div>
    <div class="auto-container">
        <div class="content-box">
            <h1><?php the_title(); ?></h1>
            <ul class="bread-crumb clearfix">
                <li><a href="<?php echo home_url(); ?>">Главная</a></li>
                <li><a href="<?php echo get_post_type_archive_link('service'); ?>">Услуги</a></li>
                <li><?php the_title(); ?></li>
            </ul>
        </div>
    </div>
</section>
<!-- page-title end -->

<!-- service-details -->
<section class="service-details pt_90 pb_90">
    <div class="auto-container">
        <div class="row clearfix">
            <div class="col-lg-8 col-md-12 col-sm-12 content-side">
                <div class="service-details-content">
                    <!-- Основное описание -->
                    <div class="inner-box mb_50">
                        <?php if($description): ?>
                        <div class="description-box">
                            <?php echo wp_kses_post($description); ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php the_content(); ?>
                    </div>
                    
                    <!-- Процесс оказания услуги -->
                    <?php if($process && is_array($process)): ?>
                    <div class="process-box mb_50">
                        <h3>Как мы работаем</h3>
                        <div class="process-steps">
                            <?php foreach($process as $step): ?>
                            <div class="process-step">
                                <div class="step-number">
                                    <span><?php echo esc_html($step['step_number']); ?></span>
                                </div>
                                <div class="step-content">
                                    <h4><?php echo esc_html($step['step_title']); ?></h4>
                                    <p><?php echo esc_html($step['step_description']); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Что включено -->
                    <?php if($features && is_array($features)): ?>
                    <div class="features-box mb_50">
                        <h3>Что включено в услугу</h3>
                        <ul class="features-list">
                            <?php foreach($features as $feature): ?>
                            <li>
                                <i class="<?php echo esc_attr($feature['feature_icon']); ?>"></i>
                                <?php echo esc_html($feature['feature_text']); ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <!-- FAQ -->
                    <?php if($faqs && is_array($faqs)): ?>
                    <div class="faq-box mb_50">
                        <h3>Часто задаваемые вопросы</h3>
                        <ul class="accordion-box">
                            <?php 
                            $faq_index = 0;
                            foreach($faqs as $faq): 
                            ?>
                            <li class="accordion block <?php echo $faq_index === 0 ? 'active-block' : ''; ?>">
                                <div class="acc-btn <?php echo $faq_index === 0 ? 'active' : ''; ?>">
                                    <div class="icon-box">
                                        <i class="fa-solid fa-question"></i>
                                    </div>
                                    <?php echo esc_html($faq['question']); ?>
                                </div>
                                <div class="acc-content <?php echo $faq_index === 0 ? 'current' : ''; ?>">
                                    <div class="content">
                                        <p><?php echo wp_kses_post($faq['answer']); ?></p>
                                    </div>
                                </div>
                            </li>
                            <?php 
                            $faq_index++;
                            endforeach; 
                            ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Галерея -->
                    <?php if($gallery && is_array($gallery)): ?>
                    <div class="gallery-box mb_50">
                        <h3>Примеры работ</h3>
                        <div class="gallery-grid">
                            <?php foreach($gallery as $image): ?>
                            <div class="gallery-item">
                                <a href="<?php echo esc_url($image['url']); ?>" data-fancybox="gallery">
                                    <img src="<?php echo esc_url($image['sizes']['medium']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Видео -->
                    <?php if($video_url): ?>
                    <div class="video-box mb_50">
                        <h3>Видео о услуге</h3>
                        <div class="video-wrapper">
                            <?php
                            // Преобразуем YouTube URL в embed
                            $video_id = '';
                            if(preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $video_url, $matches)) {
                                $video_id = $matches[1];
                            }
                            if($video_id):
                            ?>
                            <iframe width="100%" height="400"
                                src="https://www.youtube.com/embed/<?php echo $video_id; ?>"
                                title="YouTube video player" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4 col-md-12 col-sm-12 sidebar-side">
                <div class="service-sidebar ml_10">
                    <!-- Цены -->
                    <?php if($price_options && is_array($price_options)): ?>
                    <div class="pricing-widget mb_30">
                        <h3>Стоимость услуги</h3>
                        <div class="pricing-cards">
                            <?php foreach($price_options as $option): ?>
                            <div class="pricing-card">
                                <?php if($option['price_name']): ?>
                                <h4><?php echo esc_html($option['price_name']); ?></h4>
                                <?php endif; ?>
                                
                                <div class="price">
                                    <span class="amount"><?php echo number_format($option['price_amount']); ?></span>
                                    <span class="currency">$</span>
                                    <?php if($option['price_period']): ?>
                                    <span class="period">/ <?php echo esc_html($option['price_period']); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if($option['price_description']): ?>
                                <p><?php echo esc_html($option['price_description']); ?></p>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Информация об услуге -->
                    <div class="info-widget mb_30">
                        <h3>Информация</h3>
                        <ul class="info-list">
                            <?php if($duration): ?>
                            <li>
                                <i class="fas fa-clock"></i>
                                <span>Длительность:</span>
                                <strong><?php echo esc_html($duration); ?></strong>
                            </li>
                            <?php endif; ?>
                            
                            <?php if($format && is_array($format)): ?>
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Формат:</span>
                                <strong><?php echo implode(', ', $format); ?></strong>
                            </li>
                            <?php endif; ?>
                            
                            <li>
                                <i class="fas fa-tag"></i>
                                <span>Тип услуги:</span>
                                <strong>
                                    <?php 
                                    $type_labels = array(
                                        'consulting' => 'Консультация',
                                        'training' => 'Обучение',
                                        'setup' => 'Настройка',
                                        'support' => 'Поддержка',
                                        'development' => 'Разработка',
                                        'analytics' => 'Аналитика',
                                        'other' => 'Другое'
                                    );
                                    echo esc_html($type_labels[$service_type] ?? 'Услуга');
                                    ?>
                                </strong>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- CTA блок -->
                    <div class="cta-widget">
                        <h3>Готовы начать?</h3>
                        <p>Свяжитесь с нами для обсуждения деталей и получения персонального предложения.</p>
                        
                        <?php if(is_user_logged_in()): ?>
                            <a href="<?php echo home_url('/dashboard'); ?>" class="theme-btn btn-one w-100 mb_15">
                                <?php echo esc_html($cta_text); ?>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo home_url('/auth?redirect=' . urlencode(get_permalink())); ?>" class="theme-btn btn-one w-100 mb_15">
                                Войти для заказа
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?php echo home_url('/contacts'); ?>" class="theme-btn btn-two w-100">
                            Связаться с нами
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- service-details end -->

<style>
/* Процесс */
.process-steps {
    margin-top: 30px;
}

.process-step {
    display: flex;
    align-items: flex-start;
    margin-bottom: 30px;
    position: relative;
}

.process-step:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 25px;
    top: 50px;
    width: 2px;
    height: calc(100% + 10px);
    background: #e0e0e0;
}

.step-number {
    width: 50px;
    height: 50px;
    background: #1a73e8;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 20px;
    flex-shrink: 0;
    margin-right: 20px;
}

.step-content h4 {
    margin-bottom: 10px;
    color: #2c3e50;
}

/* Features */
.features-list {
    list-style: none;
    padding: 0;
    margin-top: 30px;
}

.features-list li {
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
}

.features-list li i {
    color: #1a73e8;
    margin-right: 15px;
    font-size: 20px;
    width: 25px;
}

/* Pricing Cards */
.pricing-cards {
    margin-top: 20px;
}

.pricing-card {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 20px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.pricing-card:hover {
    border-color: #1a73e8;
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.pricing-card h4 {
    margin-bottom: 15px;
    color: #2c3e50;
}

.pricing-card .price {
    margin-bottom: 15px;
    display: flex;
    align-items: baseline;
    gap: 5px;
}

.pricing-card .amount {
    font-size: 36px;
    font-weight: 700;
    color: #1a73e8;
}

.pricing-card .currency {
    font-size: 20px;
    color: #1a73e8;
}

.pricing-card .period {
    color: #666;
    font-size: 16px;
}

/* Info Widget */
.info-widget {
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.info-list {
    list-style: none;
    padding: 0;
    margin-top: 20px;
}

.info-list li {
    display: flex;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-list li:last-child {
    border-bottom: none;
}

.info-list li i {
    color: #1a73e8;
    margin-right: 15px;
    width: 20px;
}

.info-list li span {
    color: #666;
    margin-right: 10px;
}

.info-list li strong {
    color: #2c3e50;
    margin-left: auto;
}

/* CTA Widget */
.cta-widget {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 30px;
    border-radius: 10px;
    color: #fff;
    text-align: center;
}

.cta-widget h3 {
    color: #fff;
    margin-bottom: 15px;
}

.cta-widget p {
    color: rgba(255,255,255,0.9);
    margin-bottom: 25px;
}

.w-100 {
    width: 100%;
}

/* Gallery */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 30px;
}

.gallery-item {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    cursor: pointer;
}

.gallery-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-item:hover img {
    transform: scale(1.1);
}

/* Video */
.video-wrapper {
    margin-top: 20px;
    border-radius: 10px;
    overflow: hidden;
}
</style>

<?php endwhile; ?>

<?php
/**
 * Добавьте этот код в конец файла single-service.php перед get_footer()
 */
?>

<!-- Попап заказа услуги -->
<div id="service-order-modal" class="service-modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>Заказать услугу</h3>
            <span class="close-modal">&times;</span>
        </div>
        
        <div class="modal-body">
            <!-- Информация об услуге -->
            <div class="service-info-box">
                <h4><?php the_title(); ?></h4>
                <?php 
                $service_type = get_field('service_type');
                $type_labels = array(
                    'consulting' => 'Консультация',
                    'training' => 'Обучение',
                    'setup' => 'Настройка',
                    'support' => 'Поддержка',
                    'development' => 'Разработка',
                    'analytics' => 'Аналитика',
                    'other' => 'Другое'
                );
                ?>
                <p class="service-type-label"><?php echo esc_html($type_labels[$service_type] ?? 'Услуга'); ?></p>
            </div>
            
            <form id="service-order-form">
                <input type="hidden" name="service_id" value="<?php echo get_the_ID(); ?>">
                <input type="hidden" name="service_name" value="<?php the_title(); ?>">
                
                <!-- Выбор тарифа -->
                <?php 
                $price_options = get_field('service_price_options');
                if($price_options && is_array($price_options)):
                ?>
                <div class="form-group">
                    <label>Выберите тариф</label>
                    <select name="price_option" id="price-option-select" class="form-control" required>
                        <option value="">-- Выберите тариф --</option>
                        <?php foreach($price_options as $index => $option): ?>
                        <option value="<?php echo $index; ?>" 
                                data-price="<?php echo esc_attr($option['price_amount']); ?>"
                                data-name="<?php echo esc_attr($option['price_name']); ?>"
                                data-period="<?php echo esc_attr($option['price_period']); ?>">
                            <?php echo esc_html($option['price_name']); ?> - 
                            <?php echo number_format($option['price_amount'], 0, '.', ' '); ?> $ 
                            <?php if($option['price_period']): ?>
                                / <?php echo esc_html($option['price_period']); ?>
                            <?php endif; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <!-- Контактные данные -->
                <div class="form-group">
                    <label>Ваше имя</label>
                    <input type="text" name="client_name" class="form-control" 
                           value="<?php echo esc_attr(wp_get_current_user()->display_name); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="client_email" class="form-control" 
                           value="<?php echo esc_attr(wp_get_current_user()->user_email); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Телефон (необязательно)</label>
                    <input type="tel" name="client_phone" class="form-control" 
                           value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'phone', true)); ?>">
                </div>
                
                <div class="form-group">
                    <label>Комментарий к заказу</label>
                    <textarea name="order_comment" class="form-control" rows="3" 
                              placeholder="Дополнительная информация о вашем заказе"></textarea>
                </div>
                
                <!-- Информация об оплате -->
                <div class="payment-info-box">
                    <h4>Оплата в USDT (TRC20)</h4>
                    <p>Сумма к оплате: <strong id="payment-amount">0</strong> USDT</p>
                    
                    <div class="crypto-address-box">
                        <label>Адрес для перевода:</label>
                        <div class="address-input-group">
                            <input type="text" id="crypto-address" 
                                   value="TWVsrW7qEfFxzwt4xMVMWJwvQPn9ssTSxp" readonly>
                            <button type="button" class="copy-btn" onclick="copyServiceAddress()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Hash транзакции *</label>
                        <input type="text" name="transaction_hash" class="form-control" 
                               placeholder="Введите hash транзакции после оплаты" required>
                        <small>После перевода USDT введите hash транзакции для подтверждения</small>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="theme-btn btn-one">
                        <span class="btn-text">Отправить заказ</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Отправка...
                        </span>
                    </button>
                    <button type="button" class="theme-btn btn-two close-modal-btn">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Стили для попапа -->
<style>
.service-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 99999;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
}

.modal-content {
    position: relative;
    background: #fff;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    margin: 50px auto;
    border-radius: 12px;
    overflow-y: auto;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 25px 30px;
    border-bottom: 1px solid #eee;
    background: #f8f9fa;
    border-radius: 12px 12px 0 0;
}

.modal-header h3 {
    margin: 0;
    color: #2c3e50;
}

.close-modal {
    font-size: 30px;
    color: #999;
    cursor: pointer;
    transition: color 0.3s;
}

.close-modal:hover {
    color: #333;
}

.modal-body {
    padding: 30px;
}

.service-info-box {
    background: #f0f7ff;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    border-left: 4px solid #1a73e8;
}

.service-info-box h4 {
    margin: 0 0 10px 0;
    color: #2c3e50;
}

.service-type-label {
    display: inline-block;
    background: #1a73e8;
    color: #fff;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 13px;
    margin: 0;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 15px;
    transition: all 0.3s;
}

.form-control:focus {
    border-color: #1a73e8;
    outline: none;
    box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.1);
}

.payment-info-box {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    padding: 20px;
    border-radius: 8px;
    margin: 25px 0;
}

.payment-info-box h4 {
    margin: 0 0 15px 0;
    color: #856404;
}

.payment-info-box p {
    margin-bottom: 15px;
    color: #856404;
}

#payment-amount {
    font-size: 20px;
    color: #1a73e8;
}

.crypto-address-box {
    margin: 20px 0;
}

.address-input-group {
    display: flex;
    gap: 10px;
    margin-top: 8px;
}

.address-input-group input {
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
    transition: background 0.3s;
}

.copy-btn:hover {
    background: #1557b0;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 25px;
}

.form-actions .theme-btn {
    flex: 1;
    padding: 14px 20px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.form-actions .btn-one {
    background: #1a73e8;
    color: white;
}

.form-actions .btn-one:hover {
    background: #1557b0;
}

.form-actions .btn-two {
    background: #e0e0e0;
    color: #333;
}

.form-actions .btn-two:hover {
    background: #d0d0d0;
}

/* Responsive */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 20px auto;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<!-- JavaScript для попапа -->
<script>
jQuery(document).ready(function($) {
    // Открытие попапа при клике на кнопку заказа
    $('.cta-widget .theme-btn.btn-one, .pricing-card .order-btn, [href*="#order-service"]').on('click', function(e) {
        e.preventDefault();
        $('#service-order-modal').fadeIn();
    });
    
    // Закрытие попапа
    $('.close-modal, .close-modal-btn, .modal-overlay').on('click', function() {
        $('#service-order-modal').fadeOut();
    });
    
    // Обновление суммы при выборе тарифа
    $('#price-option-select').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var price = selectedOption.data('price');
        if(price) {
            $('#payment-amount').text(price + ' $');
        }
    });
    
    // Отправка формы
    $('#service-order-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        
        // Проверка выбора тарифа
        if($('#price-option-select').length && !$('#price-option-select').val()) {
            alert('Пожалуйста, выберите тариф');
            return;
        }
        
        // Показываем загрузку
        $submitBtn.find('.btn-text').hide();
        $submitBtn.find('.btn-loading').show();
        $submitBtn.prop('disabled', true);
        
        // Собираем данные
        var formData = $form.serialize();
        
        // Добавляем выбранный тариф
        var selectedOption = $('#price-option-select option:selected');
        formData += '&price_amount=' + selectedOption.data('price');
        formData += '&price_name=' + selectedOption.data('name');
        formData += '&price_period=' + selectedOption.data('period');
        
        formData += '&action=submit_service_order';
        formData += '&nonce=<?php echo wp_create_nonce("service_order_nonce"); ?>';
        
        // Отправляем AJAX запрос
        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    alert(response.data.message);
                    $('#service-order-modal').fadeOut();
                    $form[0].reset();
                    
                    // Редирект в личный кабинет
                    if(response.data.redirect) {
                        window.location.href = response.data.redirect;
                    }
                } else {
                    alert('Ошибка: ' + response.data);
                }
            },
            error: function() {
                alert('Произошла ошибка при отправке заказа. Попробуйте еще раз.');
            },
            complete: function() {
                $submitBtn.find('.btn-text').show();
                $submitBtn.find('.btn-loading').hide();
                $submitBtn.prop('disabled', false);
            }
        });
    });
});

// Функция копирования адреса
function copyServiceAddress() {
    var copyText = document.getElementById("crypto-address");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert("Адрес скопирован: " + copyText.value);
}
</script>

<?php get_footer(); ?>