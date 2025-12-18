<?php
/**
 * Force ACF to sync field groups from JSON
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_init', function() {
    // Check if we need to sync
    if ( ! isset( $_GET['acf_sync_json'] ) || $_GET['acf_sync_json'] !== '1' ) {
        return;
    }

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Unauthorized' );
    }

    // Force ACF to load from JSON
    if ( function_exists( 'acf_get_local_field_groups' ) ) {
        // Get all local field groups
        $groups = acf_get_local_field_groups();
        
        foreach ( $groups as $group ) {
            $key = $group['key'];
            
            // Check if it exists in DB
            $existing = get_posts( array(
                'post_type'  => 'acf-field-group',
                'meta_query' => array(
                    array(
                        'key'   => 'key',
                        'value' => $key,
                    ),
                ),
            ) );

            if ( empty( $existing ) ) {
                // Field group doesn't exist in DB, so ACF will create it
                $field_group = acf_get_local_field_group( $key );
                if ( $field_group ) {
                    acf_import_field_group( $field_group );
                }
            }
        }

        wp_safe_remote_get( add_query_arg( array( 'acf_sync_json' => '0' ), admin_url( 'admin.php' ) ) );
        wp_die( 'ACF field groups synchronized from JSON!' );
    }
} );

// Add a link to trigger sync if needed
add_action( 'admin_notices', function() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $sync_url = add_query_arg( array( 'acf_sync_json' => '1' ), admin_url( 'admin.php' ) );
    echo '<div class="notice notice-warning"><p>';
    echo 'ACF: <a href="' . esc_url( $sync_url ) . '" class="button">Sync Field Groups from JSON</a>';
    echo '</p></div>';
} );
