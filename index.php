<?php 
/*
Plugin Name: DLINQ Map It
Plugin URI:  https://github.com/
Description: For stuff that's magical
Version:     1.0
Author:      DLINQ
Author URI:  https://dlinq.middcreate.net
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: my-toolset

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


add_action('wp_enqueue_scripts', 'dlinq_mapit_load_scripts');

function dlinq_mapit_load_scripts() {                           
    $deps = array('jquery');
    $version= '1.0'; 
    $in_footer = true; 
      if ( is_page( 'map-display' ) ) {
         wp_enqueue_script('leaflet', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.8.0-beta.1/leaflet.js', '', '1', $in_footer);
         wp_enqueue_script('leaflet-cluster', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js', '', '1', $in_footer);
          wp_enqueue_script('dlinq-map-js', plugin_dir_url( __FILE__) . 'js/dlinq-map.js', 'leaflet', $version, $in_footer);           
       }
    wp_enqueue_style( 'dlinq-map-css', plugin_dir_url( __FILE__) . 'css/dlinq-map.css');
    wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css');
    wp_enqueue_style('cluster-default-css', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.css');
    wp_enqueue_style('cluster-marker-css', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.min.css');
}







function wpse255804_add_page_template ($templates) {
    $templates['map-display.php'] = 'Map Display';
    return $templates;
    }
add_filter ('theme_page_templates', 'wpse255804_add_page_template');


function wpse255804_redirect_page_template ($template) {
    $post = get_post();
    $page_template = get_post_meta( $post->ID, '_wp_page_template', true );
    if ('map-display.php' == basename ($page_template))
        $template = WP_PLUGIN_DIR . '/dlinq_map_it/inc/map-display.php';
    return $template;
    }
add_filter ('page_template', 'wpse255804_redirect_page_template');


//put custom field in api

function address_get_post_meta_cb($object, $field_name, $request){
        return get_post_meta($object['id'], $field_name, true); 
}
function address_update_post_meta_cb($value, $object, $field_name){
  return update_post_meta($object['id'], $field_name, $value); 
}
add_action('rest_api_init', function(){
  register_rest_field('post', 'address', 
    array(
    'get_callback' => 'address_get_post_meta_cb', 
    'update_callback' => 'address_update_post_meta_cb', 
    'schema' => null
    )
  ); 
});

function lat_get_post_meta_cb($object, $field_name, $request){
        return get_post_meta($object['id'], $field_name, true); 
}
function lat_update_post_meta_cb($value, $object, $field_name){
  return update_post_meta($object['id'], $field_name, $value); 
}
add_action('rest_api_init', function(){
  register_rest_field('post', 'lat', 
    array(
    'get_callback' => 'lat_get_post_meta_cb', 
    'update_callback' => 'lat_update_post_meta_cb', 
    'schema' => null
    )
  ); 
});

function lng_get_post_meta_cb($object, $field_name, $request){
        return get_post_meta($object['id'], $field_name, true); 
}
function lng_update_post_meta_cb($value, $object, $field_name){
  return update_post_meta($object['id'], $field_name, $value); 
}
add_action('rest_api_init', function(){
  register_rest_field('post', 'lng', 
    array(
    'get_callback' => 'lng_get_post_meta_cb', 
    'update_callback' => 'lng_update_post_meta_cb', 
    'schema' => null
    )
  ); 
});



function fimg_get_post_meta_cb($object, $field_name, $request){
        return get_post_meta($object['id'], $field_name, true); 
}
function fimg_update_post_meta_cb($value, $object, $field_name){
  return update_post_meta($object['id'], $field_name, $value); 
}
add_action('rest_api_init', function(){
  register_rest_field('post', 'f_img', 
    array(
    'get_callback' => 'fimg_get_post_meta_cb', 
    'update_callback' => 'fimg_update_post_meta_cb', 
    'schema' => null
    )
  ); 
});



add_action( 'gform_after_submission', 'dlinq_mapit_add_latlong', 10, 2 );


function dlinq_mapit_add_latlong($entry, $form) {
   //write_log(__LINE__);
   $post = get_post($entry['post_id']);
  if($post->post_type === 'post') {
      dlinq_mapit_get_latlng($entry, $form);
  }
}


add_action('save_post', 'dlinq_mapit_doublecheck_img', 10, 3);

function dlinq_mapit_doublecheck_img(){
   global $post;
   $post_id = $post->ID;
   if(!get_post_meta( $post_id,'f_img', true)){
      $img_url = get_the_post_thumbnail_url( $post_id, 'medium' );
      update_post_meta( $post_id, 'f_img', $img_url, ''); 
   }

}

add_action('save_post', 'dlinq_mapit_doublecheck_location', 10, 3);

function dlinq_mapit_doublecheck_location(){
   global $post;
   $post_id = $post->ID;
   if(!get_post_meta( $post_id,'f_img', true)){
      $img_url = get_the_post_thumbnail_url( $post_id, 'medium' );
      update_post_meta( $post_id, 'f_img', $img_url, ''); 
   }

}

function dlinq_mapit_get_latlng($entry, $form){
   $post_id = $entry['post_id'];
   $args = array();
   // if(rgar($entry, '2.1')){
   //    $street = 'street=' . rgar($entry, '2.1');      
   //    array_push($args, $street);
   // }
   if(rgar($entry, '2.3')){
      $city = 'city=' . rgar($entry, '2.3');      
      array_push($args, $city);
   }
   if(rgar($entry, '2.4')){
      $state = 'state=' . rgar($entry, '2.4');      
      array_push($args, $state);
   }
   // if(rgar($entry, '2.5')){
   //    $postalcode = 'postalcode=' . rgar($entry, '2.5');      
   //    array_push($args, $postalcode);
   // }
   
   if(rgar($entry, '2.6')){
      if (rgar($entry, '2.6') == "United States"){
         $clean_country = 'United States of America';
      } else {
         $clean_country = rgar($entry, '2.6');
      }
      $country = 'country=' . $clean_country;      
      array_push($args, $country);
   }   
   $addy = implode('&', $args);
   $clean = str_replace(' ', '%20', $addy);
   //var_dump($addy);
   $resp = dlinq_mapit_fetch($clean);

   //var_dump($resp);
   $json = json_decode($resp);

   $lat = $json[0]->lat;
   $lng = $json[0]->lon;
   update_post_meta( $post_id, 'lat', $lat, '' );
   update_post_meta( $post_id, 'lng', $lng, '' );
   $img_url = get_the_post_thumbnail_url( $post_id, 'medium' );
   update_post_meta( $post_id, 'f_img', $img_url, '');
}

function dlinq_mapit_fetch($clean){
   $url = "https://nominatim.openstreetmap.org/search?{$clean}&limit=1&format=json";
   //var_dump($url);
   $curl = curl_init($url);
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

   $headers = array(
      "Accept: application/json",
   );
   curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
   //for debug only!
   // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
   // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

   curl_setopt($curl,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

   $resp = curl_exec($curl);
   curl_close($curl);
   return $resp;
}


//LOGGER -- like frogger but more useful

if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}

  //print("<pre>".print_r($a,true)."</pre>");


//[foobar]
function foobar_func( $atts ){
   var_dump( get_post_meta( 76, 'street'));
}
add_shortcode( 'foobar', 'foobar_func' );