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
            <?php the_content(); ?>
        </div>
    </article>
</main>
<?php get_footer(); ?>

