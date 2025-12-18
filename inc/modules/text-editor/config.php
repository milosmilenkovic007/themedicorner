<?php
/**
 * Text Editor Module - Config
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
    acf_add_local_field_group( array(
        'key' => 'group_text_editor_module',
        'title' => 'Text Editor Module',
        'fields' => array(
            array(
                'key' => 'field_text_content',
                'label' => 'Content',
                'name' => 'content',
                'type' => 'wysiwyg',
                'required' => 1,
            ),
        ),
        'active' => false,
    ));
}
