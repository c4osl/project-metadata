<?php
// Single Project Template
global $post;
get_header();
?>
<main class="project-single">
    <article>
        <header>
            <h1><?php the_title(); ?></h1>
        </header>
        <div class="project-content">
            <?php 
            // Get the content and check if it's empty (after stripping HTML tags)
            $content = trim(strip_tags(get_the_content()));
            if ( empty( $content ) ) {
                echo '<p>Project details coming soon!</p>';
            } else {
                the_content();
            }
            ?>
        </div>
    </article>
</main>
<?php get_footer(); ?>

