<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

//Add Custom scripts in Admin
function wmi_custom_script(){

	//Script
	wp_enqueue_script( 'wmi-admin-script', WMI_MENU_IMG_URL . '/assets/js/wmi-admin-script.js', ['jquery'], WMI_VERSION, true );
	wp_localize_script( 'wmi-admin-script', 'deleteimg_ajax', 
		array( 
			'ajax_url'  => admin_url('admin-ajax.php'),
			'nonce'     => wp_create_nonce('wmi_img_nonce'),
			'edit_img'  => esc_url( WMI_MENU_IMG_URL . 'assets/images/edit-icon.svg'  ),
        	'deleteimg' => esc_url( WMI_MENU_IMG_URL . 'assets/images/delete-icon.svg' )
		) 
	);

	//Style
	wp_enqueue_style( 'wmi-admin-style', WMI_MENU_IMG_URL . '/assets/css/wmi-style.css', [], WMI_VERSION, 'all');

	if (is_admin()){
	    wp_enqueue_media ();
	}
}
add_action( 'admin_enqueue_scripts', 'wmi_custom_script' );

//Add Custom scripts in Admin
function wmi_custom_style(){

	wp_register_style( 'wmi-front-style',  WMI_MENU_IMG_URL . 'assets/css/wmi-front-style.css', [], WMI_VERSION, 'all');
	wp_enqueue_style( 'wmi-front-style' );
}
add_action( 'wp_enqueue_scripts', 'wmi_custom_style' );

//Update menu custom field
add_action( 'wp_update_nav_menu_item', 'wmi_update_custom_img_field', 10, 3 );
function wmi_update_custom_img_field( $menu_id, $menu_item_db_id, $args ) {

	// Verify this came from our screen and with proper authorization.
	$nonce_val = isset($_POST['_menu_list_image_nonce_name']) ? sanitize_text_field( wp_unslash( $_POST['_menu_list_image_nonce_name'] ) ) : '';

	$menu_item_image 		= isset($_REQUEST['menu-item-image']) 		 ?	array_map( 'esc_url_raw', wp_unslash($_REQUEST['menu-item-image'])) : '';
	$menu_item_img_position = isset($_REQUEST['menu-item-img-position']) ? 	array_map( 'sanitize_text_field', wp_unslash($_REQUEST['menu-item-img-position']) ) : '';
	$menu_item_id 			= isset($_REQUEST['menu-item-id']) 			 ? 	array_map( 'absint', wp_unslash($_REQUEST['menu-item-id'])) : '';

	if ( ! isset( $nonce_val ) || ! wp_verify_nonce( $nonce_val, 'menu_list_image_nonce' ) ) {
		return $menu_id;
	}

	if ( ! current_user_can('manage_options') ) {
		return $menu_id;
    }

    if ( is_array($menu_item_image) || is_array($menu_item_img_position) ) {
		
        $image_value 	= isset($menu_item_image[$menu_item_db_id]) 		? sanitize_text_field( wp_unslash( $menu_item_image[$menu_item_db_id] ) ) 		 : '';
        $image_id 		= isset($menu_item_id[$menu_item_db_id]) 			? sanitize_text_field( wp_unslash( $menu_item_id[$menu_item_db_id] ) ) 			 : '';
        $image_position = isset($menu_item_img_position[$menu_item_db_id]) 	? sanitize_text_field( wp_unslash( $menu_item_img_position[$menu_item_db_id] ) ) : '';
		
		if ( $image_value && $image_id ) {
        	update_post_meta( $menu_item_db_id,  '_menu_list_image', 			$image_value 	);
        	update_post_meta( $menu_item_db_id,  '_menu_list_image_id', 		$image_id 		);
        	update_post_meta( $menu_item_db_id,  '_menu_list_image_position', 	$image_position );
        }
    }
}

