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

$arrow_src = get_stylesheet_directory_uri() . '/assets/images/arrow.svg';
?>

<section class="module-testimonials">
    <div class="container">
		<?php if ( $title ) : ?>
            <div class="testimonials__header">
                <div class="testimonials__header-left">
                    <?php if ( $title ) : ?>
                        <h2 class="testimonials__title"><?php echo wp_kses_post( $title ); ?></h2>
                    <?php endif; ?>
                </div>

                <?php if ( $shortcode ) : ?>
                    <div class="testimonials__header-right">
                        <div class="testimonials__nav" aria-label="Testimonials navigation">
                            <button class="testimonials__nav-btn testimonials__nav-btn--prev" type="button" aria-label="Previous testimonial">
                                <img src="<?php echo esc_url( $arrow_src ); ?>" alt="" />
                            </button>
                            <button class="testimonials__nav-btn testimonials__nav-btn--next" type="button" aria-label="Next testimonial">
                                <img src="<?php echo esc_url( $arrow_src ); ?>" alt="" />
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ( $shortcode ) : ?>
            <div class="testimonials__carousel">
                <?php echo do_shortcode( $shortcode ); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
