<?php

// Load JS on the frontend
if ( !function_exists( 'social_media_frame_scripts' ) ) {
    function social_media_frame_scripts() {

     
      wp_register_script('jquery', 'https://code.jquery.com/jquery-3.1.1.min.js', [], null, true);

      wp_register_script('imf-croppie', IMFPLUGIN_URL.'public/js/croppie.min.js', [], time());
      wp_register_script('imf-app', IMFPLUGIN_URL.'public/js/app.js', [], time());

    }
  add_action( 'init', 'social_media_frame_scripts');
}
?>