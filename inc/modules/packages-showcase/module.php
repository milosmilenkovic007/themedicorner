<?php
/**
 * Packages Showcase Module
 * Refactored: image/content layout blocks with bullets and buttons
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Support both new and legacy ACF field keys
$bg_color = $data['background_color'] ?? ($data['field_showcase_bg_color'] ?? '');
$title    = $data['title'] ?? ($data['field_showcase_title'] ?? '');
$packages = $data['packages'] ?? ($data['field_showcase_packages'] ?? array());

$normalize_image = static function( $image ): array {
    // Returns: ['url' => string, 'alt' => string, 'id' => int]
    if ( empty( $image ) ) {
        return array( 'url' => '', 'alt' => '', 'id' => 0 );
    }

    if ( is_numeric( $image ) ) {
        $id = (int) $image;
        $url = wp_get_attachment_url( $id );
        $alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
        return array( 'url' => $url ?: '', 'alt' => is_string( $alt ) ? $alt : '', 'id' => $id );
    }

    if ( is_string( $image ) ) {
        return array( 'url' => $image, 'alt' => '', 'id' => 0 );
    }

    if ( is_array( $image ) ) {
        $url = $image['url'] ?? '';
        $alt = $image['alt'] ?? '';
        $id = (int) ( $image['ID'] ?? 0 );
        return array( 'url' => is_string( $url ) ? $url : '', 'alt' => is_string( $alt ) ? $alt : '', 'id' => $id );
    }

    return array( 'url' => '', 'alt' => '', 'id' => 0 );
};

$read_svg = static function( array $image ): string {
    $url = $image['url'] ?? '';
    if ( ! is_string( $url ) || $url === '' ) {
        return '';
    }

    $path = (string) ( parse_url( $url, PHP_URL_PATH ) ?? '' );
    if ( strtolower( substr( $path, -4 ) ) !== '.svg' ) {
        return '';
    }

    $svg = '';
    $id = (int) ( $image['id'] ?? 0 );
    if ( $id > 0 ) {
        $file = get_attached_file( $id );
        if ( $file && is_string( $file ) && file_exists( $file ) ) {
            $svg = (string) file_get_contents( $file );
        }
    }

    if ( $svg === '' ) {
        $resp = wp_remote_get( $url );
        if ( ! is_wp_error( $resp ) ) {
            $svg = (string) wp_remote_retrieve_body( $resp );
        }
    }

    if ( $svg === '' ) {
        return '';
    }

    // Basic hardening
    $svg = preg_replace( '#<\s*script[^>]*>.*?<\s*/\s*script\s*>#is', '', $svg );
    $svg = preg_replace( '/\son\w+\s*=\s*"[^"]*"/i', '', $svg );
    $svg = preg_replace( '/\son\w+\s*=\s*\'[^\']*\'/i', '', $svg );
    $svg = preg_replace( '/<\?xml[^>]*\?>/i', '', $svg );
    $svg = preg_replace( '/<!DOCTYPE[^>]*>/i', '', $svg );

    // Make fill/stroke colorable via currentColor
    $svg = preg_replace( '/\s(fill|stroke)\s*=\s*"[^"]*"/i', ' $1="currentColor"', $svg );
    $svg = preg_replace( '/\s(fill|stroke)\s*=\s*\'[^\']*\'/i', ' $1="currentColor"', $svg );

    // Also fix inline style-based fills like: style="fill:#010002;"
    $svg = preg_replace( '/fill\s*:\s*[^;\"\']+/i', 'fill:currentColor', $svg );
    $svg = preg_replace( '/stroke\s*:\s*[^;\"\']+/i', 'stroke:currentColor', $svg );
    if ( stripos( $svg, '<svg' ) !== false && stripos( $svg, 'currentColor' ) === false ) {
        $svg = preg_replace( '/<svg\b/i', '<svg fill="currentColor" stroke="currentColor"', $svg, 1 );
    }

    return trim( $svg );
};
?>

