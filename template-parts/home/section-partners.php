<?php
/**
 * Partners Section
 */

$partners = get_field('partners');
?>

<?php if($partners): ?>
<div class="header-bottom">
    <div class="large-container">
        <div class="bottom-inner">
            <div class="inner-box">
                <ul class="stock-list">
                    <?php foreach($partners as $partner): ?>
                    <li><img src="<?php echo esc_url($partner['url']); ?>" alt="<?php echo esc_attr($partner['alt']); ?>"></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>