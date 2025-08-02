<?php
/**
 * Template Name: Вход
 * 
 * @package FXForTrader
 */

// Если пользователь уже авторизован, перенаправляем в личный кабинет
if (is_user_logged_in()) {
    wp_redirect(home_url('/dashboard'));
    exit;
}

// Обработка формы входа
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_nonce'])) {
    if (wp_verify_nonce($_POST['login_nonce'], 'fxfortrader_login')) {
        $creds = array(
            'user_login'    => $_POST['user_email'],
            'user_password' => $_POST['user_password'],
            'remember'      => isset($_POST['remember']),
        );

        $user = wp_signon($creds, false);

        if (is_wp_error($user)) {
            $error = 'Неверный email или пароль';
        } else {
            $redirect = isset($_GET['redirect']) ? esc_url_raw($_GET['redirect']) : home_url('/dashboard');
            wp_redirect($redirect);
            exit;
        }
    }
}

// Минимальный хедер для страниц авторизации
get_header('auth');
?>

<section class="account-section" style="padding-top:40px;padding-bottom:60px;position:relative;z-index:2;background: transparent;">
    <div class="auto-container">
        <div class="row clearfix justify-content-center">
            <div class="col-lg-5 col-md-8">
                <div class="account-block-one inner-box"
                    style="background:#fff;box-shadow:0 4px 32px 0 rgba(0,0,0,0.10);border-radius:16px;padding:36px 28px 28px 28px;">
                    <h3 class="text-center mb_30">Вход в личный кабинет</h3>
                    
                    <?php if($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo esc_html($error); ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <?php wp_nonce_field('fxfortrader_login', 'login_nonce'); ?>
                        
                        <div class="form-group" style="margin-bottom:22px;">
                            <label for="user_email">Электронная почта</label>
                            <input type="email" id="user_email" name="user_email" class="form-control" 
                                placeholder="Введите e-mail" required
                                value="<?php echo isset($_POST['user_email']) ? esc_attr($_POST['user_email']) : ''; ?>"
                                style="transition:none;box-shadow:none;border:1px solid #e5e5e5;background:#fafbfc;">
                        </div>
                        
                        <div class="form-group" style="margin-bottom:22px;">
                            <label for="user_password">Пароль</label>
                            <input type="password" id="user_password" name="user_password" class="form-control"
                                placeholder="Введите пароль" required
                                style="transition:none;box-shadow:none;border:1px solid #e5e5e5;background:#fafbfc;">
                        </div>
                        
                        <div class="form-group mb_20 d-flex justify-content-between align-items-center" style="margin-bottom:22px;">
                            <div class="custom-checkbox-group">
                                <input type="checkbox" id="remember" name="remember" value="1">
                                <label for="remember">Запомнить меня</label>
                            </div>
                            <a href="<?php echo wp_lostpassword_url(); ?>" class="text-link">Забыли пароль?</a>
                        </div>
                        
                        <div class="form-group" style="margin-bottom:10px;">
                            <button type="submit" class="theme-btn btn-one w-100">Войти</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt_20">
                        Нет аккаунта? <a href="<?php echo home_url('/auth/register'); ?>" class="text-link">Зарегистрироваться</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer('auth'); ?>