<section class="module-packages-showcase" <?php echo $bg_color ? 'style="background-color:' . esc_attr( $bg_color ) . '"' : ''; ?>>
    <div class="packages-showcase__inner">
        <?php if ( $title ) : ?>
            <h2 class="packages-showcase__title"><?php echo wp_kses_post( $title ); ?></h2>
        <?php endif; ?>

        <?php if ( ! empty( $packages ) ) : ?>
            <div class="packages-showcase__stack">
                <?php foreach ( $packages as $index => $package ) : ?>
                    <?php
                    // New keys + legacy fallbacks
                    $image_raw      = $package['image'] ?? ($package['field_showcase_pkg_image'] ?? null);
                    $image          = $normalize_image( $image_raw );
                    $image_position = $package['image_position'] ?? ($package['field_showcase_pkg_image_position'] ?? 'left');
                    // Heading field was renamed from legacy 'name' -> 'heading' (keep wide fallbacks)
                    $heading = '';
                    foreach ( array( 'heading', 'pkg_heading', 'title', 'name', 'field_showcase_pkg_heading', 'field_showcase_pkg_name' ) as $key ) {
                        if ( ! empty( $package[ $key ] ) && is_string( $package[ $key ] ) ) {
                            $heading = $package[ $key ];
                            break;
                        }
                    }
                    $description    = $package['description'] ?? ($package['field_showcase_pkg_description'] ?? '');
                    $items          = $package['items'] ?? ($package['field_showcase_pkg_items'] ?? array());
                    $btn_primary    = $package['button_primary'] ?? ($package['field_showcase_pkg_button_primary'] ?? null);
                    $btn_secondary  = $package['button_secondary'] ?? ($package['field_showcase_pkg_button_secondary'] ?? null);
                    $row_class = $image_position === 'right' ? 'is-reversed' : '';
                    $num = (int) $index + 1;
                    ?>
                    <div class="packages-showcase__row <?php echo esc_attr( $row_class ); ?>">
                        <div class="packages-showcase__media">
                            <?php if ( ! empty( $image['url'] ) ) : ?>
                                <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ?: $heading ); ?>" loading="lazy" />
                            <?php endif; ?>
                        </div>
                        <div class="packages-showcase__content">
                            <?php if ( $heading ) : ?>
                                <h3 class="packages-showcase__heading"><span class="packages-showcase__index"><?php echo esc_html( $num . '.' ); ?></span> <?php echo esc_html( $heading ); ?></h3>
                            <?php endif; ?>

                            <?php if ( $description ) : ?>
                                <div class="packages-showcase__description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
                            <?php endif; ?>

                            <?php if ( ! empty( $items ) ) : ?>
                                <ul class="packages-showcase__list">
                                    <?php foreach ( $items as $item ) : ?>
                                        <?php
                                        $icon_raw = $item['icon'] ?? ($item['field_showcase_pkg_item_icon'] ?? []);
                                        $icon = $normalize_image( $icon_raw );
                                        $icon_color = $item['icon_color'] ?? ($item['field_showcase_pkg_item_icon_color'] ?? '');
                                        $text = $item['text'] ?? ($item['field_showcase_pkg_item_text'] ?? '');
                                        $svg = $read_svg( $icon );
                                        ?>
                                        <li class="packages-showcase__list-item">
                                            <?php if ( $svg !== '' ) : ?>
                                                <span class="packages-showcase__icon" <?php echo ( is_string( $icon_color ) && $icon_color !== '' ) ? 'style="color:' . esc_attr( $icon_color ) . '"' : ''; ?>><?php echo $svg; ?></span>
                                            <?php elseif ( ! empty( $icon['url'] ) ) : ?>
                                                <img class="packages-showcase__icon" src="<?php echo esc_url( $icon['url'] ); ?>" alt="" loading="lazy" />
                                            <?php endif; ?>
                                            <span class="packages-showcase__text"><?php echo esc_html( $text ); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                            <?php if ( $btn_primary || $btn_secondary ) : ?>
                                <div class="packages-showcase__actions">
                                    <?php if ( $btn_primary && ! empty( $btn_primary['url'] ) ) : ?>
                                        <a class="btn btn--primary" href="<?php echo esc_url( $btn_primary['url'] ); ?>" target="<?php echo esc_attr( $btn_primary['target'] ?? '_self' ); ?>">
                                            <span class="btn__text"><?php echo esc_html( $btn_primary['title'] ?? 'Get a free consultation' ); ?></span>
                                            <span class="btn__icon" aria-hidden="true">
                                                <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/arrow.svg' ); ?>" alt="" />
                                            </span>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ( $btn_secondary && ! empty( $btn_secondary['url'] ) ) : ?>
                                        <a class="btn btn--outline" href="<?php echo esc_url( $btn_secondary['url'] ); ?>" target="<?php echo esc_attr( $btn_secondary['target'] ?? '_self' ); ?>">
                                            <span class="btn__text"><?php echo esc_html( $btn_secondary['title'] ?? 'Full overview' ); ?></span>
                                            <span class="btn__icon" aria-hidden="true">
                                                <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/arrow.svg' ); ?>" alt="" />
                                            </span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
