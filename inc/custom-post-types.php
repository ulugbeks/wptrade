<?php


function fxfortrader_register_post_types() {
    // Продукты (Software)
    register_post_type('product', array(
        'labels' => array(
            'name' => 'Продукты',
            'singular_name' => 'Продукт',
            'add_new' => 'Добавить продукт',
            'add_new_item' => 'Добавить новый продукт',
            'edit_item' => 'Редактировать продукт',
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'soft'),
        'supports' => array('title', 'editor', 'thumbnail'),
        'menu_icon' => 'dashicons-chart-line',
    ));
    
    // Курсы (Courses)
    register_post_type('course', array(
        'labels' => array(
            'name' => 'Курсы',
            'singular_name' => 'Курс',
            'add_new' => 'Добавить курс',
            'add_new_item' => 'Добавить новый курс',
            'edit_item' => 'Редактировать курс',
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'learning'),
        'supports' => array('title', 'editor', 'thumbnail'),
        'menu_icon' => 'dashicons-welcome-learn-more',
    ));
}
add_action('init', 'fxfortrader_register_post_types');