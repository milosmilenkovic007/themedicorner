<?php
/**
 * Analiza Elementor modula
 * Ekstraktuj sve widget tipove i custom CSS iz svih stranica
 */

// WordPress setup
define('WP_USE_THEMES', false);
require('/Volumes/Data/Websites/medicorner/app/public/wp-load.php');

$pages = get_pages(['post_status' => 'publish']);
$all_widgets = [];
$all_custom_css = [];

foreach ($pages as $page) {
    $elementor_data = get_post_meta($page->ID, '_elementor_data', true);
    
    if ($elementor_data && !empty($elementor_data)) {
        $data = json_decode($elementor_data, true);
        
        if ($data) {
            extract_widgets($data, $all_widgets, $all_custom_css);
        }
    }
}

/**
 * Rekurzivno ekstraktuj widgete
 */
function extract_widgets($elements, &$widgets, &$css) {
    if (!is_array($elements)) {
        return;
    }
    
    foreach ($elements as $element) {
        if (!is_array($element)) {
            continue;
        }
        
        // Pronađi widget tip
        if (isset($element['widgetType'])) {
            $widget = $element['widgetType'];
            $widgets[$widget] = ($widgets[$widget] ?? 0) + 1;
        }
        
        // Pronađi custom CSS
        if (isset($element['settings']['custom_css'])) {
            $c = $element['settings']['custom_css'];
            if ($c && !in_array($c, $css)) {
                $css[] = $c;
            }
        }
        
        // Rekurzivno
        if (isset($element['elements'])) {
            extract_widgets($element['elements'], $widgets, $css);
        }
    }
}

// Prikaz rezultata
echo "\n";
echo "="*70 . "\n";
echo "ELEMENTOR WIDGET ANALIZA\n";
echo "="*70 . "\n\n";

arsort($all_widgets);
foreach ($all_widgets as $widget => $count) {
    printf("  %-30s %d\n", $widget, $count);
}

echo "\n" . "="*70 . "\n";
echo "CUSTOM CSS SNIPPETS\n";
echo "="*70 . "\n\n";

foreach ($all_custom_css as $i => $css) {
    echo "[$i] " . substr($css, 0, 80) . "...\n\n";
    echo $css . "\n\n";
    echo str_repeat("-", 70) . "\n\n";
}

echo "\nUkupno: " . count($all_widgets) . " widget tipova\n";
echo "CSS snippets: " . count($all_custom_css) . "\n\n";
?>
