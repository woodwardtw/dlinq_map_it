<?php
/**
 * Template Name: Map Display
 *
 * Template for displaying a map.
 *
 * @package understrap
 */

get_header();

while ( have_posts() ) : the_post();
    echo "<div id='map'></div>";    
    
endwhile;

get_footer();?>

<div class="embed-tools" id="embed-tools">
        <!--<button id="preview-embed">Preview</button>-->
        <button class="btn btn-primary" id="copy-embed-button">Copy Embed Code</button>
        <input id="lms-embed-code" value="<iframe frameborder='0' scrolling='no' width='100%' height='auto' src='<?php echo get_permalink();?>?show=article'></iframe>
      <script async src='https://rampages.us/extras/js/set-iframe-height-parent-min.js'></script>">
        </div>