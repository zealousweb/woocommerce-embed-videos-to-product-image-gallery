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
 * Embed Videos To Product Image Gallery WooCommerce styles and scripts.
 */
add_action( 'wp_head', 'wcevzw_woo_scripts_styles' );
function wcevzw_woo_scripts_styles() {
	$enable_lightbox = get_option( 'woocommerce_enable_lightbox' );
}

/**
 * Remove Gallery Thumbnail Images
 */
add_action( 'template_redirect', 'wcevzw_remove_gallery_thumbnail_images' );
function wcevzw_remove_gallery_thumbnail_images() {
	if ( is_product() ) {
		remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
	}
}

/**
 * Add new html layout of single product thumbnails
 */
add_action( 'woocommerce_product_thumbnails', 'wcevzw_woo_display_embed_video', 20 );
function wcevzw_woo_display_embed_video( $html ) {
	?>
	<script type="text/javascript">
		jQuery(window).load(function(){
			jQuery( '.woocommerce-product-gallery .flex-viewport' ).prepend( '<div class="emoji-search-icon"></div>' );
			jQuery( 'a.woocommerce-product-gallery__trigger' ).hide();
		});
	</script>
	<?php
	global $wpdb;
	$post_id_arr = $wpdb->get_results( "SELECT post_id,meta_value FROM $wpdb->postmeta WHERE meta_key = 'videolink_id' " );
	foreach ( $post_id_arr as $key => $value ) {
		$new_post_id_arr[$value->meta_value] = $value->post_id;
	}

	$product_thum_id = get_post_meta( get_the_ID(), '_thumbnail_id', true );
	if( in_array( $product_thum_id, $new_post_id_arr ) ) {
		$videolink_id_value = get_post_meta( $product_thum_id,'videolink_id', true );
		if( !empty( $videolink_id_value ) ){
			$video_link_name = get_post_meta( $product_thum_id, 'video_site', true );
			?>
			<script type="text/javascript">
				var video_links = '<?php echo esc_js( video_site_name( $video_link_name, $videolink_id_value ) ); ?>';
				jQuery(window).load(function(){
					var id = '.woocommerce-product-gallery__wrapper';
					jQuery('.woocommerce-product-gallery__wrapper').find('div a').first().attr('href','#');
					jQuery('.woocommerce-product-gallery__wrapper').find('div a').first().attr('data-type','video');
					jQuery('.woocommerce-product-gallery__wrapper').find('div a').first().attr('data-video','<div class="wrapper"><div class="video-wrapper"><iframe width="1000" height="640" src="'+video_links+'" frameborder="0" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true"></iframe></div></div>');
					jQuery(id+' div:first-child a img').remove();
					jQuery(id+' div:first-child img').remove();
					jQuery(id+' div:first-child a').html('<iframe height="" src="'+video_links+'" frameborder="0" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true"></iframe>');
				});
			</script>
			<?php
		}
	}
	global $woocommerce;
	global $product;

	$attachment_ids = $product->get_gallery_image_ids();
	$enable_lightbox = get_option( 'woocommerce_enable_lightbox' );
	if ( $attachment_ids ) {
		$newhtml = "";
		$loop       = 0;
		$columns    = apply_filters( 'woocommerce_product_thumbnails_columns', 3 );
		foreach ( $attachment_ids as $attachment_id ) {
			$newhtml .= '<div data-thumb="'. wp_get_attachment_url( $attachment_id ) .'" class="woocommerce-product-gallery__image" >';
			$classes = array( 'zoom' );
			if ( $loop == 0 || $loop % $columns == 0 )
				$classes[] = 'first';
			if ( ( $loop + 1 ) % $columns == 0 )
				$classes[] = 'last';
			$image_link = wp_get_attachment_url( $attachment_id );
			if ( ! $image_link )
				continue;
			$video_link = '';
			$full_size_image = wp_get_attachment_image_src( $attachment_id, 'full' );
			$thumbnail       = wp_get_attachment_image_src( $attachment_id, 'shop_thumbnail' );
			$attributes      = array(
				'title'                   => get_post_field( 'post_title', $attachment_id ),
				'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
				'data-src'                => $full_size_image[0],
				'data-large_image'        => $full_size_image[0],
				'data-large_image_width'  => $full_size_image[1],
				'data-large_image_height' => $full_size_image[2],
			);
			

			$image = wp_get_attachment_image( $attachment_id, 'woocommerce_single', false, $attributes );
			$image_class = esc_attr( implode( ' ', $classes ) );
			$image_title = esc_attr( get_the_title( $attachment_id ) );
			$videolink_id = get_post_meta( $attachment_id, 'videolink_id', true );
			$video_site = get_post_meta( $attachment_id, 'video_site', true );
			if( !empty( $videolink_id ) && !empty( $video_site ) ) {
				switch ( $video_site ) {
					case 'youtube':
					$autoplay = get_option( 'embed_videos_autoplay' );
					$autoplay =  ( empty( $autoplay ) ) ? 0 : 1;
					$rel = get_option( 'embed_videos_rel' );
					$rel = ( empty( $rel ) ) ? 0 : 1;
					//$showinfo = get_option( 'embed_videos_showinfo' );
					//$showinfo = ( empty( $showinfo ) ) ? 0 : 1;
					$disablekb = get_option( 'embed_videos_disablekb' );
					$disablekb = ( empty( $disablekb ) ) ? 0 : 1;
					$fs = get_option( 'embed_videos_fs' );
					$fs = ( empty( $fs ) ) ? 0 : 1;
					$controls = get_option( 'embed_videos_controls' );
					$controls = ( empty( $controls ) ) ? 0 : 1;
					$hd = get_option( 'embed_videos_hd' );
					$hd = ( empty( $hd ) ) ? 0 : 1;

					$parameters = "?autoplay=".$autoplay."&rel=".$rel."&fs=".$fs."&disablekb=".$disablekb."&controls=".$controls."&hd=".$hd."&mute=".$autoplay;

					$video_link = 'https://www.youtube.com/embed/'.$videolink_id.$parameters;
					break;
					case 'vimeo':
					$video_link = 'https://player.vimeo.com/video/'.$videolink_id;
					break;
				}
			}
			$video = '';
			if( !empty( $video_link ) ) {
				$newhtml .= '<a href="#"  data-type="video" data-video="<div class=&quot;wrapper&quot;><div class=&quot;video-wrapper&quot;><iframe width=&quot;1000&quot; height=&quot;640&quot; src=&quot;'. esc_url ( $video_link ) .'&quot; frameborder=&quot;0&quot; allowfullscreen=&quot;true&quot; webkitallowfullscreen=&quot;true&quot; mozallowfullscreen=&quot;true&quot;></iframe></div></div>" ><iframe class="woo-iframelist" width="" height="" src="'. esc_url( $video_link ) .'" frameborder="0" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" ></iframe>'. $image .'</a>';
			} else {
				$link = (empty($video_link)) ? $image_link : $video_link;
				$newhtml .= '<a href="'. esc_url( $link) .'" class="'. $image_class.'" title="'. sanitize_title( $image_title ) .'" rel="prettyPhoto[product-gallery]" data-type="image"  >'. $image .' </a>';
			}

			$loop++;
			$newhtml .= '</div>';
		}
		echo esc_html($newhtml);
	}
}

