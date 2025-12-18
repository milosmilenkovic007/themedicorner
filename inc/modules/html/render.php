<?php
/**
 * HTML Module - Render
 */

$html_code = $layout['html_code'] ?? '';

if ( ! $html_code ) {
    return;
}
?>

<div class="html-module">
    <?php echo wp_kses_post( $html_code ); ?>
</div>
