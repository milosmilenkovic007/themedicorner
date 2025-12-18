#!/usr/bin/env php
<?php
/**
 * Standalone migration script for Packages page (Elementor → ACF)
 * Direct MySQL access for Local by Flywheel
 * Run with: php scripts/migrate-packages-db.php [dry-run]
 */

// Get config from wp-config.php
$wp_config = '/Volumes/Data/Websites/medicorner/app/public/wp-config.php';
if ( ! file_exists( $wp_config ) ) {
    die( "Error: wp-config.php not found\n" );
}

$config_content = file_get_contents( $wp_config );
preg_match( "/define\(\s*'DB_NAME',\s*'([^']+)'\s*\)/", $config_content, $m );
$db_name = $m[1] ?? 'local';
preg_match( "/define\(\s*'DB_USER',\s*'([^']+)'\s*\)/", $config_content, $m );
$db_user = $m[1] ?? 'root';
preg_match( "/define\(\s*'DB_PASSWORD',\s*'([^']+)'\s*\)/", $config_content, $m );
$db_pass = $m[1] ?? 'root';
preg_match( "/define\(\s*'DB_HOST',\s*'([^']+)'\s*\)/", $config_content, $m );
$db_host = $m[1] ?? 'localhost';

// Determine table prefix
preg_match( "/\\\$table_prefix\s*=\s*'([^']+)'/", $config_content, $m );
$prefix = $m[1] ?? 'wp_';

// Local by Flywheel socket
$socket = '/Users/user/Library/Application Support/Local/run/skejlzMMw/mysql/mysqld.sock';

echo "=== Packages Migration (Elementor → ACF) ===\n";
echo "Database: $db_name\n";
echo "Table prefix: $prefix\n";
echo "Socket: $socket\n\n";

// Connect to DB via socket
$mysqli = new mysqli( 'localhost', $db_user, $db_pass, $db_name, 0, $socket );
if ( $mysqli->connect_error ) {
    die( "Connection failed: " . $mysqli->connect_error . "\n" );
}

$page_id = 590;
$dry_run = isset( $argv[1] ) && $argv[1] === 'dry-run';

echo "Page ID: $page_id\n";
echo "Dry Run: " . ( $dry_run ? 'YES' : 'NO' ) . "\n\n";

// Get Elementor data
$query = $mysqli->prepare( "SELECT meta_value FROM {$prefix}postmeta WHERE post_id = ? AND meta_key = '_elementor_data'" );
$query->bind_param( 'i', $page_id );
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();

if ( ! $row ) {
    die( "✗ No Elementor data found for page $page_id\n" );
}

$raw = $row['meta_value'];
$data = json_decode( $raw, true );
if ( ! is_array( $data ) ) {
    die( "✗ Failed to decode Elementor JSON\n" );
}

echo "✓ Elementor data loaded (" . strlen( $raw ) . " bytes)\n";

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
        $title = strip_tags( $w['settings']['title'] );
        break;
    }
}

$subtitle = '';
foreach ( $widgets as $w ) {
    if ( $w['widgetType'] === 'text-editor' && ! empty( $w['settings']['editor'] ) ) {
        $subtitle = strip_tags( $w['settings']['editor'] );
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
    $cta_text = isset( $btn['settings']['text'] ) ? $btn['settings']['text'] : '';
    if ( ! empty( $btn['settings']['link']['url'] ) ) {
        $cta_link['url'] = $btn['settings']['link']['url'];
    }
}

echo "--- HEADER ---\n";
echo "Title: " . ( $title ? "✓ $title" : "✗ (empty)" ) . "\n";
echo "Subtitle: " . ( $subtitle ? "✓ " . substr( $subtitle, 0, 50 ) . "..." : "✗ (empty)" ) . "\n";
echo "Image ID: " . ( $header_image_id ? "✓ $header_image_id" : "✗ (none)" ) . "\n";
echo "CTA: " . ( $cta_text ? "✓ $cta_text" : "✗ (empty)" ) . "\n\n";

// Helper functions for extraction
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
            return strip_tags( $widget['settings']['title'] );
        }
        if ( $widget['widgetType'] === 'text-editor' && ! empty( $widget['settings']['editor'] ) ) {
            return strip_tags( $widget['settings']['editor'] );
        }
    }
    return '';
};

