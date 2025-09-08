<?php
/* One-page listing detail template */
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        global $listingpro_options;

        if ( ! function_exists( 'lp_onepage_meta' ) ) {
            function lp_onepage_meta( $key, $post_id = null ) {
                $id = $post_id ? $post_id : get_the_ID();
                if ( function_exists( 'listingpro_get_metabox' ) ) {
                    return listingpro_get_metabox( $key );
                }
                return get_post_meta( $id, $key, true );
            }
        }

        if ( ! function_exists( 'lp_onepage_meta_by_id' ) ) {
            function lp_onepage_meta_by_id( $key, $post_id ) {
                if ( function_exists( 'listing_get_metabox_by_ID' ) ) {
                    return listing_get_metabox_by_ID( $key, $post_id );
                }
                return get_post_meta( $post_id, $key, true );
            }
        }

        $layout_keys = isset( $listingpro_options['lp-detail-page-layout6-content']['general'] )
            ? array_keys( $listingpro_options['lp-detail-page-layout6-content']['general'] )
            : array();
        $layout_general = array();
        foreach ( $layout_keys as $key ) {
            if ( in_array( $key, array( 'lp_content_section', 'lp_video_section' ), true ) ) {
                $layout_general[] = $key;
            }
        }
        if ( empty( $layout_general ) ) {
            $layout_general = array( 'lp_content_section', 'lp_services_section', 'lp_gallery_section', 'lp_video_section' );
        } else {
            if ( ( $pos = array_search( 'lp_content_section', $layout_general, true ) ) !== false ) {
                array_splice( $layout_general, $pos + 1, 0, array( 'lp_services_section', 'lp_gallery_section' ) );
            } else {
                array_splice( $layout_general, 0, 0, array( 'lp_services_section', 'lp_gallery_section' ) );
            }
        }

        $menu_items = array( 'home' => __( 'Anasayfa', 'listingpro' ) );
        $description = lp_onepage_meta( 'lp_listing_description' );
        if ( empty( $description ) ) {
            $description = get_post_field( 'post_content', get_the_ID() );
        }
        $services = wp_get_post_terms( get_the_ID(), 'features' );
        $gallery_ids = get_post_meta( get_the_ID(), 'gallery_image_ids', true );
        $gallery_ids = ! empty( $gallery_ids ) ? array_filter( explode( ',', $gallery_ids ) ) : array();
        $video = lp_onepage_meta_by_id( 'video', get_the_ID() );
        if ( empty( $video ) ) {
            $video = lp_onepage_meta( 'lp_video_embed' );
        }

        $plan_id = lp_onepage_meta_by_id( 'Plan_id', get_the_ID() );
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

        $address   = lp_onepage_meta( 'gAddress' );
        $phone     = lp_onepage_meta( 'phone' );
        $website   = lp_onepage_meta( 'website' );
        $email     = lp_onepage_meta( 'email' );
        $whatsapp  = lp_onepage_meta( 'whatsapp' );
        $latitude  = lp_onepage_meta( 'latitude' );
        $longitude = lp_onepage_meta( 'longitude' );
        $hours     = lp_onepage_meta( 'business_hours' );

        $facebook  = lp_onepage_meta( 'facebook' );
        $twitter   = lp_onepage_meta( 'twitter' );
        $linkedin  = lp_onepage_meta( 'linkedin' );
        $youtube   = lp_onepage_meta( 'youtube' );
        $instagram = lp_onepage_meta( 'instagram' );

        $locations  = get_the_terms( get_the_ID(), 'location' );
        $categories = get_the_terms( get_the_ID(), 'listing-category' );
        $price_html = function_exists( 'listingpro_price_dynesty' ) ? listingpro_price_dynesty( get_the_ID() ) : '';

        $lp_title    = get_the_title();
        $tagline_text = lp_onepage_meta( 'tagline_text' );
        if ( empty( $tagline_text ) ) {
            $tagline_text = '&nbsp;';
        }
        $claimed_section = lp_onepage_meta( 'claimed_section' );
        $claimed_position = '';
        $title_len = strlen( $lp_title );
        if ( $title_len > 34 && $title_len < 43 ) {
            $claimed_position = 'position-static';
        }
        $claim = '';
        if ( 'claimed' === $claimed_section ) {
            $claim = '<span class="claimed ' . $claimed_position . '"><i class="fa fa-check"></i> ' . esc_html__( 'Claimed', 'listingpro' ) . '</span>';
        }

        $NumberRating = function_exists( 'listingpro_ratings_numbers' ) ? listingpro_ratings_numbers( get_the_ID() ) : 0;
        $rating       = get_post_meta( get_the_ID(), 'listing_rate', true );
        $rating       = $rating ? $rating : 0;
        $rating_num_bg = '';
        $rating_num_clr = '';
        if ( $rating < 2 ) {
            $rating_num_bg = 'num-level1';
            $rating_num_clr = 'level1';
        } elseif ( $rating < 3 ) {
            $rating_num_bg = 'num-level2';
            $rating_num_clr = 'level2';
        } elseif ( $rating < 4 ) {
            $rating_num_bg = 'num-level3';
            $rating_num_clr = 'level3';
        } else {
            $rating_num_bg = 'num-level4';
            $rating_num_clr = 'level4';
        }
        $resurva_url = get_post_meta( get_the_ID(), 'resurva_url', true );

        foreach ( $layout_general as $section_key ) {
            switch ( $section_key ) {
                case 'lp_content_section':
                    if ( ! empty( $description ) ) {
                        $menu_items['about'] = __( 'Hakkımızda', 'listingpro' );
                    }
                    break;
                case 'lp_services_section':
                    if ( ! empty( $services ) ) {
                        $menu_items['services'] = __( 'Hizmetler', 'listingpro' );
                    }
                    break;
                case 'lp_gallery_section':
                    if ( ! empty( $gallery_ids ) ) {
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
            $business_logo     = lp_onepage_meta_by_id( 'business_logo', get_the_ID() );
            $business_logo_url = ! empty( $business_logo ) ? $business_logo : $b_logo_default;
        }
        ?>
        <style>
        .lp-onepage-header{position:sticky;top:0;background:#fff;z-index:999;border-bottom:1px solid #eee;}
        .lp-onepage-header-inner{display:flex;align-items:center;justify-content:space-between;gap:30px;padding:15px 0;}
        .lp-onepage-logo img{max-height:80px;width:auto;}
        .lp-onepage-nav ul{list-style:none;margin:0;padding:0;display:flex;gap:30px;}
        .lp-onepage-nav a{text-decoration:none;color:#333;font-weight:600;}
        .lp-onepage-nav a:hover{color:#0073aa;}
        .lp-section{padding:60px 0;}
        .lp-section .container{max-width:1170px;margin:0 auto;}
        .lp-section-title{margin:0 0 30px;font-size:28px;font-weight:700;text-align:center;}
        .lp-gallery-grid{display:flex;flex-wrap:wrap;gap:15px;}
        .lp-gallery-grid img{max-width:100%;height:auto;border-radius:4px;}
        #singlepostmap{width:100%;height:300px;border-radius:4px;}
        .lp-contact-list{list-style:none;margin:0;padding:0;}
        .lp-contact-list li{margin-bottom:8px;}
        .lp-social-list{list-style:none;margin:20px 0 0;padding:0;display:flex;gap:10px;justify-content:center;}
        .lp-social-list a{text-decoration:none;font-size:20px;}
        .lp-listing-tagline{margin-top:10px;font-size:18px;color:#555;}
        .lp-home-meta{list-style:none;margin:10px 0 0;padding:0;display:flex;gap:15px;font-size:14px;color:#777;justify-content:center;}
        .lp-home-meta li{display:flex;align-items:center;gap:5px;}
        </style>
        <div class="lp-onepage-wrapper">
        <header class="lp-onepage-header">
            <div class="container lp-onepage-header-inner">
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
            </div>
        </header>

        <?php
        $header_bg = isset( $listingpro_options['lp_detail_page_styles4_bg'] ) ? $listingpro_options['lp_detail_page_styles4_bg'] : array();
        ?>
        <section id="home" class="lp-section lp-section-home">
            <div class="lp-listing-top-title-header" <?php if ( ! empty( $header_bg['url'] ) ) : ?>style="background-image:url(<?php echo esc_url( $header_bg['url'] ); ?>)"<?php endif; ?>>
                <div class="lp-header-overlay"></div>
                <div class="container pos-relative">
                    <div class="row">
                        <div class="col-md-8">
                            <?php
                            include locate_template( 'templates/single-list/listing-details-style4/content/title-bar.php' );
                            get_template_part( 'templates/single-list/listing-details-style4/content/gallery' );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php foreach ( $layout_general as $section_key ) {
            switch ( $section_key ) {
                case 'lp_content_section':
                    if ( isset( $menu_items['about'] ) ) {
                        ?>
                        <section id="about" class="lp-section lp-section-about">
                            <div class="container">
                                <h2 class="lp-section-title"><?php echo esc_html( $menu_items['about'] ); ?></h2>
                                <?php echo apply_filters( 'the_content', $description ); ?>
                            </div>
                        </section>
                        <?php
                    }
                    break;
                case 'lp_services_section':
                    if ( isset( $menu_items['services'] ) ) {
                        ?>
                        <section id="services" class="lp-section lp-section-services">
                            <div class="container">
                                <h2 class="lp-section-title"><?php echo esc_html( $menu_items['services'] ); ?></h2>
                                <ul>
                                <?php foreach ( $services as $term ) : ?>
                                    <li><?php echo esc_html( $term->name ); ?></li>
                                <?php endforeach; ?>
                                </ul>
                            </div>
                        </section>
                        <?php
                    }
                    break;
                case 'lp_gallery_section':
                    if ( isset( $menu_items['gallery'] ) ) {
                        ?>
                        <section id="gallery" class="lp-section lp-section-gallery">
                            <div class="container">
                                <h2 class="lp-section-title"><?php echo esc_html( $menu_items['gallery'] ); ?></h2>
                                <div class="lp-gallery-grid">
                                <?php foreach ( $gallery_ids as $image_id ) { echo wp_get_attachment_image( $image_id, 'large' ); } ?>
                                </div>
                            </div>
                        </section>
                        <?php
                    }
                    break;
                case 'lp_video_section':
                    if ( isset( $menu_items['video'] ) ) {
                        ?>
                        <section id="video" class="lp-section lp-section-video">
                            <div class="container">
                                <h2 class="lp-section-title"><?php echo esc_html( $menu_items['video'] ); ?></h2>
                                <?php echo apply_filters( 'the_content', (string) $video ); ?>
                            </div>
                        </section>
                        <?php
                    }
                    break;
            }
        }
        ?>

        <?php if ( isset( $menu_items['map'] ) ) :
            $lp_map_pin = $listingpro_options['lp_map_pin']['url']; ?>
            <section id="map" class="lp-section lp-section-map">
                <div class="container">
                    <h2 class="lp-section-title"><?php echo esc_html( $menu_items['map'] ); ?></h2>
                    <div id="singlepostmap" class="singlemap" data-lat="<?php echo esc_attr( $latitude ); ?>" data-lan="<?php echo esc_attr( $longitude ); ?>" data-pinicon="<?php echo esc_attr( $lp_map_pin ); ?>"></div>
                </div>
            </section>
        <?php endif; ?>

        <?php if ( isset( $menu_items['hours'] ) ) : ?>
            <section id="hours" class="lp-section lp-section-hours">
                <div class="container">
                    <h2 class="lp-section-title"><?php echo esc_html( $menu_items['hours'] ); ?></h2>
                    <?php get_template_part( 'include/timings' ); ?>
                </div>
            </section>
        <?php endif; ?>

        <section id="contact" class="lp-section lp-section-contact">
            <div class="container">
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
            </div>
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
