<?php
/**
 * Icon List Module - Render
 */

$items = $layout['items'] ?? [];

if ( empty( $items ) ) {
    return;
}
?>

<ul class="icon-list-module">
    <?php foreach ( $items as $item ) : ?>
        <li class="icon-list-module__item">
            <?php if ( ! empty( $item['icon'] ) ) : ?>
                <i class="icon-list-module__icon fas <?php echo esc_attr( $item['icon'] ); ?>"></i>
            <?php endif; ?>
            <span class="icon-list-module__text"><?php echo esc_html( $item['text'] ); ?></span>
        </li>
    <?php endforeach; ?>
</ul>
