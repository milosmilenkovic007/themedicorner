<?php
/**
 * Text Editor Module - Render
 */

$content = $layout['content'] ?? '';
?>

<div class="text-editor-module">
    <div class="text-editor-module__content">
        <?php echo wp_kses_post( $content ); ?>
    </div>
</div>
