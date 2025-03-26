
<?php
/**
 * Template Name: Campaigns
 * Description: Displays projects in the Coalition Action category
 */

get_header(); ?>

<div class="wrap">
    <header class="page-header">
        <h1 class="page-title">Campaign Projects</h1>
    </header>

    <?php
    $args = array(
        'post_type' => 'project',
        'posts_per_page' => 10,
        'tax_query' => array(
            array(
                'taxonomy' => 'project_type',
                'field'    => 'slug',
                'terms'    => 'coalition-action',
            ),
        ),
    );

    $campaign_query = new WP_Query($args);

    if ($campaign_query->have_posts()) : ?>
        <div class="campaign-grid">
            <?php while ($campaign_query->have_posts()) : $campaign_query->the_post(); ?>
                <article class="campaign-entry">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="campaign-thumbnail">
                            <?php the_post_thumbnail('medium'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="campaign-content">
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        
                        <?php 
                        $motto = get_post_meta(get_the_ID(), 'pmm_motto', true);
                        if ($motto) : ?>
                            <p class="campaign-motto"><?php echo esc_html($motto); ?></p>
                        <?php endif; ?>
                        
                        <?php the_excerpt(); ?>
                        
                        <div class="campaign-meta">
                            <?php 
                            $from_date = get_post_meta(get_the_ID(), 'pmm_from_date', true);
                            $to_date = get_post_meta(get_the_ID(), 'pmm_to_date', true);
                            if ($from_date) :
                                echo '<span class="campaign-date">' . esc_html($from_date);
                                echo $to_date ? ' - ' . esc_html($to_date) : ' - Current';
                                echo '</span>';
                            endif;
                            ?>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <?php the_posts_pagination(); ?>
        <?php wp_reset_postdata(); ?>
    <?php else : ?>
        <p><?php esc_html_e('No campaign projects found.', 'text-domain'); ?></p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
