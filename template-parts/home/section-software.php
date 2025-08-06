<?php
/**
 * Software Section
 */

$args = array(
    'post_type' => 'product',
    'posts_per_page' => 4,
    'order' => 'ASC',
);

$products = new WP_Query($args);
?>

<?php if($products->have_posts()): ?>
<section class="news-section pt_100 pb_70">
    <div class="auto-container">
        <div class="sec-title centred pb_60">
            <h2>Программное обеспечение</h2>
        </div>
        <div class="row clearfix">
            <?php 
            $delay = 0;
            while($products->have_posts()): $products->the_post(); 
                $price = get_field('product_price');
                $short_description = get_field('product_short_description');
            ?>
            <div class="col-lg-6 col-md-6 col-sm-12 news-block">
                <div class="news-block-one wow fadeInUp animated" data-wow-delay="<?php echo $delay; ?>ms"
                    data-wow-duration="1500ms">
                    <div class="inner-box">
                        <?php if($price): ?>
                        <span class="post-date">от <?php echo esc_html($price); ?> USD</span>
                        <?php endif; ?>
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <?php if($short_description): ?>
                        <p><?php echo wp_kses_post($short_description); ?></p>
                        <?php else: ?>
                        <p><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                        <?php endif; ?>
                        <div class="link"><a href="<?php the_permalink(); ?>">Подробнее</a></div>
                    </div>
                </div>
            </div>
            <?php 
            $delay += 200;
            endwhile; 
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>
<?php endif; ?>