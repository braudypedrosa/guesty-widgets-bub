<?php 
// Template for single property page
    get_header(); 

    global $post;
    $id = $post->ID;

    $id = get_the_ID();
    $listing_id = get_post_meta($id, 'listing_id');
    $title = get_the_title();
    $featured_image = get_the_post_thumbnail_url($id, 'large');

    $notes = get_field('notes', $id);

    $nickname = get_field('short_name', $id);
    $address = get_field('location', $id);
    $content = get_the_content($id);
    $excerpt = _guesty_get_excerpt();
    $roomType = get_field('property_type', $id);
    $accommodates = get_field('sleeps', $id);
    $bedrooms = get_field('bedrooms', $id);
    $bathrooms = get_field('bathrooms', $id);
    $price = get_field('price', $id);

    $lat = get_field('latitude', $id);
    $lng = get_field('longitude', $id);

    $images = get_field('listing_images');
    $image_array = explode(',',$images);
?>


<div class="guesty-main-wrapper">
    <div class="guesty-single-listing-container" id="guesty-listing-<?php echo $listing_id[0]; ?>">
        <div class="guesty-single-listing-hero">
            <div class="guesty-listing-images main-carousel">
                <?php 
                    foreach ($image_array as $images) {
                ?>
                    <div class="guesty-listing-image">
                        <img src="<?php echo $images ?>" alt="">
                    </div>
                        
                <?php } ?>
            </div>
            
            <div class="guesty-listing-featured-images">
                <?php for ($i=1; $i < 4; $i++) { ?>
                    
                    <img src="<?php echo $image_array[$i]; ?>" alt=""/>
                
                <?php } ?>
            </div>
        </div>
        <div class="guesty-single-listing-content">
            <div class="mobile-book-button gl_booking_button mobile-only">
                <a href="#guesty-booking-<?php echo $listing_id[0]; ?>">Book Now</a>
            </div>
        <div class="guesty-single-listing-left-area">
                <h1 class="guesty-single-listing-title"><?php echo $nickname; ?></h1>

                <ul class="guesty-listing-room-details">
                    <li class="gl-room-type"><i class="fa-regular fa-building"></i><p>Type<span><?php echo $roomType; ?></span></p></li>
                    <li class="gl-accommodates"><i class="fa-regular fa-user"></i><p>Sleeps<span><?php echo $accommodates; ?></span></p></li>
                    <li class="gl-bedrooms"><i class="fa-solid fa-bed"></i><p>Bedrooms<span><?php echo $bedrooms; ?></span></p></li>
                    <li class="gl-bathrooms"><i class="fa-solid fa-shower"></i><p>Bathrooms<span><?php echo $bathrooms; ?></span></p></li>
                </ul>

                <h5>Description</h5>
                <p><?php echo wpautop($content, true); ?></p>

                <?php if($notes) { 
                    echo $notes;
                 } ?>

                 <h5>Amenities</h5>
                 <ul class="guesty-listing-amenities">
                 <?php 
                    $amenities = get_the_terms($id, 'amenities');
                    foreach ($amenities as $amenity) { ?>
                        <li class="guesty-listing-amenity"><?php echo $amenity->name; ?></li>
                <?php  } ?>
                    </ul>

                 <h5>Map</h5>
                 <iframe src="https://maps.google.com/maps?q=<?php echo $lat; ?>, <?php echo $lng; ?>&z=15&output=embed" width="100%" height="300" frameborder="0" style="border:0"></iframe>
            </div>

            <div class="guesty-single-listing-right-area" id="guesty-booking-<?php echo $listing_id[0]; ?>">
                    <?php echo do_shortcode('[display_calendar listingid="'.$listing_id[0].'" buttonText="Book Now"]'); ?>
            </div>
        </div>
    </div>
</div>



<?php 
    get_footer(); 
?>