<?php
/**
 * HTML Module - Config
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
    acf_add_local_field_group( array(
        'key' => 'group_html_module',
        'title' => 'HTML Module',
        'fields' => array(
            array(
                'key' => 'field_html_code',
                'label' => 'HTML Code',
                'name' => 'html_code',
                'type' => 'textarea',
                'required' => 1,
                'rows' => 6,
            ),
        ),
        'active' => false,
    ));
}
