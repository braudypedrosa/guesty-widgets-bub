<?php

if(!function_exists('slugify')) {
    function slugify($text) { 
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        return strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $text));
    }
}



// insert all amenities 
function _guesty_insert_amenities($amenities = []){

    $amenitiesIDs = array();

    if(!empty($amenities)) {

        foreach ($amenities as $amenity) {

            if(!term_exists($amenity, 'amenities')) {
                // add term
                $term = wp_insert_term($amenity, 'amenities');
                array_push($amenitiesIDs, $term['term_id']);
            }

        }
    }

    return $amenitiesIDs;
}

// convert response to image array
function _guesty_convert_image($images = [], $post_id) {

    $convertedImages = array();

    if(!empty($images)) {

        foreach ($images as $image) {
            // $toUpload = media_sideload_image( $image['original'], $post_id, ($image['caption'] == '') ? '' : $image['caption'] ,'html   ' );
            array_push($convertedImages, $image['original']);
        }

    }

    return $convertedImages;
}

function _guesty_get_excerpt(){
        $excerpt = get_the_content();
        $excerpt = preg_replace(" ([.*?])",'',$excerpt);
        $excerpt = strip_shortcodes($excerpt);
        $excerpt = strip_tags($excerpt);
        $excerpt = substr($excerpt, 0, 120);
        $excerpt = substr($excerpt, 0, strripos($excerpt, " "));
        $excerpt = trim(preg_replace( '/\s+/', ' ', $excerpt));
        $excerpt = $excerpt.'...';
    return $excerpt;
}