<?php
/**
 * Template Name: Map Entry
 *
 * Template for displaying a map.
 *
 * @package understrap
 */
acf_form_head();
get_header();

while ( have_posts() ) : the_post();

   acf_form(array(
        'post_id'       => 'new_post',
        'new_post'      => array(
            'post_type'     => 'post',
            'post_status'   => 'publish'
        ),
        'submit_value'  => 'Submit'
    )); 
    
endwhile;

get_footer();?>