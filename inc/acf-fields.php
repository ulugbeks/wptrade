<?php

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
    'key' => 'group_homepage',
    'title' => 'Настройки главной страницы',
    'fields' => array(
        // Баннер
        array(
            'key' => 'field_banner_title',
            'label' => 'Заголовок баннера',
            'name' => 'banner_title',
            'type' => 'text',
            'default_value' => 'FXForTrader — один из лидеров рынка ПО для биржевого онлайн-анализа',
        ),
        array(
            'key' => 'field_banner_description',
            'label' => 'Описание баннера',
            'name' => 'banner_description',
            'type' => 'textarea',
            'rows' => 8,
        ),
        array(
            'key' => 'field_banner_video',
            'label' => 'Видео для фона',
            'name' => 'banner_video',
            'type' => 'file',
            'return_format' => 'url',
            'mime_types' => 'mp4',
        ),
        
        // Секция "О нас"
        array(
            'key' => 'field_about_title',
            'label' => 'Заголовок "О нас"',
            'name' => 'about_title',
            'type' => 'text',
            'default_value' => 'О нас',
        ),
        array(
            'key' => 'field_about_content',
            'label' => 'Контент "О нас"',
            'name' => 'about_content',
            'type' => 'wysiwyg',
        ),
        array(
            'key' => 'field_about_features',
            'label' => 'Преимущества',
            'name' => 'about_features',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_feature_icon',
                    'label' => 'Иконка (класс FontAwesome)',
                    'name' => 'feature_icon',
                    'type' => 'text',
                    'placeholder' => 'fas fa-chart-line',
                ),
                array(
                    'key' => 'field_feature_title',
                    'label' => 'Заголовок',
                    'name' => 'feature_title',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_feature_description',
                    'label' => 'Описание',
                    'name' => 'feature_description',
                    'type' => 'textarea',
                    'rows' => 3,
                ),
            ),
        ),
        
        // Партнеры
        array(
            'key' => 'field_partners',
            'label' => 'Партнеры',
            'name' => 'partners',
            'type' => 'gallery',
            'return_format' => 'array',
        ),
        
        // Клиенты
        array(
            'key' => 'field_clients',
            'label' => 'Клиенты',
            'name' => 'clients',
            'type' => 'gallery',
            'return_format' => 'array',
        ),
    ),
    'location' => array(
        array(
            array(
                'param' => 'page_type',
                'operator' => '==',
                'value' => 'front_page',
            ),
        ),
    ),
));

endif;