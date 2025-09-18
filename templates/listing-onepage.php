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

        if ( ! function_exists( 'lp_onepage_capture' ) ) {
            function lp_onepage_capture( $template ) {
                $template_path = locate_template( $template, false, false );
                if ( empty( $template_path ) ) {
                    return '';
                }

                ob_start();

                global $post;
                $original_post = $post;
                if ( ! ( $post instanceof WP_Post ) ) {
                    $post = get_post( get_the_ID() );
                }

                include $template_path;

                $post = $original_post;

                return trim( ob_get_clean() );
            }
        }

        $layout_general = isset( $listingpro_options['lp-detail-page-layout6-content']['general'] )
            ? array_keys( $listingpro_options['lp-detail-page-layout6-content']['general'] )
            : array( 'lp_content_section', 'lp_services_section', 'lp_gallery_section', 'lp_video_section', 'lp_faqs_section' );

        $description = lp_onepage_meta( 'lp_listing_description' );
        if ( empty( $description ) ) {
            $description = get_post_field( 'post_content', get_the_ID() );
        }
        $services_terms = wp_get_post_terms( get_the_ID(), 'features' );
        $service_names  = array();
        if ( ! is_wp_error( $services_terms ) && ! empty( $services_terms ) ) {
            foreach ( $services_terms as $service_term ) {
                $name = trim( $service_term->name );
                if ( '' !== $name ) {
                    $service_names[] = $name;
                }
            }
        }
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
        $gallery_show  = get_post_meta( $plan_id, 'gallery_show', true );
        if ( 'none' === $plan_id ) {
            $map_show = $social_show = $location_show = $contact_show = $website_show = $hours_show = $faqs_show = $price_show = $tags_show = $gallery_show = 'true';
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
        $has_faq   = false;
        if ( is_array( $faqs ) ) {
            if ( isset( $faqs['faq'] ) && is_array( $faqs['faq'] ) ) {
                foreach ( $faqs['faq'] as $faq_item ) {
                    $question = isset( $faq_item['lp_title'] ) ? trim( $faq_item['lp_title'] ) : '';
                    $answer   = isset( $faq_item['lp_desc'] ) ? trim( $faq_item['lp_desc'] ) : '';
                    if ( '' !== $question || '' !== $answer ) {
                        $has_faq = true;
                        break;
                    }
                }
            }
        }
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
            $price_html = listingpro_price_dynesty( get_the_ID() );
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

        $enabled_sections = array_flip( $layout_general );

        $has_map   = lp_onepage_on( $map_show ) && ! empty( $latitude ) && ! empty( $longitude );
        $has_hours = lp_onepage_on( $hours_show ) && ( is_array( $hours ) ? ! empty( array_filter( $hours ) ) : ! empty( $hours ) );

        $sections_markup = array();
        $menu_items      = array( 'home' => __( 'Anasayfa', 'listingpro' ) );
        
        foreach ( $layout_general as $section_key ) {
            switch ( $section_key ) {
                case 'lp_content_section':
                    if ( empty( $description ) || isset( $sections_markup['about'] ) ) {
                        break;
                    }
                    ob_start();
                    ?>
                    <section id="about" class="lp-section lp-section-about">
                        <div class="container">
                            <h2 class="lp-section-title"><?php echo esc_html__( 'Hakkımızda', 'listingpro' ); ?></h2>
                            <?php echo apply_filters( 'the_content', $description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </section>
                    <?php
                    $sections_markup['about'] = ob_get_clean();
                    $menu_items['about']      = __( 'Hakkımızda', 'listingpro' );
                    break;

                case 'lp_services_section':
                    if ( empty( $service_names ) || isset( $sections_markup['services'] ) ) {
                        break;
                    }
                    ob_start();
                    ?>
                    <section id="services" class="lp-section lp-section-services">
                        <div class="container">
                            <h2 class="lp-section-title"><?php echo esc_html__( 'Hizmetler', 'listingpro' ); ?></h2>
                            <ul class="lp-services-list">
                                <?php foreach ( $service_names as $service_name ) : ?>
                                    <li><?php echo esc_html( $service_name ); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </section>
                    <?php
                    $sections_markup['services'] = ob_get_clean();
                    $menu_items['services']      = __( 'Hizmetler', 'listingpro' );
                    break;

                case 'lp_features_section':
                    if ( isset( $sections_markup['features'] ) ) {
                        break;
                    }
                    $features_html = lp_onepage_capture( 'templates/single-list/listing-details-style6/content/features.php' );
                    if ( empty( $features_html ) ) {
                        break;
                    }
                    ob_start();
                    ?>
                    <section id="features" class="lp-section lp-section-features">
                        <div class="container">
                            <h2 class="lp-section-title"><?php echo esc_html__( 'Özellikler', 'listingpro' ); ?></h2>
                            <?php echo $features_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </section>
                    <?php
                    $sections_markup['features'] = ob_get_clean();
                    $menu_items['features']      = __( 'Özellikler', 'listingpro' );
                    break;

                case 'lp_gallery_section':
                    if ( ! lp_onepage_on( $gallery_show ) || empty( $gallery_ids ) || isset( $sections_markup['gallery'] ) ) {
                        break;
                    }
                    ob_start();
                    ?>
                    <section id="gallery" class="lp-section lp-section-gallery">
                        <div class="container">
                            <h2 class="lp-section-title"><?php echo esc_html__( 'Resim', 'listingpro' ); ?></h2>
                            <div class="lp-gallery-grid">
                                <?php foreach ( $gallery_ids as $img_id ) :
                                    $full = wp_get_attachment_image_src( $img_id, 'full' );
                                    if ( empty( $full[0] ) ) {
                                        continue;
                                    }
                                    $thumb = wp_get_attachment_image( $img_id, 'large', false, array( 'class' => 'lp-gallery-thumb' ) );
                                    if ( empty( $thumb ) ) {
                                        $thumb = '<img class="lp-gallery-thumb" src="' . esc_url( $full[0] ) . '" alt="' . esc_attr( $lp_title ) . '" />';
                                    }
                                    ?>
                                    <a class="lp-gallery-item" href="<?php echo esc_url( $full[0] ); ?>" rel="prettyPhoto[gallery1]">
                                        <?php echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>
                    <?php
                    $sections_markup['gallery'] = ob_get_clean();
                    $menu_items['gallery']      = __( 'Resim', 'listingpro' );
                    break;

                case 'lp_video_section':
                    if ( empty( $video_html ) || isset( $sections_markup['video'] ) ) {
                        break;
                    }
                    ob_start();
                    ?>
                    <section id="video" class="lp-section lp-section-video">
                        <div class="container">
                            <h2 class="lp-section-title"><?php echo esc_html__( 'Video', 'listingpro' ); ?></h2>
                            <div class="lp-video-wrapper">
                                <?php echo wp_kses( $video_html, array_merge( wp_kses_allowed_html( 'post' ), array( 'iframe' => array(
                                    'src' => true,
                                    'width' => true,
                                    'height' => true,
                                    'frameborder' => true,
                                    'allowfullscreen' => true,
                                ) ) ) ); ?>
                            </div>
                        </div>
                    </section>
                    <?php
                    $sections_markup['video'] = ob_get_clean();
                    $menu_items['video']      = __( 'Video', 'listingpro' );
                    break;

                case 'lp_additional_section':
                    if ( isset( $sections_markup['additional'] ) ) {
                        break;
                    }
                    $additional_html = '';
                    if ( function_exists( 'listing_all_extra_fields' ) ) {
                        $additional_html = listing_all_extra_fields( get_the_ID() );
                    }
                    if ( empty( $additional_html ) && function_exists( 'listing_all_extra_fields_v2' ) ) {
                        $additional_html = listing_all_extra_fields_v2( get_the_ID() );
                    }
                    if ( empty( $additional_html ) ) {
                        break;
                    }
                    ob_start();
                    ?>
                    <section id="additional" class="lp-section lp-section-additional">
                        <div class="container">
                            <h2 class="lp-section-title"><?php echo esc_html__( 'Ek Bilgiler', 'listingpro' ); ?></h2>
                            <?php echo $additional_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </section>
                    <?php
                    $sections_markup['additional'] = ob_get_clean();
                    $menu_items['additional']      = __( 'Ek Bilgiler', 'listingpro' );
                    break;

                case 'lp_faqs_section':
                    if ( ! lp_onepage_on( $faqs_show ) || ! $has_faq || isset( $sections_markup['faq'] ) ) {
                        break;
                    }
                    $faq_markup = lp_onepage_capture( 'templates/single-list/listing-details-style4/content/list-faq.php' );
                    if ( empty( $faq_markup ) ) {
                        break;
                    }
                    ob_start();
                    ?>
                    <section id="faq" class="lp-section lp-section-faq">
                        <div class="container">
                            <h2 class="lp-section-title"><?php echo esc_html__( 'SSS', 'listingpro' ); ?></h2>
                            <?php echo $faq_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </section>
                    <?php
                    $sections_markup['faq'] = ob_get_clean();
                    $menu_items['faq']      = __( 'SSS', 'listingpro' );
                    break;

                case 'lp_announcements_section':
                    if ( isset( $sections_markup['announcements'] ) ) {
                        break;
                    }
                    $announcements_markup = lp_onepage_capture( 'templates/single-list/listing-details-style4/content/list-announcements.php' );
                    if ( empty( $announcements_markup ) ) {
                        break;
                    }
                    ob_start();
                    ?>
                    <section id="announcements" class="lp-section lp-section-announcements">
                        <div class="container">
                            <h2 class="lp-section-title"><?php echo esc_html__( 'Duyurular', 'listingpro' ); ?></h2>
                            <?php echo $announcements_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </section>
                    <?php
                    $sections_markup['announcements'] = ob_get_clean();
                    $menu_items['announcements']      = __( 'Duyurular', 'listingpro' );
                    break;

                case 'lp_offers_section':
                    if ( isset( $sections_markup['offers'] ) ) {
                        break;
                    }
                    $offers_markup = lp_onepage_capture( 'templates/single-list/listing-details-style4/content/list-offer-deals-discount.php' );
                    if ( empty( $offers_markup ) ) {
                        break;
                    }
                    ob_start();
                    ?>
                    <section id="offers" class="lp-section lp-section-offers">
                        <div class="container">
                            <h2 class="lp-section-title"><?php echo esc_html__( 'Fırsatlar', 'listingpro' ); ?></h2>
                            <?php echo $offers_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </section>
                    <?php
                    $sections_markup['offers'] = ob_get_clean();
                    $menu_items['offers']      = __( 'Fırsatlar', 'listingpro' );
                    break;

                case 'lp_menu_section':
                    if ( isset( $sections_markup['menu'] ) ) {
                        break;
                    }
                    $menu_markup = lp_onepage_capture( 'templates/single-list/listing-details-style4/content/list-menu.php' );
                    if ( empty( $menu_markup ) ) {
                        break;
                    }
                    ob_start();
                    ?>
                    <section id="menu" class="lp-section lp-section-menu">
                        <div class="container">
                            <h2 class="lp-section-title"><?php echo esc_html__( 'Menü', 'listingpro' ); ?></h2>
                            <?php echo $menu_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </section>
                    <?php
                    $sections_markup['menu'] = ob_get_clean();
                    $menu_items['menu']      = __( 'Menü', 'listingpro' );
                    break;

                case 'lp_event_section':
                    if ( isset( $sections_markup['events'] ) ) {
                        break;
                    }
                    $event_displayin = get_user_meta( $post_author_id, 'event_display_area', true );
                    if ( ! empty( $event_displayin ) && 'content' !== $event_displayin ) {
                        break;
                    }
                    $GLOBALS['event_grid_call'] = 'content_area';
                    $events_markup              = lp_onepage_capture( 'templates/single-list/event.php' );
                    unset( $GLOBALS['event_grid_call'] );
                    if ( empty( $events_markup ) ) {
                        break;
                    }
                    ob_start();
                    ?>
                    <section id="events" class="lp-section lp-section-events">
                        <div class="container">
                            <h2 class="lp-section-title"><?php echo esc_html__( 'Etkinlikler', 'listingpro' ); ?></h2>
                            <?php echo $events_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </section>
                    <?php
                    $sections_markup['events'] = ob_get_clean();
                    $menu_items['events']      = __( 'Etkinlikler', 'listingpro' );
                    break;

                case 'lp_booking_section':
                    if ( isset( $sections_markup['booking'] ) ) {
                        break;
                    }
                    $booking_markup = '';
                    if ( class_exists( 'Listingpro_bookings' ) ) {
                        $booking_template = WP_CONTENT_DIR . '/plugins/listingpro-bookings/templates/bookings.php';
                        if ( file_exists( $booking_template ) ) {
                            ob_start();
                            include $booking_template;
                            $booking_markup = trim( ob_get_clean() );
                        }
                    } elseif ( ! empty( $resurva_url ) ) {
                        $booking_markup = '<iframe src="' . esc_url( $resurva_url ) . '" frameborder="0" style="width:100%;height:600px"></iframe>';
                    } elseif ( ! empty( $timekit_booking ) ) {
                        $booking_markup = $timekit_booking;
                    }

                    if ( empty( $booking_markup ) ) {
                        break;
                    }

                    ob_start();
                    ?>
                    <section id="booking" class="lp-section lp-section-booking">
                        <div class="container">
                            <h2 class="lp-section-title"><?php echo esc_html__( 'Randevu', 'listingpro' ); ?></h2>
                            <?php echo $booking_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </section>
                    <?php
                    $sections_markup['booking'] = ob_get_clean();
                    $menu_items['booking']      = __( 'Randevu', 'listingpro' );
                    break;

                case 'lp_quicks_section':
                    if ( isset( $sections_markup['quick'] ) ) {
                        break;
                    }
                    $quicks_markup = lp_onepage_capture( 'templates/single-list/listing-details-style4/sidebar/quicks.php' );
                    if ( empty( $quicks_markup ) ) {
                        break;
                    }
                    ob_start();
                    ?>
                    <section id="quick" class="lp-section lp-section-quick">
                        <div class="container">
                            <h2 class="lp-section-title"><?php echo esc_html__( 'Hızlı İşlemler', 'listingpro' ); ?></h2>
                            <?php echo $quicks_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </section>
                    <?php
                    $sections_markup['quick'] = ob_get_clean();
                    $menu_items['quick']      = __( 'Hızlı İşlemler', 'listingpro' );
                    break;

                case 'lp_reviews_section':
                    if ( isset( $sections_markup['reviews'] ) ) {
                        break;
                    }
                    $reviews_markup = lp_onepage_capture( 'templates/single-list/listing-details-style6/content/reviews.php' );
                    if ( empty( $reviews_markup ) ) {
                        break;
                    }
                    ob_start();
                    ?>
                    <section id="reviews" class="lp-section lp-section-reviews">
                        <div class="container">
                            <h2 class="lp-section-title"><?php echo esc_html__( 'Yorumlar', 'listingpro' ); ?></h2>
                            <?php echo $reviews_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </section>
                    <?php
                    $sections_markup['reviews'] = ob_get_clean();
                    $menu_items['reviews']      = __( 'Yorumlar', 'listingpro' );
                    break;

                case 'lp_reviewform_section':
                    if ( isset( $sections_markup['reviewform'] ) ) {
                        break;
                    }
                    $reviewform_markup = lp_onepage_capture( 'templates/single-list/listing-details-style4/content/list-review-form.php' );
                    if ( empty( $reviewform_markup ) ) {
                        break;
                    }
                    ob_start();
                    ?>
                    <section id="reviewform" class="lp-section lp-section-reviewform">
                        <div class="container">
                            <h2 class="lp-section-title"><?php echo esc_html__( 'Yorum Yaz', 'listingpro' ); ?></h2>
                            <?php echo $reviewform_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </section>
                    <?php
                    $sections_markup['reviewform'] = ob_get_clean();
                    $menu_items['reviewform']      = __( 'Yorum Yaz', 'listingpro' );
                    break;
            }
        }

        if ( $has_map ) {
            $menu_items['map'] = __( 'Harita', 'listingpro' );
        }
        if ( $has_hours ) {
            $menu_items['hours'] = __( 'Çalışma Saatleri', 'listingpro' );
        }
        $menu_items['contact'] = __( 'İletişim', 'listingpro' );

        $preferred_menu_order = array(
            'home'    => __( 'Anasayfa', 'listingpro' ),
            'about'   => __( 'Hakkımızda', 'listingpro' ),
            'services'=> __( 'Hizmetler', 'listingpro' ),
            'video'   => __( 'Video', 'listingpro' ),
            'gallery' => __( 'Resim', 'listingpro' ),
            'reviews' => __( 'Yorumlar', 'listingpro' ),
            'hours'   => __( 'Çalışma Saatleri', 'listingpro' ),
            'contact' => __( 'İletişim', 'listingpro' ),
        );

        $filtered_menu = array();
        foreach ( $preferred_menu_order as $slug => $label ) {
            if ( 'home' === $slug ) {
                $filtered_menu[ $slug ] = $label;
                continue;
            }

            if ( 'hours' === $slug ) {
                if ( $has_hours ) {
                    $filtered_menu[ $slug ] = $label;
                }
                continue;
            }

            if ( isset( $menu_items[ $slug ] ) ) {
                $filtered_menu[ $slug ] = $menu_items[ $slug ];
            }
        }

        $menu_items = $filtered_menu;

        $preferred_section_order = array( 'about', 'services', 'video', 'gallery', 'reviews' );
        $ordered_sections        = array();
        foreach ( $preferred_section_order as $section_slug ) {
            if ( isset( $sections_markup[ $section_slug ] ) ) {
                $ordered_sections[ $section_slug ] = $sections_markup[ $section_slug ];
                unset( $sections_markup[ $section_slug ] );
            }
        }
        $sections_markup = $ordered_sections + $sections_markup;

        $b_logo       = $listingpro_options['business_logo_switch'];
        $allow_logo   = isset( $listingpro_options['listingpro_allow_logo_styles_switch'] ) ? $listingpro_options['listingpro_allow_logo_styles_switch'] : '';
        $business_logo_url = '';
        if ( $b_logo && 'yes' === $allow_logo ) {
            $b_logo_default    = isset( $listingpro_options['business_logo_default']['url'] ) ? $listingpro_options['business_logo_default']['url'] : '';
            $business_logo     = lp_onepage_meta_by_id( 'business_logo', get_the_ID() );
            $business_logo_url = ! empty( $business_logo ) ? $business_logo : $b_logo_default;
        }
        $logo_html = '';
        if ( ! empty( $business_logo_url ) ) {
            $logo_html = '<img src="' . esc_url( $business_logo_url ) . '" alt="' . esc_attr__( 'Listing Logo', 'listingpro' ) . '" />';
        } elseif ( has_post_thumbnail() ) {
            $logo_html = get_the_post_thumbnail( get_the_ID(), 'thumbnail' );
        } else {
            $initial = function_exists( 'mb_substr' ) ? mb_substr( $lp_title, 0, 1 ) : substr( $lp_title, 0, 1 );
            $logo_html = '<span class="lp-logo-initial">' . esc_html( strtoupper( $initial ) ) . '</span>';
        }
        $header_bg = isset( $listingpro_options['lp_detail_page_styles4_bg'] ) ? $listingpro_options['lp_detail_page_styles4_bg'] : array();
        $hero_image_url = '';
        $featured_id    = get_post_thumbnail_id( get_the_ID() );
        if ( $featured_id ) {
            $hero_image_url = wp_get_attachment_image_url( $featured_id, 'full' );
        }
        if ( empty( $hero_image_url ) && ! empty( $gallery_ids ) ) {
            $first_gallery = reset( $gallery_ids );
            if ( $first_gallery ) {
                $hero_image_url = wp_get_attachment_image_url( $first_gallery, 'full' );
            }
        }
        if ( empty( $hero_image_url ) && ! empty( $header_bg['url'] ) ) {
            $hero_image_url = $header_bg['url'];
        }
        ?>
        <style>
        .lp-onepage-header{position:sticky;top:0;background:#fff;z-index:999;border-bottom:1px solid #eee;}
        .lp-onepage-header-inner{display:flex;align-items:center;justify-content:space-between;gap:30px;padding:15px 0;}
        .lp-onepage-brand{display:flex;align-items:center;gap:15px;}
        .lp-onepage-logo{width:60px;height:60px;border-radius:50%;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#eef2f7;font-weight:700;font-size:22px;color:#1f2933;text-transform:uppercase;}
        .lp-onepage-logo img{width:100%;height:100%;object-fit:cover;}
        .lp-logo-initial{display:block;width:100%;height:100%;line-height:60px;text-align:center;}
        .lp-onepage-name{font-weight:700;font-size:20px;}
        .lp-onepage-nav ul{list-style:none;margin:0;padding:0;display:flex;gap:30px;}
        .lp-onepage-nav a{text-decoration:none;color:#333;font-weight:600;display:flex;align-items:center;gap:5px;}
        .lp-onepage-nav a:hover{color:#0073aa;}
        .lp-section{padding:60px 0;}
        .lp-section .container{max-width:1170px;margin:0 auto;}
        .lp-section-title{margin:0 0 30px;font-size:28px;font-weight:700;text-align:center;}
        .lp-services-list{list-style:none;margin:0 auto;max-width:700px;padding:0;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;text-align:center;}
        .lp-services-list li{background:#f7f9fc;border-radius:10px;padding:12px 16px;font-weight:600;color:#1f2933;box-shadow:0 8px 20px rgba(15,23,42,0.06);}
        .lp-gallery-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;}
        .lp-gallery-item{display:block;overflow:hidden;border-radius:8px;box-shadow:0 10px 25px rgba(15,23,42,0.08);transition:transform .3s ease,box-shadow .3s ease;}
        .lp-gallery-item:hover{transform:translateY(-4px);box-shadow:0 14px 30px rgba(15,23,42,0.12);}
        .lp-gallery-thumb{width:100%;height:100%;object-fit:cover;display:block;}
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
        .lp-whatsapp-float{position:fixed;right:20px;bottom:20px;width:50px;height:50px;border-radius:50%;background:#25d366;color:#fff;display:flex;align-items:center;justify-content:center;font-size:24px;z-index:1000;}
        .lp-hero-banner{position:relative;min-height:360px;background-size:cover;background-position:center center;border-radius:12px;margin:20px auto;max-width:1170px;overflow:hidden;}
        .lp-hero-banner .lp-header-overlay{position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(15,23,42,0.25);}
        </style>
        <div class="lp-onepage-wrapper">
        <header class="lp-onepage-header">
            <div class="container lp-onepage-header-inner">
            <div class="lp-onepage-brand">
                <div class="lp-onepage-logo"><?php echo $logo_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
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

        <section id="home" class="lp-section lp-section-home">
            <div class="lp-hero-banner" <?php if ( ! empty( $hero_image_url ) ) : ?>style="background-image:url(<?php echo esc_url( $hero_image_url ); ?>)"<?php endif; ?>>
                <div class="lp-header-overlay"></div>
            </div>
        </section>
        <?php foreach ( $sections_markup as $section_html ) : ?>
            <?php echo $section_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php endforeach; ?>

        <?php if ( $has_map ) :
            $lp_map_pin = $listingpro_options['lp_map_pin']['url']; ?>
            <section id="map" class="lp-section lp-section-map">
                <div class="container">
                    <h2 class="lp-section-title"><?php echo esc_html__( 'Harita', 'listingpro' ); ?></h2>
                    <div id="singlepostmap" class="singlemap" data-lat="<?php echo esc_attr( $latitude ); ?>" data-lan="<?php echo esc_attr( $longitude ); ?>" data-pinicon="<?php echo esc_attr( $lp_map_pin ); ?>"></div>
                </div>
            </section>
        <?php endif; ?>

        <?php if ( $has_hours ) : ?>
            <section id="hours" class="lp-section lp-section-hours">
                <div class="container">
                    <h2 class="lp-section-title"><?php echo esc_html__( 'Çalışma Saatleri', 'listingpro' ); ?></h2>
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
                        <li class="lp-contact-price"><?php echo wp_kses_post( $price_html ); ?></li>
                    <?php endif; ?>
                    <?php if ( lp_onepage_on( $tags_show ) && ! empty( $tags_terms ) ) :
                        $tag_names = array();
                        foreach ( $tags_terms as $tag_term ) {
                            $tag_names[] = $tag_term->name;
                        }
                        $tag_names = array_filter( $tag_names );
                        if ( ! empty( $tag_names ) ) : ?>
                        <li class="lp-contact-tags"><i class="fa fa-tags"></i><?php echo esc_html( implode( ', ', $tag_names ) ); ?></li>
                        <?php endif; ?>
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
