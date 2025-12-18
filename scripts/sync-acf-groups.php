#!/usr/bin/env php
<?php
/**
 * Direct ACF field group insertion with all wp_posts columns
 */

$socket = '/Users/user/Library/Application Support/Local/run/skejlzMMw/mysql/mysqld.sock';
$mysqli = new mysqli( 'localhost', 'root', 'root', 'local', 0, $socket );

if ( $mysqli->connect_error ) {
    die( "Connection failed: " . $mysqli->connect_error );
}

// Read JSON files
$json_dir = dirname( dirname( __FILE__ ) ) . '/acf-json';
$files = glob( $json_dir . '/*.json' );

$groups = [
    'group_template_control',
    'group_page_modules',
    'group_packages_content'
];

foreach ( $groups as $group_name ) {
    $file = "$json_dir/$group_name.json";
    if ( ! file_exists( $file ) ) {
        echo "✗ File not found: $file\n";
        continue;
    }

    $json = file_get_contents( $file );
    $data = json_decode( $json, true );
    
    if ( ! is_array( $data ) ) {
        $data = [ $data ];
    }

    foreach ( $data as $field_group ) {
        $key = $field_group['key'];
        $title = $field_group['title'];
        $content = addslashes( json_encode( $field_group ) );
        $now = date( 'Y-m-d H:i:s' );

        // Check if exists
        $check = $mysqli->query( "SELECT ID FROM wp_posts WHERE post_name='$key' AND post_type='acf-field-group'" );
        
        if ( $check->num_rows > 0 ) {
            echo "ℹ Exists: $title\n";
            continue;
        }

        $sql = "INSERT INTO wp_posts 
        (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, post_type, post_name, ping_status, comment_status, to_ping, pinged, post_content_filtered, menu_order)
        VALUES 
        (1, '$now', '$now', '$content', '$title', '', 'publish', 'acf-field-group', '$key', 'open', 'closed', '', '', '', 0)";

        if ( $mysqli->query( $sql ) ) {
            $post_id = $mysqli->insert_id;
            echo "✓ Created: $title (ID: $post_id)\n";
        } else {
            echo "✗ Error inserting $title: " . $mysqli->error . "\n";
        }
    }
}

$mysqli->close();
echo "\n✓ Complete!\n";
?>
