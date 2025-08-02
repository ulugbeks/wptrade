<?php
/**
 * Banner Section
 */

$banner_title = get_field('banner_title');
$banner_description = get_field('banner_description');
$banner_video = get_field('banner_video');
?>

<section class="banner-section p_relative pt_20">
    <div class="large-container">
        <div class="banner-carousel owl-theme owl-carousel owl-nav-none">
            <div class="slide-item p_relative">
                <?php if($banner_video): ?>
                <div class="bg-layer" style="position: absolute; inset: 0; z-index: 1; overflow: hidden;">
                    <video autoplay muted loop playsinline preload="auto"
                        style="width:100%;height:100%;object-fit:cover;">
                        <source src="<?php echo esc_url($banner_video); ?>" type="video/mp4">
                        Ваш браузер не поддерживает видео.
                    </video>
                </div>
                <?php endif; ?>
                <div class="overlay p_fixed z_2 t_0 l_0"
                    style="width: 100%; height:100%; background: #0000003f;"></div>
                <div class="content-box">
                    <?php if($banner_title): ?>
                        <h2><?php echo esc_html($banner_title); ?></h2>
                    <?php endif; ?>
                    
                    <?php if($banner_description): ?>
                        <p><?php echo wp_kses_post($banner_description); ?></p>
                    <?php endif; ?>
                    
                    <div class="btn-box">
                        <a href="<?php echo esc_url(home_url('/auth/register')); ?>" class="theme-btn btn-one">Присоединиться</a>
                        <a href="<?php echo esc_url(home_url('/auth')); ?>" class="theme-btn btn-two ml_40" style="margin-left: 40px;">Войти в кабинет</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>