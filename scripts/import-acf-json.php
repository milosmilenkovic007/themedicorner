#!/usr/bin/env php
<?php
/**
 * Import ACF field groups from JSON to database
 * Run with: php scripts/import-acf-json.php
 */

$socket = '/Users/user/Library/Application Support/Local/run/skejlzMMw/mysql/mysqld.sock';
$mysqli = new mysqli( 'localhost', 'root', 'root', 'local', 0, $socket );

if ( $mysqli->connect_error ) {
    die( "Connection failed: " . $mysqli->connect_error );
}

$json_dir = dirname( dirname( __FILE__ ) ) . '/acf-json';
$prefix = 'wp_';

echo "=== ACF JSON Import ===\n";
echo "Reading from: $json_dir\n\n";

$files = glob( $json_dir . '/*.json' );

foreach ( $files as $file ) {
    $content = file_get_contents( $file );
    $data = json_decode( $content, true );

    if ( ! is_array( $data ) ) {
        echo "✗ Invalid JSON: " . basename( $file ) . "\n";
        continue;
    }

    // Handle both single group and array of groups
    if ( ! isset( $data[0] ) ) {
        $data = [ $data ];
    }

    foreach ( $data as $field_group ) {
        $key = $field_group['key'] ?? null;
        $title = $field_group['title'] ?? 'Untitled';

        if ( ! $key ) {
            echo "✗ No key in: " . basename( $file ) . "\n";
            continue;
        }

        // Check if group already exists
        $check = $mysqli->prepare( "SELECT ID FROM {$prefix}posts WHERE post_type = 'acf-field-group' AND post_name = ?" );
        $check->bind_param( 's', $key );
        $check->execute();
        $result = $check->get_result();
        $existing = $result->fetch_assoc();

        if ( $existing ) {
            // Update existing
            $post_id = $existing['ID'];
            $field_data = json_encode( $field_group );
            $field_data = $mysqli->real_escape_string( $field_data );

            $update = $mysqli->prepare( "UPDATE {$prefix}posts SET post_title = ?, post_content = ? WHERE ID = ?" );
            $update->bind_param( 'ssi', $title, $field_data, $post_id );
            $update->execute();

            echo "✓ Updated: $title\n";
        } else {
            // Insert new
            $field_data = $mysqli->real_escape_string( json_encode( $field_group ) );
            $title_esc = $mysqli->real_escape_string( $title );
            $now = date( 'Y-m-d H:i:s' );

            $sql = "INSERT INTO {$prefix}posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, post_type, post_name, ping_status, comment_status, to_ping, pinged) VALUES (1, '$now', '$now', '$field_data', '$title_esc', '', 'publish', 'acf-field-group', '$key', 'open', 'open', '', '')";
            
            if ( $mysqli->query( $sql ) ) {
                $post_id = $mysqli->insert_id;
                echo "✓ Created: $title (ID: $post_id)\n";
            } else {
                echo "✗ Failed to create: $title - " . $mysqli->error . "\n";
            }
        }
    }
}

echo "\n✓ ACF field groups imported successfully!\n";
$mysqli->close();
