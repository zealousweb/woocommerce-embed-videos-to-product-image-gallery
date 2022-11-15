<?php
/**
 *
 * Handles the admin functionality.
 *
 * @package WordPress
 * @subpackage Embed Videos For Product Image Gallery Using WooCommerce
 * @since 1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Check if WooCommerce is active
 */
require_once ( WCEVZW_FILE );
global $post;

register_activation_hook ( WCEVZW_FILE, 'wcevzw_woo_activation_check');
function wcevzw_woo_activation_check()
{
	if( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		deactivate_plugins( WCEVZW_PLUGIN_BASENAME );
		wp_die( _e( '<b>Warning</b> : Install/Activate Woocommerce to activate "Embed Videos For Product Image Gallery Using WooCommerce" plugin.' , 'embed-videos-product-image-gallery-woocommerce' ) );
	}
}

/**
 * Add settings link to plugins page
 */
add_filter( 'plugin_action_links_' . WCEVZW_PLUGIN_BASENAME, 'wcevzw_add_action_links_embed_video' );
function wcevzw_add_action_links_embed_video ( $links ) {
	 $settingslinks = array(
	 '<a href="' . admin_url( 'admin.php?page=embed-videos-settings' ) . '"> '. __( 'Settings', 'embed-videos-product-image-gallery-woocommerce') .'</a>',
	 );
	return array_merge( $settingslinks, $links );
}

/**
 * Set up menu under woocommerce
 */
add_action( 'admin_menu', 'wcevzw_embed_videos_setup_menu' );
function wcevzw_embed_videos_setup_menu() {
	add_submenu_page( 'woocommerce', 'Embed Videos To Product Image Gallery', 'Embed Videos Settings', 'manage_options', 'embed-videos-settings', 'embed_videos_init');
}

/**
 * Register options of this plugin
 */
add_action( 'admin_init', 'wcevzw_register_embed_videos_settings' );
function wcevzw_register_embed_videos_settings() {
	register_setting( 'embed-videos-settings', 'embed_videos_autoplay' );
	register_setting( 'embed-videos-settings', 'embed_videos_rel' );
	register_setting( 'embed-videos-settings', 'embed_videos_showinfo' );
	register_setting( 'embed-videos-settings', 'embed_videos_disablekb' );
	register_setting( 'embed-videos-settings', 'embed_videos_fs' );
	register_setting( 'embed-videos-settings', 'embed_videos_controls' );
	register_setting( 'embed-videos-settings', 'embed_videos_hd' );
 }


/**
* Initialize the plugin and display all options at admin side
*/
function embed_videos_init() {
?>
  <h1><?php echo _e( 'Youtube Video Settings', 'embed-videos-product-image-gallery-woocommerce' ); ?></h1>
  <form method="post" action="options.php">
	<?php settings_fields( 'embed-videos-settings' ); ?>
	<?php do_settings_sections( 'embed-videos-settings' ); ?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php echo _e( 'Autoplay videos', 'embed-videos-product-image-gallery-woocommerce' ).':'; ?></th>
			<td><input type="checkbox" name="embed_videos_autoplay" value="1" <?php echo ( get_option( 'embed_videos_autoplay' ) == 1 ) ? 'checked': ''; ?> /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo _e( 'Show relative videos', 'embed-videos-product-image-gallery-woocommerce' ).':'; ?></th>
			<td><input type="checkbox" name="embed_videos_rel" value="1" <?php echo ( get_option( 'embed_videos_rel' ) == 1 ) ? 'checked': ''; ?> /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo _e( 'Show video information', 'embed-videos-product-image-gallery-woocommerce' ).':'; ?></th>
			<td>
				<input type="checkbox" name="embed_videos_showinfo" value="1" <?php echo ( get_option( 'embed_videos_showinfo' ) == 1 ) ? 'checked': ''; ?> />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo _e( 'Show fullscreen button', 'embed-videos-product-image-gallery-woocommerce' ).':'; ?></th>
			<td>
				<input type="checkbox" name="embed_videos_fs" value="1" <?php echo ( get_option( 'embed_videos_fs' ) == 1 ) ? 'checked': ''; ?> />
			</td>
		</tr>
		 <tr valign="top">
			<th scope="row"><?php echo _e( 'Show video player controls', 'embed-videos-product-image-gallery-woocommerce' ).':'; ?></th>
			<td>
				<input type="checkbox" name="embed_videos_controls" value="1" <?php echo ( get_option( 'embed_videos_controls' ) == 1 ) ? 'checked': ''; ?> />
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
	</div>
  </form>

<?php
}

/**
 * Add form field to get video link id for product image
 */
add_filter( 'attachment_fields_to_edit', 'wcevzw_woo_embed_video', 20, 2);
function wcevzw_woo_embed_video( $form_fields, $attachment ) {

	$post_id = (int) $_GET[ 'post' ];
	$nonce = wp_create_nonce( 'bdn-attach_' . $attachment->ID );
	$attach_image_action_url = admin_url( "media-upload.php?tab=library&post_id=$post_id" );

	$field_value = get_post_meta( $attachment->ID, 'videolink_id', true );
	$video_site = get_post_meta( $attachment->ID, 'video_site', true );
	$youtube = ( $video_site == 'youtube' ) ? 'checked' : '';
	$vimeo = ( $video_site == 'vimeo' ) ? 'checked' : '';
	$checked = '';
	if( empty( $youtube ) && empty( $vimeo) )
	{
		$checked = 'checked';
	}
	$form_fields['videolink_id'] = array(
		'value' => $field_value ? $field_value : '',
		'input' => "text",
		'label' => __( 'Video Link ID', 'embed-videos-product-image-gallery-woocommerce' )
	);
	$form_fields['video_site'] = array(
		'input' => 'html',
		'value' => $video_site,
		'html' => "<input type='radio' name='attachments[{$attachment->ID}][video_site]' value='youtube' $youtube $checked> Youtube
					<input type='radio' name='attachments[{$attachment->ID}][video_site]' value='vimeo' $vimeo> Vimeo",
		'helps' => __( '<b>For Eg.:</b> <br>"112233445" for URL - https://vimeo.com/112233445 <br>
					 <br>"n93gYncUD" for URL - https://www.youtube.com/watch?v=n93gYncUD' )
	);
	return $form_fields;
}

/**
* Save form field of video link to display video on product image
*/
add_action( 'edit_attachment', 'wcevzw_woo_save_embed_video' );
function wcevzw_woo_save_embed_video( $attachment_id ) {
	if ( isset( $_REQUEST['attachments'][$attachment_id]['videolink_id'] ) ) {
		$videolink_id = sanitize_text_field ( $_REQUEST['attachments'][$attachment_id]['videolink_id'] );
		update_post_meta( $attachment_id, 'videolink_id', $videolink_id );
	}
	if ( isset( $_REQUEST['attachments'][$attachment_id]['video_site'] ) ) {
		$video_site = sanitize_text_field ( $_REQUEST['attachments'][$attachment_id]['video_site'] );
		update_post_meta( $attachment_id, 'video_site', $video_site );
	}
}
