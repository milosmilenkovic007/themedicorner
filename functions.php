<?php
/**
 * Hello Elementor Child Theme Functions
 *
 * @package Hello_Elementor_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Define constants
 */
define( 'HELLO_CHILD_VERSION', '1.0.0' );
define( 'HELLO_CHILD_DIR', get_stylesheet_directory() );
define( 'HELLO_CHILD_URI', get_stylesheet_directory_uri() );

/**
 * Enqueue parent and child theme styles
 */
function hello_elementor_child_enqueue_styles() {
    // Enqueue parent theme stylesheet
    wp_enqueue_style(
        'hello-elementor',
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme()->parent()->get('Version')
    );

    // Enqueue main compiled CSS
    wp_enqueue_style(
        'hello-elementor-child-main',
        get_stylesheet_directory_uri() . '/dist/main.css',
        ['hello-elementor'],
        HELLO_CHILD_VERSION
    );

    // Enqueue child theme stylesheet (fallback)
    wp_enqueue_style(
        'hello-elementor-child',
        get_stylesheet_directory_uri() . '/style.css',
        ['hello-elementor-child-main'],
        HELLO_CHILD_VERSION
    );

    // Enqueue main compiled JS
    wp_enqueue_script(
        'hello-elementor-child-main',
        get_stylesheet_directory_uri() . '/dist/main.js',
        ['jquery'],
        HELLO_CHILD_VERSION,
        true
    );

    // Enqueue packages CSS on packages page
    if ( is_page( 'our-packages' ) ) {
        // CSS already included in main.css, but can add page-specific if needed
    }
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_styles', 20 );

/**
 * ACF Options Page
 * Uncomment this when ACF Pro is installed
 */
/*
if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
        'page_title'    => 'Theme General Settings',
        'menu_title'    => 'Theme Settings',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
}
*/

/**
 * Register ACF Blocks
 * Uncomment and modify this when ready to create custom blocks
 */
/*
function hello_child_register_acf_blocks() {
    if ( function_exists( 'acf_register_block_type' ) ) {
        // Register a packages block
        acf_register_block_type(array(
            'name'              => 'packages',
            'title'             => __('Packages', 'hello-elementor-child'),
            'description'       => __('A custom packages block', 'hello-elementor-child'),
            'render_template'   => 'template-parts/blocks/packages/packages.php',
            'category'          => 'formatting',
            'icon'              => 'grid-view',
            'keywords'          => array( 'packages', 'pricing' ),
            'mode'              => 'edit',
            'supports'          => array(
                'align' => array( 'wide', 'full' ),
                'mode' => false,
            ),
        ));
    }
}
add_action('acf/init', 'hello_child_register_acf_blocks');
*/

/**
 * Custom template for Our Packages page
 * This will check if we should use ACF template instead of Elementor
 */
function hello_child_custom_page_template( $template ) {
    if ( is_page( 'our-packages' ) ) {
        // Check if ACF fields are set for this page
        // If yes, use custom template, otherwise use default (Elementor)
        if ( get_field( 'use_acf_template' ) ) {
            $custom_template = locate_template( 'page-templates/packages-acf.php' );
            if ( $custom_template ) {
                return $custom_template;
            }
        }
    }
    return $template;
}
// Uncomment when ready to switch to ACF
add_filter( 'template_include', 'hello_child_custom_page_template' );

/**
 * Include additional files
 */
require_once HELLO_CHILD_DIR . '/inc/acf-fields.php';
require_once HELLO_CHILD_DIR . '/inc/acf-flexible-layouts.php';
// require_once HELLO_CHILD_DIR . '/inc/custom-functions.php';
