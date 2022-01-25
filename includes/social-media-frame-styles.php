<?php

// Load CSS on the frontend
if ( !function_exists( 'social_media_frame_styles' ) ) {
  function social_media_frame_styles() {
    wp_register_style('imf-style', IMFPLUGIN_URL.'public/css/style.css', [], time());
    wp_register_style('imf-croppie', IMFPLUGIN_URL.'public/css/croppie.css', [], time());
  }
  add_action( 'init', 'social_media_frame_styles');
}
?>