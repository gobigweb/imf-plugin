<?php 



if ( !function_exists( 'addMenu' ) ) {
    function addMenu() {
        add_menu_page('Social Media Frame','Social Media Frame',4,'social-media-frame','shortcode_display','dashicons-format-image');
    }
    
    add_action( 'admin_menu', 'addMenu' );
}

if ( !function_exists( 'shortcode_display' ) ) {
    function shortcode_display(){
        echo <<<'EOD'
        <h2>Social Media Frame Shortcode</h2>
        <p>Just use the shortcode as:</p>
        <p><code>[social-media-frame]</code></p>
        EOD;
    }
}

// Add a link to display shortcode page in plugin page
if ( !function_exists( 'add_settings_link' ) ) {
    function add_settings_link( $links ) {
        $settings_link = '<a href="admin.php?page=social-media-frame">' . __( 'Get Shortcode', 'social-media-frame' ) . '</a>';
        array_push( $links, $settings_link );
        return $links;
    }
    
    $filter_name = "plugin_action_links_" . IMFPLUGIN_BASE_URL;
    add_filter( $filter_name, 'add_settings_link' );
}



?>