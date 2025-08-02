<?php
/**
 * Courses Section
 */

$args = array(
    'post_type' => 'course',
    'posts_per_page' => 6,
    'order' => 'ASC',
);

$courses = new WP_Query($args);
?>

<?php if($courses->have_posts()): ?>
<section class="news-section pt_100 pb_70">
    <div class="auto-container">
        <div class="sec-title centred pb_60">
            <h2>Обучение и курсы</h2>
        </div>
        <div class="row clearfix">
            <?php 
            $delay = 0;
            while($courses->have_posts()): $courses->the_post(); 
                $price = get_field('course_price');
                $short_description = get_field('course_short_description');
            ?>
            <div class="col-lg-6 col-md-6 col-sm-12 news-block">
                <div class="news-block-one wow fadeInUp animated" data-wow-delay="<?php echo $delay; ?>ms"
                    data-wow-duration="1500ms">
                    <div class="inner-box">
                        <?php if($price): ?>
                        <span class="post-date">от <?php echo esc_html($price); ?> руб</span>
                        <?php endif; ?>
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <?php if($short_description): ?>
                        <p><?php echo esc_html($short_description); ?></p>
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