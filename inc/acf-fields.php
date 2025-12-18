<?php
/**
 * ACF Field Groups Registration for Hello Elementor Child Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function hello_child_register_acf_field_groups() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    // Template Control
    acf_add_local_field_group( array(
        'key' => 'group_template_control',
        'title' => 'Template Control',
        'fields' => array(
            array(
                'key' => 'field_use_acf_template',
                'label' => 'Use ACF Template',
                'name' => 'use_acf_template',
                'type' => 'true_false',
                'instructions' => 'Enable this to use the ACF template instead of Elementor',
                'message' => 'Use ACF template for this page',
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
            ),
        ),
        'position' => 'side',
        'style' => 'default',
        'active' => true,
    ) );

    // Packages Content
    acf_add_local_field_group( array(
        'key' => 'group_packages_content',
        'title' => 'Packages Content',
        'fields' => array(
            // Header group
            array(
                'key' => 'field_packages_header',
                'label' => 'Page Header',
                'name' => 'packages_header',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_header_title',
                        'label' => 'Title',
                        'name' => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_header_subtitle',
                        'label' => 'Subtitle',
                        'name' => 'subtitle',
                        'type' => 'textarea',
                        'rows' => 3,
                    ),
                    array(
                        'key' => 'field_header_image',
                        'label' => 'Header Image',
                        'name' => 'image',
                        'type' => 'image',
                        'return_format' => 'id',
                    ),
                    array(
                        'key' => 'field_header_cta_text',
                        'label' => 'CTA Button Text',
                        'name' => 'cta_text',
                        'type' => 'text',
                        'default_value' => 'Get a free consultation',
                    ),
                    array(
                        'key' => 'field_header_cta_link',
                        'label' => 'CTA Button Link',
                        'name' => 'cta_link',
                        'type' => 'link',
                    ),
                ),
            ),

            // Sections
            array(
                'key' => 'field_package_sections',
                'label' => 'Package Sections',
                'name' => 'package_sections',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add Package Section',
                'sub_fields' => array(
                    array(
                        'key' => 'field_section_title',
                        'label' => 'Section Title',
                        'name' => 'section_title',
                        'type' => 'text',
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
                        'key' => 'field_packages_repeater',
                        'label' => 'Packages',
                        'name' => 'packages',
                        'type' => 'repeater',
                        'layout' => 'block',
                        'button_label' => 'Add Package',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_pkg_name',
                                'label' => 'Package Name',
                                'name' => 'name',
                                'type' => 'text',
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_pkg_price',
                                'label' => 'Price',
                                'name' => 'price',
                                'type' => 'text',
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_pkg_currency',
                                'label' => 'Currency',
                                'name' => 'currency',
                                'type' => 'select',
                                'choices' => array(
                                    'EUR' => '€ EUR',
                                    'USD' => '$ USD',
                                    'RSD' => 'дин RSD',
                                ),
                                'default_value' => 'EUR',
                            ),
                            array(
                                'key' => 'field_pkg_features',
                                'label' => 'Features',
                                'name' => 'features',
                                'type' => 'wysiwyg',
                                'toolbar' => 'basic',
                                'media_upload' => 0,
                                'instructions' => 'Use list format for features',
                            ),
                            array(
                                'key' => 'field_pkg_btn_text',
                                'label' => 'Button Text',
                                'name' => 'button_text',
                                'type' => 'text',
                                'default_value' => 'Book Now',
                            ),
                            array(
                                'key' => 'field_pkg_btn_link',
                                'label' => 'Button Link',
                                'name' => 'button_link',
                                'type' => 'link',
                            ),
                            array(
                                'key' => 'field_pkg_featured',
                                'label' => 'Featured Package',
                                'name' => 'is_featured',
                                'type' => 'true_false',
                                'ui' => 1,
                            ),
                            array(
                                'key' => 'field_pkg_badge',
                                'label' => 'Badge Text',
                                'name' => 'badge_text',
                                'type' => 'text',
                                'instructions' => 'e.g., "Most Popular", "Best Value"',
                            ),
                        ),
                    ),
                ),
            ),

            // Bottom CTA
            array(
                'key' => 'field_cta_section',
                'label' => 'Bottom CTA Section',
                'name' => 'cta_section',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_cta_enabled',
                        'label' => 'Show CTA Section',
                        'name' => 'enabled',
                        'type' => 'true_false',
                        'ui' => 1,
                    ),
                    array(
                        'key' => 'field_cta_heading',
                        'label' => 'Heading',
                        'name' => 'heading',
                        'type' => 'text',
                        'conditional_logic' => array(
                            array(
                                array('field' => 'field_cta_enabled', 'operator' => '==', 'value' => '1'),
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_cta_text',
                        'label' => 'Text Content',
                        'name' => 'text',
                        'type' => 'textarea',
                        'rows' => 3,
                        'conditional_logic' => array(
                            array(
                                array('field' => 'field_cta_enabled', 'operator' => '==', 'value' => '1'),
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_cta_btn_text',
                        'label' => 'Button Text',
                        'name' => 'button_text',
                        'type' => 'text',
                        'default_value' => 'Schedule Your Check-up',
                        'conditional_logic' => array(
                            array(
                                array('field' => 'field_cta_enabled', 'operator' => '==', 'value' => '1'),
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_cta_btn_link',
                        'label' => 'Button Link',
                        'name' => 'button_link',
                        'type' => 'link',
                        'conditional_logic' => array(
                            array(
                                array('field' => 'field_cta_enabled', 'operator' => '==', 'value' => '1'),
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_id',
                    'operator' => '==',
                    'value' => '590',
                ),
            ),
        ),
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
        'description' => 'ACF field group for Our Packages page content',
    ) );
}

add_action( 'acf/init', 'hello_child_register_acf_field_groups' );
