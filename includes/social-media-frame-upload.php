<?php
session_start();
include('../convert.php');
require('../../../../wp-load.php' );
$wordpress_upload_dir = wp_upload_dir();

if(isset($_FILES["image"])){

  function rand_string($length) {
     $str="";
     $chars = "abcdefghijklmanopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
     $size = strlen($chars);
     for($i = 0;$i < $length;$i++) {
      $str .= $chars[rand(0,$size-1)];
    }
    return $str;
  }

  /**
   * If image is not valid, output a default image
   * @var [type]
   */
  $sourceImg = @imagecreatefromstring(@file_get_contents($_FILES["image"]["tmp_name"]));
  if ($sourceImg === false){
    echo esc_url( plugins_url( 'images/default-profile-pic.png', __FILE__ ) );
    exit;
  }

  $image = makeDP($_FILES["image"]["tmp_name"], (
    isset($_POST["design"]) ? $_POST["design"] : 0
  ));

  $image_name = rand_string(10) . ".png";
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
    echo $_SESSION['loc'] = $wordpress_upload_dir['url'] . '/' . basename( $new_file_path );
  }
  
}