<?php
/**
 * ACF Field Groups Registration
 * 
 * Register ACF fields programmatically for Packages page
 * This is better than creating fields in UI because it's version controlled
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register ACF Fields for Packages Page
 */
function hello_child_register_package_fields() {
    if ( function_exists( 'acf_add_local_field_group' ) ) {
        
        // Template Control Field
        acf_add_local_field_group( array(
            'key' => 'group_template_control',
            'title' => 'Template Control',
            'fields' => array(
                array(
                    'key' => 'field_use_acf_template',
                    'label' => 'Use ACF Template',
                    'name' => 'use_acf_template',
                    'type' => 'true_false',
                    'instructions' => 'Enable this to use ACF template instead of Elementor for this page',
                    'default_value' => 0,
                    'ui' => 1,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'page',
                    ),
                    array(
                        'param' => 'page',
                        'operator' => '==',
                        'value' => '590', // Our Packages page ID
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'side',
            'style' => 'default',
        ));

        // Packages Content Fields
        acf_add_local_field_group( array(
            'key' => 'group_packages_content',
            'title' => 'Packages Content',
            'fields' => array(
                array(
                    'key' => 'field_package_sections',
                    'label' => 'Package Sections',
                    'name' => 'package_sections',
                    'type' => 'repeater',
                    'instructions' => 'Add different package sections (e.g., Medical Packages, Wellness Packages)',
                    'layout' => 'block',
                    'button_label' => 'Add Section',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_section_title',
                            'label' => 'Section Title',
                            'name' => 'section_title',
                            'type' => 'text',
                            'placeholder' => 'e.g., Medical Packages',
                        ),
                        array(
                            'key' => 'field_section_description',
                            'label' => 'Section Description',
                            'name' => 'section_description',
                            'type' => 'wysiwyg',
                            'toolbar' => 'basic',
                            'media_upload' => 0,
                        ),
                        array(
                            'key' => 'field_packages',
                            'label' => 'Packages',
                            'name' => 'packages',
                            'type' => 'repeater',
                            'layout' => 'block',
                            'button_label' => 'Add Package',
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_package_name',
                                    'label' => 'Package Name',
                                    'name' => 'package_name',
                                    'type' => 'text',
                                    'required' => 1,
                                ),
                                array(
                                    'key' => 'field_package_price',
                                    'label' => 'Package Price',
                                    'name' => 'package_price',
                                    'type' => 'text',
                                    'placeholder' => 'e.g., $299',
                                ),
                                array(
                                    'key' => 'field_package_features',
                                    'label' => 'Package Features',
                                    'name' => 'package_features',
                                    'type' => 'wysiwyg',
                                    'toolbar' => 'basic',
                                    'media_upload' => 0,
                                    'instructions' => 'Use bullet points for features',
                                ),
                                array(
                                    'key' => 'field_package_button_text',
                                    'label' => 'Button Text',
                                    'name' => 'package_button_text',
                                    'type' => 'text',
                                    'placeholder' => 'e.g., Book Now',
                                    'default_value' => 'Book Now',
                                ),
                                array(
                                    'key' => 'field_package_button_link',
                                    'label' => 'Button Link',
                                    'name' => 'package_button_link',
                                    'type' => 'url',
                                    'placeholder' => 'https://',
                                ),
                                array(
                                    'key' => 'field_is_featured',
                                    'label' => 'Featured Package',
                                    'name' => 'is_featured',
                                    'type' => 'true_false',
                                    'instructions' => 'Highlight this package as popular/recommended',
                                    'default_value' => 0,
                                    'ui' => 1,
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'page',
                    ),
                    array(
                        'param' => 'page',
                        'operator' => '==',
                        'value' => '590', // Our Packages page ID
                    ),
                ),
            ),
            'menu_order' => 1,
            'position' => 'normal',
            'style' => 'default',
            'active' => true,
        ));
    }
}
add_action( 'acf/init', 'hello_child_register_package_fields' );
