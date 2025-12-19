<?php
/**
 * Page Template - Renders ACF flexible modules when enabled
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main id="content" class="site-main">
    <?php
    while ( have_posts() ) :
        the_post();

        $use_acf = function_exists( 'get_field' ) ? get_field( 'use_acf_template' ) : false;

        if ( $use_acf && function_exists( 'have_rows' ) && have_rows( 'page_modules' ) ) :
            echo '<div class="page-modules">';
            while ( have_rows( 'page_modules' ) ) : the_row();
                $layout = get_row();
                hello_child_render_flexible_layout( $layout );
            endwhile;
            echo '</div>';
        else :
            // Fallback to default content (Elementor/Themes)
            the_content();
        endif;

    endwhile;
    ?>
</main>

<?php get_footer();
