<?php
/**
 * Template Name: Contact Page Template
 * Location: pages/contact/template-contact.php
 *
 * @package DesignerCoffee
 */

get_header();
?>

<div class="contact-page-wrapper section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Get In Touch</span>
            <h1 class="section-title">Design Your Own Workshop & Contact</h1>
        </div>

        <div class="contact-grid">
            <div class="contact-info-card">
                <h3>Connect With Us</h3>
                <p style="margin-bottom: 2rem; color: var(--text-muted);">
                    Have questions about our Fine Robusta beans, OEM coffee services, or workshops? Reach out to our team.
                </p>

                <div class="contact-info-item">
                    <h4>Location</h4>
                    <p>Fair Farm Vietnam & Designer Coffee Roastery</p>
                </div>

                <div class="contact-info-item">
                    <h4>Email</h4>
                    <p>info@designer-coffee.local</p>
                </div>

                <div class="contact-info-item">
                    <h4>Workshops</h4>
                    <p>Cupping, sensory training, & custom coffee blending workshops.</p>
                </div>
            </div>

            <div style="background: var(--bg-card); padding: 2.5rem; border-radius: var(--radius-md); border: var(--border-subtle);">
                <?php
                $cf7_posts = get_posts(array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => 1));
                if (!empty($cf7_posts)) {
                    echo do_shortcode('[contact-form-7 id="' . $cf7_posts[0]->ID . '" title="' . esc_attr($cf7_posts[0]->post_title) . '"]');
                } else {
                    ?>
                    <form action="#" method="post" onsubmit="event.preventDefault(); alert('Thank you for reaching out to Designer Coffee!');">
                        <input type="text" placeholder="Your Full Name" required>
                        <input type="email" placeholder="Your Email Address" required>
                        <textarea rows="4" placeholder="Your Message or Workshop Inquiry..." required></textarea>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Send Message</button>
                    </form>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
