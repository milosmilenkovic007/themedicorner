<?php
/**
 * Hero Section Module
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$title    = $data['field_hero_title'] ?? $data['title'] ?? '';
$subtitle = $data['field_hero_subtitle'] ?? $data['subtitle'] ?? '';
$image    = $data['field_hero_image'] ?? $data['background_image'] ?? [];
$height   = $data['field_hero_height'] ?? $data['height'] ?? 'large';

$height_class = 'hero--' . $height;
if ( $height === 'full' ) {
    $height_class = 'hero--full-screen';
}

$bg_style = '';
if ( ! empty( $image ) && ! is_array( $image ) ) {
    // Image is an ID, get the URL
    $image_url = wp_get_attachment_url( $image );
    if ( $image_url ) {
        $bg_style = 'background-image: url(' . esc_url( $image_url ) . ')';
    }
} elseif ( is_array( $image ) && isset( $image['url'] ) ) {
    // Image is already an array with URL
    $bg_style = 'background-image: url(' . esc_url( $image['url'] ) . ')';
}
?>

<section class="hero <?php echo esc_attr( $height_class ); ?>" style="<?php echo esc_attr( $bg_style ); ?>">
    <div class="container">
        <div class="hero__content">
            <?php if ( $title ) : ?>
                <h1 class="hero__title"><?php echo wp_kses_post( $title ); ?></h1>
            <?php endif; ?>

            <?php if ( $subtitle ) : ?>
                <p class="hero__subtitle"><?php echo wp_kses_post( $subtitle ); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>
