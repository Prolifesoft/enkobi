<?php
/* One-page listing detail template */
if ( ! function_exists( 'lp_onepage_meta' ) ) {
    function lp_onepage_meta( $key, $post_id = null ) {
        $id = $post_id ? $post_id : get_the_ID();

        if ( function_exists( 'listing_get_metabox' ) ) {
            $value = listing_get_metabox( $key );
            if ( null !== $value && '' !== $value ) {
                return $value;
            }
        }

        if ( function_exists( 'listingpro_get_metabox' ) ) {
            $value = listingpro_get_metabox( $key );
            if ( null !== $value && '' !== $value ) {
                return $value;
            }
        }

        return get_post_meta( $id, $key, true );
    }
}

if ( ! function_exists( 'lp_onepage_meta_by_id' ) ) {
    function lp_onepage_meta_by_id( $key, $post_id ) {
        if ( function_exists( 'listing_get_metabox_by_ID' ) ) {
            $value = listing_get_metabox_by_ID( $key, $post_id );
            if ( null !== $value && '' !== $value ) {
                return $value;
            }
        }

        if ( function_exists( 'listingpro_get_metabox_by_ID' ) ) {
            $value = listingpro_get_metabox_by_ID( $key, $post_id );
            if ( null !== $value && '' !== $value ) {
                return $value;
            }
        }

        return get_post_meta( $post_id, $key, true );
    }
}

