<?php
/**
 * Heading Module - Config
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
    acf_add_local_field_group( array(
        'key' => 'group_heading_module',
        'title' => 'Heading Module',
        'fields' => array(
            array(
                'key' => 'field_heading_title',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
                'required' => 1,
            ),
            array(
                'key' => 'field_heading_tag',
                'label' => 'HTML Tag',
                'name' => 'tag',
                'type' => 'select',
                'choices' => array(
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                ),
                'default_value' => 'h2',
            ),
            array(
                'key' => 'field_heading_alignment',
                'label' => 'Alignment',
                'name' => 'alignment',
                'type' => 'select',
                'choices' => array(
                    'left' => 'Left',
                    'center' => 'Center',
                    'right' => 'Right',
                ),
                'default_value' => 'left',
            ),
        ),
        'active' => false,
    ));
}
