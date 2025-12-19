<?php
/**
 * Hero Section Module
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$title    = $data['title'] ?? '';
$subtitle = $data['subtitle'] ?? '';
$image    = $data['background_image'] ?? [];
$height   = $data['height'] ?? 'large';

$height_class = 'hero--' . $height;
if ( $height === 'full' ) {
    $height_class = 'hero--full-screen';
}

$bg_style = '';
if ( ! empty( $image ) && isset( $image['url'] ) ) {
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
