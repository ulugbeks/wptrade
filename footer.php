<?php
/**
 * The template for displaying the footer
 *
 * @package FXForTrader
 */

$footer_about = get_field('footer_about', 'option');
$footer_logo = get_field('footer_logo', 'option');
$footer_copyright = get_field('footer_copyright', 'option');
?>

    <!-- main-footer -->
    <footer class="main-footer">
        <div class="widget-section p_relative pt_70 pb_80">
            <div class="auto-container">
                <div class="row clearfix">
                    <div class="col-lg-8 col-md-12 col-sm-12 big-column">
                        <div class="row clearfix">
                            <div class="col-lg-3 col-md-6 col-sm-12 footer-column">
                                <div class="footer-widget links-widget">
                                    <div class="widget-title mb_11">
                                        <h3>О нас</h3>
                                    </div>
                                    <div class="widget-content">
                                        <?php
                                        wp_nav_menu(array(
                                            'theme_location' => 'footer-about',
                                            'container' => false,
                                            'menu_class' => 'links-list clearfix',
                                            'fallback_cb' => function() {
                                                echo '<ul class="links-list clearfix">
                                                    <li><a href="' . home_url('/learning') . '">Обучение</a></li>
                                                    <li><a href="' . home_url('/blog') . '">Блог</a></li>
                                                    <li><a href="' . home_url('/soft') . '">Продукты</a></li>
                                                    <li><a href="' . home_url('/contacts') . '">Контакты</a></li>
                                                </ul>';
                                            }
                                        ));
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 footer-column">
                                <div class="footer-widget links-widget">
                                    <div class="widget-title mb_11">
                                        <h3>Платформа</h3>
                                    </div>
                                    <div class="widget-content">
                                        <ul class="links-list clearfix">
                                            <?php if(is_user_logged_in()): ?>
                                                <li><a href="<?php echo esc_url(home_url('/dashboard')); ?>">Личный кабинет</a></li>
                                                <li><a href="<?php echo wp_logout_url(home_url()); ?>">Выйти</a></li>
                                            <?php else: ?>
                                                <li><a href="<?php echo esc_url(home_url('/auth')); ?>">Личный кабинет</a></li>
                                                <li><a href="<?php echo esc_url(home_url('/auth/register')); ?>">Регистрация</a></li>
                                                <li><a href="<?php echo esc_url(home_url('/auth')); ?>">Войти в аккаунт</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 footer-column">
                        <figure class="footer-logo">
                            <a href="<?php echo esc_url(home_url('/')); ?>">
                                <?php if($footer_logo): ?>
                                    <img src="<?php echo esc_url($footer_logo['url']); ?>" alt="<?php bloginfo('name'); ?>">
                                <?php else: ?>
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="<?php bloginfo('name'); ?>">
                                <?php endif; ?>
                            </a>
                        </figure>
                        <?php if($footer_about): ?>
                            <p><?php echo esc_html($footer_about); ?></p>
                        <?php else: ?>
                            <p>FXForTrader – портал для профессиональных и начинающих трейдеров. Уникальный софт в помощь при торговле. Аналитические данные и обзоры каждый день!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="auto-container">
                <div class="bottom-inner">
                    <p><?php echo $footer_copyright ? esc_html($footer_copyright) : '© FXForTrader. Все права защищены.'; ?></p>
                </div>
            </div>
        </div>
    </footer>
    <!-- main-footer end -->

    <!--Scroll to top-->
    <div class="scroll-to-top">
        <svg class="scroll-top-inner" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>

</div><!-- .boxed_wrapper -->


<?php wp_footer(); ?>

<script src="<?php echo get_template_directory_uri(); ?>/assets/js/main.js"></script>

</body>
</html>