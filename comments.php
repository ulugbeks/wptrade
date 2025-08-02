<?php
/**
 * The template for displaying comments
 *
 * @package FXForTrader
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    <?php if (have_comments()): ?>
        <div class="group-title mb_25">
            <h3>
                <?php
                $comment_count = get_comments_number();
                if ('1' === $comment_count) {
                    printf('1 комментарий');
                } else {
                    printf('%s комментариев', $comment_count);
                }
                ?>
            </h3>
        </div>

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style'      => 'ol',
                'short_ping' => true,
                'avatar_size' => 60,
            ));
            ?>
        </ol>

        <?php the_comments_navigation(); ?>
    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')): ?>
        <p class="no-comments">Комментарии закрыты.</p>
    <?php endif; ?>

    <?php if (comments_open()): ?>
        <div class="group-title mb_25">
            <h3>Оставьте ваш комментарий</h3>
        </div>
        
        <div class="form-inner">
            <?php
            $fields = array(
                'author' => '<div class="col-lg-6 col-md-6 col-sm-12 single-column">
                    <div class="form-group">
                        <label>Имя <span>*</span></label>
                        <input type="text" name="author" id="author" required>
                    </div>
                </div>',
                'email' => '<div class="col-lg-6 col-md-6 col-sm-12 single-column">
                    <div class="form-group">
                        <label>Email <span>*</span></label>
                        <input type="email" name="email" id="email" required>
                    </div>
                </div>',
            );

            $args = array(
                'fields' => $fields,
                'comment_field' => '<div class="col-lg-12 col-md-12 col-sm-12 single-column">
                    <div class="form-group">
                        <label>Сообщение <span>*</span></label>
                        <textarea name="comment" id="comment" required></textarea>
                    </div>
                </div>',
                'class_form' => 'comment-form',
                'submit_button' => '<div class="col-lg-12 col-md-12 col-sm-12 single-column">
                    <div class="message-btn">
                        <button type="submit" class="theme-btn btn-one">Отправить</button>
                    </div>
                </div>',
                'format' => 'html5',
            );

            comment_form($args);
            ?>
        </div>
    <?php endif; ?>
</div>