<?php
/**
 * Blog Archive Template
 *
 * @package DesignerCoffee
 */

get_header();
?>

<section class="section" style="padding-top: 9rem;">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Journal & Coffee Stories</span>
            <h1 class="section-title"><?php single_post_title(); ?></h1>
        </div>

        <div class="grid-3">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <div class="card">
                    <div class="card-img-wrapper">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium_large', array('class' => 'card-img')); ?>
                        <?php else : ?>
                            <img src="https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=800&q=80" class="card-img" alt="<?php the_title_attribute(); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <span class="card-meta"><?php echo get_the_date(); ?> &bull; <?php the_author(); ?></span>
                        <h2 class="card-title" style="font-size: 1.25rem;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <p style="font-size: 0.9rem; color: var(--text-muted);"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                        <a href="<?php the_permalink(); ?>" style="margin-top: auto; font-weight: 600; font-size: 0.9rem;">Read Article &rarr;</a>
                    </div>
                </div>
            <?php endwhile; else : ?>
                <p style="grid-column: 1/-1; text-align: center;">No stories found.</p>
            <?php endif; ?>
        </div>

        <div style="margin-top: 3rem; text-align: center;">
            <?php the_posts_pagination(array(
                'mid_size'  => 2,
                'prev_text' => __('&laquo; Previous', 'designer-coffee'),
                'next_text' => __('Next &raquo;', 'designer-coffee'),
            )); ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
