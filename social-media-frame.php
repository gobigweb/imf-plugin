<?php 
session_start();
/**
* Plugin Name: Social Media Frame Creator
* Plugin URI: https://gobigweb.com/
* Description: Social Media Frame Creator
* Version: 1.0.0
* Author: Dev Team
* Author URI: https://gobigweb.com/
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*

* Social Media Frame Creator is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 2 of the License, or
* any later version.
 
* Social Media Frame Creator is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*/


defined( 'ABSPATH' ) or die( 'You cannot be here.' );

define('IMFPLUGIN_PLUGIN_PATH', plugin_dir_path(__FILE__));

define('IMFPLUGIN_URL', plugin_dir_url(__FILE__));

define('IMFPLUGIN_BASE_URL', plugin_basename(__FILE__));

if ( !class_exists( 'SocialMediaFrame' ) ) {
    class SocialMediaFrame {
        public function __construct(){}

        public static function init() {

            $class = new self();
            $class->action_hooks();
            $class->include_files();
        }

        private function include_files(){
            // Enqueue Plugin CSS
            include(IMFPLUGIN_PLUGIN_PATH.'includes/social-media-frame-styles.php');

            // Enqueue Plugin JavaScript
            include( IMFPLUGIN_PLUGIN_PATH.'includes/social-media-frame-scripts.php');
        }

        private function action_hooks() {
            add_shortcode('social-media-frame', array( $this,'social_media_frame'));            
            add_shortcode('social-media-frame-share', array( $this,'social_media_frame_share'));
            add_action( 'admin_menu',array( $this,'addMenu') );
            add_action( 'admin_init',array( $this,'is_this_plugin_active') );
            add_filter( 'pre_get_document_title', array( $this,'change_page_title'));
            $filter_name = "plugin_action_links_" . IMFPLUGIN_BASE_URL;
            add_filter( $filter_name, array( $this,'add_settings_link') );    
        }

        private function show_form(){
            if (isset($_GET['upload-image'])) {
                check_ajax_referer('file_upload', 'security');

                
                include('convert.php');
                include_once( ABSPATH . 'wp-load.php' );

                $wordpress_upload_dir = wp_upload_dir();

                if(isset($_FILES["image"])){
                    $sourceImg = @imagecreatefromstring(@file_get_contents($_FILES["image"]["tmp_name"]));
                    if ($sourceImg === false){
                        exit;
                    }
                
                    $image = makeDP($_FILES["image"]["tmp_name"], (
                        isset($_POST["design"]) ? $_POST["design"] : 0
                    ));
                
                    $image_name = $_POST['file_name']. ".png";

                    $new_file_path = $wordpress_upload_dir['path'] . '/' . $image_name;
                    $new_file_mime = mime_content_type( $_FILES["image"]['tmp_name'] );
                    
                    if(file_put_contents($new_file_path, $image)){
                        $upload_id = wp_insert_attachment( array(
                            'guid'           => $new_file_path, 
                            'post_mime_type' => $new_file_mime,
                            'post_title'     => preg_replace( '/\.[^.]+$/', '', $image_name ),
                            'post_content'   => preg_replace( '/\.[^.]+$/', '', $image_name ),
                            'post_status'    => 'inherit'
                        ), $new_file_path );
                    
                        // wp_generate_attachment_metadata() won't work if you do not include this file
                        require_once( ABSPATH . 'wp-admin/includes/image.php' );
                        
                        // Generate and save the attachment metas into the database
                        wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );
                        
                        // Show the uploaded file in browser
                        $_SESSION['loc'] = $wordpress_upload_dir['url'] . '/' . basename( $new_file_path );
                    
                        
                    }
                }

           
            }else{
                $html = '
                <div id="imf-wrapper">
                    <div id="imf-content">
                        <form action="" method="post" enctype="multipart/form-data">
                            <label for="upload_image">
                                <div id="preview">
                                    <div id="crop-area">
                                        <img src="'.IMFPLUGIN_URL.'public/images/hd1080.png" id="uploaded_image" width ="400"/>
                                    </div>
                                    <img src="'.IMFPLUGIN_URL.'public/images/frames/frame-0.png" id="fg" data-design="0" width ="400"/>
                                </div>
                                <input type="file" name="file" class="image" id="upload_image" style="display:none" onchange="onFileChange(this)" accept="image/png, image/jpeg">

                            </label>
                        </form>
                        <br><br>
                        <h3>Select Frame Design</h3>
                        <div id="designs">
                            <img class="design active" src="'.IMFPLUGIN_URL.'public/images/frames/frame-0.png" data-design="0"/>
                            <img class="design " src="'.IMFPLUGIN_URL.'public/images/frames/frame-1.png" data-design="1" />
                            <img class="design" src="'.IMFPLUGIN_URL.'public/images/frames/frame-2.png" data-design="2"/>
                            <img class="design" src="'.IMFPLUGIN_URL.'public/images/frames/frame-3.png" data-design="3"/>
                            <img class="design" src="'.IMFPLUGIN_URL.'public/images/frames/frame-4.png" data-design="4"/>
                        </div>
                        
                        <p>
                            <br><button id="download" disabled>Share Image</button>
                        </p>

                        <br>
                        <h3>Select a frame, upload your image and share your post on social media</h3>
                    </div>
                </div>';
            
                echo $html;
            }
            
        }
        
        public function social_media_frame(){
            // do something
            wp_enqueue_style("bootstrap");
            wp_enqueue_style("imf-style");
            wp_enqueue_style("imf-croppie");
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'bootstrap' );
            wp_enqueue_script( 'imf-croppie' );
            wp_enqueue_script( 'imf-app' );
            $this->show_form();            
        }

        private function get_attachment_url_by_slug( $slug ) {
            include_once( ABSPATH . 'wp-load.php' );
            $args = array(
              'post_type' => 'attachment',
              'name' => sanitize_title($slug),
              'posts_per_page' => 1,
              'post_status' => 'inherit',
            );
            $_header = get_posts( $args );
            $header = $_header ? array_pop($_header) : null;
            return $header ? wp_get_attachment_url($header->ID) : '';
        }

        public function change_page_title(){
            global $post;
            if ($post->post_name === "social-media-frame-share") {
                return "#MYelomaACTION"; 
            }
                   
        }

        private function add_meta_tags($slug) {

            $img_url = $this->get_attachment_url_by_slug($slug);
            echo '
            <!-- Facebook Meta Tags -->
            <meta property="og:url" content="https://myeloma.org/">
            <meta property="og:type" content="website">
            <meta property="og:title" content="MYelomaACTION">
            <meta property="og:description" content="#MYelomaACTION">
            <meta property="og:image" content="'.$img_url.'">

            <!-- Twitter Meta Tags -->
            <meta name="twitter:card" content="summary_large_image">
            <meta property="twitter:domain" content="myeloma.org">
            <meta property="twitter:url" content="https://myeloma.org/">
            <meta name="twitter:title" content="MYelomaACTION">
            <meta name="twitter:description" content="#MYelomaACTION">
            <meta name="twitter:image" content="'.$img_url.'">

            ';
        }
        
        public function social_media_frame_share(){
            
            $url = $this->get_attachment_url_by_slug($_GET['share-image']);             
            add_action('wp_head', $this->add_meta_tags($_GET['share-image']));
            
            // do something
            wp_enqueue_style("bootstrap");
            wp_enqueue_style("imf-style");
            wp_enqueue_style("imf-croppie");
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'bootstrap' );
            wp_enqueue_script( 'imf-croppie' );
            wp_enqueue_script( 'imf-app' );
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );    
            
            

            echo "
            <div id='imf-wrapper'>
                <div id='imf-content'>
                <img src='". $url ."' />
                </div>
            </div>";      
            
            if ( is_plugin_active( 'sharethis-share-buttons/sharethis-share-buttons.php') ) {
                echo sharethis_inline_buttons(); 
            }

            echo "<br><p style='text-align:center;'><a id='download'style='padding: 10px 20px;font-size:12px; border-radius: 4px;'   href='". $url ."' download='MYelomaACTION'>DOWNLOAD IMAGE</a></p>";    
    
            echo '<br><br>';            
        }

        public function addMenu() {
            add_menu_page('Social Media Frame','Social Media Frame','manage_options','social-media-frame',array( $this,'shortcode_display'),'dashicons-format-image');
        }

        public function shortcode_display(){
            echo <<<'EOD'
            <h2>Social Media Frame Shortcode</h2>
            <p>Just use the shortcode as upload page:</p>
            <p><code>[social-media-frame]</code></p>
            <br>
            <p>Create a Page Next, add the title of the page 'Social Media Frame Share'</p>
            <p>use the shortcode as share page:</p>
            <p><code>[social-media-frame-share]</code></p>
            EOD;
        }

        public function add_settings_link($links) {
            $settings_link = '<a href="admin.php?page=social-media-frame">' . __( 'Get Shortcode', 'social-media-frame' ) . '</a>';
            array_push( $links, $settings_link );
            return $links;
        }

        public function is_this_plugin_active() {
            if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'sharethis-share-buttons/sharethis-share-buttons.php' ) ) {
                add_action( 'admin_notices', array( $this, 'plugin_notice') );
        
                deactivate_plugins( plugin_basename( __FILE__ ) ); 
        
                if ( isset( $_GET['activate'] ) ) {
                    unset( $_GET['activate'] );
                }
            }
            
        }

        public function plugin_notice(){
            $plugin_name = 'ShareThis Share Buttons';
            $plugin_slug = 'sharethis-share-buttons';
            $plugin_path = $plugin_slug.'/'.$plugin_slug.'.php';

            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            $installed_plugins = get_plugins();

            if(array_key_exists( $plugin_slug, $installed_plugins ) || in_array( $plugin_slug, $installed_plugins, true )){
                $link = wp_nonce_url(admin_url('plugins.php?action=activate&plugin='.$plugin_path), 'activate-plugin_'.$plugin_path);            
                $install_link = '<a href="' . $link  . '">'.$plugin_name.'</a>';
                $plugin_to = 'Active';
            }else{
                $install_link = '<a href="' . esc_url( network_admin_url('plugin-install.php?tab=plugin-information&plugin='.$plugin_slug.'&TB_iframe=true&width=600&height=550' ) ) . '" class="thickbox" title="More info about '.$plugin_name.'">'.$plugin_name.'</a>';
                $plugin_to = 'Installed';
            }
            echo '<div class="notice notice-error is-dismissible"><p><strong>Social Media Frame Shortcode</strong> plugin requires the <strong>'.$install_link.'</strong> plugin to be '.$plugin_to.'</p></div>';
        }
      
    }

    SocialMediaFrame::init();
}

?>