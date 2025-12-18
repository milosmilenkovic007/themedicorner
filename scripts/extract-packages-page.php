<?php
/**
 * Extract Packages page Elementor data for migration to ACF
 */

// Load WordPress
require_once('/Volumes/Data/Websites/medicorner/app/public/wp-load.php');

$page_id = 590;

// Get page details
$page = get_post($page_id);
if (!$page) {
    die("Page not found");
}

echo "=== Page: " . $page->post_title . " ===\n";
echo "Post content:\n";
echo $page->post_content . "\n\n";

// Get Elementor data
$elementor_data = get_post_meta($page_id, '_elementor_data', true);
if ($elementor_data) {
    $data = json_decode($elementor_data, true);
    echo "=== Elementor Structure ===\n";
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
} else {
    echo "No Elementor data found\n";
}

// Get ACF fields if they exist
$acf_fields = get_field_objects($page_id);
if ($acf_fields) {
    echo "\n=== Existing ACF Fields ===\n";
    print_r($acf_fields);
}
