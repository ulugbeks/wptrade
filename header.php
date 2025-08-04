<?php
/**
 * The header for our theme
 *
 * @package FXForTrader
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    
    <!-- Fav Icon -->
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.png" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/2c09f7fb88.js" crossorigin="anonymous"></script>
    <link href="<?php echo get_template_directory_uri(); ?>/assets/css/module-css/education-details.css" rel="stylesheet">

    <?php if (is_page('dashboard')): ?>
        <link href="<?php echo get_template_directory_uri(); ?>/assets/css/dashboard.css" rel="stylesheet">
    <?php endif; ?>
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="boxed_wrapper ltr">

    <!-- preloader -->
    <div class="loader-wrap">
        <div class="preloader">
            <div id="handle-preloader" class="handle-preloader">
                <div class="animation-preloader">
                    <div class="spinner"></div>
                    <div class="txt-loading">
                        <span data-text-preloader="V" class="letters-loading">V</span>
                        <span data-text-preloader="O" class="letters-loading">O</span>
                        <span data-text-preloader="L" class="letters-loading">L</span>
                        <span data-text-preloader="U" class="letters-loading">U</span>
                        <span data-text-preloader="M" class="letters-loading">M</span>
                        <span data-text-preloader="E" class="letters-loading">E</span>
                        <br class="d-block d-sm-none">
                        <span data-text-preloader="F" class="letters-loading">F</span>
                        <span data-text-preloader="X" class="letters-loading">X</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- preloader end -->

    <!-- main header -->
    <header class="main-header header-style-one">
        <!-- header-lower -->
        <div class="header-lower">
            <div class="large-container">
                <div class="outer-box">
                    <figure class="logo-box">
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <?php 
                            $logo = get_field('site_logo', 'option');
                            if($logo): ?>
                                <img src="<?php echo esc_url($logo['url']); ?>" alt="<?php bloginfo('name'); ?>">
                            <?php else: ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-fx.png" width="200" alt="<?php bloginfo('name'); ?>">
                            <?php endif; ?>
                        </a>
                    </figure>
                    <div class="menu-area">
                        <!--Mobile Navigation Toggler-->
                        <div class="mobile-nav-toggler">
                            <i class="icon-bar"></i>
                            <i class="icon-bar"></i>
                            <i class="icon-bar"></i>
                        </div>
                        <nav class="main-menu navbar-expand-md navbar-light clearfix d-flex justify-content-between">
                            <div class="collapse navbar-collapse show" id="navbarSupportedContent">
                                <?php
                                wp_nav_menu(array(
                                    'theme_location' => 'primary',
                                    'container' => false,
                                    'menu_class' => 'navigation clearfix',
                                    'fallback_cb' => false,
                                    'walker' => new FXForTrader_Main_Menu_Walker(),
                                ));
                                ?>
                                <ul class="navigation clearfix">
                                    <?php if(is_user_logged_in()): ?>
                                        <li><a class="navigation__button" href="<?php echo esc_url(home_url('/dashboard')); ?>">Личный кабинет</a></li>
                                        <li><a class="navigation__button" href="<?php echo wp_logout_url(home_url()); ?>">Выйти</a></li>
                                    <?php else: ?>
                                        <li><a class="navigation__button" href="<?php echo esc_url(home_url('/auth')); ?>">Войти</a></li>
                                        <li><a class="navigation__button" href="<?php echo esc_url(home_url('/auth/register')); ?>">Регистрация</a></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- header-bottom -->

        <!--sticky Header-->
        <div class="sticky-header">
            <div class="large-container">
                <div class="outer-box">
                    <figure class="logo-box">
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <?php 
                            $logo = get_field('site_logo', 'option');
                            if($logo): ?>
                                <img src="<?php echo esc_url($logo['url']); ?>" alt="<?php bloginfo('name'); ?>">
                            <?php else: ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-fx.png" width="200" alt="<?php bloginfo('name'); ?>">
                            <?php endif; ?>
                        </a>
                    </figure>
                    <div class="menu-area">
                        <nav class="main-menu">
                            <!-- Sticky menu content будет клонирован через JS -->
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- main-header end -->

    <!-- Mobile Menu  -->
    <div class="mobile-menu">
        <div class="menu-backdrop"></div>
        <div class="close-btn"><i class="fas fa-times"></i></div>
        <nav class="menu-box">
            <div class="nav-logo">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <?php 
                    $logo = get_field('site_logo', 'option');
                    if($logo): ?>
                        <img src="<?php echo esc_url($logo['url']); ?>" alt="<?php bloginfo('name'); ?>">
                    <?php else: ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-fx.png" alt="<?php bloginfo('name'); ?>">
                    <?php endif; ?>
                </a>
            </div>
            <div class="menu-outer">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'mobile',
                    'container' => false,
                    'menu_class' => 'navigation',
                    'fallback_cb' => false,
                ));
                ?>
            </div>
        </nav>
    </div>
    <!-- End Mobile Menu -->