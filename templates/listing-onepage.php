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

        if ( ! function_exists( 'lp_onepage_on' ) ) {
            function lp_onepage_on( $flag ) {
                return ! in_array( $flag, array( 'false', '0', 'off', 'no' ), true );
            }
        }

        $layout_general = isset( $listingpro_options['lp-detail-page-layout6-content']['general'] )
            ? array_keys( $listingpro_options['lp-detail-page-layout6-content']['general'] )
            : array( 'lp_content_section', 'lp_services_section', 'lp_gallery_section', 'lp_video_section', 'lp_faqs_section' );

        $menu_items = array( 'home' => __( 'Anasayfa', 'listingpro' ) );
        $description = lp_onepage_meta( 'lp_listing_description' );
        if ( empty( $description ) ) {
            $description = get_post_field( 'post_content', get_the_ID() );
        }
        $services = wp_get_post_terms( get_the_ID(), 'features' );
        $gallery_ids = get_post_meta( get_the_ID(), 'gallery_image_ids', true );
        $gallery_ids = ! empty( $gallery_ids ) ? array_filter( explode( ',', $gallery_ids ) ) : array();
        $num_gallery = count( $gallery_ids );
        $video      = lp_onepage_meta_by_id( 'video', get_the_ID() );
        if ( empty( $video ) ) {
            $video = lp_onepage_meta( 'lp_video_embed' );
        }
        $video_html = '';
        if ( ! empty( $video ) ) {
            $video_html = wp_oembed_get( $video );
            if ( false === $video_html ) {
                $video_html = $video;
            }
        }

        $plan_id = lp_onepage_meta_by_id( 'Plan_id', get_the_ID() );
        if ( empty( $plan_id ) ) {
            $plan_id = 'none';
        }
        $map_show      = get_post_meta( $plan_id, 'map_show', true );
        $social_show   = get_post_meta( $plan_id, 'listingproc_social', true );
        $location_show = get_post_meta( $plan_id, 'listingproc_location', true );
        $contact_show  = get_post_meta( $plan_id, 'contact_show', true );
        $website_show  = get_post_meta( $plan_id, 'listingproc_website', true );
        $hours_show    = get_post_meta( $plan_id, 'listingproc_bhours', true );
        $faqs_show     = get_post_meta( $plan_id, 'listingproc_faq', true );
        $price_show    = get_post_meta( $plan_id, 'listingproc_price', true );
        $tags_show     = get_post_meta( $plan_id, 'listingproc_tag_key', true );
        if ( 'none' === $plan_id ) {
            $map_show = $social_show = $location_show = $contact_show = $website_show = $hours_show = $faqs_show = $price_show = $tags_show = 'true';
        }

        $address   = lp_onepage_meta( 'gAddress' );
        $phone     = lp_onepage_meta( 'phone' );
        $website   = lp_onepage_meta( 'website' );
        $email     = lp_onepage_meta( 'email' );
        $whatsapp  = lp_onepage_meta( 'whatsapp' );
        $wa_link   = '';
        if ( ! empty( $whatsapp ) ) {
            $wa_link = 'https://api.whatsapp.com/send?phone=' . preg_replace( '/\D+/', '', $whatsapp );
        }
        $latitude  = lp_onepage_meta( 'latitude' );
        $longitude = lp_onepage_meta( 'longitude' );
        $hours     = lp_onepage_meta( 'business_hours' );
        $faqs      = lp_onepage_meta_by_id( 'faqs', get_the_ID() );
        $email_switcher = function_exists( 'lp_theme_option' ) ? lp_theme_option( 'listingpro_email_display_switch' ) : 'yes';

        $tags_terms = get_the_terms( get_the_ID(), 'list-tags' );

        $facebook  = lp_onepage_meta( 'facebook' );
        $twitter   = lp_onepage_meta( 'twitter' );
        $linkedin  = lp_onepage_meta( 'linkedin' );
        $youtube   = lp_onepage_meta( 'youtube' );
        $instagram = lp_onepage_meta( 'instagram' );

        $locations  = get_the_terms( get_the_ID(), 'location' );
        $categories = get_the_terms( get_the_ID(), 'listing-category' );
        $price_html = '';
        if ( function_exists( 'listingpro_price_dynesty' ) ) {
            ob_start();
            listingpro_price_dynesty( get_the_ID() );
            $price_html = ob_get_clean();
        }

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
        $post_author_id = get_post_field( 'post_author', get_the_ID() );
        $menuOption = false;
        $menuMeta = get_post_meta( get_the_ID(), 'menu_listing', true );
        if ( ! empty( $menuMeta ) ) {
            $menuOption = true;
        }
        $timekit = false;
        $timekit_booking = get_post_meta( get_the_ID(), 'timekit_bookings', true );
        if ( ! empty( $timekit_booking ) ) {
            $timekit = true;
        }
        $announcements_raw = get_post_meta( get_the_ID(), 'lp_listing_announcements', true );
        $has_announcements = is_array( $announcements_raw ) && count( $announcements_raw ) > 0;

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
                    if ( ! empty( $video_html ) ) {
                        $menu_items['video'] = __( 'Video', 'listingpro' );
                    }
                    break;
                case 'lp_faqs_section':
                    if ( lp_onepage_on( $faqs_show ) && ! empty( $faqs ) && ! empty( $faqs['faq'][1] ) ) {
                        $menu_items['faq'] = __( 'SSS', 'listingpro' );
                    }
                    break;
                case 'lp_announcements_section':
                    if ( $has_announcements ) {
                        $menu_items['announcements'] = __( 'Duyurular', 'listingpro' );
                    }
                    break;
                case 'lp_offers_section':
                    $menu_items['offers'] = __( 'Fırsatlar', 'listingpro' );
                    break;
                case 'lp_menu_section':
                    if ( $menuOption ) {
                        $menu_items['menu'] = __( 'Menü', 'listingpro' );
                    }
                    break;
                case 'lp_event_section':
                    $menu_items['event'] = __( 'Etkinlikler', 'listingpro' );
                    break;
                case 'lp_reviews_section':
                    $menu_items['reviews'] = __( 'Yorumlar', 'listingpro' );
                    break;
                case 'lp_reviewform_section':
                    $menu_items['reviewform'] = __( 'Yorum Yaz', 'listingpro' );
                    break;
                case 'lp_features_section':
                    $menu_items['features'] = __( 'Özellikler', 'listingpro' );
                    break;
                case 'lp_additional_section':
                    $menu_items['additional'] = __( 'Ek Bilgiler', 'listingpro' );
                    break;
                case 'lp_booking_section':
                    if ( $timekit || ! empty( $resurva_url ) || class_exists( 'Listingpro_bookings' ) ) {
                        $menu_items['booking'] = __( 'Randevu', 'listingpro' );
                    }
                    break;
            }
        }

        if ( lp_onepage_on( $map_show ) && ! empty( $latitude ) && ! empty( $longitude ) ) {
            $menu_items['map'] = __( 'Harita', 'listingpro' );
        }
        $has_hours = ! empty( $hours );
        if ( is_array( $hours ) ) {
            $has_hours = ! empty( array_filter( $hours ) );
        }
        if ( lp_onepage_on( $hours_show ) && $has_hours ) {
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
        .lp-onepage-brand{display:flex;align-items:center;gap:15px;}
        .lp-onepage-logo img{width:60px;height:60px;border-radius:50%;object-fit:cover;}
        .lp-onepage-name{font-weight:700;font-size:20px;}
        .lp-onepage-nav ul{list-style:none;margin:0;padding:0;display:flex;gap:30px;}
        .lp-onepage-nav a{text-decoration:none;color:#333;font-weight:600;display:flex;align-items:center;gap:5px;}
        .lp-onepage-nav a:hover{color:#0073aa;}
        .lp-section{padding:60px 0;}
        .lp-section .container{max-width:1170px;margin:0 auto;}
        .lp-section-title{margin:0 0 30px;font-size:28px;font-weight:700;text-align:center;}
        .lp-gallery-grid{display:flex;flex-wrap:wrap;gap:15px;}
        .lp-gallery-item{flex:1 1 calc(33.333% - 10px);}
        .lp-gallery-grid img{width:100%;height:auto;border-radius:4px;}
        .lp-video-wrapper{position:relative;padding-bottom:73.5%;height:0;overflow:hidden;}
        .lp-video-wrapper iframe{position:absolute;top:0;left:0;width:100%;height:100%;}
        #singlepostmap{width:100%;height:300px;border-radius:4px;}
        .lp-contact-list{list-style:none;margin:0;padding:0;}
        .lp-contact-list li{margin-bottom:8px;display:flex;align-items:center;gap:8px;}
        .lp-contact-list i{width:16px;text-align:center;}
        .lp-contact-list a{color:inherit;text-decoration:none;}
        .lp-contact-list a:hover{text-decoration:underline;}
        .lp-social-list{list-style:none;margin:20px 0 0;padding:0;display:flex;gap:10px;justify-content:center;}
        .lp-social-list a{text-decoration:none;font-size:20px;}
        .lp-listing-tagline{margin-top:10px;font-size:18px;color:#555;}
        .lp-home-meta{list-style:none;margin:10px 0 0;padding:0;display:flex;gap:15px;font-size:14px;color:#777;justify-content:center;}
        .lp-home-meta li{display:flex;align-items:center;gap:5px;}
        .lp-whatsapp-float{position:fixed;right:20px;bottom:20px;width:50px;height:50px;border-radius:50%;background:#25d366;color:#fff;display:flex;align-items:center;justify-content:center;font-size:24px;z-index:1000;}
        </style>
        <div class="lp-onepage-wrapper">
        <header class="lp-onepage-header">
            <div class="container lp-onepage-header-inner">
            <div class="lp-onepage-brand">
            <?php if ( ! empty( $business_logo_url ) ) : ?>
                <div class="lp-onepage-logo"><img src="<?php echo esc_attr( $business_logo_url ); ?>" alt="<?php esc_attr_e( 'Listing Logo', 'listingpro' ); ?>"></div>
            <?php else : ?>
                <div class="lp-onepage-logo"><?php the_post_thumbnail( 'thumbnail' ); ?></div>
            <?php endif; ?>
            <span class="lp-onepage-name"><?php echo esc_html( $lp_title ); ?></span>
            </div>
            <nav class="lp-onepage-nav">
                <ul>
                    <?php foreach ( $menu_items as $slug => $label ) : ?>
                        <li><a href="#<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></a></li>
                    <?php endforeach; ?>
                    <?php if ( ! empty( $phone ) ) : ?>
                        <li class="lp-nav-phone"><a href="tel:<?php echo esc_attr( $phone ); ?>"><i class="fa fa-phone"></i><?php echo esc_html( $phone ); ?></a></li>
                    <?php endif; ?>
                    <?php if ( ! empty( $whatsapp ) ) : ?>
                        <li class="lp-nav-whatsapp"><a href="<?php echo esc_url( $wa_link ); ?>" target="_blank"><i class="fa fa-whatsapp"></i><?php echo esc_html( $whatsapp ); ?></a></li>
                    <?php endif; ?>
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
        <?php if ( ! empty( $locations ) || ! empty( $categories ) || ! empty( $price_html ) ) : ?>
        <div class="lp-home-meta-wrap">
            <div class="container">
                <ul class="lp-home-meta">
                    <?php if ( ! empty( $locations ) ) : ?>
                        <li><i class="fa fa-map-marker"></i><?php echo esc_html( $locations[0]->name ); ?></li>
                    <?php endif; ?>
                    <?php if ( ! empty( $categories ) ) : ?>
                        <li><i class="fa fa-folder-open"></i><?php echo esc_html( $categories[0]->name ); ?></li>
                    <?php endif; ?>
                    <?php if ( ! empty( $price_html ) ) : ?>
                        <li><?php echo $price_html; ?></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

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
                                <?php foreach ( $gallery_ids as $image_id ) :
                                    $img_full = wp_get_attachment_image_src( $image_id, 'full' );
                                    if ( $img_full ) : ?>
                                        <div class="lp-gallery-item"><img src="<?php echo esc_url( $img_full[0] ); ?>" alt="<?php echo esc_attr( $lp_title ); ?>"></div>
                                    <?php endif;
                                endforeach; ?>
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
                                <div class="lp-video-wrapper">
                                <?php echo wp_kses( $video_html, array_merge( wp_kses_allowed_html( 'post' ), array( 'iframe' => array( 'src' => true, 'width' => true, 'height' => true, 'frameborder' => true, 'allowfullscreen' => true ) ) ) ); ?>
                                </div>
                            </div>
                        </section>
                        <?php
                    }
                    break;
                case 'lp_faqs_section':
                    if ( isset( $menu_items['faq'] ) ) {
                        ?>
                        <section id="faq" class="lp-section lp-section-faq">
                            <div class="container">
                                <h2 class="lp-section-title"><?php echo esc_html( $menu_items['faq'] ); ?></h2>
                                <?php get_template_part( 'templates/single-list/listing-details-style4/content/list-faq' ); ?>
                            </div>
                        </section>
                        <?php
                    }
                    break;
                case 'lp_announcements_section':
                    if ( isset( $menu_items['announcements'] ) ) {
                        ?>
                        <section id="announcements" class="lp-section lp-section-announcements">
                            <div class="container">
                                <h2 class="lp-section-title"><?php echo esc_html( $menu_items['announcements'] ); ?></h2>
                                <?php get_template_part( 'templates/single-list/listing-details-style6/content/list-announcements' ); ?>
                            </div>
                        </section>
                        <?php
                    }
                    break;
                case 'lp_offers_section':
                    if ( isset( $menu_items['offers'] ) ) {
                        ?>
                        <section id="offers" class="lp-section lp-section-offers">
                            <div class="container">
                                <h2 class="lp-section-title"><?php echo esc_html( $menu_items['offers'] ); ?></h2>
                                <?php
                                $post_author_id = get_post_field( 'post_author', get_the_ID() );
                                $discount_displayin = get_user_meta( $post_author_id, 'discount_display_area', true );
                                if ( $discount_displayin == 'content' || empty( $discount_displayin ) ) {
                                    get_template_part( 'templates/single-list/listing-details-style6/content/list-offer-deals-discount' );
                                }
                                ?>
                            </div>
                        </section>
                        <?php
                    }
                    break;
                case 'lp_menu_section':
                    if ( isset( $menu_items['menu'] ) ) {
                        ?>
                        <section id="menu" class="lp-section lp-section-menu">
                            <div class="container">
                                <h2 class="lp-section-title"><?php echo esc_html( $menu_items['menu'] ); ?></h2>
                                <?php get_template_part( 'templates/single-list/listing-details-style6/content/list-menu' ); ?>
                            </div>
                        </section>
                        <?php
                    }
                    break;
                case 'lp_event_section':
                    if ( isset( $menu_items['event'] ) ) {
                        ?>
                        <section id="event" class="lp-section lp-section-event">
                            <div class="container">
                                <h2 class="lp-section-title"><?php echo esc_html( $menu_items['event'] ); ?></h2>
                                <?php $GLOBALS['event_grid_call'] = 'content_area'; get_template_part( 'templates/single-list/event' ); ?>
                            </div>
                        </section>
                        <?php
                    }
                    break;
                case 'lp_reviews_section':
                    if ( isset( $menu_items['reviews'] ) ) {
                        ?>
                        <section id="reviews" class="lp-section lp-section-reviews">
                            <div class="container">
                                <h2 class="lp-section-title"><?php echo esc_html( $menu_items['reviews'] ); ?></h2>
                                <?php get_template_part( 'templates/single-list/listing-details-style6/content/reviews' ); ?>
                            </div>
                        </section>
                        <?php
                    }
                    break;
                case 'lp_reviewform_section':
                    if ( isset( $menu_items['reviewform'] ) ) {
                        ?>
                        <section id="reviewform" class="lp-section lp-section-reviewform">
                            <div class="container">
                                <h2 class="lp-section-title"><?php echo esc_html( $menu_items['reviewform'] ); ?></h2>
                                <?php get_template_part( 'templates/single-list/listing-details-style6/content/reviewform' ); ?>
                            </div>
                        </section>
                        <?php
                    }
                    break;
                case 'lp_features_section':
                    if ( isset( $menu_items['features'] ) ) {
                        ?>
                        <section id="features" class="lp-section lp-section-features">
                            <div class="container">
                                <h2 class="lp-section-title"><?php echo esc_html( $menu_items['features'] ); ?></h2>
                                <?php get_template_part( 'templates/single-list/listing-details-style6/content/features' ); ?>
                            </div>
                        </section>
                        <?php
                    }
                    break;
                case 'lp_additional_section':
                    if ( isset( $menu_items['additional'] ) ) {
                        ?>
                        <section id="additional" class="lp-section lp-section-additional">
                            <div class="container">
                                <h2 class="lp-section-title"><?php echo esc_html( $menu_items['additional'] ); ?></h2>
                                <?php get_template_part( 'templates/single-list/listing-details-style6/content/additional' ); ?>
                            </div>
                        </section>
                        <?php
                    }
                    break;
                case 'lp_booking_section':
                    if ( isset( $menu_items['booking'] ) ) {
                        ?>
                        <section id="booking" class="lp-section lp-section-booking">
                            <div class="container">
                                <?php
                                if ( class_exists( 'Listingpro_bookings' ) ) {
                                    include WP_CONTENT_DIR . '/plugins/listingpro-bookings/templates/bookings.php';
                                } elseif ( ! empty( $resurva_url ) ) {
                                    echo '<iframe src="' . esc_url( $resurva_url ) . '" frameborder="0" style="width:100%;height:600px"></iframe>';
                                }
                                ?>
                            </div>
                        </section>
                        <?php
                    }
                    break;
                case 'lp_quicks_section':
                    ?>
                    <section id="quicks" class="lp-section lp-section-quicks">
                        <div class="container">
                            <?php get_template_part( 'templates/single-list/listing-details-style6/sidebar/quicks' ); ?>
                        </div>
                    </section>
                    <?php
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
                    <?php if ( ! empty( $locations ) && lp_onepage_on( $location_show ) ) : ?>
                        <li class="lp-contact-location"><i class="fa fa-map-marker"></i><?php echo esc_html( $locations[0]->name ); ?></li>
                    <?php endif; ?>
                    <?php if ( ! empty( $categories ) ) : ?>
                        <li class="lp-contact-category"><i class="fa fa-folder-open"></i><?php echo esc_html( $categories[0]->name ); ?></li>
                    <?php endif; ?>
                    <?php if ( lp_onepage_on( $location_show ) && ! empty( $address ) ) : ?>
                        <li class="lp-contact-address"><i class="fa fa-location-arrow"></i><?php echo esc_html( $address ); ?><?php if ( ! empty( $latitude ) && ! empty( $longitude ) ) : ?> <a href="https://www.google.com/maps/search/?api=1&amp;query=<?php echo esc_attr( $latitude ); ?>,<?php echo esc_attr( $longitude ); ?>" target="_blank"><?php echo esc_html__( 'Yol Tarifi Al', 'listingpro' ); ?></a><?php endif; ?></li>
                    <?php endif; ?>
                    <?php if ( lp_onepage_on( $contact_show ) && 'yes' === $email_switcher && ! empty( $email ) ) : ?>
                        <li class="lp-contact-email"><i class="fa fa-envelope"></i><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></li>
                    <?php endif; ?>
                    <?php if ( ! empty( $phone ) ) : ?>
                        <li class="lp-contact-phone"><i class="fa fa-phone"></i><a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></li>
                    <?php endif; ?>
                    <?php if ( ! empty( $whatsapp ) ) : ?>
                        <li class="lp-contact-whatsapp"><i class="fa fa-whatsapp"></i><a href="<?php echo esc_url( $wa_link ); ?>" target="_blank"><?php echo esc_html( $whatsapp ); ?></a></li>
                    <?php endif; ?>
                    <?php if ( lp_onepage_on( $website_show ) && ! empty( $website ) ) : ?>
                        <li class="lp-contact-website"><i class="fa fa-globe"></i><a href="<?php echo esc_url( $website ); ?>" target="_blank"><?php echo esc_html( $website ); ?></a></li>
                    <?php endif; ?>
                    <?php if ( lp_onepage_on( $price_show ) && ! empty( $price_html ) ) : ?>
                        <li class="lp-contact-price"><?php echo $price_html; ?></li>
                    <?php endif; ?>
                    <?php if ( lp_onepage_on( $tags_show ) && ! empty( $tags_terms ) ) : ?>
                        <li class="lp-contact-tags"><i class="fa fa-tags"></i><?php echo esc_html( join( ', ', wp_list_pluck( $tags_terms, 'name' ) ) ); ?></li>
                    <?php endif; ?>
                </ul>
                <?php if ( lp_onepage_on( $social_show ) && ( $facebook || $twitter || $linkedin || $youtube || $instagram ) ) : ?>
                    <ul class="lp-social-list">
                        <?php if ( ! empty( $facebook ) ) : ?><li><a href="<?php echo esc_url( $facebook ); ?>" target="_blank"><i class="fa fa-facebook-square"></i></a></li><?php endif; ?>
                        <?php if ( ! empty( $twitter ) ) : ?><li><a href="<?php echo esc_url( $twitter ); ?>" target="_blank"><i class="fa fa-twitter"></i></a></li><?php endif; ?>
                        <?php if ( ! empty( $linkedin ) ) : ?><li><a href="<?php echo esc_url( $linkedin ); ?>" target="_blank"><i class="fa fa-linkedin"></i></a></li><?php endif; ?>
                        <?php if ( ! empty( $youtube ) ) : ?><li><a href="<?php echo esc_url( $youtube ); ?>" target="_blank"><i class="fa fa-youtube"></i></a></li><?php endif; ?>
                        <?php if ( ! empty( $instagram ) ) : ?><li><a href="<?php echo esc_url( $instagram ); ?>" target="_blank"><i class="fa fa-instagram"></i></a></li><?php endif; ?>
                    </ul>
                <?php endif; ?>
                <?php
                $additional_pos = isset( $listingpro_options['lp_detail_page_additional_styles'] ) ? $listingpro_options['lp_detail_page_additional_styles'] : '';
                if ( function_exists( 'listing_all_extra_fields_v2_right' ) && 'right' === $additional_pos ) {
                    listing_all_extra_fields_v2_right( get_the_ID() );
                }
                ?>
                <?php get_template_part( 'templates/single-list/listing-details-style3/sidebar/lead-form' ); ?>
            </div>
        </section>
        <?php if ( ! empty( $wa_link ) ) : ?>
            <a class="lp-whatsapp-float" href="<?php echo esc_url( $wa_link ); ?>" target="_blank"><i class="fa fa-whatsapp"></i></a>
        <?php endif; ?>

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
