<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package FXForTrader
 */

/**
 * Adds custom classes to the array of body classes.
 */
function fxfortrader_body_classes( $classes ) {
    // Adds a class of hfeed to non-singular pages.
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }

    // Adds a class of no-sidebar when there is no sidebar present.
    if ( ! is_active_sidebar( 'sidebar-1' ) ) {
        $classes[] = 'no-sidebar';
    }

    return $classes;
}
add_filter( 'body_class', 'fxfortrader_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function fxfortrader_pingback_header() {
    if ( is_singular() && pings_open() ) {
        printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
    }
}
add_action( 'wp_head', 'fxfortrader_pingback_header' );

/**
 * Изменение длины excerpt
 */
function fxfortrader_excerpt_length( $length ) {
    return 20;
}
add_filter( 'excerpt_length', 'fxfortrader_excerpt_length', 999 );

/**
 * Изменение окончания excerpt
 */
function fxfortrader_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'fxfortrader_excerpt_more' );

/**
 * Пагинация
 */
function fxfortrader_pagination() {
    global $wp_query;
    $big = 999999999;
    
    $pages = paginate_links( array(
        'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format' => '?paged=%#%',
        'current' => max( 1, get_query_var('paged') ),
        'total' => $wp_query->max_num_pages,
        'prev_text' => '<i class="fas fa-angle-left"></i>',
        'next_text' => '<i class="fas fa-angle-right"></i>',
        'type'  => 'array',
        'prev_next' => true,
        'end_size' => 1,
        'mid_size' => 2,
    ) );
    
    if( is_array( $pages ) ) {
        echo '<div class="pagination-wrapper centred"><ul class="pagination clearfix">';
        foreach ( $pages as $page ) {
            echo "<li>$page</li>";
        }
        echo '</ul></div>';
    }
}