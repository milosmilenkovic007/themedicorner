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
    $dry_run  = ! empty( $_POST['dry_run'] );

    if ( $run ) {
        check_admin_referer( 'hello_child_migrate_packages' );
        $result = hello_child_run_packages_migration( $page_id, $dry_run );
        echo '<div class="notice notice-success"><p>' . wp_kses_post( $result ) . '</p></div>';
    }

    echo '<div class="wrap">';
    echo '<h1>Migrate "Our Packages" Page to ACF</h1>';
    echo '<p>This tool reads Elementor JSON from page ID ' . intval( $page_id ) . ' and populates ACF fields (header, sections, packages, CTA). Use Dry Run to preview mappings.</p>';

    echo '<form method="post">';
    wp_nonce_field( 'hello_child_migrate_packages' );
    echo '<p><label><input type="checkbox" name="dry_run" ' . ( $dry_run ? 'checked' : '' ) . '> Dry Run (preview only)</label></p>';
    submit_button( 'Run Migration', 'primary', 'run_migration' );
    echo '</form>';
    echo '</div>';
}

function hello_child_run_packages_migration( $page_id, $dry_run = false ) {
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
    $containers = [];

    $walk = function( $node ) use ( &$walk, &$widgets, &$images, &$buttons, &$containers ) {
        if ( isset( $node['elType'] ) && $node['elType'] === 'widget' && isset( $node['widgetType'] ) ) {
            $widgets[] = $node;
            if ( $node['widgetType'] === 'image' ) {
                $images[] = $node;
            }
            if ( $node['widgetType'] === 'button' ) {
                $buttons[] = $node;
            }
        }
        if ( isset( $node['elType'] ) && $node['elType'] === 'container' ) {
            $containers[] = $node;
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
    if ( ! $dry_run ) {
        update_field( 'packages_header', $header, $page_id );
    }

    // Collect sections + packages heuristically
    $mapped_sections = hello_child_el_map_sections_and_packages( $containers );
    if ( ! empty( $mapped_sections ) ) {
        if ( ! $dry_run ) {
            update_field( 'package_sections', $mapped_sections, $page_id );
        }
    }

    // Bottom CTA from existing button (simple default)
    $cta_section = [
        'enabled'     => 1,
        'heading'     => 'Need Help Choosing?',
        'text'        => 'Get a free consultation and we’ll tailor a check-up package for you.',
        'button_text' => $cta_text ?: 'Get a free consultation',
        'button_link' => $cta_link,
    ];
    if ( ! $dry_run ) {
        update_field( 'cta_section', $cta_section, $page_id );
    }

    // Enable ACF template
    if ( ! $dry_run ) {
        update_field( 'use_acf_template', 1, $page_id );
    }

    $preview = '';
    if ( $dry_run ) {
        $preview .= '<h3>Preview</h3>';
        $preview .= '<p><strong>Header Title:</strong> ' . esc_html( $title ) . '</p>';
        $preview .= '<p><strong>CTA:</strong> ' . esc_html( $cta_text ) . ' → ' . esc_html( $cta_link['url'] ?? '' ) . '</p>';
        $preview .= '<p><strong>Sections mapped:</strong> ' . count( $mapped_sections ) . '</p>';
        foreach ( $mapped_sections as $idx => $sec ) {
            $preview .= '<div style="padding:8px;border:1px solid #ddd;margin:8px 0">';
            $preview .= '<p><strong>Section ' . ( $idx + 1 ) . ':</strong> ' . esc_html( $sec['section_title'] ?? '' ) . '</p>';
            $preview .= '<ul>';
            if ( ! empty( $sec['packages'] ) ) {
                foreach ( $sec['packages'] as $pkg ) {
                    $preview .= '<li>' . esc_html( ($pkg['name'] ?? '') . ' — ' . ($pkg['price'] ?? '') . ' ' . ($pkg['currency'] ?? '') ) . '</li>';
                }
            }
            $preview .= '</ul>';
            $preview .= '</div>';
        }
    }

    return ( $dry_run ? 'Dry run complete.' . $preview : 'Migration complete: header, sections/packages, CTA populated; ACF template enabled.' );
}

/**
 * Map Elementor containers into ACF package sections + packages
 * Heuristics-based parser; safe defaults if data missing.
 */
function hello_child_el_map_sections_and_packages( array $containers ): array {
    $sections = [];

    // Helpers
    $extract_widgets = function( $node, $type = null ) {
        $out = [];
        $walk = function( $n ) use ( &$walk, &$out, $type ) {
            if ( isset( $n['elType'] ) && $n['elType'] === 'widget' ) {
                if ( ! $type || ( isset( $n['widgetType'] ) && $n['widgetType'] === $type ) ) {
                    $out[] = $n;
                }
            }
            if ( ! empty( $n['elements'] ) && is_array( $n['elements'] ) ) {
                foreach ( $n['elements'] as $c ) { $walk( $c ); }
            }
        };
        $walk( $node );
        return $out;
    };

    $extract_text = function( $widget ) {
        if ( isset( $widget['widgetType'] ) ) {
            if ( $widget['widgetType'] === 'heading' && ! empty( $widget['settings']['title'] ) ) {
                return wp_kses_post( $widget['settings']['title'] );
            }
            if ( $widget['widgetType'] === 'text-editor' && ! empty( $widget['settings']['editor'] ) ) {
                return wp_kses_post( $widget['settings']['editor'] );
            }
        }
        return '';
    };

    $extract_button = function( $widget ) {
        if ( isset( $widget['widgetType'] ) && $widget['widgetType'] === 'button' ) {
            $text = sanitize_text_field( $widget['settings']['text'] ?? '' );
            $url  = esc_url_raw( $widget['settings']['link']['url'] ?? '' );
            return [ 'text' => $text, 'link' => [ 'url' => $url ] ];
        }
        return null;
    };

    $extract_price = function( $text ) {
        $price = '';
        $currency = 'EUR';
        if ( preg_match( '/([€$]|RSD|EUR|USD)\s?([0-9]+(?:[\.,][0-9]{2})?)/iu', wp_strip_all_tags( $text ), $m ) ) {
            $sym = strtoupper( $m[1] );
            $price = $m[2];
            if ( $sym === '€' || $sym === 'EUR' ) $currency = 'EUR';
            elseif ( $sym === '$' || $sym === 'USD' ) $currency = 'USD';
            elseif ( $sym === 'RSD' ) $currency = 'RSD';
        }
        return [ $price, $currency ];
    };

    // Heuristic: a section is a container that contains a heading and optionally a description
    foreach ( $containers as $container ) {
        $headings = $extract_widgets( $container, 'heading' );
        $texts    = $extract_widgets( $container, 'text-editor' );

        if ( empty( $headings ) ) {
            continue; // not a section
        }

        $section_title = $extract_text( $headings[0] );
        $section_desc  = '';
        if ( ! empty( $texts ) ) {
            $section_desc = $extract_text( $texts[0] );
        }

        // Find child containers that look like package cards (have heading/text/button)
        $packages = [];
        if ( ! empty( $container['elements'] ) ) {
            foreach ( $container['elements'] as $child ) {
                if ( isset( $child['elType'] ) && $child['elType'] === 'container' ) {
                    $pkg_headings = $extract_widgets( $child, 'heading' );
                    $pkg_texts    = $extract_widgets( $child, 'text-editor' );
                    $pkg_buttons  = $extract_widgets( $child, 'button' );

                    if ( empty( $pkg_headings ) && empty( $pkg_texts ) ) {
                        continue; // unlikely to be a card
                    }

                    $name = $pkg_headings ? wp_strip_all_tags( $extract_text( $pkg_headings[0] ) ) : '';
                    $features_html = $pkg_texts ? $extract_text( $pkg_texts[0] ) : '';
                    list( $price, $currency ) = $extract_price( $features_html . ' ' . $name );
                    $button = null;
                    if ( ! empty( $pkg_buttons ) ) {
                        $button = $extract_button( $pkg_buttons[0] );
                    }

                    $packages[] = [
                        'name'         => $name ?: 'Package',
                        'price'        => $price ?: '',
                        'currency'     => $currency,
                        'features'     => $features_html,
                        'button_text'  => $button['text'] ?? 'Book Now',
                        'button_link'  => $button['link'] ?? [ 'url' => '' ],
                        'is_featured'  => 0,
                        'badge_text'   => '',
                    ];
                }
            }
        }

        // Only add section if it has at least a title or packages
        if ( $section_title || $packages ) {
            $sections[] = [
                'section_title'       => $section_title,
                'section_description' => $section_desc,
                'packages'            => $packages,
            ];
        }
    }

    return $sections;
}
