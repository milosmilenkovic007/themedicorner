<?php
/**
 * ACF Field Groups Registration
 * Clean setup with flexible page modules
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function hello_child_register_acf_field_groups() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    // Template Control - Global
    acf_add_local_field_group( array(
        'key' => 'group_template_control',
        'title' => 'Template Control',
        'fields' => array(
            array(
                'key' => 'field_use_acf_template',
                'label' => 'Use ACF Template',
                'name' => 'use_acf_template',
                'type' => 'true_false',
                'instructions' => 'Enable to use ACF flexible modules instead of Elementor',
                'message' => 'Use ACF flexible modules',
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

    // Page Modules - Flexible Content
    acf_add_local_field_group( array(
        'key' => 'group_page_modules',
        'title' => 'Page Modules',
        'fields' => array(
            array(
                'key' => 'field_page_modules',
                'label' => 'Modules',
                'name' => 'page_modules',
                'type' => 'flexible_content',
                'button_label' => 'Add Module',
                'layouts' => array(
                    // Hero Section
                    array(
                        'key' => 'layout_hero_section',
                        'name' => 'hero-section',
                        'label' => 'Hero Section',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_hero_title',
                                'label' => 'Title',
                                'name' => 'title',
                                'type' => 'text',
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_hero_subtitle',
                                'label' => 'Subtitle',
                                'name' => 'subtitle',
                                'type' => 'textarea',
                                'rows' => 3,
                            ),
                            array(
                                'key' => 'field_hero_image',
                                'label' => 'Background Image',
                                'name' => 'background_image',
                                'type' => 'image',
                                'return_format' => 'array',
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
                        ),
                    ),
                    // Packages Showcase
                    array(
                        'key' => 'layout_packages_showcase',
                        'name' => 'packages-showcase',
                        'label' => 'Packages Showcase',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_showcase_title',
                                'label' => 'Section Title',
                                'name' => 'title',
                                'type' => 'text',
                            ),
                            array(
                                'key' => 'field_showcase_packages',
                                'label' => 'Packages',
                                'name' => 'packages',
                                'type' => 'repeater',
                                'button_label' => 'Add Package',
                                'layout' => 'block',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_showcase_pkg_name',
                                        'label' => 'Package Name',
                                        'name' => 'name',
                                        'type' => 'text',
                                        'required' => 1,
                                    ),
                                    array(
                                        'key' => 'field_showcase_pkg_number',
                                        'label' => 'Number',
                                        'name' => 'number',
                                        'type' => 'text',
                                    ),
                                    array(
                                        'key' => 'field_showcase_pkg_description',
                                        'label' => 'Description',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'rows' => 3,
                                    ),
                                    array(
                                        'key' => 'field_showcase_pkg_featured',
                                        'label' => 'Featured',
                                        'name' => 'is_featured',
                                        'type' => 'true_false',
                                        'ui' => 1,
                                    ),
                                ),
                            ),
                        ),
                    ),
                    // Packages Details
                    array(
                        'key' => 'layout_packages_details',
                        'name' => 'packages-details',
                        'label' => 'Packages Details',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_details_title',
                                'label' => 'Section Title',
                                'name' => 'title',
                                'type' => 'text',
                            ),
                            array(
                                'key' => 'field_details_accordions',
                                'label' => 'Accordion Sections',
                                'name' => 'accordions',
                                'type' => 'repeater',
                                'button_label' => 'Add Accordion',
                                'layout' => 'block',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_accordion_title',
                                        'label' => 'Title',
                                        'name' => 'title',
                                        'type' => 'text',
                                        'required' => 1,
                                    ),
                                    array(
                                        'key' => 'field_accordion_items',
                                        'label' => 'Items',
                                        'name' => 'items',
                                        'type' => 'repeater',
                                        'button_label' => 'Add Item',
                                        'layout' => 'block',
                                        'sub_fields' => array(
                                            array(
                                                'key' => 'field_item_text',
                                                'label' => 'Text',
                                                'name' => 'text',
                                                'type' => 'text',
                                                'required' => 1,
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    // Testimonials
                    array(
                        'key' => 'layout_testimonials',
                        'name' => 'testimonials',
                        'label' => 'Testimonials',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_testimonials_title',
                                'label' => 'Section Title',
                                'name' => 'title',
                                'type' => 'text',
                            ),
                            array(
                                'key' => 'field_testimonials_rating_text',
                                'label' => 'Rating Text',
                                'name' => 'rating_text',
                                'type' => 'text',
                                'default_value' => 'Rated 5/5 Based on 109 reviews',
                            ),
                            array(
                                'key' => 'field_testimonials_shortcode',
                                'label' => 'Shortcode',
                                'name' => 'shortcode',
                                'type' => 'text',
                            ),
                        ),
                    ),
                    // CTA Section
                    array(
                        'key' => 'layout_cta_section',
                        'name' => 'cta-section',
                        'label' => 'CTA Section',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_cta_heading',
                                'label' => 'Heading',
                                'name' => 'heading',
                                'type' => 'text',
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_cta_content',
                                'label' => 'Content',
                                'name' => 'content',
                                'type' => 'wysiwyg',
                                'toolbar' => 'basic',
                                'media_upload' => 0,
                            ),
                            array(
                                'key' => 'field_cta_features',
                                'label' => 'Features',
                                'name' => 'features',
                                'type' => 'repeater',
                                'button_label' => 'Add Feature',
                                'layout' => 'block',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_cta_feature_icon',
                                        'label' => 'Icon',
                                        'name' => 'icon',
                                        'type' => 'image',
                                        'return_format' => 'array',
                                    ),
                                    array(
                                        'key' => 'field_cta_feature_title',
                                        'label' => 'Title',
                                        'name' => 'title',
                                        'type' => 'text',
                                        'required' => 1,
                                    ),
                                    array(
                                        'key' => 'field_cta_feature_text',
                                        'label' => 'Description',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'rows' => 2,
                                    ),
                                ),
                            ),
                            array(
                                'key' => 'field_cta_button_text',
                                'label' => 'Button Text',
                                'name' => 'button_text',
                                'type' => 'text',
                                'default_value' => 'Get a free consultation',
                            ),
                            array(
                                'key' => 'field_cta_button_link',
                                'label' => 'Button Link',
                                'name' => 'button_link',
                                'type' => 'link',
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
            ),
        ),
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
        'description' => 'Flexible content page modules',
    ) );
}

add_action( 'acf/init', 'hello_child_register_acf_field_groups' );
