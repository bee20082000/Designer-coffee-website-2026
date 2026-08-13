<?php
/**
 * Template Name: Home Page Template
 * Location: pages/home/template-home.php
 *
 * @package DesignerCoffee
 */

get_header();
?>

<!-- INTRO BRAND SCREEN BEFORE HERO SECTION (RED BACKGROUND, BIG STATEMENT TEXT & 2 STICKERS) -->
<section class="intro-brand-screen">
    <div class="intro-brand-wrapper">
        <div class="intro-logo-container intro-text-container">
            <h1 class="intro-statement-headline">
                Design for Lover, wildly loved.
            </h1>
            <p class="intro-statement-desc">
                Sustainable coffee products with fair trade practices, the best raw materials, the ideal roasting, the perfect grinding and a designer who cares about a better world.
            </p>
            <div class="intro-cta-wrapper">
                <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/shop')); ?>" class="btn-brand-pill btn-white">
                    SHOP NOW
                </a>
            </div>
            
            <!-- STICKER 1: MEO BANH MI (TOP LEFT OFFSET) -->
            <div class="intro-sticker-wrapper sticker-left">
                <img src="<?php echo esc_url(home_url('/wp-content/uploads/2021/12/7-Meo-banh-mi-600x424.png')); ?>" alt="Meo Banh Mi Sticker" class="intro-sticker-img">
            </div>

            <!-- STICKER 2: MEO OM CUP (BOTTOM RIGHT OFFSET) -->
            <div class="intro-sticker-wrapper sticker-right">
                <img src="<?php echo esc_url(home_url('/wp-content/uploads/2021/12/1-Meo-om-cup-500x300.png')); ?>" alt="Meo Om Cup Sticker" class="intro-sticker-img">
            </div>
        </div>
    </div>
</section>

