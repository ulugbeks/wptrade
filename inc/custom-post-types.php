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
            'view_item' => 'Просмотреть продукт',
            'all_items' => 'Все продукты',
            'search_items' => 'Искать продукты',
            'not_found' => 'Продукты не найдены',
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'soft'),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-chart-line',
        'show_in_menu' => true,
        'menu_position' => 25,
    ));
    
    // Курсы (Courses)
    register_post_type('course', array(
        'labels' => array(
            'name' => 'Курсы',
            'singular_name' => 'Курс',
            'add_new' => 'Добавить курс',
            'add_new_item' => 'Добавить новый курс',
            'edit_item' => 'Редактировать курс',
            'view_item' => 'Просмотреть курс',
            'all_items' => 'Все курсы',
            'search_items' => 'Искать курсы',
            'not_found' => 'Курсы не найдены',
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'learning'),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-welcome-learn-more',
        'show_in_menu' => true,
        'menu_position' => 26,
    ));
    
    // Услуги (Services)
    register_post_type('service', array(
        'labels' => array(
            'name' => 'Услуги',
            'singular_name' => 'Услуга',
            'add_new' => 'Добавить услугу',
            'add_new_item' => 'Добавить новую услугу',
            'edit_item' => 'Редактировать услугу',
            'view_item' => 'Просмотреть услугу',
            'all_items' => 'Все услуги',
            'search_items' => 'Искать услуги',
            'not_found' => 'Услуги не найдены',
            'not_found_in_trash' => 'В корзине услуг не найдено',
        ),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'capability_type' => 'post',
        'has_archive' => false, // Отключаем архив, так как используем страницу
        'hierarchical' => false,
        'menu_position' => 27,
        'menu_icon' => 'dashicons-admin-tools',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'rewrite' => array(
            'slug' => 'usluga',
            'with_front' => false,
            'pages' => true,
            'feeds' => false,
        ),
        'show_in_rest' => true,
    ));
}
add_action('init', 'fxfortrader_register_post_types', 0); // Приоритет 0 для ранней загрузки