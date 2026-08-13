<?php
/**
 * Single Article Template
 *
 * @package DesignerCoffee
 */

get_header();
?>

<?php while (have_posts()) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('single-article'); ?>>
        <header class="section" style="padding-top: 9rem; padding-bottom: 3rem; text-align: center;">
            <div class="container" style="max-width: 850px;">
                <span class="hero-badge" style="margin-bottom: 1rem;"><?php the_date(); ?> &bull; <?php the_author(); ?></span>
                <h1 style="font-size: clamp(2rem, 4vw, 3.25rem); margin-bottom: 1.5rem;"><?php the_title(); ?></h1>
                <?php if (has_category()) : ?>
                    <p style="color: var(--accent-gold); font-size: 0.9rem;">Categories: <?php the_category(', '); ?></p>
                <?php endif; ?>
            </div>
        </header>

        <?php if (has_post_thumbnail()) : ?>
            <div class="container" style="max-width: 1000px; margin-bottom: 3rem;">
                <div style="border-radius: var(--radius-md); overflow: hidden; max-height: 500px;">
                    <?php the_post_thumbnail('full', array('style' => 'width:100%; height:100%; object-fit:cover;')); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="container" style="max-width: 800px; padding-bottom: 6rem;">
            <div class="article-body" style="font-size: 1.1rem; line-height: 1.8;">
                <?php the_content(); ?>
            </div>

            <div style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 3rem; padding-top: 2rem; display: flex; justify-content: space-between;">
                <div><?php previous_post_link('%link', '&larr; %title'); ?></div>
                <div><?php next_post_link('%link', '%title &rarr;'); ?></div>
            </div>

            <?php
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>
        </div>
    </article>
<?php endwhile; ?>

<?php get_footer(); ?>
