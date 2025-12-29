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
 * Packages CPT + taxonomies
 */
function hello_child_register_packages_cpt() {
    $labels = array(
        'name'                  => __( 'Packages', 'hello-elementor-child' ),
        'singular_name'         => __( 'Package', 'hello-elementor-child' ),
        'menu_name'             => __( 'Packages', 'hello-elementor-child' ),
        'name_admin_bar'        => __( 'Package', 'hello-elementor-child' ),
        'add_new'               => __( 'Add New', 'hello-elementor-child' ),
        'add_new_item'          => __( 'Add New Package', 'hello-elementor-child' ),
        'new_item'              => __( 'New Package', 'hello-elementor-child' ),
        'edit_item'             => __( 'Edit Package', 'hello-elementor-child' ),
        'view_item'             => __( 'View Package', 'hello-elementor-child' ),
        'all_items'             => __( 'All Packages', 'hello-elementor-child' ),
        'search_items'          => __( 'Search Packages', 'hello-elementor-child' ),
        'not_found'             => __( 'No packages found.', 'hello-elementor-child' ),
        'not_found_in_trash'    => __( 'No packages found in Trash.', 'hello-elementor-child' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_in_rest'       => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'packages' ),
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-clipboard',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'capability_type'    => 'post',
    );

    register_post_type( 'package', $args );
}
add_action( 'init', 'hello_child_register_packages_cpt' );

function hello_child_register_packages_taxonomies() {
    // Custom category (hierarchical)
    register_taxonomy(
        'package_category',
        array( 'package' ),
        array(
            'labels' => array(
                'name'          => __( 'Package Categories', 'hello-elementor-child' ),
                'singular_name' => __( 'Package Category', 'hello-elementor-child' ),
            ),
            'public'            => true,
            'show_in_rest'      => true,
            'hierarchical'      => true,
            'show_admin_column' => true,
            'rewrite'           => array( 'slug' => 'package-category' ),
        )
    );

    // Custom tags (non-hierarchical)
    register_taxonomy(
        'package_tag',
        array( 'package' ),
        array(
            'labels' => array(
                'name'          => __( 'Package Tags', 'hello-elementor-child' ),
                'singular_name' => __( 'Package Tag', 'hello-elementor-child' ),
            ),
            'public'            => true,
            'show_in_rest'      => true,
            'hierarchical'      => false,
            'show_admin_column' => true,
            'rewrite'           => array( 'slug' => 'package-tag' ),
        )
    );
}
add_action( 'init', 'hello_child_register_packages_taxonomies' );

/**
 * Redirect Packages archive (and related taxonomies) to the curated /our-packages/ page on frontend.
 */
function hello_child_redirect_package_archive() {
    if ( is_admin() || is_feed() || is_preview() ) {
        return;
    }

    // Avoid interfering with REST/AJAX.
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return;
    }

    $should_redirect = is_post_type_archive( 'package' ) || is_tax( array( 'package_category', 'package_tag' ) );
    if ( ! $should_redirect ) {
        return;
    }

    $target = home_url( '/our-packages/' );
    wp_safe_redirect( $target, 301 );
    exit;
}
add_action( 'template_redirect', 'hello_child_redirect_package_archive' );

/**
 * Admin UI tweaks for ACF fields
 */
add_action( 'admin_head', function() {
        // Make Packages Showcase bullet icons preview smaller inside repeater.
        echo '<style>
            .acf-field[data-key="field_showcase_pkg_item_icon"] .acf-image-uploader .image-wrap img,
            .acf-field[data-key="field_showcase_pkg_item_icon"] img {
                max-width: 28px !important;
                max-height: 28px !important;
                width: 28px !important;
                height: 28px !important;
                object-fit: contain;
            }
            .acf-field[data-key="field_showcase_pkg_item_icon"] .acf-image-uploader .image-wrap {
                max-width: 34px;
            }
        </style>';
} );

/**
 * ACF debug helper (opt-in)
 * Visit any wp-admin page with ?hello_acf_debug=1 to see status.
 */
add_action( 'admin_notices', function() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    if ( empty( $_GET['hello_acf_debug'] ) ) {
        return;
    }

    $acf_loaded = function_exists( 'acf_add_local_field_group' );
    $groups = $acf_loaded && function_exists( 'acf_get_local_field_groups' ) ? acf_get_local_field_groups() : array();
    $group_count = is_array( $groups ) ? count( $groups ) : 0;

    echo '<div class="notice notice-info"><p>';
    echo '<strong>Hello Child ACF Debug</strong><br>';
    echo 'ACF loaded: ' . ( $acf_loaded ? 'YES' : 'NO' ) . '<br>';
    echo 'Local field groups registered: ' . intval( $group_count ) . '<br>';
    if ( $acf_loaded && $group_count ) {
        $keys = array();
        foreach ( $groups as $g ) {
            if ( is_array( $g ) && ! empty( $g['key'] ) ) {
                $keys[] = sanitize_text_field( $g['key'] );
            }
        }
        echo 'Keys: ' . esc_html( implode( ', ', $keys ) );
    }
    echo '</p></div>';
} );

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
 * Include additional files
 */
require_once HELLO_CHILD_DIR . '/inc/acf-fields.php';
require_once HELLO_CHILD_DIR . '/inc/acf-flexible-layouts.php';
// require_once HELLO_CHILD_DIR . '/inc/migrate-packages.php';
// require_once HELLO_CHILD_DIR . '/inc/custom-functions.php';

/**
 * ACF JSON Sync - Save Point
 * This allows ACF to save field groups as JSON in /acf-json folder
 */
add_filter( 'acf/settings/save_json', function( $path ) {
    return HELLO_CHILD_DIR . '/acf-json';
} );

/**
 * ACF JSON Sync - Load Point
 * This allows ACF to load field groups from /acf-json folder
 */
add_filter( 'acf/settings/load_json', function( $paths ) {
    $paths[] = HELLO_CHILD_DIR . '/acf-json';
    return $paths;
} );
