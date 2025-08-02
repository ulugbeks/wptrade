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
    
}