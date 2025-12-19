<?php
/**
 * Testimonials Module
 * Renders testimonials section with rating and shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$title = $data['title'] ?? '';
$rating_text = $data['rating_text'] ?? '';
$shortcode = $data['shortcode'] ?? '';
?>

<section class="module-testimonials">
    <?php if ( $title ) : ?>
        <h2 class="testimonials__title"><?php echo wp_kses_post( $title ); ?></h2>
    <?php endif; ?>

    <?php if ( $rating_text ) : ?>
        <p class="testimonials__rating"><?php echo esc_html( $rating_text ); ?></p>
    <?php endif; ?>

    <?php if ( $shortcode ) : ?>
        <div class="testimonials__carousel">
            <?php echo do_shortcode( $shortcode ); ?>
        </div>
    <?php endif; ?>
</section>
