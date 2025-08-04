<?php
/**
 * The template for displaying single product
 *
 * @package FXForTrader
 */

get_header(); 

while(have_posts()): the_post();
    $price_options = get_field('product_price_options');
    $faqs = get_field('product_faqs');
    $features = get_field('product_features');
    $video_url = get_field('product_video');
    $gallery = get_field('product_gallery');
?>

<!-- page-title -->
<section class="page-title centred pt_90 pb_0">
    <div class="pattern-layer rotate-me" style="background-image: url(<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-34.png);"></div>
    <div class="auto-container">
        <div class="content-box">
            <h1><?php the_title(); ?></h1>
            <ul class="bread-crumb clearfix">
                <li><a href="<?php echo home_url(); ?>">Главная</a></li>
                <li><a href="<?php echo get_post_type_archive_link('product'); ?>">Продукты</a></li>
                <li><?php the_title(); ?></li>
            </ul>
        </div>
    </div>
</section>
<!-- page-title end -->

<!-- education-details -->
<section class="education-details pt_90 pb_90">
    <div class="auto-container">
        <div class="row clearfix">
            <div class="col-lg-8 col-md-12 col-sm-12 content-side">
                <div class="education-details-content">
                    <div class="inner-box mb_70">
                        <?php if(get_field('product_description')): ?>
                        <p data-description class="eductation-details-info mb_40">
                            <?php echo wp_kses_post(get_field('product_description')); ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php if($faqs): ?>
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
                        <?php endif; ?>
                    </div>
                    
                    <div class="text-box markdown">
                        <?php if($features): ?>
                        <h3>Особенности продукта:</h3>
                        <ul class="list-item clearfix">
                            <?php foreach($features as $feature): ?>
                            <li><?php echo esc_html($feature['feature_text']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                        
                        <?php the_content(); ?>
                        
                        <?php if($gallery): ?>
                            <?php foreach($gallery as $image): ?>
                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <?php if($video_url): ?>
                        <iframe width="560" height="315"
                            src="<?php echo esc_url($video_url); ?>"
                            title="YouTube video player" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 sidebar-side">
                <div class="education-sidebar ml_10">
                    <?php if($price_options): ?>
                    <div data-prices="mini">
                        <?php foreach($price_options as $option): ?>
                        <h4 data-price="<?php echo esc_attr($option['price_type']); ?>">
                            <?php echo esc_html($option['price_amount']); ?>$ - <?php echo esc_html($option['price_period']); ?>
                        </h4>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <ul class="info-list mb_50">
                        <li class="d-block">Ваша подписка будет действовать до <b data-date-end></b> включительно. На почту придет письмо с ключом для софта, который так же будет доступен в Личном кабинете.</li>
                        <?php if(!is_user_logged_in()): ?>
                        <li class="text-danger">Зарегистрируйтесь или войдите в Личный кабинет, для того чтобы приобрести продукт/услугу.</li>
                        <?php endif; ?>
                        <li>Совершая покупку, вы соглашаетесь с Правилами и Пользовательским соглашением, а так же подтверждаете условия Оплаты и Политики возврата.</li>
                    </ul>
                    <div class="btn-box mb_50">
                        <?php if(is_user_logged_in()): ?>
                            <a href="<?php echo home_url('/buy?product_id=' . get_the_ID()); ?>" class="theme-btn btn-one">Купить</a>
                        <?php else: ?>
                            <a href="<?php echo home_url('/auth?redirect=' . urlencode(get_permalink())); ?>" class="theme-btn btn-one">Войти для покупки</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- education-details end -->

<?php endwhile; ?>

<?php get_footer(); ?>