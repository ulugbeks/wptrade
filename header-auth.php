<?php
/**
 * The header for auth pages
 *
 * @package FXForTrader
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?> <?php bloginfo('name'); ?></title>
    
    <!-- Fav Icon -->
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.png" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">
    
    <?php wp_head(); ?>
    
    <style>
        .custom-checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .custom-checkbox-group input[type="checkbox"] {
            width: 22px;
            height: 22px;
            accent-color: #1a73e8;
            margin: 0;
            vertical-align: middle;
        }
        .custom-checkbox-group label {
            margin-bottom: 0;
            cursor: pointer;
            user-select: none;
            font-size: 16px;
            line-height: 1;
        }
        @media (max-width: 575px) {
            .account-block-one.inner-box {
                padding: 24px 10px 18px 10px !important;
            }
        }
    </style>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="boxed_wrapper ltr" style="position:relative; min-height:100vh; overflow:hidden;">
    <?php 
    $auth_video = get_field('auth_background_video', 'option');
    if($auth_video): 
    ?>
    <video autoplay muted loop playsinline preload="auto"
        style="position:fixed;top:0;left:0;width:100vw;height:100vh;object-fit:cover;z-index:0;">
        <source src="<?php echo esc_url($auth_video); ?>" type="video/mp4">
    </video>
    <?php else: ?>
    <video autoplay muted loop playsinline preload="auto"
        style="position:fixed;top:0;left:0;width:100vw;height:100vh;object-fit:cover;z-index:0;">
        <source src="<?php echo get_template_directory_uri(); ?>/assets/images/index/header__bg.mp4" type="video/mp4">
    </video>
    <?php endif; ?>
    <div style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);z-index:1;"></div>

    <header class="main-header header-style-one">
        <div class="header-lower" style="background-color: white;">
            <div class="large-container">
                <div class="outer-box">
                    <figure class="logo-box">
                        <a href="<?php echo home_url(); ?>">
                            <?php 
                            $logo = get_field('site_logo', 'option');
                            if($logo): ?>
                                <img src="<?php echo esc_url($logo['url']); ?>" alt="<?php bloginfo('name'); ?>">
                            <?php else: ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="<?php bloginfo('name'); ?>">
                            <?php endif; ?>
                        </a>
                    </figure>
                    <div class="menu-area">
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
                                ));
                                ?>
                                <ul class="navigation clearfix">
                                    <li><a class="navigation__button" href="<?php echo home_url('/auth'); ?>">Войти</a></li>
                                    <li><a class="navigation__button" href="<?php echo home_url('/auth/register'); ?>">Регистрация</a></li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>