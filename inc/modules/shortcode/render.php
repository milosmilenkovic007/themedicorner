<?php
/**
 * Shortcode Module - Render
 */

$shortcode = $layout['shortcode'] ?? '';

if ( ! $shortcode ) {
    return;
}
?>

<div class="shortcode-module">
    <?php echo do_shortcode( wp_kses_post( $shortcode ) ); ?>
</div>
