<?php
/**
 * The template for displaying the footer on auth pages
 *
 * @package FXForTrader
 */

$footer_about = get_field('footer_about', 'option');
$footer_logo = get_field('footer_logo', 'option');
$footer_copyright = get_field('footer_copyright', 'option');
?>

    <!-- footer -->
    <footer class="main-footer z_3">
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
                                        <ul class="links-list clearfix">
                                            <li><a href="<?php echo home_url('/learning'); ?>">Обучение</a></li>
                                            <li><a href="<?php echo home_url('/blog'); ?>">Блог</a></li>
                                            <li><a href="<?php echo home_url('/soft'); ?>">Продукты</a></li>
                                            <li><a href="<?php echo home_url('/contacts'); ?>">Контакты</a></li>
                                        </ul>
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
                                            <li><a href="<?php echo home_url('/auth'); ?>">Личный кабинет</a></li>
                                            <li><a href="<?php echo home_url('/auth/register'); ?>">Регистрация</a></li>
                                            <li><a href="<?php echo home_url('/auth'); ?>">Войти в аккаунт</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 footer-column">
                        <figure class="footer-logo">
                            <a href="<?php echo home_url(); ?>">
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
    <!-- /footer -->
</div>

<?php wp_footer(); ?>
</body>
</html>