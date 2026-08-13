<?php
/**
 * Template Name: Blog Page Template
 * Location: pages/blog/template-blog.php
 *
 * @package DesignerCoffee
 */

get_header();
?>

<div class="blog-page-wrapper section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Journal & Insights</span>
            <h1 class="section-title">Coffee Stories</h1>
        </div>

        <div class="grid-3">
            <?php
            $blog_query = new WP_Query(array(
                'post_type'      => 'post',
                'posts_per_page' => 9,
                'post_status'    => 'publish',
            ));

            if ($blog_query->have_posts()) :
                while ($blog_query->have_posts()) : $blog_query->the_post();
                    ?>
                    <div class="card">
                        <div class="card-img-wrapper">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium_large', array('class' => 'card-img')); ?>
                            <?php else : ?>
                                <img src="https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=800&q=80" class="card-img" alt="<?php the_title_attribute(); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <span class="card-meta"><?php echo get_the_date(); ?></span>
                            <h3 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <p style="font-size: 0.9rem; color: var(--text-muted);"><?php echo wp_trim_words(get_the_excerpt(), 18); ?></p>
                            <a href="<?php the_permalink(); ?>" style="margin-top: auto; font-weight: 800; font-size: 0.9rem; color: var(--color-red);">Read Article &rarr;</a>
                        </div>
                    </div>
                <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p style="grid-column: 1/-1; text-align: center;">No stories found.</p>';
            endif;
            ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
