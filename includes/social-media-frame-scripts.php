<?php

// Load JS on the frontend
if ( !function_exists( 'social_media_frame_scripts' ) ) {
    function social_media_frame_scripts() {

     
      wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js', [], null, true);
     // wp_register_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', [], null, true);
      wp_register_script('imf-croppie', IMFPLUGIN_URL.'public/js/croppie.min.js', [], time());

      $script_data_array = array(
        'security' => wp_create_nonce( 'file_upload' ),
      );
      wp_register_script('imf-app', IMFPLUGIN_URL.'public/js/app.js', [], time());
      wp_localize_script( 'imf-app', 'data_ajax', $script_data_array );

    }
  add_action( 'init', 'social_media_frame_scripts');
}
?>