<?php
/**
 * CTA Section Module
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$heading      = $data['heading'] ?? '';
$content      = $data['content'] ?? '';
$features     = $data['features'] ?? array();
$button_text  = $data['button_text'] ?? '';
$button_link  = $data['button_link'] ?? null;
?>

<section class="module-cta-section">
    <div class="cta-section__inner">
        <?php if ( $heading ) : ?>
            <h2 class="cta-section__heading"><?php echo wp_kses_post( $heading ); ?></h2>
        <?php endif; ?>

        <?php if ( $content ) : ?>
            <div class="cta-section__content"><?php echo wp_kses_post( $content ); ?></div>
        <?php endif; ?>

        <?php if ( ! empty( $features ) ) : ?>
            <div class="cta-section__features">
                <?php foreach ( $features as $feature ) : ?>
                    <?php
                    $icon = $feature['icon'] ?? null;
                    $title = $feature['title'] ?? '';
                    $description = $feature['description'] ?? '';
                    ?>
                    <div class="cta-section__feature">
                        <?php if ( $icon && is_array( $icon ) && ! empty( $icon['url'] ) ) : ?>
                            <div class="cta-section__feature-icon">
                                <img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $title ); ?>">
                            </div>
                        <?php endif; ?>
                        <?php if ( $title ) : ?>
                            <h3 class="cta-section__feature-title"><?php echo esc_html( $title ); ?></h3>
                        <?php endif; ?>
                        <?php if ( $description ) : ?>
                            <p class="cta-section__feature-description"><?php echo esc_html( $description ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ( $button_text && $button_link ) : ?>
            <?php $url = $button_link['url'] ?? ''; $target = ! empty( $button_link['target'] ) ? $button_link['target'] : '_self'; ?>
            <a class="cta-section__button" href="<?php echo esc_url( $url ); ?>" target="<?php echo esc_attr( $target ); ?>">
                <?php echo esc_html( $button_text ); ?>
            </a>
        <?php endif; ?>
    </div>
</section>
