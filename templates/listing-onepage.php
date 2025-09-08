<?php
/* One-page listing detail template */
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        global $listingpro_options;

        $layout_keys = isset( $listingpro_options['lp-detail-page-layout6-content']['general'] )
            ? array_keys( $listingpro_options['lp-detail-page-layout6-content']['general'] )
            : array();
        $layout_general = array();
        foreach ( $layout_keys as $key ) {
            if ( in_array( $key, array( 'lp_content_section', 'lp_video_section' ), true ) ) {
                $layout_general[] = $key;
            }
        }
        if ( ( $pos = array_search( 'lp_content_section', $layout_general, true ) ) !== false ) {
            array_splice( $layout_general, $pos + 1, 0, array( 'lp_services_section', 'lp_gallery_section' ) );
        } else {
            array_splice( $layout_general, 0, 0, array( 'lp_services_section', 'lp_gallery_section' ) );
        }

        $menu_items = array( 'home' => __( 'Anasayfa', 'listingpro' ) );
        $services   = listingpro_get_metabox( 'lp_services' );
        $gallery    = listingpro_get_metabox( 'lp_gallery' );
        $video      = listingpro_get_metabox( 'lp_video_embed' );

        $plan_id = listing_get_metabox_by_ID( 'Plan_id', get_the_ID() );
        if ( empty( $plan_id ) ) {
            $plan_id = 'none';
        }
        $map_show    = get_post_meta( $plan_id, 'map_show', true );
        $social_show = get_post_meta( $plan_id, 'listingproc_social', true );
        $location_show = get_post_meta( $plan_id, 'listingproc_location', true );
        $contact_show = get_post_meta( $plan_id, 'contact_show', true );
        $website_show = get_post_meta( $plan_id, 'listingproc_website', true );
        $hours_show   = get_post_meta( $plan_id, 'listingproc_bhours', true );
        if ( 'none' === $plan_id ) {
            $map_show = $social_show = $location_show = $contact_show = $website_show = $hours_show = 'true';
        }

        $address   = listingpro_get_metabox( 'gAddress' );
        $phone     = listingpro_get_metabox( 'phone' );
        $website   = listingpro_get_metabox( 'website' );
        $email     = listingpro_get_metabox( 'email' );
        $whatsapp  = listingpro_get_metabox( 'whatsapp' );
        $latitude  = listingpro_get_metabox( 'latitude' );
        $longitude = listingpro_get_metabox( 'longitude' );
        $hours     = listingpro_get_metabox( 'business_hours' );

        $facebook  = listingpro_get_metabox( 'facebook' );
        $twitter   = listingpro_get_metabox( 'twitter' );
        $linkedin  = listingpro_get_metabox( 'linkedin' );
        $youtube   = listingpro_get_metabox( 'youtube' );
        $instagram = listingpro_get_metabox( 'instagram' );

        foreach ( $layout_general as $section_key ) {
            switch ( $section_key ) {
                case 'lp_content_section':
                    if ( listingpro_get_metabox( 'lp_listing_description' ) ) {
                        $menu_items['about'] = __( 'Hakkımızda', 'listingpro' );
                    }
                    break;
                case 'lp_services_section':
                    if ( ! empty( $services ) ) {
                        $menu_items['services'] = __( 'Hizmetler', 'listingpro' );
                    }
                    break;
                case 'lp_gallery_section':
                    if ( ! empty( $gallery ) ) {
                        $menu_items['gallery'] = __( 'Resim', 'listingpro' );
                    }
                    break;
                case 'lp_video_section':
                    if ( ! empty( $video ) ) {
                        $menu_items['video'] = __( 'Video', 'listingpro' );
                    }
                    break;
            }
        }

        if ( 'true' === $map_show && ! empty( $latitude ) && ! empty( $longitude ) ) {
            $menu_items['map'] = __( 'Harita', 'listingpro' );
        }
        if ( 'true' === $hours_show && ! empty( $hours ) ) {
            $menu_items['hours'] = __( 'Çalışma Saatleri', 'listingpro' );
        }
        $menu_items['contact'] = __( 'İletişim', 'listingpro' );

        $b_logo       = $listingpro_options['business_logo_switch'];
        $allow_logo   = isset( $listingpro_options['listingpro_allow_logo_styles_switch'] ) ? $listingpro_options['listingpro_allow_logo_styles_switch'] : '';
        $business_logo_url = '';
        if ( $b_logo && 'yes' === $allow_logo ) {
            $b_logo_default    = $listingpro_options['business_logo_default']['url'];
            $business_logo     = listing_get_metabox_by_ID( 'business_logo', get_the_ID() );
            $business_logo_url = ! empty( $business_logo ) ? $business_logo : $b_logo_default;
        }
        ?>
        <style>
        .lp-onepage-header{display:flex;align-items:center;justify-content:space-between;gap:30px;margin-bottom:40px;}
        .lp-onepage-logo img{max-height:80px;width:auto;}
        .lp-onepage-nav ul{list-style:none;margin:0;padding:0;display:flex;gap:20px;}
        .lp-onepage-nav a{text-decoration:none;color:inherit;font-weight:600;}
        .lp-section{margin-bottom:60px;}
        .lp-section-title{margin:0 0 20px;font-size:28px;font-weight:700;}
        .lp-gallery-grid{display:flex;flex-wrap:wrap;gap:15px;}
        .lp-gallery-grid img{max-width:100%;height:auto;border-radius:4px;}
        #singlepostmap{width:100%;height:300px;border-radius:4px;}
        .lp-contact-list{list-style:none;margin:0;padding:0;}
        .lp-contact-list li{margin-bottom:8px;}
        .lp-social-list{list-style:none;margin:20px 0 0;padding:0;display:flex;gap:10px;}
        .lp-social-list a{text-decoration:none;font-size:20px;}
        </style>
        <div class="lp-onepage-wrapper">
        <header class="lp-onepage-header">
            <?php if ( ! empty( $business_logo_url ) ) : ?>
                <div class="lp-onepage-logo"><img src="<?php echo esc_attr( $business_logo_url ); ?>" alt="<?php esc_attr_e( 'Listing Logo', 'listingpro' ); ?>"></div>
            <?php else : ?>
                <div class="lp-onepage-logo"><?php the_post_thumbnail( 'medium' ); ?></div>
            <?php endif; ?>
            <nav class="lp-onepage-nav">
                <ul>
                    <?php foreach ( $menu_items as $slug => $label ) : ?>
                        <li><a href="#<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </header>

        <section id="home" class="lp-section lp-section-home">
            <?php the_title('<h1 class="lp-listing-title">', '</h1>'); ?>
        </section>

        <?php foreach ( $layout_general as $section_key ) :
            switch ( $section_key ) {
                case 'lp_content_section':
                    if ( isset( $menu_items['about'] ) ) : ?>
                        <section id="about" class="lp-section lp-section-about">
                            <h2 class="lp-section-title"><?php echo esc_html( $menu_items['about'] ); ?></h2>
                            <?php echo apply_filters( 'the_content', listingpro_get_metabox( 'lp_listing_description' ) ); ?>
                        </section>
                    <?php endif;
                    break;
                case 'lp_services_section':
                    if ( isset( $menu_items['services'] ) ) : ?>
                        <section id="services" class="lp-section lp-section-services">
                            <h2 class="lp-section-title"><?php echo esc_html( $menu_items['services'] ); ?></h2>
                            <?php echo apply_filters( 'the_content', $services ); ?>
                        </section>
                    <?php endif;
                    break;
                case 'lp_gallery_section':
                    if ( isset( $menu_items['gallery'] ) ) : ?>
                        <section id="gallery" class="lp-section lp-section-gallery">
                            <h2 class="lp-section-title"><?php echo esc_html( $menu_items['gallery'] ); ?></h2>
                            <div class="lp-gallery-grid">
                            <?php
                            if ( is_array( $gallery ) ) {
                                foreach ( $gallery as $image_id ) {
                                    echo wp_get_attachment_image( $image_id, 'large' );
                                }
                            } else {
                                echo apply_filters( 'the_content', $gallery );
                            }
                            ?>
                            </div>
                        </section>
                    <?php endif;
                    break;
                case 'lp_video_section':
                    if ( isset( $menu_items['video'] ) ) : ?>
                        <section id="video" class="lp-section lp-section-video">
                            <h2 class="lp-section-title"><?php echo esc_html( $menu_items['video'] ); ?></h2>
                            <?php echo apply_filters( 'the_content', $video ); ?>
                        </section>
                    <?php endif;
                    break;
            }
        endforeach; ?>

        <?php if ( isset( $menu_items['map'] ) ) :
            $lp_map_pin = $listingpro_options['lp_map_pin']['url']; ?>
            <section id="map" class="lp-section lp-section-map">
                <h2 class="lp-section-title"><?php echo esc_html( $menu_items['map'] ); ?></h2>
                <div id="singlepostmap" class="singlemap" data-lat="<?php echo esc_attr( $latitude ); ?>" data-lan="<?php echo esc_attr( $longitude ); ?>" data-pinicon="<?php echo esc_attr( $lp_map_pin ); ?>"></div>
            </section>
        <?php endif; ?>

        <?php if ( isset( $menu_items['hours'] ) ) : ?>
            <section id="hours" class="lp-section lp-section-hours">
                <h2 class="lp-section-title"><?php echo esc_html( $menu_items['hours'] ); ?></h2>
                <?php get_template_part( 'include/timings' ); ?>
            </section>
        <?php endif; ?>

        <section id="contact" class="lp-section lp-section-contact">
            <h2 class="lp-section-title"><?php echo esc_html( $menu_items['contact'] ); ?></h2>
            <ul class="lp-contact-list">
                <?php if ( 'true' === $location_show && ! empty( $address ) ) : ?>
                    <li class="lp-contact-address"><?php echo esc_html( $address ); ?></li>
                <?php endif; ?>
                <?php if ( 'true' === $contact_show && ! empty( $email ) ) : ?>
                    <li class="lp-contact-email"><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></li>
                <?php endif; ?>
                <?php if ( 'true' === $contact_show && ! empty( $phone ) ) : ?>
                    <li class="lp-contact-phone"><?php echo esc_html( $phone ); ?></li>
                <?php endif; ?>
                <?php if ( 'true' === $contact_show && ! empty( $whatsapp ) ) :
                    $wa_link = 'https://api.whatsapp.com/send?phone=' . $whatsapp; ?>
                    <li class="lp-contact-whatsapp"><a href="<?php echo esc_url( $wa_link ); ?>" target="_blank"><?php echo esc_html( $whatsapp ); ?></a></li>
                <?php endif; ?>
                <?php if ( 'true' === $website_show && ! empty( $website ) ) : ?>
                    <li class="lp-contact-website"><a href="<?php echo esc_url( $website ); ?>" target="_blank"><?php echo esc_html( $website ); ?></a></li>
                <?php endif; ?>
            </ul>
            <?php if ( 'true' === $social_show && ( $facebook || $twitter || $linkedin || $youtube || $instagram ) ) : ?>
                <ul class="lp-social-list">
                    <?php if ( ! empty( $facebook ) ) : ?><li><a href="<?php echo esc_url( $facebook ); ?>" target="_blank"><i class="fa-brands fa-square-facebook"></i></a></li><?php endif; ?>
                    <?php if ( ! empty( $twitter ) ) : ?><li><a href="<?php echo esc_url( $twitter ); ?>" target="_blank"><i class="fa-brands fa-square-x-twitter"></i></a></li><?php endif; ?>
                    <?php if ( ! empty( $linkedin ) ) : ?><li><a href="<?php echo esc_url( $linkedin ); ?>" target="_blank"><i class="fa-brands fa-linkedin"></i></a></li><?php endif; ?>
                    <?php if ( ! empty( $youtube ) ) : ?><li><a href="<?php echo esc_url( $youtube ); ?>" target="_blank"><i class="fa-brands fa-youtube"></i></a></li><?php endif; ?>
                    <?php if ( ! empty( $instagram ) ) : ?><li><a href="<?php echo esc_url( $instagram ); ?>" target="_blank"><i class="fa-brands fa-square-instagram"></i></a></li><?php endif; ?>
                </ul>
            <?php endif; ?>
        </section>

        <script>
        jQuery(function($){
            $('.lp-onepage-nav a').on('click', function(e){
                e.preventDefault();
                var target = this.hash;
                $('html, body').animate({
                    scrollTop: $(target).offset().top
                }, 500);
            });
        });
        </script>
        </div><!-- /.lp-onepage-wrapper -->
        <?php
    }
}
?>
