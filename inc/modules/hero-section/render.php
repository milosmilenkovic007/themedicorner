<?php
/**
 * Hero Section Module Render
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Handle both direct fields and cloned hero fields structure
$hero_data = $layout['hero'] ?? $layout;
$title       = $hero_data['title'] ?? '';
$subtitle    = $hero_data['subtitle'] ?? '';
$image       = $hero_data['background_image'] ?? [];
$height      = $hero_data['height'] ?? 'large';
$overlay     = $hero_data['overlay_opacity'] ?? 30;
$buttons     = $hero_data['buttons'] ?? [];

// Konvertuj visinu
$height_class = 'hero--' . $height;
if ( $height === 'full' ) {
    $height_class = 'hero--full-screen';
}

// Background image style
$bg_style = '';
if ( ! empty( $image ) && isset( $image['url'] ) ) {
    $bg_style = 'background-image: url(\'' . esc_url( $image['url'] ) . '\');';
}
?>

<section class="hero <?php echo esc_attr( $height_class ); ?>" style="<?php echo esc_attr( $bg_style ); ?>">
    <div class="hero__overlay" style="background-color: rgba(0, 0, 0, <?php echo esc_attr( $overlay / 100 ); ?>);"></div>
    
    <div class="container">
        <div class="hero__content">
            <?php if ( $title ) : ?>
                <h1 class="hero__title"><?php echo wp_kses_post( $title ); ?></h1>
            <?php endif; ?>
            
            <?php if ( $subtitle ) : ?>
                <p class="hero__subtitle"><?php echo wp_kses_post( $subtitle ); ?></p>
            <?php endif; ?>
            
            <?php if ( ! empty( $buttons ) ) : ?>
                <div class="hero__buttons">
                    <?php foreach ( $buttons as $button ) : ?>
                        <?php if ( ! empty( $button['text'] ) && ! empty( $button['link'] ) ) : ?>
                            <a href="<?php echo esc_url( $button['link'] ); ?>" class="btn btn--<?php echo esc_attr( $button['style'] ?? 'primary' ); ?>">
                                <?php echo esc_html( $button['text'] ); ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