//Add menu custom field
function wmi_customfield_menu_image( $item_id, $item ) {
	wp_nonce_field( 'menu_list_image_nonce', '_menu_list_image_nonce_name' );
	$menu_image 		 = get_post_meta( $item_id, '_menu_list_image', 			true );
	$menu_image_id 		 = get_post_meta( $item_id, '_menu_list_image_id', 			true );
	$menu_image_position = get_post_meta( $item_id, '_menu_list_image_position',	true );
	$menu_image_id  	 = (isset($menu_image_id) ? (int) $menu_image_id : '');

	if(!empty($menu_image_id)){
		$image_alt = get_post_meta($menu_image_id, '_wp_attachment_image_alt', true);
		$image_alt = !empty($image_alt) ? $image_alt : basename($menu_image);
	} else {
		$image_alt = basename($menu_image);
	} 
	?>
	<div class="field-custom_menu_meta" style="margin: 5px 0;">
		<div class="menu-img">
		    <p><?php esc_html_e( 'Image', 'wp-menu-image' ); ?></p>
			<?php if($menu_image){ ?>
				<div class="menu-img-block menu-block-<?php echo esc_attr($item_id); ?>">
					<ul class="menu-actions">
						<li>
							<a href="javascript:void(0);" class="edit-btn" id="upload-image-<?php echo esc_attr($item_id); ?>"  data-id="<?php echo esc_attr($item_id); ?>">
								<?php /*  <img src="<?php echo esc_url( WMI_MENU_IMG_URL . 'assets/images/edit-icon.svg' ); ?>" alt="edit"> */  ?>
							</a>
						</li>
						<li>
							<a href="javascript:void(0);" class="close-btn" data-id="<?php echo esc_attr($item_id); ?>">
								<?php /* <img src="<?php echo esc_url(WMI_MENU_IMG_URL . 'assets/images/delete-icon.svg'); ?>" alt="delete">  */ ?>
							</a>
						</li>
					</ul>
				    <img class="menu-image upload-image-<?php echo esc_attr($item_id); ?>" src="<?php echo esc_url($menu_image); ?>" alt="<?php echo esc_attr($image_alt); ?>" width="120" height="120">
				</div>
			<?php } ?>
			<input class="widefat custom_media_url img_txt-<?php echo esc_attr($item_id); ?>" id="edit-menu-item-image-<?php echo esc_attr($item_id); ?>" name="menu-item-image[<?php echo esc_attr($item_id); ?>]" type="hidden" value="<?php echo esc_attr($menu_image); ?>">
			<input class="widefat custom_media_id img_id-<?php echo esc_attr($item_id); ?>" id="edit-menu-item-id-<?php echo esc_attr($item_id); ?>" name="menu-item-id[<?php echo esc_attr($item_id); ?>]" type="hidden" value="<?php echo esc_attr($menu_image_id); ?>">
			<input type="button" class="button upload-image widefat" data-id="<?php echo esc_attr($item_id); ?>" id="upload-image-<?php echo esc_attr($item_id); ?>" value="Upload Image" style="margin-top:5px;" />
		</div>
		<div class="img-position" style="margin: 5px 0;">
			<p><?php esc_html_e( 'Image Position', 'wp-menu-image' ); ?></p>
			<label>
				<input type="radio" name="menu-item-img-position[<?php echo esc_attr($item_id); ?>]" value="before" <?php if( esc_attr($menu_image_position) == 'before'){ ?>checked <?php }else{ echo 'checked'; } ?>>
				<span><?php esc_html_e( 'Before', 'wp-menu-image' ); ?></span>
			</label>
			<label>
				<input type="radio" name="menu-item-img-position[<?php echo esc_attr($item_id); ?>]" value="after" <?php if( esc_attr($menu_image_position) == 'after'){ echo 'checked'; } ?>>
				<span><?php esc_html_e( 'After', 'wp-menu-image' ); ?></span>
			</label>
		</div>
	</div>
	<?php
}
add_action( 'wp_nav_menu_item_custom_fields', 'wmi_customfield_menu_image', 10, 2 );

