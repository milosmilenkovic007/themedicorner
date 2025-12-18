<?php
/**
 * Hero Section Module Config
 * ACF flexible content layout configuration
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
    acf_add_local_field_group( array(
        'key' => 'group_flexible_hero_section',
        'title' => 'Hero Section Module',
        'fields' => array(
            // Add to flexible content field
            array(
                'key' => 'field_hero_layout',
                'label' => 'Hero Section',
                'name' => 'hero_section',
                'type' => 'clone',
                'clone' => array( 'group_hero_section_fields' ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                ),
            ),
        ),
        'active' => false, // This will be added via flexible content
    ));

    // Field group untuk hero section
    acf_add_local_field_group( array(
        'key' => 'group_hero_section_fields',
        'title' => 'Hero Section Fields',
        'fields' => array(
            array(
                'key' => 'field_hero_title',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
                'required' => 1,
                'placeholder' => 'Enter hero title',
            ),
            array(
                'key' => 'field_hero_subtitle',
                'label' => 'Subtitle',
                'name' => 'subtitle',
                'type' => 'textarea',
                'rows' => 3,
                'placeholder' => 'Enter hero subtitle',
            ),
            array(
                'key' => 'field_hero_image',
                'label' => 'Background Image',
                'name' => 'background_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_hero_height',
                'label' => 'Hero Height',
                'name' => 'height',
                'type' => 'select',
                'choices' => array(
                    'auto' => 'Auto',
                    'small' => 'Small (400px)',
                    'medium' => 'Medium (600px)',
                    'large' => 'Large (800px)',
                    'full' => 'Full Screen',
                ),
                'default_value' => 'large',
            ),
            array(
                'key' => 'field_hero_overlay',
                'label' => 'Overlay Opacity',
                'name' => 'overlay_opacity',
                'type' => 'slider',
                'min' => 0,
                'max' => 100,
                'step' => 10,
                'default_value' => 30,
            ),
            array(
                'key' => 'field_hero_buttons',
                'label' => 'Buttons',
                'name' => 'buttons',
                'type' => 'repeater',
                'button_label' => 'Add Button',
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_hero_button_text',
                        'label' => 'Button Text',
                        'name' => 'text',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_hero_button_link',
                        'label' => 'Button Link',
                        'name' => 'link',
                        'type' => 'url',
                    ),
                    array(
                        'key' => 'field_hero_button_style',
                        'label' => 'Button Style',
                        'name' => 'style',
                        'type' => 'select',
                        'choices' => array(
                            'primary' => 'Primary',
                            'secondary' => 'Secondary',
                            'white' => 'White',
                        ),
                        'default_value' => 'primary',
                    ),
                ),
            ),
        ),
        'active' => false,
    ));
}
