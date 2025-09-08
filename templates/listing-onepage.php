<?php
/* One-page listing detail template */
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        global $listingpro_options;
        $menu_items = array( 'home' => __( 'Anasayfa', 'listingpro' ) );
        if ( listingpro_get_metabox( 'lp_listing_description' ) ) {
            $menu_items['about'] = __( 'Hakkımızda', 'listingpro' );
        }
        $services = listingpro_get_metabox( 'lp_services' );
        if ( ! empty( $services ) ) {
            $menu_items['services'] = __( 'Hizmetler', 'listingpro' );
        }
        $gallery = listingpro_get_metabox( 'lp_gallery' );
        if ( ! empty( $gallery ) ) {
            $menu_items['gallery'] = __( 'Galeri', 'listingpro' );
        }
        $video = listingpro_get_metabox( 'lp_video_embed' );
        if ( ! empty( $video ) ) {
            $menu_items['video'] = __( 'Video', 'listingpro' );
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

        <?php if ( isset( $menu_items['about'] ) ) : ?>
        <section id="about" class="lp-section lp-section-about">
            <?php echo apply_filters( 'the_content', listingpro_get_metabox( 'lp_listing_description' ) ); ?>
        </section>
        <?php endif; ?>

        <?php if ( isset( $menu_items['services'] ) ) : ?>
        <section id="services" class="lp-section lp-section-services">
            <?php echo apply_filters( 'the_content', $services ); ?>
        </section>
        <?php endif; ?>

        <?php if ( isset( $menu_items['gallery'] ) ) : ?>
        <section id="gallery" class="lp-section lp-section-gallery">
            <?php echo apply_filters( 'the_content', $gallery ); ?>
        </section>
        <?php endif; ?>

        <?php if ( isset( $menu_items['video'] ) ) : ?>
        <section id="video" class="lp-section lp-section-video">
            <?php echo apply_filters( 'the_content', $video ); ?>
        </section>
        <?php endif; ?>

        <section id="contact" class="lp-section lp-section-contact">
            <ul class="lp-contact-list">
                <?php $address = listingpro_get_metabox( 'gAddress' ); if ( $address ) : ?>
                    <li class="lp-contact-address"><?php echo esc_html( $address ); ?></li>
                <?php endif; ?>
                <?php $phone = listingpro_get_metabox( 'phone' ); if ( $phone ) : ?>
                    <li class="lp-contact-phone"><?php echo esc_html( $phone ); ?></li>
                <?php endif; ?>
                <?php $website = listingpro_get_metabox( 'website' ); if ( $website ) : ?>
                    <li class="lp-contact-website"><a href="<?php echo esc_url( $website ); ?>" target="_blank"><?php echo esc_html( $website ); ?></a></li>
                <?php endif; ?>
            </ul>
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
        <?php
    }
}
?>