//Display Image in Menu
function wmi_display_img_menu( $title, $item, $args, $depth ) {

	$menu_image 	= get_post_meta( $item->ID,  '_menu_list_image', true );
	$menu_image_id 	= get_post_meta( $item->ID,  '_menu_list_image_id', true );
	$menu_image_pos = get_post_meta( $item->ID,  '_menu_list_image_position', true );
	$menu_image_id  = (isset($menu_image_id) ? (int) $menu_image_id : '');

	if(!empty($menu_image_id) ){
		$image_alt  = get_post_meta($menu_image_id, '_wp_attachment_image_alt', true);
		$image_alt  = !empty($image_alt) ? $image_alt : basename($menu_image);
	} else {
		$image_alt  = basename($menu_image);
	}

	if ( $menu_image_id ) {
		$get_menu_image = wp_get_attachment_image( $menu_image_id, array('25', '25'), false, array( 'alt' => esc_attr($image_alt), 'loading'=>'lazy' ) );
	}

	if($menu_image){
		if ($menu_image_pos == 'after') {
			$title = '<span>'.$title.'</span>'. $get_menu_image;
		} else {
    		$title = $get_menu_image . '<span>'.$title.'</span>';
    	}
    }
    return $title;
}
add_filter( 'nav_menu_item_title', 'wmi_display_img_menu', 10, 4 );

//Add class in menu items
add_filter('nav_menu_css_class' , 'wmi_add_custom_class' , 10 , 2);
function wmi_add_custom_class($classes, $item){
	$menu_image_pos = get_post_meta( $item->ID, '_menu_list_image_position', true );
	$classes[] = 'wp-menu-img';
	if($menu_image_pos == 'after'){
		$classes[] = 'wp-menu-img-after';	
	}else{
    	$classes[] = 'wp-menu-img-before';
    }
    return $classes;
}


//Delete Image in Menu
add_action('wp_ajax_del_img', 'wmi_delete_img_menu');  // For logged-in users
// add_action('wp_ajax_nopriv_del_img', 'wmi_delete_img_menu');
function wmi_delete_img_menu(){

	$nonce = isset($_POST['nonce']) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

	// Verify nonce for CSRF protection
	if ( ! isset($nonce) || ! wp_verify_nonce( $nonce, 'wmi_img_nonce' ) ) {
		$message = esc_html__('Invalid nonce','wp-menu-image');
		$response = array('message' => $message);
		wp_send_json_error($response);
        wp_die();
    }

	// Check user capability
	if ( ! current_user_can('manage_options') ) {
		$message = esc_html__('Unauthorized action','wp-menu-image');
		$response = array('message' => $message);
		wp_send_json_error($response);
        wp_die();
    }

	// Sanitize and process the menu ID
    $menu_id = ( isset($_POST['menu_id']) ? sanitize_text_field( wp_unslash($_POST['menu_id'])) : 0);

	if ( $menu_id ) {

		// Delete the Post meta from the Menu
		delete_post_meta( $menu_id, '_menu_list_image' );
		update_post_meta( $menu_id, '_menu_list_image_position', 'before' );

		$message = esc_html__('Image deleted','wp-menu-image');
		$response = array('message' => $message);
		wp_send_json_success($response);

	} else {
		$message = esc_html__('Invalid menu ID','wp-menu-image');
		$response = array('message' => $message);
		wp_send_json_error($response);
	}

	wp_die();
}

function wmi_add_action_links( $actions ) {
    
    $custom_actions[] = '<a href="https://profiles.wordpress.org/yudiz/#content-plugins" target="_blank">' . esc_html__('More from Yudiz', 'wp-menu-image') . '</a>';
    return array_merge( $actions, $custom_actions );
}
add_filter( 'plugin_action_links_' . WMI_MENU_IMG_BASENAME, 'wmi_add_action_links' );
