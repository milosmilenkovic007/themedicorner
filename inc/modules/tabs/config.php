<?php
/**
 * Tabs Module - Config
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
    acf_add_local_field_group( array(
        'key' => 'group_tabs_module',
        'title' => 'Tabs Module',
        'fields' => array(
            array(
                'key' => 'field_tabs_items',
                'label' => 'Tabs',
                'name' => 'tabs',
                'type' => 'repeater',
                'button_label' => 'Add Tab',
                'layout' => 'table',
                'sub_fields' => array(
                    array(
                        'key' => 'field_tabs_item_title',
                        'label' => 'Tab Title',
                        'name' => 'title',
                        'type' => 'text',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_tabs_item_content',
                        'label' => 'Tab Content',
                        'name' => 'content',
                        'type' => 'wysiwyg',
                    ),
                ),
            ),
        ),
        'active' => false,
    ));
}
