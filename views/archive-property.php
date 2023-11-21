<?php
// Template for property archive

// Load theme header    
    get_header();

    $args = array(
        'post_type' => 'guesty_listings',
        'post_per_page' => 9,
        'order' => 'ASC'
    );

    $query = new WP_Query( $args );

    ?>

<div class="guesty-main-wrapper">
    <div class="guesty-listings-container">
        <div style="display: none;">
            <?php echo do_shortcode('[facetwp template="guesty_listings"]'); ?>
        </div>
    <?php if ( $query->have_posts() ) { ?>

    <div class="guesty-listing-items">
        <?php while ( $query->have_posts() ) { 
            $query->the_post();

            $id = get_the_ID();
            $title = get_the_title();
            $featured_image = get_the_post_thumbnail_url($id, 'large');

            $nickname = get_field('short_name', $id);
            $address = get_field('location', $id);
            $excerpt = _guesty_get_excerpt();
            $roomType = get_field('property_type', $id);
            $accommodates = get_field('sleeps', $id);
            $bedrooms = get_field('bedrooms', $id);
            $bathrooms = get_field('bathrooms', $id);
            $price = get_field('price', $id);

            $url = get_the_permalink();
            
            ?>
        <div class="guesty-listing-item">
            <div class="guesty-listing-image" style="background-size: cover; background-image: url(<?php echo $featured_image; ?>);">
                <img src="<?php echo $featured_image; ?>" alt="<?php echo $title; ?>">
            </div>
            <div class="guesty-listing-details">
                <div class="guesty-listing-title">
                    <h3 class="guesty-listing-nickname"><?php echo $nickname; ?></h3>
                    <span class="guesty-listing-address"><i class="fa-solid fa-map-location-dot"></i><?php echo $address; ?></span>
                </div>
                <div class="guesty-listing-meta">
                    <div class="guesty-listing-content">
                        <p class="guesty-listing-excerpt">
                            <?php echo $excerpt; ?>
                        </p>
                        <ul class="guesty-listing-room-details">
                            <li class="gl-room-type"><i class="fa-regular fa-building"></i><?php echo $roomType; ?></li>
                            <li class="gl-accommodates"><i class="fa-regular fa-user"></i><?php echo $accommodates; ?></li>
                            <li class="gl-bedrooms"><i class="fa-solid fa-bed"></i><?php echo $bedrooms; ?></li>
                            <li class="gl-bathrooms"><i class="fa-solid fa-shower"></i><?php echo $bathrooms; ?></li>
                        </ul>
                    </div>
                    <div class="guesty-listing-price">
                        from<br>
                        <span class="gl-price">$<?php echo $price; ?></span> Per night<br>
                        <span class="gl-price-small-text">Additional charges may apply</span>
                        <div class="book">
                            <a href="<?php echo $url; ?>" class="gl-booking-button">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>

    <?php }  ?>
<div class="map">
    <?php echo do_shortcode('[facetwp facet="map_facet_for_guesty"]'); ?>
</div>
    </div>
</div>


<?php get_footer();  ?>