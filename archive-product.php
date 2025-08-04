<?php
/**
 * The template for displaying product archive
 *
 * @package FXForTrader
 */

get_header(); ?>

<!-- page-title -->
<section class="page-title centred pt_90 pb_0">
    <div class="pattern-layer rotate-me" style="background-image: url(<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-34.png);"></div>
    <div class="auto-container">
        <div class="content-box">
            <h1>Продукты</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="<?php echo home_url(); ?>">Главная</a></li>
                <li>Продукты</li>
            </ul>
        </div>
    </div>
</section>
<!-- page-title end -->

<!-- news-section -->
<section class="news-section pt_100 pb_100">
    <div class="auto-container">
        <div class="row clearfix">
            <?php 
            if(have_posts()): 
                $delay = 0;
                while(have_posts()): the_post(); 
                    $price = get_field('product_price');
                    $short_description = get_field('product_short_description');
                    $note = get_field('product_note'); // Дополнительное примечание
            ?>
            <div class="col-lg-6 col-md-6 col-sm-12 news-block">
                <div class="news-block-one wow fadeInUp animated" data-wow-delay="<?php echo $delay; ?>ms"
                    data-wow-duration="1500ms">
                    <div class="inner-box">
                        <?php if($price): ?>
                        <span class="post-date">от <?php echo esc_html($price); ?> usd</span>
                        <?php endif; ?>
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <?php if($short_description): ?>
                        <p><?php echo esc_html($short_description); ?></p>
                        <?php endif; ?>
                        <?php if($note): ?>
                        <p class="muted"><?php echo esc_html($note); ?></p>
                        <?php endif; ?>
                        <div class="link"><a href="<?php the_permalink(); ?>">Подробнее</a></div>
                    </div>
                </div>
            </div>
            <?php 
                $delay += 200;
                endwhile; 
            else:
            ?>
            <div class="col-12">
                <p>Продукты не найдены.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if(function_exists('wp_pagenavi')): ?>
            <?php wp_pagenavi(); ?>
        <?php endif; ?>
    </div>
</section>
<!-- news-section end -->

<?php get_footer(); ?>