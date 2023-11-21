<?php
require GUESTY_DIR.'/includes/util/listing-helper.php';


// disabled fields
function _guesty_disable_listing_fields($field) {
    
}

// get availability of a specific property
function _guesty_widgets_get_availability($listingID){

    $token = _guesty_get_bearer_token();

    // only check availability within current day to end of the year
    $today = date("Y-m-d");
    $yearEnd = date('Y-m-d', strtotime('+3 months', strtotime('12/31')));

    // initialize array for available dates
    $availableDates = array();

    if(!$token == '') {

        $data = array(
            'from' => $today,
            'to' => $yearEnd,
        );

        $ch = curl_init();    

        curl_setopt($ch, CURLOPT_URL,"https://booking.guesty.com/api/listings/".$listingID."/calendar?".http_build_query($data));

        $headers = array();
        $headers[] = 'Accept: application/json; charset=utf-8';
        $headers[] = 'cache-control : no-cache,no-cache';
        $headers[] = 'Content-Type : application/x-www-form-urlencoded';
        $headers[] = "Authorization: Bearer ".$token;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER , 0 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


        $result = curl_exec($ch);
        $curl_close = curl_errno($ch);
        curl_close ($ch);
        
        if ($curl_close) {
            $err = "<div id=\"message\" class=\"error\"><p>Error:" . curl_error($ch)."</p></div>";
        } else {
            
            $json_result = json_decode($result, true);
        
            foreach($json_result as $date) {
                if($date['status'] == 'available') {
                    array_push($availableDates, $date['date']);
                }
            }

        }
    } else {
        echo "<div class='guesty-error'>Failed to display availability! Please check your API token!</div>";
    }
 
    return $availableDates;
}

// fetch all listings
function _guesty_widgets_get_all_listings(){

    $token = _guesty_get_bearer_token();

    if(!$token == '') {

        $ch = curl_init();    

        curl_setopt($ch, CURLOPT_URL,"https://booking.guesty.com/api/listings/");

        $headers = array(); 
        $headers[] = 'Accept: application/json; charset=utf-8';
        $headers[] = 'cache-control : no-cache,no-cache';
        $headers[] = 'Content-Type : application/x-www-form-urlencoded';
        $headers[] = "Authorization: Bearer ".$token;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER , 0 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


        $result = curl_exec($ch);
        $curl_close = curl_errno($ch);
        curl_close ($ch);

        if ($curl_close) {
            $err = "<div id=\"message\" class=\"error\"><p>Error:" . curl_error($ch)."</p></div>";
        } else {
            $json_result = json_decode($result, true);

            if($json_result['results']) {
                foreach ($json_result['results'] as $listing) {

                    $listing_data = array(
                        "listing_id" => $listing['_id'],
                        "title" => $listing['title'],
                        "shortname" => $listing['nickname'],
                        "description" => $listing['publicDescription']['summary'],
                        "roomType" => $listing['roomType'],
                        "propertyType" => $listing['propertyType'],
                        "amenities" => $listing['amenities'],
                        "bathrooms" => $listing['bathrooms'],
                        "sleep" => $listing['accommodates'],
                        "bedrooms" => $listing['bedrooms'],
                        "beds" => $listing['beds'],
                        "address" => $listing['address']['full'],
                        "longitude" => $listing['address']['lng'],
                        "latitude" => $listing['address']['lat'],
                        "featuredImage" => $listing['picture']['thumbnail'],
                        "featuredImageCaption" => $listing['picture']['caption'],
                        "gallery" => $listing['pictures'],
                        "price" => $listing['prices']['basePrice'],
                        "tags" => $listing['tags'],
                    );

                    _guesty_insert_new_listing($listing_data);

                    // return $listing_data;

                }
            }
        } 
    
    } else {
        echo "<div class='guesty-error'>Failed to display availability! Please check your API token!</div>";
    }
}

// insert new listing to post type
function _guesty_insert_new_listing($listing_data){

    global $wpdb;

    $sql = "SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'listing_id' AND meta_value='".$listing_data['listing_id']."'";

    $result = $wpdb->get_results($sql,ARRAY_A);
    $post_id = $result[0]['post_id'];

    // add listing if search returns null
    if($post_id == null) {
        $post_id = wp_insert_post(array(
            'post_title'=> $listing_data['shortname'],
            'post_content' => $listing_data['description'],
            'post_type'=> 'guesty_listings',
            'post_status'=> 'publish'
        ));
    } else {  // update listing
        wp_update_post(array(
            'ID' => $post_id,
            'post_title'=> $listing_data['shortname'],
            'post_content' => $listing_data['description'],
        ));
    }

    update_post_meta($post_id, 'listing_id', $listing_data['listing_id']);

    // update_field('notes', $listing_data['notes'], $post_id);
    update_field('short_name', $listing_data['title'], $post_id);
    update_field('room_type', $listing_data['roomType'], $post_id);
    update_field('property_type', $listing_data['propertyType'], $post_id);
    update_field('bathrooms', $listing_data['bathrooms'], $post_id);
    update_field('bedrooms', $listing_data['bedrooms'], $post_id);
    update_field('beds', $listing_data['beds'], $post_id);
    update_field('sleeps', $listing_data['sleep'], $post_id);
    update_field('location', $listing_data['address'], $post_id);
    update_field('longitude', $listing_data['longitude'], $post_id);
    update_field('latitude', $listing_data['latitude'], $post_id);
    update_field('price', $listing_data['price'], $post_id);
    
    update_field('listing_images', implode(',', _guesty_convert_image($listing_data['gallery'], $post_id)), $post_id);

    wp_set_post_tags($post_id, $listing_data['tags']);

    wp_set_post_terms($post_id, implode(',',_guesty_insert_amenities($listing_data['amenities'])), 'amenities',  true);

    $image = media_sideload_image( $listing_data['featuredImage'], $post_id, ($listing_data['featuredImageCaption'] == '') ? $listing_data['adderss'] : $listing_data['featuredImageCaption'] ,'id' );
    set_post_thumbnail( $post_id, $image );

}
