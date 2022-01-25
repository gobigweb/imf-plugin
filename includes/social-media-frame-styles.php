<?php

// Load CSS on the frontend
if ( !function_exists( 'social_media_frame_styles' ) ) {
  function social_media_frame_styles() {
    wp_register_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', [], time());
    wp_register_style('imf-style', IMFPLUGIN_URL.'public/css/style.css', [], time());
    wp_register_style('imf-croppie', IMFPLUGIN_URL.'public/css//cropper.css', [], time());
  }
  add_action( 'init', 'social_media_frame_styles');
}
?>