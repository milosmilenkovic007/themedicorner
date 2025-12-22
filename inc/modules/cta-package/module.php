<?php
/**
 * CTA Package Module
 * Call-to-action section with image
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Extract data
$heading = $data['heading'] ?? '';
$subheading = $data['subheading'] ?? '';
$content = $data['content'] ?? '';
$button_text = $data['button_text'] ?? '';
$button_link = $data['button_link'] ?? '';
$image_raw = $data['image'] ?? array();

// Normalize image to an array with url/alt even if ACF returns an ID
$image = array();
if ( is_array( $image_raw ) ) {
    // ACF return format "array"
    $image = $image_raw;
} elseif ( is_numeric( $image_raw ) ) {
    // ID only
    $src = wp_get_attachment_image_src( (int) $image_raw, 'full' );
    if ( $src ) {
        $image = array(
            'url' => $src[0],
            'alt' => get_post_meta( (int) $image_raw, '_wp_attachment_image_alt', true ),
        );
    }
}

// Style options
$bg_block_color = $data['bg_block_color'] ?? '#EBF2F2';
$bg_inner_color = $data['bg_inner_color'] ?? '#FFFFFF';
$heading_color = $data['heading_color'] ?? '#1EAFA0';
$subheading_color = $data['subheading_color'] ?? '#053B3F';
$button_text_color = $data['button_text_color'] ?? '#FFFFFF';
$button_bg_color = $data['button_bg_color'] ?? '#1EAFA0';

// Build inline styles
$block_style = sprintf(
    '--cta-bg-block: %s; --cta-bg-inner: %s; --cta-heading-color: %s; --cta-subheading-color: %s; --cta-btn-text: %s; --cta-btn-bg: %s;',
    esc_attr( $bg_block_color ),
    esc_attr( $bg_inner_color ),
    esc_attr( $heading_color ),
    esc_attr( $subheading_color ),
    esc_attr( $button_text_color ),
    esc_attr( $button_bg_color )
);
?>

<section class="module-cta-package" style="<?php echo esc_attr( $block_style ); ?>">
    <div class="cta-package__inner">
        <div class="cta-package__block">
            <div class="cta-package__content">
                <?php if ( $heading ) : ?>
                    <p class="cta-package__heading"><?php echo esc_html( $heading ); ?></p>
                <?php endif; ?>

                <?php if ( $subheading ) : ?>
                    <h2 class="cta-package__subheading"><?php echo esc_html( $subheading ); ?></h2>
                <?php endif; ?>

                <?php if ( $content ) : ?>
                    <p class="cta-package__text"><?php echo esc_html( $content ); ?></p>
                <?php endif; ?>

                <?php if ( $button_text && $button_link ) : ?>
                    <a href="<?php echo esc_url( $button_link ); ?>" class="cta-package__button">
                        <?php echo esc_html( $button_text ); ?>
                        <svg class="cta-package__arrow" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 1L8 15M8 15L15 8M8 15L1 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $image['url'] ) ) : ?>
                <div class="cta-package__image-wrap">
                    <img 
                        src="<?php echo esc_url( $image['url'] ); ?>" 
                        alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>"
                        class="cta-package__image"
                    >
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
