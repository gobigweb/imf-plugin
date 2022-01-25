<?php 
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

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('IMFPLUGIN_URL', plugin_dir_url(__FILE__));

define('IMFPLUGIN_PLUGIN_PATH', plugin_dir_path(__FILE__));

define('IMFPLUGIN_BASE_URL', plugin_basename(__FILE__));

// Create Plugin Admin Menus and Setting Pages
include( plugin_dir_path( __FILE__ ) . 'includes/social-media-frame-menus.php');

include( plugin_dir_path( __FILE__ ) . 'includes/social-media-frame-html.php');

add_shortcode('social-media-frame', 'social_media_frame');


?>