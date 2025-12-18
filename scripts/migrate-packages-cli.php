#!/usr/bin/env php
<?php
/**
 * Standalone migration script for Packages page (Elementor → ACF)
 * Run with: php scripts/migrate-packages-cli.php [dry-run]
 */

// Detect WordPress root
$wp_root = dirname( dirname( dirname( dirname( __DIR__ ) ) ) );
$wp_load = $wp_root . '/wp-load.php';

if ( ! file_exists( $wp_load ) ) {
    die( "Error: wp-load.php not found at $wp_load\n" );
}

require_once $wp_load;

// Check if we have ACF
if ( ! function_exists( 'get_field' ) ) {
    die( "Error: ACF not found\n" );
}

$dry_run = isset( $argv[1] ) && $argv[1] === 'dry-run';
$page_id = 590; // Our Packages

echo "=== Packages Migration (Elementor → ACF) ===\n";
echo "Page ID: $page_id\n";
echo "Dry Run: " . ( $dry_run ? 'YES' : 'NO' ) . "\n\n";

// Get Elementor data
$raw = get_post_meta( $page_id, '_elementor_data', true );
if ( empty( $raw ) ) {
    die( "✗ No Elementor data found for page $page_id\n" );
}

$data = json_decode( $raw, true );
if ( ! is_array( $data ) ) {
    die( "✗ Failed to decode Elementor JSON\n" );
}

echo "✓ Elementor data loaded\n";

// Parse widgets
$widgets    = [];
$images     = [];
$buttons    = [];
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

echo "✓ Found: " . count( $widgets ) . " widgets, " . count( $containers ) . " containers\n\n";

// Extract header
$title = '';
foreach ( $widgets as $w ) {
    if ( $w['widgetType'] === 'heading' && ! empty( $w['settings']['title'] ) ) {
        $title = wp_kses_post( $w['settings']['title'] );
        break;
    }
}

$subtitle = '';
foreach ( $widgets as $w ) {
    if ( $w['widgetType'] === 'text-editor' && ! empty( $w['settings']['editor'] ) ) {
        $subtitle = wp_kses_post( $w['settings']['editor'] );
        break;
    }
}

$header_image_id = 0;
if ( ! empty( $images ) ) {
    $img = $images[0];
    if ( ! empty( $img['settings']['image']['id'] ) ) {
        $header_image_id = intval( $img['settings']['image']['id'] );
    }
}

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

echo "--- HEADER ---\n";
echo "Title: " . ( $title ? "✓ $title" : "✗ (empty)" ) . "\n";
echo "Subtitle: " . ( $subtitle ? "✓ " . substr( $subtitle, 0, 50 ) . "..." : "✗ (empty)" ) . "\n";
echo "Image ID: " . ( $header_image_id ? "✓ $header_image_id" : "✗ (none)" ) . "\n";
echo "CTA: " . ( $cta_text ? "✓ $cta_text" : "✗ (empty)" ) . "\n\n";

// Map sections & packages
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

$sections = [];
foreach ( $containers as $container ) {
    $headings = $extract_widgets( $container, 'heading' );
    $texts    = $extract_widgets( $container, 'text-editor' );

    if ( empty( $headings ) ) {
        continue;
    }

    $section_title = $extract_text( $headings[0] );
    $section_desc  = '';
    if ( ! empty( $texts ) ) {
        $section_desc = $extract_text( $texts[0] );
    }

    $packages = [];
    if ( ! empty( $container['elements'] ) ) {
        foreach ( $container['elements'] as $child ) {
            if ( isset( $child['elType'] ) && $child['elType'] === 'container' ) {
                $pkg_headings = $extract_widgets( $child, 'heading' );
                $pkg_texts    = $extract_widgets( $child, 'text-editor' );
                $pkg_buttons  = $extract_widgets( $child, 'button' );

                if ( empty( $pkg_headings ) && empty( $pkg_texts ) ) {
                    continue;
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

    if ( $section_title || $packages ) {
        $sections[] = [
            'section_title'       => $section_title,
            'section_description' => $section_desc,
            'packages'            => $packages,
        ];
    }
}

echo "--- SECTIONS & PACKAGES ---\n";
echo "Found " . count( $sections ) . " section(s)\n";
foreach ( $sections as $idx => $sec ) {
    echo "\nSection " . ( $idx + 1 ) . ": " . ( $sec['section_title'] ?: '(untitled)' ) . "\n";
    echo "  Packages: " . count( $sec['packages'] ) . "\n";
    foreach ( $sec['packages'] as $pkg ) {
        echo "    - " . $pkg['name'] . " | " . $pkg['price'] . " " . $pkg['currency'] . "\n";
    }
}

$cta_section = [
    'enabled'     => 1,
    'heading'     => 'Need Help Choosing?',
    'text'        => "Get a free consultation and we'll tailor a check-up package for you.",
    'button_text' => $cta_text ?: 'Get a free consultation',
    'button_link' => $cta_link,
];

echo "\n--- CTA SECTION ---\n";
echo "Status: " . ( $cta_section['enabled'] ? 'Enabled' : 'Disabled' ) . "\n";
echo "Button: " . $cta_section['button_text'] . "\n\n";

// Execute migration
if ( ! $dry_run ) {
    echo "🔄 Saving to ACF...\n";
    update_field( 'packages_header', $header, $page_id );
    update_field( 'package_sections', $sections, $page_id );
    update_field( 'cta_section', $cta_section, $page_id );
    update_field( 'use_acf_template', 1, $page_id );
    echo "✓ Migration complete!\n";
} else {
    echo "✓ Dry run complete (no changes made)\n";
}

echo "\nNext: Edit page $page_id in WP Admin to verify or adjust content.\n";
