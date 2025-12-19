<?php
/**
 * ACF Flexible Content Layouts
 * Sve flexible module u sistemu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registruj sve ACF flexible content layouts
 */
function hello_child_register_flexible_layouts() {
    // Pronađi sve module i registruj ih
    $modules_dir = get_stylesheet_directory() . '/inc/modules';
    
    if ( is_dir( $modules_dir ) ) {
        $modules = array_diff( scandir( $modules_dir ), array( '.', '..' ) );
        
        foreach ( $modules as $module ) {
            $module_path = $modules_dir . '/' . $module . '/config.php';
            
            if ( file_exists( $module_path ) ) {
                require_once $module_path;
            }
        }
    }
}
add_action( 'acf/init', 'hello_child_register_flexible_layouts' );

/**
 * Render flexible content layout
 */
function hello_child_render_flexible_layout( $layout ) {
    if ( empty( $layout ) || ! is_array( $layout ) ) {
        return;
    }

    $layout_type = $layout['acf_fc_layout'] ?? '';
    $module_path = get_stylesheet_directory() . '/inc/modules/' . $layout_type . '/module.php';

    if ( file_exists( $module_path ) ) {
        // Pass layout data into module template
        $data = $layout;
        include $module_path;
    }
}

/**
 * Get module config
 */
function hello_child_get_module_config( $module_name ) {
    $config_path = get_stylesheet_directory() . '/inc/modules/' . $module_name . '/config.php';
    
    if ( file_exists( $config_path ) ) {
        return require $config_path;
    }
    
    return [];
}
