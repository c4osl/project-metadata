<?php
/**
 * Template for displaying the project archive in a table format
 */

get_header(); ?>

<div class="wrap">
    <header class="page-header">
        <h1 class="page-title"><?php post_type_archive_title(); ?></h1>
    </header>

    <?php if (have_posts()) : ?>
        <table class="project-table">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Priority Area</th>
                    <th>From Date</th>
                    <th>To Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while (have_posts()) : the_post(); ?>
                    <tr>
                        <td><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
                        <td><?php echo esc_html(get_post_meta(get_the_ID(), 'pmm_motto', true)); ?></td>
                        <td><?php echo get_the_term_list(get_the_ID(), 'project_type', '', ', '); ?></td>
                        <td><?php echo get_the_term_list(get_the_ID(), 'priority_area', '', ', '); ?></td>
                        <td><?php echo esc_html(get_post_meta(get_the_ID(), 'pmm_from_date', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta(get_the_ID(), 'pmm_to_date', true)); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e('No projects found.', 'text-domain'); ?></p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>

