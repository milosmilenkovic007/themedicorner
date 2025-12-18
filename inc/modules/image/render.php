<?php
/**
 * Image Module - Render
 */

$image_id = $layout['image'] ?? '';
$alt_text = $layout['alt_text'] ?? '';
$caption = $layout['caption'] ?? '';
$width = $layout['width'] ?? 100;

if ( ! $image_id ) {
    return;
}

$image_src = wp_get_attachment_image_src( $image_id, 'large' );
?>

<figure class="image-module" style="width: <?php echo intval( $width ); ?>%;">
    <img 
        src="<?php echo esc_url( $image_src[0] ); ?>" 
        alt="<?php echo esc_attr( $alt_text ); ?>"
        class="image-module__img"
    >
    <?php if ( $caption ) : ?>
        <figcaption class="image-module__caption">
            <?php echo wp_kses_post( $caption ); ?>
        </figcaption>
    <?php endif; ?>
</figure>