$extract_button = function( $widget ) {
    if ( isset( $widget['widgetType'] ) && $widget['widgetType'] === 'button' ) {
        $text = $widget['settings']['text'] ?? '';
        $url  = $widget['settings']['link']['url'] ?? '';
        return [ 'text' => $text, 'link' => [ 'url' => $url ] ];
    }
    return null;
};

$extract_price = function( $text ) {
    $price = '';
    $currency = 'EUR';
    if ( preg_match( '/([€$]|RSD|EUR|USD)\s?([0-9]+(?:[\.,][0-9]{2})?)/iu', strip_tags( $text ), $m ) ) {
        $sym = strtoupper( $m[1] );
        $price = $m[2];
        if ( $sym === '€' || $sym === 'EUR' ) $currency = 'EUR';
        elseif ( $sym === '$' || $sym === 'USD' ) $currency = 'USD';
        elseif ( $sym === 'RSD' ) $currency = 'RSD';
    }
    return [ $price, $currency ];
};

// Map sections & packages
// Only include containers with direct child card containers (heuristic for package sections)
$sections = [];
foreach ( $containers as $container ) {
    $direct_children = $container['elements'] ?? [];
    
    // Count direct child containers (likely cards)
    $card_count = 0;
    foreach ( $direct_children as $child ) {
        if ( isset( $child['elType'] ) && $child['elType'] === 'container' ) {
            $card_count++;
        }
    }
    
    // Skip if this looks like a structural wrapper (too few or too many direct cards)
    if ( $card_count < 2 || $card_count > 10 ) {
        continue;
    }

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
    foreach ( $direct_children as $child ) {
        if ( isset( $child['elType'] ) && $child['elType'] === 'container' ) {
            $pkg_headings = $extract_widgets( $child, 'heading' );
            $pkg_texts    = $extract_widgets( $child, 'text-editor' );
            $pkg_buttons  = $extract_widgets( $child, 'button' );

            if ( empty( $pkg_headings ) && empty( $pkg_texts ) ) {
                continue;
            }

            $name = $pkg_headings ? strip_tags( $extract_text( $pkg_headings[0] ) ) : '';
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

    if ( $section_title && ! empty( $packages ) ) {
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

echo "\n--- CTA SECTION ---\n";
echo "Status: Enabled\n";
echo "Button: " . ( $cta_text ?: 'Get a free consultation' ) . "\n\n";

// Build ACF field values
$header = [ 'title' => $title, 'subtitle' => $subtitle, 'image' => $header_image_id ?: null, 'cta_text' => $cta_text ?: 'Get a free consultation', 'cta_link' => $cta_link ];
$cta_section = [ 'enabled' => 1, 'heading' => 'Need Help Choosing?', 'text' => "Get a free consultation and we'll tailor a check-up package for you.", 'button_text' => $cta_text ?: 'Get a free consultation', 'button_link' => $cta_link ];

if ( ! $dry_run ) {
    echo "🔄 Saving to ACF...\n";

    // Helper to insert/update post meta
    $save_field = function( $field_name, $value, $post_id ) use ( $mysqli, $prefix ) {
        $field_value = json_encode( $value );
        $field_value = $mysqli->real_escape_string( $field_value );

        // Check if exists
        $check = $mysqli->prepare( "SELECT meta_id FROM {$prefix}postmeta WHERE post_id = ? AND meta_key = ?" );
        $check->bind_param( 'is', $post_id, $field_name );
        $check->execute();
        $exists = $check->get_result()->fetch_assoc();

        if ( $exists ) {
            $update = $mysqli->prepare( "UPDATE {$prefix}postmeta SET meta_value = ? WHERE post_id = ? AND meta_key = ?" );
            $update->bind_param( 'sis', $field_value, $post_id, $field_name );
            $update->execute();
        } else {
            $insert = $mysqli->prepare( "INSERT INTO {$prefix}postmeta (post_id, meta_key, meta_value) VALUES (?, ?, ?)" );
            $insert->bind_param( 'iss', $post_id, $field_name, $field_value );
            $insert->execute();
        }
    };

    $save_field( 'packages_header', $header, $page_id );
    $save_field( 'package_sections', $sections, $page_id );
    $save_field( 'cta_section', $cta_section, $page_id );
    $save_field( 'use_acf_template', [ true ], $page_id );

    echo "✓ Migration complete!\n";
} else {
    echo "✓ Dry run complete (no changes made)\n";
}

echo "\nNext: Edit page $page_id in WP Admin to verify or adjust content.\n";

$mysqli->close();
