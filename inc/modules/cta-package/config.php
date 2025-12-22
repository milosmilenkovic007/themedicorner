<?php
/**
 * CTA Package Module Config
 * Module registration for flexible content
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// This file is loaded by hello_child_register_flexible_layouts() in acf-flexible-layouts.php
// Module is defined in inc/acf-fields.php as layout_cta_package
// No additional configuration needed - ACF fields are registered globally

return array(
    'name' => 'cta-package',
    'label' => 'CTA Package',
);
