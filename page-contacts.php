<?php
/**
 * Template Name: Контакты
 * 
 * @package FXForTrader
 */

get_header(); 

// ACF поля
$contact_emails = get_field('contact_emails');
$contact_phones = get_field('contact_phones');
$social_links = get_field('social_links');
$map_embed = get_field('map_embed_code');
?>

<!-- page-title -->
<section class="page-title centred pt_90 pb_0">
    <div class="pattern-layer rotate-me" style="background-image: url(<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-34.png);"></div>
    <div class="auto-container">
        <div class="content-box">
            <h1><?php the_title(); ?></h1>
            <ul class="bread-crumb clearfix">
                <li><a href="<?php echo home_url(); ?>">Главная</a></li>
                <li><?php the_title(); ?></li>
            </ul>
        </div>
    </div>
</section>
<!-- page-title end -->

<!-- contact-section -->
<section class="contact-section pt_90 pb_100">
    <div class="auto-container">
        <div class="info-inner pb_25">
            <div class="row clearfix">
                <?php if($contact_emails): ?>
                <div class="col-lg-4 col-md-6 col-sm-12 info-column">
                    <div class="single-info">
                        <div class="icon-box">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <p class="d-flex flex-column justify-content-start gap-2 h_70 pt_8">
                            <?php foreach($contact_emails as $email): ?>
                            <a href="mailto:<?php echo esc_attr($email['email']); ?>"><?php echo esc_html($email['email']); ?></a>
                            <?php endforeach; ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if($contact_phones): ?>
                <div class="col-lg-4 col-md-6 col-sm-12 info-column">
                    <div class="single-info">
                        <div class="icon-box">
                            <i class="fas fa-phone"></i>
                        </div>
                        <p class="d-flex flex-column justify-content-start gap-2 h_70 pt_8">
                            <?php foreach($contact_phones as $phone): ?>
                            <a href="tel:<?php echo esc_attr(str_replace(' ', '', $phone['phone'])); ?>"><?php echo esc_html($phone['phone']); ?></a>
                            <?php endforeach; ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if($social_links): ?>
                <div class="col-lg-4 col-md-6 col-sm-12 info-column">
                    <div class="single-info">
                        <div class="icon-box">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <p class="d-flex flex-column justify-content-start gap-2 h_70 pt_8">
                            <?php foreach($social_links as $social): ?>
                            <a href="<?php echo esc_url($social['url']); ?>" target="_blank"><?php echo esc_html($social['name']); ?></a>
                            <?php endforeach; ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<!-- contact-section end -->

<?php if($map_embed): ?>
<section class="social-channels">
    <h2 class="section-title">Где мы находимся</h2>
    <div class="w-100 mb_100 mt_80" style="height:30vw;">
        <?php echo $map_embed; // Уже содержит iframe ?>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>