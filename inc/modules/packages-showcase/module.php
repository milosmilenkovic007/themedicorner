<?php
/**
 * Packages Showcase Module
 * Renders 3-column package showcase boxes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$title = $data['field_showcase_title'] ?? $data['title'] ?? '';
$packages = $data['field_showcase_packages'] ?? $data['packages'] ?? array();
?>

<section class="module-packages-showcase">
    <?php if ( $title ) : ?>
        <h2 class="packages-showcase__title"><?php echo wp_kses_post( $title ); ?></h2>
    <?php endif; ?>

    <?php if ( ! empty( $packages ) ) : ?>
        <div class="packages-showcase__grid">
            <?php foreach ( $packages as $package ) : ?>
                <?php
                $number = $package['field_showcase_pkg_number'] ?? $package['number'] ?? '';
                $name = $package['field_showcase_pkg_name'] ?? $package['name'] ?? '';
                $description = $package['field_showcase_pkg_description'] ?? $package['description'] ?? '';
                $is_featured = $package['field_showcase_pkg_featured'] ?? $package['is_featured'] ?? false;
                ?>
                <div class="packages-showcase__item <?php echo $is_featured ? 'is-featured' : ''; ?>">
                    <?php if ( $number ) : ?>
                        <div class="packages-showcase__number"><?php echo esc_html( $number ); ?></div>
                    <?php endif; ?>
                    
                    <h3 class="packages-showcase__name"><?php echo esc_html( $name ); ?></h3>
                    
                    <?php if ( $description ) : ?>
                        <p class="packages-showcase__description"><?php echo wp_kses_post( $description ); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