if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        global $listingpro_options;

        if ( ! function_exists( 'lp_onepage_on' ) ) {
            function lp_onepage_on( $flag ) {
                return ! in_array( $flag, array( 'false', '0', 'off', 'no' ), true );
            }
        }

        if ( ! function_exists( 'lp_onepage_capture' ) ) {
            function lp_onepage_capture( $template, $vars = array() ) {
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

                if ( ! empty( $vars ) && is_array( $vars ) ) {
                    extract( $vars, EXTR_SKIP );
                }

                include $template_path;

                $post = $original_post;

                return trim( ob_get_clean() );
            }
        }

        $currentUserId = get_current_user_id();
        $showReport    = true;
        if ( isset( $listingpro_options['lp_detail_page_report_button'] ) && 'off' === $listingpro_options['lp_detail_page_report_button'] ) {
            $showReport = false;
        }

        if ( ! function_exists( 'lp_onepage_collect_keys' ) ) {
            function lp_onepage_collect_keys( $option_array ) {
                $keys = array();
                if ( is_array( $option_array ) ) {
                    foreach ( $option_array as $key => $value ) {
                        if ( is_string( $key ) && '' !== $key && ! is_numeric( $key ) ) {
                            $keys[] = $key;
                        } elseif ( is_string( $value ) && '' !== $value ) {
                            $keys[] = $value;
                        }
                    }
                }
                return $keys;
            }
        }

        $layout_general = array();
        if ( isset( $listingpro_options['lp-detail-page-layout4-content']['general'] ) ) {
            $layout_general = lp_onepage_collect_keys( $listingpro_options['lp-detail-page-layout4-content']['general'] );
        }

        $layout_sidebar = array();
        if ( isset( $listingpro_options['lp-detail-page-layout4-rsidebar']['sidebar'] ) ) {
            $layout_sidebar = lp_onepage_collect_keys( $listingpro_options['lp-detail-page-layout4-rsidebar']['sidebar'] );
        }

        $layout_sections = $layout_general;
        foreach ( $layout_sidebar as $sidebar_key ) {
            if ( ! in_array( $sidebar_key, $layout_sections, true ) ) {
                $layout_sections[] = $sidebar_key;
            }
        }

        if ( empty( $layout_sections ) ) {
            $layout_sections = array(
                'lp_content_section',
                'lp_services_section',
                'lp_features_section',
                'lp_gallery_section',
                'lp_video_section',
                'lp_announcements_section',
                'lp_offers_section',
                'lp_menu_section',
                'lp_event_section',
                'lp_booking_section',
                'lp_quicks_section',
                'lp_faqs_section',
                'lp_additional_section',
                'lp_reviews_section',
                'lp_reviewform_section',
                'lp_sidebar_video',
                'lp_mapsocial_section',
                'lp_timing_section',
            );
        }

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
        if ( is_array( $faqs ) && isset( $faqs['faq'] ) && is_array( $faqs['faq'] ) ) {
            foreach ( $faqs['faq'] as $index => $faq_item ) {
                if ( is_array( $faq_item ) ) {
                    $question = isset( $faq_item['lp_title'] ) ? trim( $faq_item['lp_title'] ) : '';
                    $answer   = isset( $faq_item['lp_desc'] ) ? trim( $faq_item['lp_desc'] ) : '';
                } else {
                    $question = trim( (string) $faq_item );
                    $answer   = '';
                    if ( isset( $faqs['faqans'][ $index ] ) ) {
                        $answer = trim( (string) $faqs['faqans'][ $index ] );
                    }
                }

                if ( '' !== $question || '' !== $answer ) {
                    $has_faq = true;
                    break;
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

        $lp_title      = get_the_title();
        $tagline_text  = lp_onepage_meta( 'tagline_text' );
        $tagline_plain = is_string( $tagline_text ) ? trim( wp_strip_all_tags( $tagline_text ) ) : '';
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

        $has_map   = ! empty( $latitude ) && ! empty( $longitude );
        $has_hours = is_array( $hours ) ? ! empty( array_filter( $hours ) ) : ! empty( $hours );

        $sections_markup = array();
        $menu_items      = array();
        
        foreach ( $layout_sections as $section_key ) {
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
                    $gallery_markup = lp_onepage_capture( 'templates/single-list/listing-details-style4/content/gallery.php' );
                    if ( empty( $gallery_markup ) ) {
                        break;
                    }
                    ob_start();
                    ?>
                    <section id="gallery" class="lp-section lp-section-gallery">
                        <div class="container">
                            <h2 class="lp-section-title"><?php echo esc_html__( 'Resim', 'listingpro' ); ?></h2>
                            <div class="lp-gallery-slider-wrap">
                                <?php echo $gallery_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </div>
                        </div>
                    </section>
                    <?php
                    $sections_markup['gallery'] = ob_get_clean();
                    $menu_items['gallery']      = __( 'Resim', 'listingpro' );
                    break;

                case 'lp_sidebar_video':
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
                    $quicks_markup = lp_onepage_capture(
                        'templates/single-list/listing-details-style4/sidebar/quicks.php',
                        array(
                            'currentUserId' => $currentUserId,
                            'showReport'    => $showReport,
                        )
                    );
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

        $preferred_menu_order = array(
            'about'   => __( 'Hakkımızda', 'listingpro' ),
            'services'=> __( 'Hizmetler', 'listingpro' ),
            'video'   => __( 'Video', 'listingpro' ),
            'gallery' => __( 'Resim', 'listingpro' ),
            'hours'   => __( 'Çalışma Saatleri', 'listingpro' ),
            'contact' => __( 'İletişim', 'listingpro' ),
        );

        $filtered_menu = array();
        foreach ( $preferred_menu_order as $slug => $label ) {
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
        .lp-onepage-wrapper{position:relative;background:#f5f7fb;}
        .lp-onepage-header{position:sticky;top:0;z-index:1000;background:rgba(255,255,255,0.96);backdrop-filter:blur(14px);border-bottom:1px solid rgba(148,163,184,0.2);}
        .lp-onepage-header-inner{display:flex;align-items:center;justify-content:space-between;gap:24px;padding:14px 0;}
        .lp-onepage-brand{display:flex;align-items:center;gap:14px;min-width:0;}
        .lp-onepage-brand-link{display:flex;align-items:center;gap:14px;text-decoration:none;color:#0f172a;}
        .lp-onepage-brand-link:hover{color:#2563eb;}
        .lp-onepage-logo{width:64px;height:64px;border-radius:50%;overflow:hidden;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#e0f2fe 0%,#dbeafe 100%);font-weight:700;font-size:22px;color:#0f172a;text-transform:uppercase;box-shadow:0 8px 20px rgba(15,23,42,0.08);}
        .lp-onepage-logo img{width:100%;height:100%;object-fit:cover;}
        .lp-logo-initial{display:flex;align-items:center;justify-content:center;width:100%;height:100%;}
        .lp-onepage-name{font-weight:700;font-size:20px;line-height:1.2;color:#0f172a;}
        .lp-menu-toggle{display:none;flex-direction:column;gap:5px;background:none;border:0;cursor:pointer;padding:4px;}
        .lp-menu-toggle span{display:block;width:24px;height:2px;background:#0f172a;transition:all .3s ease;}
        .lp-onepage-nav{margin-left:auto;}
        .lp-onepage-nav ul{display:flex;gap:26px;align-items:center;list-style:none;margin:0;padding:0;}
        .lp-onepage-nav li{position:relative;}
        .lp-onepage-nav a{font-weight:600;color:#1f2937;text-decoration:none;display:flex;align-items:center;gap:6px;transition:color .2s ease;}
        .lp-onepage-nav a:hover,.lp-onepage-nav a:focus{color:#2563eb;}
        .lp-onepage-nav .lp-nav-phone a,.lp-onepage-nav .lp-nav-whatsapp a{padding:8px 14px;border-radius:999px;background:#f1f5f9;box-shadow:0 6px 16px rgba(15,23,42,0.08);}
        .lp-onepage-nav .lp-nav-phone a:hover{background:#fee2e2;color:#b91c1c;}
        .lp-onepage-nav .lp-nav-whatsapp a:hover{background:#dcfce7;color:#15803d;}
        .lp-section{padding:72px 0;position:relative;}
        .lp-section .container{max-width:1180px;margin:0 auto;padding:0 24px;}
        .lp-section-title{margin:0 0 36px;font-size:32px;font-weight:700;text-align:center;color:#0f172a;}
        .lp-services-list{list-style:none;margin:0 auto;max-width:780px;padding:0;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;text-align:center;}
        .lp-services-list li{background:#fff;border-radius:14px;padding:16px 18px;font-weight:600;color:#1f2937;box-shadow:0 14px 30px rgba(15,23,42,0.08);}
        .lp-gallery-slider-wrap{position:relative;}
        .lp-gallery-slider-wrap .lp-listing-slider{margin:0 auto;}
        .lp-gallery-slider-wrap .lp-listing-slider .slick-list{margin:0 -12px;}
        .lp-gallery-slider-wrap .lp-listing-slide-wrap{padding:0 12px;}
        .lp-gallery-slider-wrap .lp-listing-slide{border-radius:18px;overflow:hidden;box-shadow:0 18px 36px rgba(15,23,42,0.12);}
        .lp-gallery-slider-wrap .lp-listing-slide img{width:100%;height:auto;display:block;}
        .lp-video-wrapper{position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:18px;box-shadow:0 16px 36px rgba(15,23,42,0.18);}
        .lp-video-wrapper iframe{position:absolute;top:0;left:0;width:100%;height:100%;border:0;border-radius:18px;}
        #singlepostmap{width:100%;height:360px;border-radius:16px;box-shadow:0 18px 40px rgba(15,23,42,0.1);}
        .lp-contact-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:36px;align-items:flex-start;}
        .lp-contact-card{background:#fff;border-radius:20px;padding:32px;box-shadow:0 22px 40px rgba(15,23,42,0.12);}
        .lp-contact-list{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:14px;}
        .lp-contact-list li{display:flex;align-items:center;gap:12px;font-size:15px;color:#1f2937;line-height:1.5;}
        .lp-contact-list i{width:18px;text-align:center;font-size:16px;color:#2563eb;}
        .lp-contact-list a{color:#0f172a;font-weight:600;text-decoration:none;}
        .lp-contact-list a:hover{color:#2563eb;text-decoration:underline;}
        .lp-social-list{list-style:none;margin:24px 0 0;padding:0;display:flex;gap:16px;justify-content:flex-start;}
        .lp-social-list a{display:flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:12px;background:#eff6ff;color:#1d4ed8;font-size:20px;text-decoration:none;transition:all .2s ease;}
        .lp-social-list a:hover{background:#2563eb;color:#fff;}
        .lp-phone-float,.lp-whatsapp-float{position:fixed;right:24px;width:54px;height:54px;border-radius:50%;color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;z-index:999;box-shadow:0 14px 28px rgba(15,23,42,0.25);transition:transform .2s ease,box-shadow .2s ease;}
        .lp-phone-float{bottom:96px;background:#ef4444;}
        .lp-phone-float:hover{transform:translateY(-2px);box-shadow:0 20px 36px rgba(239,68,68,0.4);}
        .lp-whatsapp-float{bottom:32px;background:#22c55e;}
        .lp-whatsapp-float:hover{transform:translateY(-2px);box-shadow:0 20px 36px rgba(34,197,94,0.45);}
        .lp-hero{position:relative;min-height:420px;border-radius:28px;overflow:hidden;display:flex;align-items:center;background-size:cover;background-position:center;box-shadow:0 28px 60px rgba(15,23,42,0.35);}
        .lp-hero::after{content:"";position:absolute;inset:0;background:linear-gradient(120deg,rgba(15,23,42,0.75) 0%,rgba(30,64,175,0.55) 55%,rgba(59,130,246,0.45) 100%);}
        .lp-hero-overlay{position:absolute;inset:0;}
        .lp-hero-grid{position:relative;z-index:2;display:flex;flex-direction:column;gap:32px;width:100%;padding:48px 36px;}
        .lp-hero-info{color:#fff;display:flex;flex-direction:column;gap:18px;}
        .lp-hero-brand{display:flex;align-items:center;gap:20px;}
        .lp-hero-logo{width:82px;height:82px;border-radius:50%;overflow:hidden;background:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 18px 36px rgba(15,23,42,0.35);}
        .lp-hero-logo img{width:100%;height:100%;object-fit:cover;}
        .lp-hero-text h1{margin:0;font-size:36px;font-weight:700;line-height:1.1;}
        .lp-hero-claim{margin-top:10px;display:inline-flex;align-items:center;gap:10px;flex-wrap:wrap;}
        .lp-hero-claim .claimed{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:6px 12px;background:rgba(34,197,94,0.18);color:#ecfdf5;font-weight:600;font-size:13px;}
        .lp-hero-claim .claimed i{color:#22c55e;}
        .lp-hero-tagline{margin-top:6px;font-size:18px;color:rgba(241,245,249,0.88);font-weight:500;}
        .lp-hero-rating{display:flex;align-items:center;gap:12px;margin-top:12px;flex-wrap:wrap;}
        .lp-hero-rating-score{display:inline-flex;align-items:center;justify-content:center;width:48px;height:48px;border-radius:14px;background:#fcd34d;color:#78350f;font-weight:700;font-size:18px;}
        .lp-hero-rating-count{font-size:15px;color:rgba(248,250,252,0.85);font-weight:600;}
        .lp-hero-meta{list-style:none;margin:0;padding:0;display:flex;flex-wrap:wrap;gap:12px;}
        .lp-hero-meta li{display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:999px;background:rgba(15,23,42,0.35);backdrop-filter:blur(6px);font-weight:600;font-size:14px;}
        .lp-hero-meta i{font-size:15px;}
        .lp-onepage-wrapper .lp-section:nth-of-type(even){background:#fff;}
        .lp-onepage-wrapper .lp-section:nth-of-type(odd){background:#f8fafc;}
        .lp-section .container > p:last-child{margin-bottom:0;}
        @media (max-width:1200px){
            .lp-hero-grid{padding:42px 28px;}
        }
        @media (max-width:992px){
            .lp-onepage-header-inner{flex-wrap:wrap;}
            .lp-menu-toggle{display:flex;}
            .lp-onepage-nav{width:100%;order:3;}
            .lp-onepage-nav ul{flex-direction:column;align-items:flex-start;gap:14px;padding:18px 0;display:none;}
            .lp-onepage-nav.is-open ul{display:flex;}
            .lp-onepage-nav li{width:100%;}
            .lp-onepage-nav a{width:100%;justify-content:flex-start;}
        }
        @media (max-width:768px){
            .lp-section{padding:56px 0;}
            .lp-hero-text h1{font-size:30px;}
            .lp-hero{min-height:380px;}
            .lp-hero-meta{gap:10px;}
            .lp-hero-meta li{font-size:13px;}
        }
        @media (max-width:576px){
            .lp-onepage-logo{width:56px;height:56px;}
            .lp-hero-logo{width:72px;height:72px;}
            .lp-contact-card{padding:24px;}
            .lp-onepage-nav .lp-nav-phone a,.lp-onepage-nav .lp-nav-whatsapp a{width:100%;justify-content:flex-start;}
        }
        </style>
        <?php
        if ( $has_map && ! isset( $menu_items['map'] ) && in_array( 'lp_mapsocial_section', $layout_sections, true ) ) {
            $menu_items['map'] = __( 'Harita', 'listingpro' );
        }
        if ( $has_hours && ! isset( $menu_items['hours'] ) && in_array( 'lp_timing_section', $layout_sections, true ) ) {
            $menu_items['hours'] = __( 'Çalışma Saatleri', 'listingpro' );
        }
        $menu_items['contact'] = __( 'İletişim', 'listingpro' );
        $menu_items = array( 'home' => __( 'Anasayfa', 'listingpro' ) ) + $menu_items;
        ?>
        <div class="lp-onepage-wrapper">
        <header class="lp-onepage-header">
            <div class="container lp-onepage-header-inner">
                <div class="lp-onepage-brand">
                    <a href="#home" class="lp-onepage-brand-link">
                        <div class="lp-onepage-logo"><?php echo $logo_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                        <span class="lp-onepage-name"><?php echo esc_html( $lp_title ); ?></span>
                    </a>
                </div>
                <button class="lp-menu-toggle" type="button" aria-expanded="false" aria-controls="lp-onepage-nav">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <nav class="lp-onepage-nav" id="lp-onepage-nav">
                    <ul>
                        <?php foreach ( $menu_items as $slug => $label ) : ?>
                            <li><a href="#<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></a></li>
                        <?php endforeach; ?>
                        <?php if ( ! empty( $phone ) ) : ?>
                            <li class="lp-nav-phone"><a href="tel:<?php echo esc_attr( $phone ); ?>"><i class="fa fa-phone"></i><?php echo esc_html( $phone ); ?></a></li>
                        <?php endif; ?>
                        <?php if ( ! empty( $whatsapp ) ) : ?>
                            <li class="lp-nav-whatsapp"><a href="<?php echo esc_url( $wa_link ); ?>" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i><?php echo esc_html( $whatsapp ); ?></a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </header>

        <section id="home" class="lp-section lp-section-home">
            <div class="lp-hero" <?php if ( ! empty( $hero_image_url ) ) : ?>style="background-image:url(<?php echo esc_url( $hero_image_url ); ?>)"<?php endif; ?>>
                <span class="lp-hero-overlay" aria-hidden="true"></span>
                <div class="container">
                    <div class="lp-hero-grid">
                        <div class="lp-hero-info">
                            <div class="lp-hero-brand">
                                <div class="lp-hero-logo"><?php echo $logo_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                                <div class="lp-hero-text">
                                    <h1><?php echo esc_html( $lp_title ); ?></h1>
                                    <?php if ( ! empty( $claim ) ) : ?>
                                        <div class="lp-hero-claim"><?php echo $claim; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $tagline_plain ) ) : ?>
                                        <div class="lp-hero-tagline"><?php echo esc_html( $tagline_plain ); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ( $NumberRating > 0 && floatval( $rating ) > 0 ) : ?>
                                <div class="lp-hero-rating">
                                    <span class="lp-hero-rating-score"><?php echo esc_html( number_format_i18n( floatval( $rating ), 1 ) ); ?></span>
                                    <span class="lp-hero-rating-count"><?php printf( esc_html__( '%s değerlendirme', 'listingpro' ), esc_html( number_format_i18n( $NumberRating ) ) ); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ( ! empty( $locations ) || ! empty( $categories ) || ! empty( $price_html ) ) : ?>
                                <ul class="lp-hero-meta">
                                    <?php if ( ! empty( $locations ) ) : ?>
                                        <li><i class="fa fa-map-marker"></i><?php echo esc_html( $locations[0]->name ); ?></li>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $categories ) ) : ?>
                                        <li><i class="fa fa-briefcase"></i><?php echo esc_html( $categories[0]->name ); ?></li>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $price_html ) ) : ?>
                                        <li><i class="fa fa-tag"></i><?php echo esc_html( wp_strip_all_tags( $price_html ) ); ?></li>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
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

        <?php
        $additional_pos    = isset( $listingpro_options['lp_detail_page_additional_styles'] ) ? $listingpro_options['lp_detail_page_additional_styles'] : '';
        $extra_right_html  = '';
        if ( function_exists( 'listing_all_extra_fields_v2_right' ) && 'right' === $additional_pos ) {
            $extra_right_html = listing_all_extra_fields_v2_right( get_the_ID() );
        }
        ob_start();
        get_template_part( 'templates/single-list/listing-details-style3/sidebar/lead-form' );
        $lead_form_html = trim( ob_get_clean() );
        ?>

        <section id="contact" class="lp-section lp-section-contact">
            <div class="container">
                <h2 class="lp-section-title"><?php echo esc_html( $menu_items['contact'] ); ?></h2>
                <div class="lp-contact-grid">
                    <div class="lp-contact-card">
                        <ul class="lp-contact-list">
                            <?php if ( ! empty( $locations ) ) : ?>
                                <li class="lp-contact-location"><i class="fa fa-map-marker"></i><?php echo esc_html( $locations[0]->name ); ?></li>
                            <?php endif; ?>
                            <?php if ( ! empty( $categories ) ) : ?>
                                <li class="lp-contact-category"><i class="fa fa-folder-open"></i><?php echo esc_html( $categories[0]->name ); ?></li>
                            <?php endif; ?>
                            <?php if ( ! empty( $address ) ) : ?>
                                <li class="lp-contact-address"><i class="fa fa-location-arrow"></i><?php echo esc_html( $address ); ?><?php if ( ! empty( $latitude ) && ! empty( $longitude ) ) : ?> <a href="https://www.google.com/maps/search/?api=1&amp;query=<?php echo esc_attr( $latitude ); ?>,<?php echo esc_attr( $longitude ); ?>" target="_blank" rel="noopener"><?php echo esc_html__( 'Yol Tarifi Al', 'listingpro' ); ?></a><?php endif; ?></li>
                            <?php endif; ?>
                            <?php if ( 'yes' === $email_switcher && ! empty( $email ) ) : ?>
                                <li class="lp-contact-email"><i class="fa fa-envelope"></i><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></li>
                            <?php endif; ?>
                            <?php if ( ! empty( $phone ) ) : ?>
                                <li class="lp-contact-phone"><i class="fa fa-phone"></i><a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></li>
                            <?php endif; ?>
                            <?php if ( ! empty( $whatsapp ) && ! empty( $wa_link ) ) : ?>
                                <li class="lp-contact-whatsapp"><i class="fa fa-whatsapp"></i><a href="<?php echo esc_url( $wa_link ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $whatsapp ); ?></a></li>
                            <?php endif; ?>
                            <?php if ( ! empty( $website ) ) : ?>
                                <li class="lp-contact-website"><i class="fa fa-globe"></i><a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $website ); ?></a></li>
                            <?php endif; ?>
                            <?php if ( ! empty( $price_html ) ) : ?>
                                <li class="lp-contact-price"><?php echo wp_kses_post( $price_html ); ?></li>
                            <?php endif; ?>
                            <?php if ( ! empty( $tags_terms ) ) :
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
                                <?php if ( ! empty( $facebook ) ) : ?><li><a href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener"><i class="fa fa-facebook-square"></i></a></li><?php endif; ?>
                                <?php if ( ! empty( $twitter ) ) : ?><li><a href="<?php echo esc_url( $twitter ); ?>" target="_blank" rel="noopener"><i class="fa fa-twitter"></i></a></li><?php endif; ?>
                                <?php if ( ! empty( $linkedin ) ) : ?><li><a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener"><i class="fa fa-linkedin"></i></a></li><?php endif; ?>
                                <?php if ( ! empty( $youtube ) ) : ?><li><a href="<?php echo esc_url( $youtube ); ?>" target="_blank" rel="noopener"><i class="fa fa-youtube"></i></a></li><?php endif; ?>
                                <?php if ( ! empty( $instagram ) ) : ?><li><a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener"><i class="fa fa-instagram"></i></a></li><?php endif; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <?php if ( ! empty( $extra_right_html ) || ! empty( $lead_form_html ) ) : ?>
                        <div class="lp-contact-card">
                            <?php echo $extra_right_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php echo $lead_form_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php if ( ! empty( $phone ) ) : ?>
            <a class="lp-phone-float" href="tel:<?php echo esc_attr( $phone ); ?>"><i class="fa fa-phone"></i></a>
        <?php endif; ?>
        <?php if ( ! empty( $wa_link ) ) : ?>
            <a class="lp-whatsapp-float" href="<?php echo esc_url( $wa_link ); ?>" target="_blank"><i class="fa fa-whatsapp"></i></a>
        <?php endif; ?>

        <script>
        jQuery(function($){
            var $window  = $(window);
            var $header  = $('.lp-onepage-header');
            var $toggle  = $('.lp-menu-toggle');
            var $nav     = $('.lp-onepage-nav');
            var headerGap = 14;

            function getHeaderOffset() {
                return $header.length ? $header.outerHeight() + headerGap : headerGap;
            }

            function closeMenu() {
                $nav.removeClass('is-open');
                $toggle.attr('aria-expanded', 'false');
            }

            $toggle.on('click', function(){
                var expanded = $(this).attr('aria-expanded') === 'true';
                $(this).attr('aria-expanded', expanded ? 'false' : 'true');
                $nav.toggleClass('is-open', !expanded);
            });

            $('.lp-onepage-wrapper').on('click', 'a[href^="#"]', function(e){
                var targetSelector = this.getAttribute('href');
                if (!targetSelector) {
                    return;
                }

                var $target = $(targetSelector);
                if ( $target.length ) {
                    e.preventDefault();
                    var offset = $target.offset().top - getHeaderOffset();
                    if ( offset < 0 ) {
                        offset = 0;
                    }
                    $('html, body').animate({ scrollTop: offset }, 500);
                }

                if ( $nav.hasClass('is-open') ) {
                    closeMenu();
                }
            });

            $window.on('resize', function(){
                if ( window.innerWidth >= 992 ) {
                    closeMenu();
                }
            });
        });
        </script>
        </div><!-- /.lp-onepage-wrapper -->
        <?php
    }
}
?>
