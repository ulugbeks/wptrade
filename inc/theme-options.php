<?php


// Создание страницы настроек темы
if( function_exists('acf_add_options_page') ) {
    
    acf_add_options_page(array(
        'page_title'    => 'Настройки темы',
        'menu_title'    => 'Настройки FXForTrader',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
    
    acf_add_options_sub_page(array(
        'page_title'    => 'Настройки шапки',
        'menu_title'    => 'Шапка',
        'parent_slug'   => 'theme-general-settings',
    ));
    
    acf_add_options_sub_page(array(
        'page_title'    => 'Настройки подвала',
        'menu_title'    => 'Подвал',
        'parent_slug'   => 'theme-general-settings',
    ));
    
    acf_add_options_sub_page(array(
        'page_title'    => 'Социальные сети',
        'menu_title'    => 'Социальные сети',
        'parent_slug'   => 'theme-general-settings',
    ));
    
}