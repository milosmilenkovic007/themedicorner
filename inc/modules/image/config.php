<?php
/**
 * Image Module - Config
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
    acf_add_local_field_group( array(
        'key' => 'group_image_module',
        'title' => 'Image Module',
        'fields' => array(
            array(
                'key' => 'field_image',
                'label' => 'Image',
                'name' => 'image',
                'type' => 'image',
                'required' => 1,
                'return_format' => 'id',
            ),
            array(
                'key' => 'field_image_alt',
                'label' => 'Alt Text',
                'name' => 'alt_text',
                'type' => 'text',
            ),
            array(
                'key' => 'field_image_caption',
                'label' => 'Caption',
                'name' => 'caption',
                'type' => 'textarea',
            ),
            array(
                'key' => 'field_image_width',
                'label' => 'Width (%)',
                'name' => 'width',
                'type' => 'number',
                'default_value' => 100,
            ),
        ),
        'active' => false,
    ));
}
