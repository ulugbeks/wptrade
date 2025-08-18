<?php
/**
 * The template for displaying service archive
 *
 * @package FXForTrader
 */

get_header(); ?>

<!-- page-title -->
<section class="page-title centred pt_90 pb_0">
    <div class="pattern-layer rotate-me" style="background-image: url(<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-34.png);"></div>
    <div class="auto-container">
        <div class="content-box">
            <h1>Наши услуги</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="<?php echo home_url(); ?>">Главная</a></li>
                <li>Услуги</li>
            </ul>
        </div>
    </div>
</section>
<!-- page-title end -->

<!-- services-section -->
<section class="services-section pt_100 pb_100">
    <div class="auto-container">
        <!-- Фильтр по типам услуг -->
        <div class="services-filter mb_50">
            <ul class="filter-tabs clearfix">
                <li class="active" data-filter="all">Все услуги</li>
                <li data-filter="consulting">Консультации</li>
                <li data-filter="training">Обучение</li>
                <li data-filter="setup">Настройка</li>
                <li data-filter="support">Поддержка</li>
                <li data-filter="development">Разработка</li>
                <li data-filter="analytics">Аналитика</li>
            </ul>
        </div>
        
        <div class="row clearfix services-grid">
            <?php 
            if(have_posts()): 
                $delay = 0;
                while(have_posts()): the_post(); 
                    $service_type = get_field('service_type');
                    $price_options = get_field('service_price_options');
                    $short_description = get_field('service_short_description');
                    $is_featured = get_field('service_is_featured');
                    $duration = get_field('service_duration');
                    
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
            <div class="col-lg-4 col-md-6 col-sm-12 service-block" data-type="<?php echo esc_attr($service_type); ?>">
                <div class="service-block-one wow fadeInUp animated <?php echo $is_featured ? 'featured' : ''; ?>" 
                     data-wow-delay="<?php echo $delay; ?>ms"
                     data-wow-duration="1500ms">
                    <div class="inner-box">
                        <?php if($is_featured): ?>
                        <div class="featured-badge">
                            <span>Популярная</span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(has_post_thumbnail()): ?>
                        <div class="image-box">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium'); ?>
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <div class="lower-content">
                            <div class="service-type">
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
                            </div>
                            
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            
                            <?php if($short_description): ?>
                            <p><?php echo esc_html($short_description); ?></p>
                            <?php endif; ?>
                            
                            <div class="service-meta">
                                <?php if($min_price): ?>
                                <div class="price">
                                    от <span><?php echo number_format($min_price); ?> $</span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if($duration): ?>
                                <div class="duration">
                                    <i class="fas fa-clock"></i> <?php echo esc_html($duration); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="link">
                                <a href="<?php the_permalink(); ?>">Подробнее <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                $delay += 100;
                endwhile; 
            else:
            ?>
            <div class="col-12">
                <p class="text-center">Услуги не найдены.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if(function_exists('wp_pagenavi')): ?>
            <?php wp_pagenavi(); ?>
        <?php endif; ?>
    </div>
</section>
<!-- services-section end -->

<style>
.services-filter {
    text-align: center;
}

.filter-tabs {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 10px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.filter-tabs li {
    padding: 10px 25px;
    background: #f5f5f5;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.filter-tabs li:hover,
.filter-tabs li.active {
    background: #1a73e8;
    color: #fff;
}

.service-block {
    transition: all 0.3s ease;
}

.service-block.hidden {
    display: none;
}

.service-block-one {
    position: relative;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    height: 100%;
}

.service-block-one:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.service-block-one.featured {
    border: 2px solid #1a73e8;
}

.featured-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    z-index: 2;
}

.featured-badge span {
    background: #ff5722;
    color: #fff;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.service-block-one .image-box {
    position: relative;
    overflow: hidden;
    height: 200px;
}

.service-block-one .image-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.service-block-one:hover .image-box img {
    transform: scale(1.1);
}

.service-block-one .lower-content {
    padding: 25px;
}

.service-type {
    display: inline-block;
    background: #f0f7ff;
    color: #1a73e8;
    padding: 5px 15px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 500;
    margin-bottom: 15px;
}

.service-block-one h3 {
    font-size: 20px;
    margin-bottom: 15px;
}

.service-block-one h3 a {
    color: #2c3e50;
    transition: color 0.3s ease;
}

.service-block-one h3 a:hover {
    color: #1a73e8;
}

.service-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 0;
    padding: 15px 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}

.service-meta .price {
    font-size: 14px;
    color: #666;
}

.service-meta .price span {
    font-size: 20px;
    font-weight: 700;
    color: #1a73e8;
}

.service-meta .duration {
    color: #666;
    font-size: 14px;
}

.service-meta .duration i {
    color: #1a73e8;
    margin-right: 5px;
}

.service-block-one .link {
    margin-top: 20px;
}

.service-block-one .link a {
    color: #1a73e8;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: gap 0.3s ease;
}

.service-block-one .link a:hover {
    gap: 10px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Фильтрация услуг
    $('.filter-tabs li').on('click', function() {
        var filterValue = $(this).data('filter');
        
        // Активный класс для кнопки
        $('.filter-tabs li').removeClass('active');
        $(this).addClass('active');
        
        // Фильтрация блоков
        if(filterValue === 'all') {
            $('.service-block').fadeIn(300);
        } else {
            $('.service-block').each(function() {
                if($(this).data('type') === filterValue) {
                    $(this).fadeIn(300);
                } else {
                    $(this).fadeOut(300);
                }
            });
        }
    });
});
</script>

<?php get_footer(); ?>