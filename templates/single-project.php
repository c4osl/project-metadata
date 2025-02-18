<?php
// Single Project Template
global $post;
get_header();
?>
<main class="project-single">
    <article>
        <header>
            <h1><?php the_title(); ?></h1>
            <?php if (has_post_thumbnail()) : ?>
                <div class="project-logo">
                    <?php the_post_thumbnail('medium'); ?>
                </div>
            <?php endif; ?>
        </header>
        <div class="project-meta">
            <p><strong>From:</strong> <?php echo get_post_meta($post->ID, 'pmm_from_date', true); ?></p>
            <p><strong>To:</strong> <?php echo get_post_meta($post->ID, 'pmm_to_date', true); ?></p>
            <p><strong>Contact:</strong> <?php echo get_post_meta($post->ID, 'pmm_contact_name', true); ?></p>
            <p><strong>Contact Details:</strong> <?php echo get_post_meta($post->ID, 'pmm_contact_details', true); ?></p>
            <p><strong>Website:</strong> <a href="<?php echo esc_url(get_post_meta($post->ID, 'pmm_project_url', true)); ?>" target="_blank">Visit</a></p>
        </div>
        <div class="project-content">
            <?php the_content(); ?>
        </div>
    </article>
</main>
<?php get_footer(); ?>

