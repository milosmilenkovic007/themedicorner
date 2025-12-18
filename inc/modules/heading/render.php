<?php
/**
 * Heading Module - Render
 */

$title = $layout['title'] ?? '';
$tag = $layout['tag'] ?? 'h2';
$alignment = $layout['alignment'] ?? 'left';
?>

<div class="heading-module text-<?php echo esc_attr( $alignment ); ?>">
    <?php
    echo "<{$tag} class='heading-module__title'>";
    echo wp_kses_post( $title );
    echo "</{$tag}>";
    ?>
</div>
