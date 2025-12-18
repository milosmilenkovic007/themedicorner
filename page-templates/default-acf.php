<?php
/**
 * Template Name: Default ACF Template
 * Description: Default template with flexible content modules
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
            // Render flexible content modules if ACF is active
            if ( function_exists( 'have_rows' ) && have_rows( 'page_modules' ) ) :
                echo '<div class="page-modules">';
                
                while ( have_rows( 'page_modules' ) ) : the_row();
                    $layout = get_row();
                    hello_child_render_flexible_layout( $layout );
                endwhile;
                
                echo '</div>'; // .page-modules
            else :
                // Fallback to standard content
                the_content();
            endif;
            ?>
        </div>

    </article>
</main>

<?php
get_footer();
