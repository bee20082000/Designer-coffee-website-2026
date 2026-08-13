<?php
/**
 * Generic Page Template
 *
 * @package DesignerCoffee
 */

get_header();
?>

<?php while (have_posts()) : the_post(); ?>
    <main id="page-<?php the_ID(); ?>" <?php post_class('site-page'); ?>>
        <section class="section" style="padding-top: 9rem; padding-bottom: 3rem;">
            <div class="container">
                <div class="section-header">
                    <h1 class="section-title"><?php the_title(); ?></h1>
                </div>

                <?php if (has_post_thumbnail()) : ?>
                    <div style="border-radius: var(--radius-md); overflow: hidden; margin-bottom: 3rem; max-height: 450px;">
                        <?php the_post_thumbnail('full', array('style' => 'width:100%; height:100%; object-fit:cover;')); ?>
                    </div>
                <?php endif; ?>

                <div class="page-content" style="font-size: 1.1rem; line-height: 1.8; max-width: 900px; margin: 0 auto;">
                    <?php the_content(); ?>
                </div>
            </div>
        </section>
    </main>
<?php endwhile; ?>

<?php get_footer(); ?>