<!-- OUR PHILOSOPHY SECTION -->
<section id="about" class="philosophy-section section">
    <div class="container">
        <!-- REAL LOVE LETTER STATIONERY CARD WITH HACHI FONT & PHIN PHOTO (TOP ROUNDED) -->
        <div class="philosophy-letter-card real-letter-paper">
            <!-- LETTER BODY GRID: CONTENT & PHOTO -->
            <div class="letter-body-grid">
                <div class="letter-text-col">
                    <h3 class="letter-salutation schoolbell-font" id="typewriter-salutation">Dear Love,</h3>
                    
                    <blockquote class="letter-quote schoolbell-font" id="typewriter-quote">"Are you going to be with those who care about the environment, take care of the farmers’ lives, respect nature, and create happier lives?"</blockquote>
                </div>

                <div class="letter-photo-col">
                    <img src="<?php echo esc_url(home_url('/wp-content/uploads/2021/12/6-Meo-chill-phin.png')); ?>" alt="Phin Coffee Chill Illustration" class="letter-photo">
                </div>
            </div>
        </div>
    </div>

    <!-- CORE BELIEFS SECTION WITH GSAP SCROLLTRIGGER PINNING -->
    <div class="core-beliefs-pin-section">
        <div class="core-beliefs-pin-container">
                
                <!-- LEFT CONTENT COLUMN (PINNED DURING SCROLL) -->
                <div class="core-beliefs-pin-content">
                    <h2 class="philosophy-title-pin">SUSTAINABLE AGRICULTURE & FAIR TRADE</h2>
                    <p class="philosophy-lead-pin">
                        Rather than paying for certification logos, we build direct, transparent trade relationships that ensure fair compensation and strengthen farmer livelihoods. We cultivate fair relationships, resilient ecosystems, and education for future generations.
                    </p>
                    <div class="pin-cta-group">
                        <a href="<?php echo esc_url(home_url('/#contact')); ?>" class="btn-brand-pill">
                            CONNECT WITH US
                        </a>
                    </div>
                </div>

                <!-- RIGHT GALLERY COLUMN (SCROLLS PAST THE PINNED CONTENT) -->
                <div class="core-beliefs-pin-gallery">
                    
                    <!-- IMAGE 1 -->
                    <div class="gallery-item item-1 tilted-left">
                        <div class="gallery-img-box">
                            <img src="<?php echo esc_url(home_url('/wp-content/uploads/2025/06/hinh-4-1-1024x683.jpg')); ?>" alt="Sustainable Farming 1">
                        </div>
                        <span class="gallery-handwriting">Direct Trade Relationships</span>
                    </div>

                    <!-- IMAGE 2 -->
                    <div class="gallery-item item-2 black-and-white">
                        <div class="gallery-img-box">
                            <img src="<?php echo esc_url(home_url('/wp-content/uploads/2025/06/Hinh-3-1-1024x576.jpg')); ?>" alt="Coffee Cupping Session">
                        </div>
                        <span class="gallery-handwriting">Continuous Learning</span>
                    </div>

                    <!-- IMAGE 3 -->
                    <div class="gallery-item item-3 tilted-right">
                        <div class="gallery-img-box">
                            <img src="<?php echo esc_url(home_url('/wp-content/uploads/2025/05/hinh-6-2-768x799.jpg')); ?>" alt="Farmer Partnerships">
                        </div>
                        <span class="gallery-handwriting">Fair Compensation</span>
                    </div>

                    <!-- IMAGE 4 -->
                    <div class="gallery-item item-4">
                        <div class="gallery-img-box">
                            <img src="<?php echo esc_url(home_url('/wp-content/uploads/2025/05/hinh-1-1-500x300.jpg')); ?>" alt="Coffee Cherry Harvest">
                        </div>
                        <span class="gallery-handwriting">Ecosystem Preservation</span>
                    </div>

                    <!-- IMAGE 5 -->
                    <div class="gallery-item item-5 tilted-left">
                        <div class="gallery-img-box">
                            <img src="<?php echo esc_url(home_url('/wp-content/uploads/2026/02/02-768x484.jpg')); ?>" alt="Coffee Roastery">
                        </div>
                        <span class="gallery-handwriting">Roasting Perfection</span>
                    </div>
                    
            </div>
        </div>
    </div>

    <div class="container">
        <!-- WHAT SUSTAINABLE AGRICULTURE MEANS TO US (OUR COMMITMENTS - NO BACKGROUND) -->
        <div class="sustainability-means-block no-bg">
            <div class="means-header">
                <h3 class="means-heading">What Sustainable Agriculture Means to Us</h3>
            </div>

            <div class="sustainability-cards-grid">
                <!-- CARD 1 -->
                <div class="sustainability-card">
                    <div class="card-top-row">
                        <span class="card-tag">Environment</span>
                        <div class="card-badge">01</div>
                    </div>
                    <div class="card-bottom-content">
                        <h3 class="card-title">Responsible Input Use</h3>
                        <p class="card-desc">Responsible use of agricultural inputs to eliminate negative environmental impact.</p>
                    </div>
                </div>

                <!-- CARD 2 -->
                <div class="sustainability-card">
                    <div class="card-top-row">
                        <span class="card-tag">Ecosystems</span>
                        <div class="card-badge">02</div>
                    </div>
                    <div class="card-bottom-content">
                        <h3 class="card-title">Soil & Biodiversity</h3>
                        <p class="card-desc">Practices that protect and enrich soil health, natural biodiversity, and water systems.</p>
                    </div>
                </div>

                <!-- CARD 3 -->
                <div class="sustainability-card">
                    <div class="card-top-row">
                        <span class="card-tag">Soil Chemistry</span>
                        <div class="card-badge">03</div>
                    </div>
                    <div class="card-bottom-content">
                        <h3 class="card-title">Microorganism Health</h3>
                        <p class="card-desc">Enriching living soil ecosystems with active, beneficial microorganisms.</p>
                    </div>
                </div>

                <!-- CARD 4 -->
                <div class="sustainability-card">
                    <div class="card-top-row">
                        <span class="card-tag">Community</span>
                        <div class="card-badge">04</div>
                    </div>
                    <div class="card-bottom-content">
                        <h3 class="card-title">Farmer Education</h3>
                        <p class="card-desc">Training farmers and enthusiasts in sustainable agriculture and regenerative practices.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- LATEST DROPS CAROUSEL SECTION (FULL WIDTH, 5 PRODUCTS PER LINE) -->
