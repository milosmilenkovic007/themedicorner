<?php
/**
 * Rating Module - Config
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
    acf_add_local_field_group( array(
        'key' => 'group_rating_module',
        'title' => 'Rating Module',
        'fields' => array(
            array(
                'key' => 'field_rating_value',
                'label' => 'Rating',
                'name' => 'rating',
                'type' => 'number',
                'min' => 0,
                'max' => 5,
                'step' => 0.5,
                'required' => 1,
            ),
            array(
                'key' => 'field_rating_label',
                'label' => 'Label',
                'name' => 'label',
                'type' => 'text',
            ),
        ),
        'active' => false,
    ));
}
