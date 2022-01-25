<?php 


// Enqueue Plugin CSS
include(IMFPLUGIN_PLUGIN_PATH . 'includes/social-media-frame-styles.php');

// Enqueue Plugin JavaScript
include( IMFPLUGIN_PLUGIN_PATH . 'includes/social-media-frame-scripts.php');

function social_media_frame() {
    // do something
    wp_enqueue_style("imf-style");
    wp_enqueue_style("imf-croppie");
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'imf-croppie' );
    wp_enqueue_script( 'imf-app' );

    show_form();

}


function show_form(){
    $html = '
    <div id="imf-wrapper">
        <div id="imf-content">
            <div id="preview">
                <div id="crop-area">
                <img src="'.IMFPLUGIN_URL.'public/images/avatar.jpg" id="profile-pic" />
                </div>
                <img src="'.IMFPLUGIN_URL.'public/images/frames/frame-0.png" id="fg" data-design="0" />
            </div>
            <p>
                <button id="download" disabled>Download Picture</button>
            </p>
            <h2>Upload</h2>
            <input type="file" name="file" onchange="onFileChange(this)">

            <h2>Frame Design</h2>
            <div id="designs">
                <img class="design active" src="'.IMFPLUGIN_URL.'public/images/frames/frame-0.png" data-design="0" />
                <img class="design " src="'.IMFPLUGIN_URL.'public/images/frames/frame-1.png" data-design="1" />
                <img class="design" src="'.IMFPLUGIN_URL.'public/images/frames/frame-2.png" data-design="2" />
            </div>
        </div>
    </div>
    ';

    echo $html;
}

?>