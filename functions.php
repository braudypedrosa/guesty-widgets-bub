<?php ob_start();

include GUESTY_DIR.'/includes/util/listing-functions.php';

// get bearer token
if( !function_exists('_guesty_get_bearer_token') ){
    
    function _guesty_get_bearer_token() {
        
        $bearer_token = get_option('_guesty_widgets_bearer_token');
        
        return !empty($bearer_token) ? $bearer_token : "";
    }
}

// generate token using client id and secret
function _guesty_widgets_generate_token($client_id, $client_secret){

    if(empty($client_id) || empty($client_secret)){
        $err = "<div id=\"message\" class=\"error\"><p>Client ID and Client Secret fields are required!</p></div>";
    }else{
             
        $data = array(
            "grant_type"  => "client_credentials",
            "scope" => "booking_engine:api",
            "client_secret" => $client_secret,
            "client_id" => $client_id,
        );
            
        $ch = curl_init();            
            
        curl_setopt($ch, CURLOPT_URL, 'https://booking.guesty.com/oauth2/token');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HEADER , 0 );
            
        $headers = array();
        $headers[] = 'accept: application/json';
        $headers[] = 'cache-control : no-cache,no-cache';
        $headers[] = 'content-type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        $curl_close = curl_errno($ch);
        curl_close ($ch);
        
        if ($curl_close) {
            
            $err = "<div id=\"message\" class=\"error\"><p>Error:" . curl_error($ch)."</p></div>";
                
        } else {
            
            $json_result = json_decode($result);
            
            if(empty($json_result->error->code)){
                
                $bearer_token = $json_result->access_token;
                update_option('_guesty_widgets_client_id',$client_id);
                update_option('_guesty_widgets_client_secret',$client_secret);
                update_option('_guesty_widgets_bearer_token',$bearer_token);
                update_option('_guesty_widgets_bearer_token_expiry_date',date("Y-m-d H:i:s", time() + $json_result->expires_in));
                
            }else{
                
                $err = "<div id=\"message\" class=\"error\"><p>Sorry but you are unable to get the bearer token this time. Error: " . $json_result->error->code."</p></div>";
            }               
        }
    }
}


// initialize plugin
function _guesty_initialize($client_id, $client_secret) {

    if(($today > $token_expiry) || $bearer_token == '') {
        _guesty_widgets_generate_token($client_id, $client_secret);

        // // schedule auto renewal CRON job
        if (! wp_next_scheduled( 'renew_token')) {
            wp_schedule_event( time(), 'daily', 'renew_token' );
        }

        _guesty_widgets_get_all_listings();
    } 
}

// renew generated token
function _guesty_widgets_renew_token() {
    $client_id = get_option('_guesty_widgets_client_id');
    $client_secret = get_option('_guesty_widgets_client_secret');

    _guesty_widgets_generate_token($client_id, $client_secret);
}
add_action( 'renew_token', '_guesty_widgets_renew_token' );

// refresh listings
function _guesty_widgets_refresh_listings() {
    _guesty_widgets_get_all_listings();
    update_option('_ran_refresh', true);
}
add_action( 'refresh_listings', '_guesty_widgets_refresh_listings' );

