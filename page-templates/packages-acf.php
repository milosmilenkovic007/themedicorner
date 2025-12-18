<?php
/**
 * Template Name: Packages ACF Template
 * Description: Custom template for Packages page using ACF fields
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main id="content" class="site-main">
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        
        <header class="entry-header">
            <h1 class="entry-title"><?php the_title(); ?></h1>
        </header>

        <div class="entry-content">
            <?php
            // Check if ACF is active
            if ( function_exists( 'have_rows' ) ) :
                
                // Package sections repeater
                if ( have_rows( 'package_sections' ) ) :
                    echo '<div class="packages-container">';
                    
                    while ( have_rows( 'package_sections' ) ) : the_row();
                        $section_title = get_sub_field( 'section_title' );
                        $section_description = get_sub_field( 'section_description' );
                        ?>
                        
                        <div class="package-section">
                            <?php if ( $section_title ) : ?>
                                <h2><?php echo esc_html( $section_title ); ?></h2>
                            <?php endif; ?>
                            
                            <?php if ( $section_description ) : ?>
                                <div class="section-description">
                                    <?php echo wp_kses_post( $section_description ); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php
                            // Packages repeater within section
                            if ( have_rows( 'packages' ) ) :
                                echo '<div class="packages-grid">';
                                
                                while ( have_rows( 'packages' ) ) : the_row();
                                    $package_name = get_sub_field( 'package_name' );
                                    $package_price = get_sub_field( 'package_price' );
                                    $package_features = get_sub_field( 'package_features' );
                                    $package_button_text = get_sub_field( 'package_button_text' );
                                    $package_button_link = get_sub_field( 'package_button_link' );
                                    $is_featured = get_sub_field( 'is_featured' );
                                    ?>
                                    
                                    <div class="package-card <?php echo $is_featured ? 'featured' : ''; ?>">
                                        <?php if ( $is_featured ) : ?>
                                            <span class="featured-badge">Popular</span>
                                        <?php endif; ?>
                                        
                                        <?php if ( $package_name ) : ?>
                                            <h3 class="package-name"><?php echo esc_html( $package_name ); ?></h3>
                                        <?php endif; ?>
                                        
                                        <?php if ( $package_price ) : ?>
                                            <div class="package-price"><?php echo esc_html( $package_price ); ?></div>
                                        <?php endif; ?>
                                        
                                        <?php if ( $package_features ) : ?>
                                            <div class="package-features">
                                                <?php echo wp_kses_post( $package_features ); ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ( $package_button_text && $package_button_link ) : ?>
                                            <a href="<?php echo esc_url( $package_button_link ); ?>" class="package-button">
                                                <?php echo esc_html( $package_button_text ); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    
                                <?php endwhile;
                                
                                echo '</div>'; // .packages-grid
                            endif;
                            ?>
                        </div>
                        
                    <?php endwhile;
                    
                    echo '</div>'; // .packages-container
                endif;
                
            else :
                // Fallback if ACF is not active
                the_content();
            endif;
            ?>
        </div>

    </article>
</main>

<?php
get_footer();
