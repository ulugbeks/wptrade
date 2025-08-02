<?php
/**
 * About Section
 */

$about_title = get_field('about_title');
$about_content = get_field('about_content');
$about_features = get_field('about_features');
?>

<section class="account-section pt_100 pb_70">
    <div class="pattern-layer" style="background-image: url(<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-1.png);"></div>
    <div class="auto-container">
        <?php if($about_title): ?>
        <div class="sec-title pb_60 centred">
            <h2><?php echo esc_html($about_title); ?></h2>
        </div>
        <?php endif; ?>

        <?php if($about_content): ?>
        <div class="text-box mb_120">
            <?php echo wp_kses_post($about_content); ?>
        </div>
        <?php endif; ?>

        <?php if($about_features): ?>
        <div class="row clearfix">
            <?php foreach($about_features as $feature): ?>
            <div class="col-lg-3 col-md-6 col-sm-12 account-block">
                <div class="account-block-one wow fadeInUp animated" data-wow-delay="00ms"
                    data-wow-duration="1500ms">
                    <div class="inner-box">
                        <div class="icon-box">
                            <i class="<?php echo esc_attr($feature['feature_icon']); ?>"></i>
                        </div>
                        <h3><?php echo esc_html($feature['feature_title']); ?></h3>
                        <p><?php echo esc_html($feature['feature_description']); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>