if ( !function_exists( 'video_site_name' ) ) {
	function video_site_name( $video_site, $videolink_id ) {
		switch ( $video_site ) {
			case 'youtube':
			$autoplay = get_option( 'embed_videos_autoplay' );
			$autoplay = ( empty( $autoplay ) ) ? 0 : 1;
			$rel = get_option( 'embed_videos_rel' );
			$rel = ( empty( $rel ) ) ? 0 : 1;
			//$showinfo = get_option( 'embed_videos_showinfo' );
			//$showinfo = ( empty( $showinfo ) ) ? 0 : 1;
			$disablekb = get_option( 'embed_videos_disablekb' );
			$disablekb = ( empty( $disablekb ) ) ? 0 : 1;
			$fs = get_option( 'embed_videos_fs' );
			$fs = ( empty( $fs ) ) ? 0 : 1;
			$controls = get_option( 'embed_videos_controls' );
			$controls = ( empty( $controls ) ) ? 0 : 1;
			$hd = get_option( 'embed_videos_hd' );
			$hd = ( empty( $hd ) ) ? 0 : 1;

			$parameters = "?autoplay=".$autoplay."&rel=".$rel."&fs=".$fs."&disablekb=".$disablekb."&controls=".$controls."&hd=".$hd."&mute=".$autoplay;

			$video_link = 'https://www.youtube.com/embed/'.$videolink_id.$parameters;
			break;
			case 'vimeo':
			$video_link = 'https://player.vimeo.com/video/'.$videolink_id;
			break;
		}
		echo esc_html($video_link);
	}
}
