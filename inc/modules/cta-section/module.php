<?php
/**
 * CTA Section Module
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$heading      = $data['heading'] ?? '';
$features     = $data['features'] ?? array();
$image        = $data['image'] ?? null;
$button_text  = $data['button_text'] ?? '';
$button_link  = $data['button_link'] ?? null;

$resolve_image = static function( $value ) {
    if ( empty( $value ) ) {
        return null;
    }

    if ( is_array( $value ) ) {
        $url = $value['url'] ?? '';
        if ( ! $url && ! empty( $value['ID'] ) ) {
            $url = wp_get_attachment_image_url( (int) $value['ID'], 'full' ) ?: '';
        }
        $alt = $value['alt'] ?? '';
        if ( ! $alt && ! empty( $value['ID'] ) ) {
            $alt = (string) get_post_meta( (int) $value['ID'], '_wp_attachment_image_alt', true );
        }

        return $url ? array( 'url' => $url, 'alt' => $alt ) : null;
    }

    if ( is_numeric( $value ) ) {
        $id = (int) $value;
        $url = wp_get_attachment_image_url( $id, 'full' );
        if ( ! $url ) {
            return null;
        }
        $alt = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
        return array( 'url' => $url, 'alt' => $alt );
    }

    if ( is_string( $value ) ) {
        return array( 'url' => $value, 'alt' => '' );
    }

    return null;
};
?>

<section class="module-cta-section">
    <div class="cta-section__panel">
        <div class="cta-section__grid">
            <div class="cta-section__left">
                <?php if ( $heading ) : ?>
                    <h2 class="cta-section__heading"><?php echo wp_kses_post( $heading ); ?></h2>
                <?php endif; ?>

                <div class="cta-section__separator" aria-hidden="true"></div>

                <?php if ( ! empty( $features ) ) : ?>
                    <div class="cta-section__items">
                        <?php foreach ( $features as $feature ) : ?>
                            <?php
                            $icon = $feature['icon'] ?? null;
                            $title = $feature['title'] ?? '';
                            $description = $feature['description'] ?? '';
                            $icon_data = $resolve_image( $icon );
                            ?>
                            <div class="cta-section__item">
                                <?php if ( $icon_data && ! empty( $icon_data['url'] ) ) : ?>
                                    <div class="cta-section__item-icon">
                                        <img src="<?php echo esc_url( $icon_data['url'] ); ?>" alt="<?php echo esc_attr( $icon_data['alt'] ?: $title ); ?>">
                                    </div>
                                <?php endif; ?>

                                <div class="cta-section__item-body">
                                    <?php if ( $title ) : ?>
                                        <h3 class="cta-section__item-heading"><?php echo esc_html( $title ); ?></h3>
                                    <?php endif; ?>
                                    <?php if ( $description ) : ?>
                                        <p class="cta-section__item-content"><?php echo esc_html( $description ); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="cta-section__right">
                <div class="cta-section__card">
                    <?php $image_data = $resolve_image( $image ); ?>
                    <?php if ( $image_data && ! empty( $image_data['url'] ) ) : ?>
                        <div class="cta-section__image-wrap">
                            <img class="cta-section__image" src="<?php echo esc_url( $image_data['url'] ); ?>" alt="<?php echo esc_attr( $image_data['alt'] ); ?>">
                        </div>
                    <?php endif; ?>

                    <?php if ( $button_text && $button_link ) : ?>
                        <?php $url = $button_link['url'] ?? ''; $target = ! empty( $button_link['target'] ) ? $button_link['target'] : '_self'; ?>
                        <a class="btn btn--primary btn--block cta-section__button" href="<?php echo esc_url( $url ); ?>" target="<?php echo esc_attr( $target ); ?>">
                            <span class="btn__text"><?php echo esc_html( $button_text ); ?></span>
                            <span class="btn__icon" aria-hidden="true">
                                <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/arrow.svg' ); ?>" alt="" />
                            </span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
