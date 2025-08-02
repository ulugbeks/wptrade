<?php
/**
 * The template for displaying single posts
 *
 * @package FXForTrader
 */

get_header(); 

while(have_posts()): the_post();
?>

<!-- page-title -->
<section class="page-title centred pt_90 pb_0">
    <div class="pattern-layer rotate-me" style="background-image: url(<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-34.png);"></div>
    <div class="auto-container">
        <div class="content-box">
            <h1><?php the_title(); ?></h1>
            <ul class="bread-crumb clearfix">
                <li><a href="<?php echo home_url(); ?>">Главная</a></li>
                <li><a href="<?php echo get_permalink(get_option('page_for_posts')); ?>">Блог</a></li>
                <li><?php the_title(); ?></li>
            </ul>
        </div>
    </div>
</section>
<!-- page-title end -->

<!-- sidebar-page-container -->
<section class="sidebar-page-container pt_90 pb_100">
    <div class="auto-container">
        <div class="row clearfix">
            <div class="content-side">
                <div class="blog-details-content">
                    <div class="news-block-two pb_20">
                        <div class="inner-box">
                            <?php if(has_post_thumbnail()): ?>
                            <div class="image-box mb_30">
                                <?php the_post_thumbnail('large'); ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="lower-content">
                                <div class="author-box mb_15">
                                    <span><?php echo get_the_date('j F Y'); ?></span>
                                </div>
                                <div class="text-box">
                                    <?php the_content(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if(comments_open() || get_comments_number()): ?>
                    <div class="comment-box">
                        <?php comments_template(); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- sidebar-page-container end -->

<?php endwhile; ?>

<?php get_footer(); ?>