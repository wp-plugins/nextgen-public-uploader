<?php
/*
Plugin Name: NextGEN Public Uploader
Plugin URI: http://webdevstudios.com/support/wordpress-plugins/nextgen-public-uploader/
Description: NextGEN Public Uploader is an extension to NextGEN Gallery which allows frontend image uploads for your users.
Version: 1.0
Author: WebDevStudios
Author URI: http://webdevstudios.com

Copyright 2009 WebDevStudios  (email : contact@webdevstudios.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function npu_error_message(){
	echo '<div class="error fade" style="background-color:red;"><p><strong>NextGEN Public Uploader requires NextGEN gallery in order to work. Please deactivate this plugin or activate <a href="http://wordpress.org/extend/plugins/nextgen-gallery/">NextGEN Gallery</a>.</strong></p></div>';
	}

if(class_exists('nggLoader')) { 

	add_action('admin_menu', 'npu_plugin_menu');

	function npu_plugin_menu() {
  		add_menu_page('NextGEN Public Uploader', 'Gallery: Uploader', '8', 'nextgen-public-uploader', 'npu_plugin_options');
	}

	function npu_plugin_options() { ?>

		<div class="wrap">
        <div class="icon32" id="icon-options-general"><br/></div>
		<h2>NextGEN Public Uploader</h2>
        
        <p><strong>Author:</strong> <a href="http://webdevstudios.com">WebDevStudios</a></p>
        <p><strong>Current Version:</strong> 1.0</p>
        
        <p><strong>Shortcode Example: </strong><code>[ngg_uploader]</code> or <code>[ngg_uploader id = GALLERY_ID_HERE]</code></p>
        
		<form method="post" action="options.php">
		<?php wp_nonce_field('update-options'); ?>

		<table class="form-table">

		<tr valign="top">
		<th scope="row">Default Gallery ID:</th>
		<td>
        <input type="text" name="npu_default_gallery" value="<?php echo get_option('npu_default_gallery'); ?>" />
        <span class="description">Enter the default gallery ID when using [ngg_uploader].</span>
        </td>
		</tr>
        
        <tr valign="top">
		<th scope="row">Notification Email:</th>
		<td>
        <input type="text" name="npu_notification_email" value="<?php echo get_option('npu_notification_email'); ?>" />
        <span class="description">Enter an email address to be notified when a image has been submitted.</span>
        </td>
		</tr>
	
		</table>

		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="npu_default_gallery, npu_email_option, npu_notification_email" />

		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>

		</form>
        
		</div>

		<?php
		
	}

	require_once(dirname (__FILE__). '/inc/npu-upload.php');

} else {
	add_action( 'admin_notices', 'npu_error_message');
}
?>
