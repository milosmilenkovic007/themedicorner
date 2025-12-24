<?php
/**
 * Template Name: ACF Modules
 * Template for pages using ACF flexible content layouts
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

        if ( function_exists( 'have_rows' ) && have_rows( 'page_modules' ) ) :
            echo '<div class="page-modules">';
            while ( have_rows( 'page_modules' ) ) : the_row();
                $layout = get_row();
                hello_child_render_flexible_layout( $layout );
            endwhile;
            echo '</div>';
        else :
            echo '<div class="no-modules-message">';
            echo '<p>Nema definisanih modula za ovu stranicu.</p>';
            echo '</div>';
        endif;

    endwhile;
    ?>
</main>

<?php get_footer();