<section id="shop" class="home-shop-section section full-width-shop-section">
    <div class="shop-full-container">
        
        <!-- Centered Header Row -->
        <div class="shop-section-header text-center">
            <h2 class="shop-title-main">Latest drops</h2>
        </div>


        <!-- Full Width Continuous Carousel Track -->
        <div class="home-carousel-wrapper">
            <div class="home-carousel-track marquee-running" id="home-carousel-track">
                <?php
                if (class_exists('WooCommerce')) {
                    $args = array(
                        'post_type'      => 'product',
                        'posts_per_page' => 10,
                        'post_status'    => 'publish',
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                    );
                    $home_products = new WP_Query($args);

                    if ($home_products->have_posts()) :
                        $found_posts = $home_products->posts;
                        $post_count  = count($found_posts);
                        
                        // Populate 10 base items
                        $base_items = array();
                        while (count($base_items) < 10 && $post_count > 0) {
                            foreach ($found_posts as $p) {
                                $base_items[] = $p->ID;
                                if (count($base_items) >= 10) break;
                            }
                        }

                        // Duplicate set (20 items) for 100% seamless infinite marquee loop
                        $marquee_items = array_merge($base_items, $base_items);

                        foreach ($marquee_items as $prod_id) {
                            if (function_exists('designer_coffee_render_product_card')) {
                                designer_coffee_render_product_card($prod_id, array('class' => 'carousel-card-item'));
                            }
                        }
                    else :
                        echo '<p style="padding: 2rem; color: #666;">No products found.</p>';
                    endif;
                }
                ?>
            </div>
        </div>


    </div>
</section>


<!-- HERO SECTION WITH 3 SMALL CARDS IN THE SAME LINE -->

<section id="home" class="hero-cards-section">
    <div class="container">
        <div class="hero-cards-grid">
            
            <!-- CARD 1 -->
            <div class="hero-card">
                <div class="hero-card-bg" style="background-image: url('<?php echo esc_url(home_url('/wp-content/uploads/2025/05/hinh-1-2.jpg')); ?>');"></div>
                <div class="hero-card-overlay"></div>
                <div class="hero-card-content">
                    <h3 class="hero-card-title">
                        <?php echo esc_html(designer_get_field('hero_slide0_title', false, "Design for Lover, wildly loved.")); ?>
                    </h3>
                    <p class="hero-card-desc">
                        <?php echo esc_html(designer_get_field('hero_slide0_desc', false, "Sustainable coffee products with fair trade practices, the best raw materials, the ideal roasting, the perfect grinding and a designer who cares about a better world.")); ?>
                    </p>
                    <div class="hero-card-cta">
                        <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/shop')); ?>" class="btn btn-primary btn-sm">
                            SHOP NOW
                        </a>
                    </div>
                </div>
            </div>

            <!-- CARD 2 -->
            <div class="hero-card">
                <div class="hero-card-bg" style="background-image: url('<?php echo esc_url(home_url('/wp-content/uploads/2025/05/hinh-3-1-1024x683.jpg')); ?>');"></div>
                <div class="hero-card-overlay"></div>
                <div class="hero-card-content">
                    <h3 class="hero-card-title">
                        <?php echo esc_html(designer_get_field('hero_slide1_title', false, "SUSTAINABLE AGRICULTURE")); ?>
                    </h3>
                    <p class="hero-card-desc">
                        <?php echo esc_html(designer_get_field('hero_slide1_desc', false, "We cultivate fair relationships, resilient ecosystems, and education for future generations. We are committed to sustainable agriculture that respects people and the soil, beyond expensive third-party certification labels.")); ?>
                    </p>
                    <div class="hero-card-cta">
                        <a href="<?php echo esc_url(home_url('/#about')); ?>" class="btn btn-primary btn-sm">
                            READ MORE
                        </a>
                    </div>
                </div>
            </div>

            <!-- CARD 3 -->
            <div class="hero-card">
                <div class="hero-card-bg" style="background-image: url('<?php echo esc_url(home_url('/wp-content/uploads/2026/02/Cupping_17122025.jpg')); ?>');"></div>
                <div class="hero-card-overlay"></div>
                <div class="hero-card-content">
                    <h3 class="hero-card-title">
                        <?php echo esc_html(designer_get_field('hero_slide2_title', false, "Education, Workshop & Tours")); ?>
                    </h3>
                    <p class="hero-card-desc">
                        <?php echo esc_html(designer_get_field('hero_slide2_desc', false, "Through farm education, immersive tours, and in-depth coffee workshops in our coffee shop. We connect sustainability, flavor, and storytelling into real learning experiences.")); ?>
                    </p>
                    <div class="hero-card-cta">
                        <a href="<?php echo esc_url(home_url('/#contact')); ?>" class="btn btn-primary btn-sm">
                            BOOK A WORKSHOP
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<?php get_footer(); ?>
