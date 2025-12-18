<?php
/**
 * Admin tool to migrate Packages page from Elementor to ACF
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_menu', function() {
    add_management_page(
        'Migrate Packages to ACF',
        'Migrate Packages to ACF',
        'manage_options',
        'hello-child-migrate-packages',
        'hello_child_render_packages_migration_page'
    );
});

function hello_child_render_packages_migration_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $page_id = 590; // Our Packages
    $run      = isset( $_POST['run_migration'] );

    if ( $run ) {
        check_admin_referer( 'hello_child_migrate_packages' );
        $result = hello_child_run_packages_migration( $page_id );
        echo '<div class="notice notice-success"><p>' . esc_html( $result ) . '</p></div>';
    }

    echo '<div class="wrap">';
    echo '<h1>Migrate "Our Packages" Page to ACF</h1>';
    echo '<p>This tool will read Elementor JSON from page ID ' . intval( $page_id ) . ' and populate the ACF fields (header + CTA). Package sections may require manual review.</p>';

    echo '<form method="post">';
    wp_nonce_field( 'hello_child_migrate_packages' );
    submit_button( 'Run Migration', 'primary', 'run_migration' );
    echo '</form>';
    echo '</div>';
}

function hello_child_run_packages_migration( $page_id ) {
    $raw = get_post_meta( $page_id, '_elementor_data', true );
    if ( empty( $raw ) ) {
        return 'No Elementor data found.';
    }

    $data = json_decode( $raw, true );
    if ( ! is_array( $data ) ) {
        return 'Failed to decode Elementor JSON.';
    }

    // Helper to walk JSON and collect widgets
    $widgets = [];
    $images  = [];
    $buttons = [];

    $walk = function( $node ) use ( &$walk, &$widgets, &$images, &$buttons ) {
        if ( isset( $node['elType'] ) && $node['elType'] === 'widget' && isset( $node['widgetType'] ) ) {
            $widgets[] = $node;
            if ( $node['widgetType'] === 'image' ) {
                $images[] = $node;
            }
            if ( $node['widgetType'] === 'button' ) {
                $buttons[] = $node;
            }
        }
        if ( isset( $node['elements'] ) && is_array( $node['elements'] ) ) {
            foreach ( $node['elements'] as $child ) {
                $walk( $child );
            }
        }
    };

    foreach ( $data as $root ) {
        $walk( $root );
    }

    // Extract header title (first heading)
    $title = '';
    foreach ( $widgets as $w ) {
        if ( $w['widgetType'] === 'heading' && ! empty( $w['settings']['title'] ) ) {
            $title = wp_kses_post( $w['settings']['title'] );
            break;
        }
    }

    // Extract subtitle (first text-editor)
    $subtitle = '';
    foreach ( $widgets as $w ) {
        if ( $w['widgetType'] === 'text-editor' && ! empty( $w['settings']['editor'] ) ) {
            $subtitle = wp_kses_post( $w['settings']['editor'] );
            break;
        }
    }

    // Extract first image as header image
    $header_image_id = 0;
    if ( ! empty( $images ) ) {
        $img = $images[0];
        if ( ! empty( $img['settings']['image']['id'] ) ) {
            $header_image_id = intval( $img['settings']['image']['id'] );
        }
    }

    // Extract CTA button (first button)
    $cta_text = '';
    $cta_link = [ 'url' => '', 'title' => '', 'target' => '' ];
    if ( ! empty( $buttons ) ) {
        $btn = $buttons[0];
        $cta_text = isset( $btn['settings']['text'] ) ? sanitize_text_field( $btn['settings']['text'] ) : '';
        if ( ! empty( $btn['settings']['link']['url'] ) ) {
            $cta_link['url'] = esc_url_raw( $btn['settings']['link']['url'] );
        }
    }

    // Build header payload
    $header = [
        'title'      => $title,
        'subtitle'   => $subtitle,
        'image'      => $header_image_id ?: null,
        'cta_text'   => $cta_text ?: 'Get a free consultation',
        'cta_link'   => $cta_link,
    ];

    // Update ACF header
    update_field( 'packages_header', $header, $page_id );

    // Optionally: initialize an empty sections repeater to avoid nulls
    if ( ! get_field( 'package_sections', $page_id ) ) {
        update_field( 'package_sections', [], $page_id );
    }

    // Bottom CTA from existing button (simple default)
    $cta_section = [
        'enabled'     => 1,
        'heading'     => 'Need Help Choosing?',
        'text'        => 'Get a free consultation and we’ll tailor a check-up package for you.',
        'button_text' => $cta_text ?: 'Get a free consultation',
        'button_link' => $cta_link,
    ];
    update_field( 'cta_section', $cta_section, $page_id );

    // Enable ACF template
    update_field( 'use_acf_template', 1, $page_id );

    return 'Migration complete: header + CTA populated, ACF template enabled.';
}
