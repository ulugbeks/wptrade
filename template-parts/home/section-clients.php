<?php
/**
 * Clients Section
 */

$clients = get_field('clients');
?>

<?php if($clients): ?>
<section class="clients-section pt_40 pb_40">
    <div class="large-container">
        <ul class="clients-list">
            <?php foreach($clients as $client): ?>
            <li><a href="/"><img src="<?php echo esc_url($client['url']); ?>" alt="<?php echo esc_attr($client['alt']); ?>"></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
<?php endif; ?>