<?php
/**
 * Button Module - Render
 */

$text = $layout['text'] ?? '';
$link = $layout['link'] ?? [];
$style = $layout['style'] ?? 'primary';
$size = $layout['size'] ?? 'medium';

if ( ! $text || ! ( $link['url'] ?? '' ) ) {
    return;
}

$link_url = esc_url( $link['url'] );
$link_target = ( $link['target'] ?? '' ) === '_blank' ? '_blank' : '_self';
?>

<div class="button-module">
    <a 
        href="<?php echo $link_url; ?>" 
        class="btn btn--<?php echo esc_attr( $style ); ?> btn--<?php echo esc_attr( $size ); ?>"
        target="<?php echo esc_attr( $link_target ); ?>"
    >
        <?php echo esc_html( $text ); ?>
    </a>
</div>
