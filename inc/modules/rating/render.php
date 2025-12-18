<?php
/**
 * Rating Module - Render
 */

$rating = $layout['rating'] ?? 0;
$label = $layout['label'] ?? '';
?>

<div class="rating-module">
    <div class="rating-module__stars">
        <?php
        $stars = floor( $rating );
        $half = ( $rating - $stars ) >= 0.5 ? 1 : 0;
        
        for ( $i = 0; $i < 5; $i++ ) {
            if ( $i < $stars ) {
                echo '<i class="fas fa-star rating-module__star--full"></i>';
            } elseif ( $i === $stars && $half ) {
                echo '<i class="fas fa-star-half-alt rating-module__star--half"></i>';
            } else {
                echo '<i class="far fa-star rating-module__star--empty"></i>';
            }
        }
        ?>
    </div>
    <?php if ( $label ) : ?>
        <span class="rating-module__label"><?php echo esc_html( $label ); ?></span>
    <?php endif; ?>
</div>
