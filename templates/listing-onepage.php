<?php
/* One-page listing detail template */
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        setPostViews( get_the_ID() );

        global $listingpro_options, $post;

        $plan_id = listing_get_metabox_by_ID( 'Plan_id', get_the_ID() );
        if ( empty( $plan_id ) ) {
            $plan_id = 'none';
        }

        $contact_show  = get_post_meta( $plan_id, 'contact_show', true );
        $map_show      = get_post_meta( $plan_id, 'map_show', true );
        $gallery_show  = get_post_meta( $plan_id, 'gallery_show', true );
        $video_show    = get_post_meta( $plan_id, 'video_show', true );
        $tagline_show  = get_post_meta( $plan_id, 'listingproc_tagline', true );
        $location_show = get_post_meta( $plan_id, 'listingproc_location', true );
        $website_show  = get_post_meta( $plan_id, 'listingproc_website', true );
        $social_show   = get_post_meta( $plan_id, 'listingproc_social', true );
        $faqs_show     = get_post_meta( $plan_id, 'listingproc_faq', true );

        if ( 'none' === $plan_id ) {
            $contact_show  = 'true';
            $map_show      = 'true';
            $gallery_show  = 'true';
            $video_show    = 'true';
            $tagline_show  = 'true';
            $location_show = 'true';
            $website_show  = 'true';
            $social_show   = 'true';
            $faqs_show     = 'true';
        }

        $tagline        = listing_get_metabox( 'tagline_text' );
        $claimed        = listing_get_metabox( 'claimed_section' );
        $featured_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
        $logo_id        = listing_get_metabox_by_ID( 'business_logo', get_the_ID() );
        $logo_url       = ! empty( $logo_id ) ? $logo_id : '';

        if ( empty( $logo_url ) ) {
            $default_logo = isset( $listingpro_options['business_logo_default']['url'] ) ? $listingpro_options['business_logo_default']['url'] : '';
            $logo_url     = $default_logo;
        }

        $rating_count = listingpro_ratings_numbers( get_the_ID() );
        $rating_html  = lp_cal_listing_rate( get_the_ID() );

        $categories = get_the_term_list( get_the_ID(), 'listing-category', '', ', ', '' );

        $address      = listingpro_get_metabox( 'gAddress' );
        $phone        = listingpro_get_metabox( 'phone' );
        $email        = listingpro_get_metabox( 'email' );
        $website      = listingpro_get_metabox( 'website' );
        $whatsapp     = listingpro_get_metabox( 'whatsapp' );
        $latitude     = listingpro_get_metabox( 'latitude' );
        $longitude    = listingpro_get_metabox( 'longitude' );
        $email_toggle = lp_theme_option( 'listingpro_email_display_switch' );

        $services_meta = listingpro_get_metabox( 'lp_services' );
        $video_meta    = listingpro_get_metabox( 'lp_video_embed' );
        $faqs_meta     = listing_get_metabox_by_ID( 'faqs', get_the_ID() );

        $gallery_ids = get_post_meta( get_the_ID(), 'gallery_image_ids', true );
        if ( ! is_array( $gallery_ids ) ) {
            $gallery_ids = array();
        }

        $content_raw  = get_post_field( 'post_content', get_the_ID() );
        $has_overview = ! empty( trim( wp_strip_all_tags( $content_raw ) ) );
        if ( ! $has_overview ) {
            $has_overview = ! empty( $tagline ) || ! empty( $categories );
        }

        $has_services = ! empty( $services_meta );
        $has_gallery  = 'true' === $gallery_show && ! empty( $gallery_ids );
        $has_video    = 'true' === $video_show && ! empty( $video_meta );

        $has_faqs = false;
        if ( 'true' === $faqs_show && ! empty( $faqs_meta ) && isset( $faqs_meta['faq'] ) ) {
            $faq_questions = array_filter( $faqs_meta['faq'] );
            $has_faqs      = ! empty( $faq_questions );
        }

        ob_start();
        get_template_part( 'templates/single-list/listing-details-style1/content/reviews' );
        $reviews_markup = trim( ob_get_clean() );
        $has_reviews    = ! empty( $reviews_markup );

        $menu_items = array( 'hero' => __( 'Anasayfa', 'listingpro' ) );
        if ( $has_overview ) {
            $menu_items['overview'] = __( 'İlan Bilgileri', 'listingpro' );
        }
        if ( $has_services ) {
            $menu_items['services'] = __( 'Hizmetler', 'listingpro' );
        }
        if ( $has_gallery ) {
            $menu_items['gallery'] = __( 'Galeri', 'listingpro' );
        }
        if ( $has_video ) {
            $menu_items['video'] = __( 'Video', 'listingpro' );
        }
        if ( $has_faqs ) {
            $menu_items['faq'] = __( 'S.S.S.', 'listingpro' );
        }
        if ( $has_reviews ) {
            $menu_items['reviews'] = __( 'Yorumlar', 'listingpro' );
        }
        if ( ! empty( $latitude ) && ! empty( $longitude ) && 'true' === $map_show ) {
            $menu_items['location'] = __( 'Konum', 'listingpro' );
        }
        $menu_items['contact'] = __( 'İletişim', 'listingpro' );
        ?>

        <header class="lp-onepage-header">
            <div class="lp-onepage-brand">
                <?php if ( ! empty( $logo_url ) ) : ?>
                    <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
                <?php else : ?>
                    <span class="lp-onepage-brand-text"><?php bloginfo( 'name' ); ?></span>
                <?php endif; ?>
            </div>
            <button class="lp-onepage-nav-toggle" aria-expanded="false" aria-controls="lp-onepage-menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <nav class="lp-onepage-nav" id="lp-onepage-menu">
                <ul>
                    <?php foreach ( $menu_items as $slug => $label ) : ?>
                        <li><a href="#<?php echo esc_attr( $slug ); ?>" data-target="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </header>

        <section id="hero" class="lp-section lp-onepage-hero" style="<?php echo $featured_image ? 'background-image: url(' . esc_url( $featured_image ) . ');' : ''; ?>">
            <div class="lp-onepage-hero__overlay"></div>
            <div class="lp-onepage-container">
                <?php if ( $categories ) : ?>
                    <div class="lp-onepage-hero__categories"><?php echo wp_kses_post( $categories ); ?></div>
                <?php endif; ?>
                <h1 class="lp-onepage-title"><?php the_title(); ?><?php if ( 'claimed' === $claimed ) : ?><span class="lp-onepage-claimed"><?php esc_html_e( 'Onaylı', 'listingpro' ); ?></span><?php endif; ?></h1>
                <?php if ( ! empty( $tagline ) && 'true' === $tagline_show ) : ?>
                    <p class="lp-onepage-tagline"><?php echo esc_html( $tagline ); ?></p>
                <?php endif; ?>
                <div class="lp-onepage-hero__meta">
                    <div class="lp-onepage-rating">
                        <?php echo wp_kses_post( $rating_html ); ?>
                        <?php if ( $rating_count ) : ?>
                            <span class="lp-onepage-rating__count"><?php echo esc_html( $rating_count ); ?> <?php esc_html_e( 'oy', 'listingpro' ); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <?php if ( $contact_show === 'true' && ( $address || ( $email && 'yes' === $email_toggle ) || $phone || $website || $whatsapp ) ) : ?>
            <div class="lp-onepage-info-bar">
                <div class="lp-onepage-container">
                    <ul>
                        <?php if ( $address && 'true' === $location_show ) : ?>
                            <li><i class="fa-solid fa-location-dot" aria-hidden="true"></i><?php echo esc_html( $address ); ?></li>
                        <?php endif; ?>
                        <?php if ( $email && 'yes' === $email_toggle ) : ?>
                            <li><i class="fa fa-envelope" aria-hidden="true"></i><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></li>
                        <?php endif; ?>
                        <?php if ( $phone ) : ?>
                            <li><i class="fa-solid fa-phone" aria-hidden="true"></i><a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></li>
                        <?php endif; ?>
                        <?php if ( $website && 'true' === $website_show ) : ?>
                            <li><i class="fa-solid fa-globe" aria-hidden="true"></i><a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="nofollow noopener"><?php echo esc_html( $website ); ?></a></li>
                        <?php endif; ?>
                        <?php if ( $whatsapp ) : ?>
                            <li><i class="fa fa-whatsapp" aria-hidden="true"></i><a href="https://api.whatsapp.com/send?phone=<?php echo rawurlencode( $whatsapp ); ?>" target="_blank" rel="nofollow noopener"><?php esc_html_e( 'WhatsApp', 'listingpro' ); ?></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( $has_overview ) : ?>
        <section id="overview" class="lp-section lp-onepage-section">
            <div class="lp-onepage-container">
                <header class="lp-onepage-section__header">
                    <h2><?php esc_html_e( 'İlan Bilgileri', 'listingpro' ); ?></h2>
                </header>
                <div class="lp-onepage-section__content">
                    <?php the_content(); ?>
                </div>
                <?php
                $features = get_the_terms( get_the_ID(), 'features' );
                if ( ! empty( $features ) && ! is_wp_error( $features ) ) :
                ?>
                    <ul class="lp-onepage-features">
                        <?php foreach ( $features as $feature ) : ?>
                            <li><?php echo esc_html( $feature->name ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>

        <?php if ( $has_services ) : ?>
        <section id="services" class="lp-section lp-onepage-section">
            <div class="lp-onepage-container">
                <header class="lp-onepage-section__header">
                    <h2><?php esc_html_e( 'Hizmetler', 'listingpro' ); ?></h2>
                </header>
                <div class="lp-onepage-section__content">
                    <?php echo do_shortcode( $services_meta ); ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <?php if ( $has_gallery ) : ?>
        <section id="gallery" class="lp-section lp-onepage-section">
            <div class="lp-onepage-container">
                <header class="lp-onepage-section__header">
                    <h2><?php esc_html_e( 'Galeri', 'listingpro' ); ?></h2>
                </header>
                <div class="lp-onepage-gallery">
                    <?php foreach ( $gallery_ids as $image_id ) :
                        $image = wp_get_attachment_image_src( $image_id, 'large' );
                        if ( empty( $image ) ) {
                            continue;
                        }
                        $full = wp_get_attachment_image_src( $image_id, 'full' );
                    ?>
                        <a href="<?php echo esc_url( $full[0] ); ?>" rel="prettyPhoto[gallery1]">
                            <img src="<?php echo esc_url( $image[0] ); ?>" alt="<?php echo esc_attr( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ); ?>">
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <?php if ( $has_video ) : ?>
        <section id="video" class="lp-section lp-onepage-section">
            <div class="lp-onepage-container">
                <header class="lp-onepage-section__header">
                    <h2><?php esc_html_e( 'Video', 'listingpro' ); ?></h2>
                </header>
                <div class="lp-onepage-video">
                    <?php
                    $video_embed = wp_oembed_get( $video_meta );
                    if ( ! $video_embed ) {
                        $video_embed = $video_meta;
                    }
                    echo wp_kses_post( $video_embed );
                    ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <?php if ( $has_faqs ) : ?>
        <section id="faq" class="lp-section lp-onepage-section">
            <div class="lp-onepage-container">
                <header class="lp-onepage-section__header">
                    <h2><?php esc_html_e( 'Sıkça Sorulan Sorular', 'listingpro' ); ?></h2>
                </header>
                <div class="lp-onepage-faq">
                    <?php
                    $faqs = $faqs_meta['faq'];
                    $answers = isset( $faqs_meta['faqans'] ) ? $faqs_meta['faqans'] : array();
                    $index = 1;
                    foreach ( $faqs as $key => $question ) {
                        if ( empty( $question ) ) {
                            continue;
                        }
                        $answer = isset( $answers[ $key ] ) ? $answers[ $key ] : '';
                        ?>
                        <details>
                            <summary><?php echo esc_html( $question ); ?></summary>
                            <div class="lp-onepage-faq__answer"><?php echo do_shortcode( wp_kses_post( $answer ) ); ?></div>
                        </details>
                        <?php
                        $index++;
                    }
                    ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <?php if ( $has_reviews ) : ?>
        <section id="reviews" class="lp-section lp-onepage-section">
            <div class="lp-onepage-container">
                <header class="lp-onepage-section__header">
                    <h2><?php esc_html_e( 'Yorumlar', 'listingpro' ); ?></h2>
                </header>
                <div class="lp-onepage-reviews">
                    <?php echo $reviews_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <?php if ( ! empty( $latitude ) && ! empty( $longitude ) && 'true' === $map_show ) :
            $google_key = lp_theme_option( 'google_api_key' );
            $lat_esc = rawurlencode( $latitude );
            $lng_esc = rawurlencode( $longitude );
            if ( ! empty( $google_key ) ) {
                $map_src = sprintf(
                    'https://www.google.com/maps/embed/v1/place?key=%1$s&amp;q=%2$s,%3$s&amp;zoom=14',
                    rawurlencode( $google_key ),
                    $lat_esc,
                    $lng_esc
                );
            } else {
                $map_src = sprintf(
                    'https://www.google.com/maps?q=%1$s,%2$s&amp;z=14&amp;output=embed',
                    $lat_esc,
                    $lng_esc
                );
            }
        ?>
        <section id="location" class="lp-section lp-onepage-section">
            <div class="lp-onepage-container">
                <header class="lp-onepage-section__header">
                    <h2><?php esc_html_e( 'Konum', 'listingpro' ); ?></h2>
                </header>
                <div class="lp-onepage-map">
                    <iframe src="<?php echo $map_src; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <section id="contact" class="lp-section lp-onepage-section">
            <div class="lp-onepage-container">
                <header class="lp-onepage-section__header">
                    <h2><?php esc_html_e( 'İletişim', 'listingpro' ); ?></h2>
                </header>
                <div class="lp-onepage-contact">
                    <ul>
                        <?php if ( $address ) : ?>
                            <li><strong><?php esc_html_e( 'Adres', 'listingpro' ); ?>:</strong> <?php echo esc_html( $address ); ?></li>
                        <?php endif; ?>
                        <?php if ( $email && 'yes' === $email_toggle ) : ?>
                            <li><strong><?php esc_html_e( 'E-posta', 'listingpro' ); ?>:</strong> <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></li>
                        <?php endif; ?>
                        <?php if ( $phone ) : ?>
                            <li><strong><?php esc_html_e( 'Telefon', 'listingpro' ); ?>:</strong> <a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></li>
                        <?php endif; ?>
                        <?php if ( $website && 'true' === $website_show ) : ?>
                            <li><strong><?php esc_html_e( 'Web', 'listingpro' ); ?>:</strong> <a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="nofollow noopener"><?php echo esc_html( $website ); ?></a></li>
                        <?php endif; ?>
                        <?php if ( $whatsapp ) : ?>
                            <li><strong><?php esc_html_e( 'WhatsApp', 'listingpro' ); ?>:</strong> <a href="https://api.whatsapp.com/send?phone=<?php echo rawurlencode( $whatsapp ); ?>" target="_blank" rel="nofollow noopener"><?php echo esc_html( $whatsapp ); ?></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </section>

        <script>
        jQuery(function($){
            var $header = $('.lp-onepage-header');
            var headerHeight = function(){
                return $header.outerHeight() || 0;
            };
            $('body').addClass('lp-onepage-active');

            $('.lp-onepage-nav a').on('click', function(e){
                var targetId = $(this).attr('href');
                if ( targetId.charAt(0) === '#' && $(targetId).length ) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: $(targetId).offset().top - headerHeight()
                    }, 500);
                    $('.lp-onepage-nav').removeClass('is-open');
                    $('.lp-onepage-nav-toggle').attr('aria-expanded', 'false');
                }
            });

            $('.lp-onepage-nav-toggle').on('click', function(){
                $('.lp-onepage-nav').toggleClass('is-open');
                var expanded = $('.lp-onepage-nav').hasClass('is-open');
                $(this).attr('aria-expanded', expanded ? 'true' : 'false');
            });

            var $links = $('.lp-onepage-nav a');
            var $sections = $('.lp-section');
            var setActiveLink = function(){
                var scrollPos = $(window).scrollTop() + headerHeight() + 10;
                var currentId = '';
                $sections.each(function(){
                    var $section = $(this);
                    var top = $section.offset().top;
                    var bottom = top + $section.outerHeight();
                    if ( scrollPos >= top && scrollPos < bottom ) {
                        currentId = $section.attr('id');
                        return false;
                    }
                });
                if ( currentId ) {
                    $links.removeClass('is-active');
                    $links.filter('[data-target="' + currentId + '"]').addClass('is-active');
                }
            };

            $(window).on('scroll', setActiveLink);
            $(window).on('load', setActiveLink);
        });
        </script>
        <?php
    }
}
?>