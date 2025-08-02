<?php
/**
 * The blog template file
 *
 * @package FXForTrader
 */

get_header(); ?>

<!-- page-title -->
<section class="page-title centred pt_90 pb_0">
    <div class="pattern-layer rotate-me" style="background-image: url(<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-34.png);"></div>
    <div class="auto-container">
        <div class="content-box">
            <h1>Наш блог</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="<?php echo home_url(); ?>">Главная</a></li>
                <li>Блог</li>
            </ul>
        </div>
    </div>
</section>
<!-- page-title end -->

<!-- sidebar-page-container -->
<section class="sidebar-page-container pt_90 pb_100">
    <style>
        .news-block:hover h3 {
            color: var(--theme-color);
        }
        .news-block {
            cursor: pointer;
        }
    </style>
    <div class="auto-container">
        <div class="row clearfix">
            <div class="content-side">
                <div class="blog-grid-content">
                    <div class="row clearfix">
                        <?php
                        if(have_posts()): 
                            $delay = 0;
                            while(have_posts()): the_post(); 
                        ?>
                        <div class="col-lg-4 col-md-4 col-sm-12 news-block">
                            <a href="<?php the_permalink(); ?>" class="d-inline">
                                <div class="news-block-two wow fadeInUp animated animated" data-wow-delay="<?php echo $delay; ?>ms"
                                    data-wow-duration="1500ms">
                                    <div class="inner-box">
                                        <?php if(has_post_thumbnail()): ?>
                                        <div class="image-box">
                                            <?php the_post_thumbnail('medium'); ?>
                                        </div>
                                        <?php endif; ?>
                                        <div class="lower-content">
                                            <h3><?php the_title(); ?></h3>
                                            <p><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                                            <div class="author-box">
                                                <span><?php echo get_the_date('j F Y'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php 
                            $delay += 100;
                            endwhile; 
                        else:
                        ?>
                        <div class="col-12">
                            <p>Записи не найдены.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if(function_exists('wp_pagenavi')): ?>
                        <?php wp_pagenavi(); ?>
                    <?php else: ?>
                        <div class="pagination-wrapper">
                            <?php the_posts_pagination(array(
                                'mid_size' => 2,
                                'prev_text' => '←',
                                'next_text' => '→',
                            )); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- sidebar-page-container end -->

<?php get_footer(); ?>