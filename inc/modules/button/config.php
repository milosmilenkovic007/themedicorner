<?php
/**
 * Button Module - Config
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
    acf_add_local_field_group( array(
        'key' => 'group_button_module',
        'title' => 'Button Module',
        'fields' => array(
            array(
                'key' => 'field_button_text',
                'label' => 'Button Text',
                'name' => 'text',
                'type' => 'text',
                'required' => 1,
            ),
            array(
                'key' => 'field_button_link',
                'label' => 'Button Link',
                'name' => 'link',
                'type' => 'link',
                'required' => 1,
            ),
            array(
                'key' => 'field_button_style',
                'label' => 'Style',
                'name' => 'style',
                'type' => 'select',
                'choices' => array(
                    'primary' => 'Primary',
                    'secondary' => 'Secondary',
                    'outline' => 'Outline',
                ),
                'default_value' => 'primary',
            ),
            array(
                'key' => 'field_button_size',
                'label' => 'Size',
                'name' => 'size',
                'type' => 'select',
                'choices' => array(
                    'small' => 'Small',
                    'medium' => 'Medium',
                    'large' => 'Large',
                ),
                'default_value' => 'medium',
            ),
        ),
        'active' => false,
    ));
}
