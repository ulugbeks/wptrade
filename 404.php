<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package FXForTrader
 */

// Минимальный хедер для 404
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Страница не найдена | <?php bloginfo('name'); ?></title>
    
    <!-- Fav Icon -->
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.png" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link href="<?php echo get_template_directory_uri(); ?>/assets/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri(); ?>/assets/css/style.css" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri(); ?>/assets/css/module-css/header.css" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri(); ?>/assets/css/module-css/footer.css" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri(); ?>/assets/css/responsive.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Ubuntu', Arial, sans-serif;
            background: #0e1013;
            color: #fff;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .error-404-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .error-404-bg video {
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            filter: blur(2px) brightness(0.7);
        }

        .error-404-bg::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.75);
        }

        .error-404-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .error-404-flex {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 32px;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .error-404-emoji {
            font-size: 6rem;
            animation: float-emoji 2.5s infinite ease-in-out;
            flex-shrink: 0;
            line-height: 1;
            user-select: none;
        }

        @keyframes float-emoji {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-18px);
            }
        }

        .error-404-title {
            font-size: 8rem;
            font-weight: 700;
            color: #1a73e8;
            text-shadow: 0 4px 32px rgba(26, 115, 232, 0.2);
            letter-spacing: 8px;
            animation: shake404 2s infinite alternate;
            line-height: 1;
            margin: 0;
        }

        @keyframes shake404 {
            0% {
                transform: rotate(-2deg) scale(1.01);
            }
            100% {
                transform: rotate(2deg) scale(1.03);
            }
        }

        .error-404-text {
            font-size: 1.6rem;
            margin-bottom: 1.5rem;
            color: #fff;
            text-align: center;
        }

        .error-404-hint {
            font-size: 1.1rem;
            color: #b0b8c1;
            margin-bottom: 2.5rem;
            text-align: center;
        }

        .error-404-btn {
            display: inline-block;
            background: #1a73e8;
            color: #fff;
            font-weight: 700;
            padding: 14px 38px;
            border-radius: 32px;
            font-size: 1.2rem;
            text-decoration: none;
            box-shadow: 0 4px 24px 0 rgba(26, 115, 232, 0.15);
            transition: background 0.2s, transform 0.2s;
        }

        .error-404-btn:hover {
            background: #155ab6;
            transform: scale(1.05);
            color: #fff;
        }

        @media (max-width: 900px) {
            .error-404-flex {
                flex-direction: column;
                gap: 0.5rem;
            }
            .error-404-title {
                font-size: 4.5rem;
                letter-spacing: 4px;
            }
            .error-404-emoji {
                font-size: 3.2rem;
            }
        }

        @media (max-width: 600px) {
            .error-404-title {
                font-size: 3.2rem;
            }
            .error-404-emoji {
                font-size: 2.2rem;
            }
            .error-404-text {
                font-size: 1.1rem;
            }
        }
    </style>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <div class="error-404-bg">
        <?php 
        $video_404 = get_field('404_video', 'option');
        if($video_404): 
        ?>
        <video autoplay muted loop playsinline preload="auto">
            <source src="<?php echo esc_url($video_404); ?>" type="video/mp4">
        </video>
        <?php else: ?>
        <video autoplay muted loop playsinline preload="auto">
            <source src="<?php echo get_template_directory_uri(); ?>/assets/images/index/header__bg.mp4" type="video/mp4">
        </video>
        <?php endif; ?>
    </div>
    
    <div class="error-404-container">
        <div class="error-404-flex">
            <span class="error-404-emoji">🚀</span>
            <span class="error-404-title">404</span>
        </div>
        <div class="error-404-text">
            Похоже, вы попали не туда — такой страницы здесь нет
        </div>
        <div class="error-404-hint">
            Не переживайте, вы всегда можете вернуться на главную или воспользоваться меню.<br>
            <span style="font-size:0.95em;">Если что-то пошло не так — мы всегда рядом и готовы помочь!</span>
        </div>
        <a href="<?php echo home_url(); ?>" class="error-404-btn">На главную</a>
    </div>
    
    <?php wp_footer(); ?>
</body>
</html>