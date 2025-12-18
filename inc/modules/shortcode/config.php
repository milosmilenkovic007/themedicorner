<?php
/**
 * Shortcode Module - Config
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
    acf_add_local_field_group( array(
        'key' => 'group_shortcode_module',
        'title' => 'Shortcode Module',
        'fields' => array(
            array(
                'key' => 'field_shortcode_code',
                'label' => 'Shortcode',
                'name' => 'shortcode',
                'type' => 'textarea',
                'required' => 1,
                'instructions' => 'Enter WordPress shortcode, e.g., [contact-form-7 id="123"]',
            ),
        ),
        'active' => false,
    ));
}
