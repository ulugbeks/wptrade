<?php
/**
 * Template Name: Регистрация
 * 
 * @package FXForTrader
 */

// Если пользователь уже авторизован, перенаправляем в личный кабинет
if (is_user_logged_in()) {
    wp_redirect(home_url('/dashboard'));
    exit;
}

// Обработка формы регистрации
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_nonce'])) {
    if (wp_verify_nonce($_POST['register_nonce'], 'fxfortrader_register')) {
        $user_email = sanitize_email($_POST['user_email']);
        $user_pass = $_POST['user_password'];
        $user_pass2 = $_POST['user_password2'];
        $user_name = sanitize_text_field($_POST['user_name']);
        
        // Валидация
        if (!is_email($user_email)) {
            $error = 'Введите корректный email адрес';
        } elseif (email_exists($user_email)) {
            $error = 'Пользователь с таким email уже существует';
        } elseif (strlen($user_pass) < 6) {
            $error = 'Пароль должен содержать минимум 6 символов';
        } elseif ($user_pass !== $user_pass2) {
            $error = 'Пароли не совпадают';
        } else {
            // Создаем пользователя
            $user_id = wp_create_user($user_email, $user_pass, $user_email);
            
            if (!is_wp_error($user_id)) {
                // Обновляем имя пользователя
                wp_update_user(array(
                    'ID' => $user_id,
                    'display_name' => $user_name,
                    'first_name' => $user_name,
                ));
                
                // Отправляем email с подтверждением
                wp_new_user_notification($user_id, null, 'both');
                
                $success = true;
            } else {
                $error = 'Произошла ошибка при регистрации. Попробуйте еще раз.';
            }
        }
    }
}

get_header('auth');
?>

<section class="account-section" style="padding-top:40px;padding-bottom:60px;position:relative;z-index:2;background: transparent;">
    <div class="auto-container">
        <div class="row clearfix justify-content-center">
            <div class="col-lg-5 col-md-8">
                <div class="account-block-one inner-box"
                    style="background:#fff;box-shadow:0 4px 32px 0 rgba(0,0,0,0.10);border-radius:16px;padding:36px 28px 28px 28px;">
                    
                    <?php if($success): ?>
                        <h3 class="text-center mb_30">Регистрация успешна!</h3>
                        <div class="alert alert-success" role="alert">
                            Вы успешно зарегистрированы. На ваш email отправлено письмо с данными для входа.
                        </div>
                        <div class="text-center">
                            <a href="<?php echo home_url('/auth'); ?>" class="theme-btn btn-one">Войти в аккаунт</a>
                        </div>
                    <?php else: ?>
                        <h3 class="text-center mb_30">Регистрация</h3>
                        
                        <?php if($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo esc_html($error); ?>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <?php wp_nonce_field('fxfortrader_register', 'register_nonce'); ?>
                            
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
                                    placeholder="Придумайте пароль" required
                                    style="transition:none;box-shadow:none;border:1px solid #e5e5e5;background:#fafbfc;">
                            </div>
                            
                            <div class="form-group" style="margin-bottom:22px;">
                                <label for="user_password2">Повторите пароль</label>
                                <input type="password" id="user_password2" name="user_password2" class="form-control"
                                    placeholder="Повторите пароль" required
                                    style="transition:none;box-shadow:none;border:1px solid #e5e5e5;background:#fafbfc;">
                            </div>
                            
                            <div class="form-group" style="margin-bottom:22px;">
                                <label for="user_name">Имя</label>
                                <input type="text" id="user_name" name="user_name" class="form-control" 
                                    placeholder="Ваше имя" required
                                    value="<?php echo isset($_POST['user_name']) ? esc_attr($_POST['user_name']) : ''; ?>"
                                    style="transition:none;box-shadow:none;border:1px solid #e5e5e5;background:#fafbfc;">
                            </div>
                            
                            <div class="form-group" style="margin-bottom:10px;">
                                <button type="submit" class="theme-btn btn-one w-100">Зарегистрироваться</button>
                            </div>
                        </form>
                        
                        <div class="text-center mt_20">
                            Уже есть аккаунт? <a href="<?php echo home_url('/auth'); ?>" class="text-link">Войти</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer('auth'); ?>