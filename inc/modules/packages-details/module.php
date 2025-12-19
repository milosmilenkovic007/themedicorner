<?php
/**
 * Packages Details Module
 * Renders accordion sections with package details
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$title = $data['title'] ?? '';
$accordions = $data['accordions'] ?? array();
?>

<section class="module-packages-details">
    <?php if ( $title ) : ?>
        <h2 class="packages-details__title"><?php echo wp_kses_post( $title ); ?></h2>
    <?php endif; ?>

    <?php if ( ! empty( $accordions ) ) : ?>
        <div class="packages-details__accordions">
            <?php foreach ( $accordions as $index => $accordion ) : ?>
                <?php
                $accordion_title = $accordion['title'] ?? '';
                $items = $accordion['items'] ?? array();
                $unique_id = 'accordion-' . uniqid();
                ?>
                <details class="packages-details__accordion">
                    <summary class="packages-details__accordion-title">
                        <?php echo esc_html( $accordion_title ); ?>
                    </summary>
                    
                    <?php if ( ! empty( $items ) ) : ?>
                        <div class="packages-details__accordion-content">
                            <ul class="packages-details__items-list">
                                <?php foreach ( $items as $item ) : ?>
                                    <li class="packages-details__item">
                                        <?php echo esc_html( $item['text'] ?? '' ); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </details>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
