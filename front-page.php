<?php
/**
 * Template for displaying the front page
 *
 * @package FXForTrader
 */

get_header(); ?>

<!-- banner-section -->
<?php get_template_part('template-parts/home/section', 'banner'); ?>

<!-- header-bottom partners -->
<?php get_template_part('template-parts/home/section', 'partners'); ?>

<!-- clients-section -->
<?php get_template_part('template-parts/home/section', 'clients'); ?>

<!-- account-section (О нас) -->
<?php get_template_part('template-parts/home/section', 'about'); ?>

<!-- news-section (Программное обеспечение) -->
<?php get_template_part('template-parts/home/section', 'software'); ?>

<!-- news-section (Обучение и курсы) -->
<?php get_template_part('template-parts/home/section', 'courses'); ?>

<!-- services-section (Услуги) -->
<?php get_template_part('template-parts/home/section', 'services'); ?>

<?php get_footer(); ?>