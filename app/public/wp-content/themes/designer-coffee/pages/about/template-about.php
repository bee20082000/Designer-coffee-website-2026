<?php
/**
 * Template Name: About Page Template
 * Location: pages/about/template-about.php
 *
 * @package DesignerCoffee
 */

get_header();
?>

<div class="about-page-wrapper section">
    <div class="container">
        <div class="about-grid">
            <div class="about-img-box">
                <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1000&q=80" alt="Specialty Coffee Craft">
            </div>
            <div class="about-content">
                <span class="section-subtitle">Craft & Passion</span>
                <h1 style="margin-bottom: 1.5rem;">
                    <?php echo esc_html(designer_get_field('about_heading', false, 'Designer Coffee: Where Coffee is Loved in Its Most Primitive Form')); ?>
                </h1>
                <p>
                    <?php echo esc_html(designer_get_field('about_body', false, 'At Designer Coffee, coffee is not just a drink—it is an art form born from sustainable farming, meticulously selected Fine Robusta beans, and relentless dedication to taste perfection.')); ?>
                </p>
                <div class="about-highlights">
                    <div>
                        <h4 style="color: var(--color-red);">🌱 Fair Farm</h4>
                        <p style="font-size: 0.9rem; color: var(--text-muted);">Direct trade partnerships supporting sustainable farming communities in Vietnam.</p>
                    </div>
                    <div>
                        <h4 style="color: var(--color-red);">🔥 Precision Roasting</h4>
                        <p style="font-size: 0.9rem; color: var(--text-muted);">Small-batch roasting designed to highlight signature flavor notes.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
