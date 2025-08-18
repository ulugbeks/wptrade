<?php
/**
 * Services Section for Homepage
 */

$args = array(
    'post_type' => 'service',
    'posts_per_page' => 6,
    'meta_key' => 'service_is_featured',
    'orderby' => array(
        'meta_value' => 'DESC',
        'date' => 'DESC'
    ),
);

$services = new WP_Query($args);
?>

<?php if($services->have_posts()): ?>
<section class="services-section pt_100 pb_70">
    <div class="auto-container">
        <div class="sec-title centred pb_60">
            <h2>Наши услуги</h2>
            <p>Профессиональные услуги для успешной торговли</p>
        </div>
        <div class="row clearfix">
            <?php 
            $delay = 0;
            while($services->have_posts()): $services->the_post(); 
                $service_type = get_field('service_type');
                $price_options = get_field('service_price_options');
                $short_description = get_field('service_short_description');
                $is_featured = get_field('service_is_featured');
                
                // Получаем минимальную цену
                $min_price = null;
                if($price_options && is_array($price_options)) {
                    foreach($price_options as $option) {
                        if(!$min_price || $option['price_amount'] < $min_price) {
                            $min_price = $option['price_amount'];
                        }
                    }
                }
            ?>
            <div class="col-lg-4 col-md-6 col-sm-12 service-block">
                <div class="service-block-home wow fadeInUp animated" data-wow-delay="<?php echo $delay; ?>ms"
                    data-wow-duration="1500ms">
                    <div class="inner-box">
                        <?php if($is_featured): ?>
                        <div class="badge-box">
                            <span>Популярная</span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="icon-box">
                            <?php 
                            $icons = array(
                                'consulting' => 'flaticon-consultation',
                                'training' => 'flaticon-online-learning',
                                'setup' => 'flaticon-settings',
                                'support' => 'flaticon-support',
                                'development' => 'flaticon-coding',
                                'analytics' => 'flaticon-analytics',
                                'other' => 'flaticon-gear'
                            );
                            $icon_class = $icons[$service_type] ?? 'flaticon-gear';
                            ?>
                            <i class="<?php echo $icon_class; ?>"></i>
                        </div>
                        
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        
                        <?php if($short_description): ?>
                        <p><?php echo wp_trim_words($short_description, 15); ?></p>
                        <?php endif; ?>
                        
                        <?php if($min_price): ?>
                        <div class="price-box">
                            от <span><?php echo number_format($min_price); ?> $</span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="link"><a href="<?php the_permalink(); ?>">Подробнее</a></div>
                    </div>
                </div>
            </div>
            <?php 
            $delay += 100;
            endwhile; 
            wp_reset_postdata();
            ?>
        </div>
        
        <div class="more-btn centred pt_30">
            <a href="<?php echo get_post_type_archive_link('service'); ?>" class="theme-btn btn-one">
                Все услуги
            </a>
        </div>
    </div>
</section>

<style>
.service-block-home .inner-box {
    background: #fff;
    padding: 40px 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    text-align: center;
    position: relative;
    height: 100%;
}

.service-block-home .inner-box:hover {
    transform: translateY(-10px);
    box-shadow: 0 5px 25px rgba(0,0,0,0.15);
}

.service-block-home .badge-box {
    position: absolute;
    top: 20px;
    right: 20px;
}

.service-block-home .badge-box span {
    background: #ff5722;
    color: #fff;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.service-block-home .icon-box {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
}

.service-block-home .icon-box i {
    font-size: 40px;
    color: #fff;
}

.service-block-home h3 {
    font-size: 22px;
    margin-bottom: 15px;
}

.service-block-home h3 a {
    color: #2c3e50;
    transition: color 0.3s ease;
}

.service-block-home h3 a:hover {
    color: #1a73e8;
}

.service-block-home p {
    color: #666;
    line-height: 1.8;
    margin-bottom: 20px;
}

.service-block-home .price-box {
    font-size: 16px;
    color: #666;
    margin-bottom: 20px;
}

.service-block-home .price-box span {
    font-size: 24px;
    font-weight: 700;
    color: #1a73e8;
}

.service-block-home .link a {
    color: #1a73e8;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.service-block-home .link a::after {
    content: '→';
    transition: transform 0.3s ease;
    display: inline-block;
    margin-left: 5px;
}

.service-block-home:hover .link a::after {
    transform: translateX(5px);
}
</style>
<?php endif; ?>