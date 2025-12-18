<?php
/**
 * Icon List Module - Config
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
    acf_add_local_field_group( array(
        'key' => 'group_icon_list_module',
        'title' => 'Icon List Module',
        'fields' => array(
            array(
                'key' => 'field_icon_list_items',
                'label' => 'Items',
                'name' => 'items',
                'type' => 'repeater',
                'button_label' => 'Add Item',
                'layout' => 'table',
                'sub_fields' => array(
                    array(
                        'key' => 'field_icon_list_item_text',
                        'label' => 'Text',
                        'name' => 'text',
                        'type' => 'text',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_icon_list_item_icon',
                        'label' => 'Icon Class',
                        'name' => 'icon',
                        'type' => 'text',
                        'instructions' => 'e.g., fa-check, fa-star, etc.',
                    ),
                ),
            ),
        ),
        'active' => false,
    ));
}
