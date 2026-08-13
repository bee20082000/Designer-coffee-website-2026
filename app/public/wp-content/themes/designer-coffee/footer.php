<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="brand-logo" style="margin-bottom: 1rem;">
                    <img src="<?php echo esc_url(home_url('/wp-content/uploads/Logo/logo.png')); ?>" alt="<?php bloginfo('name'); ?>" class="site-logo-img" style="max-height: 40px;">
                </a>
                <p><?php bloginfo('description'); ?></p>
                <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--color-red); font-weight: 800; letter-spacing: 1px; text-transform: uppercase;">
                    Fine Robusta & Specialty Coffee from Vietnam.
                </p>
            </div>

            <div class="footer-col">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/#home')); ?>">Home</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#about')); ?>">Our Philosophy</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#team')); ?>">Farm To Cup</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#works')); ?>">Products</a></li>
                    <?php if (class_exists('WooCommerce')) : ?>
                        <li><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Shop</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Explore</h4>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/blog')); ?>">Stories & Journal</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#contact')); ?>">Workshops</a></li>
                    <li><a href="<?php echo esc_url(home_url('/impressum')); ?>">Impressum</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Contact Us</h4>
                <p style="font-size: 0.9rem; color: var(--text-muted);">
                    📍 Vietnam / Germany<br>
                    ✉️ info@designer-coffee.local<br>
                    ☕ Crafted for coffee lovers
                </p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<a href="#home" class="to-top-btn" aria-label="Scroll back to top">↑</a>

<?php wp_footer(); ?>
</body>
</html>
