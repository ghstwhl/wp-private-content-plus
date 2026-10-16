<?php

/* Manage menu related settings */
class WPPCP_Menu{

	/* Initialize menu related acttions and filters */
	public function __construct(){
		global $wppcp;

		$this->private_content_settings  = get_option('wppcp_options');  

		if(isset($this->private_content_settings['general']['private_content_module_status'])){
           
			add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'menu_item_custom_fields' ), 10, 4 );
			add_filter( 'wp_edit_nav_menu_walker', array( $this, 'edit_nav_menu_walker' ) );
			add_action( 'wp_update_nav_menu_item', array( $this, 'update_nav_menu_item' ), 10, 3 );	
			if ( ! is_admin() ) {
				add_filter( 'wp_get_nav_menu_items', array( &$this, 'restrict_nav_menu_items' ), 10, 3 );			
			}
			

		}

	}

	

	/* Include a custom menu walker class to modify the menu */
	public function edit_nav_menu_walker( $walker ) {
		require_once( dirname( __FILE__ ) . '/class-wppcp-walker-nav-menu-edit.php' );
		return 'WPPCP_Walker_Nav_Menu_Edit';
	}

	/* Display restriction settings for menu items */
	public function menu_item_custom_fields($item_id, $item, $depth, $args ) {
		global $wp_roles;

		$user_roles = apply_filters( 'nav_menu_roles', $wp_roles->role_names, $item );

		$roles = (array) get_post_meta( $item->ID, 'wppcp_nav_menu_roles', true );
		$visibility_level = get_post_meta( $item->ID, 'wppcp_nav_menu_visibility_level', true );
		if($visibility_level == ''){
			$visibility_level = '0';
		}

		$users = (array) get_post_meta( $item->ID, 'wppcp_nav_menu_users', true );

		?>

		<?php wp_nonce_field( 'wppcp_nav_menu_page_nonce', 'wppcp_nav_menu_page_nonce_field' );  ?>

		<div class="description-wide">
		    <span class="description"><?php _e( "Visibility", 'wppcp' ); ?></span>
		    <br />

		    <input type="hidden" class="nav-menu-id" value="<?php echo esc_attr($item->ID) ;?>" />

		    <div class="logged-input-holder" style="float: left; width: 35%;">
		        <select class="wppcp_menu_visibility" name='wppcp_menu_visibility_<?php echo esc_attr($item->ID) ;?>' id='wppcp_menu_visibility_<?php echo esc_attr($item->ID) ;?>' >
		        	<option value='0' <?php selected('0',$visibility_level); ?> ><?php _e('Everyone','wppcp'); ?></option>
		        	<option value='1' <?php selected('1',$visibility_level); ?> ><?php _e('Members','wppcp'); ?></option>
		        	<option value='2' <?php selected('2',$visibility_level); ?> ><?php _e('Guests','wppcp'); ?></option>
		        	<option value='3' <?php selected('3',$visibility_level); ?> ><?php _e('By User Role','wppcp'); ?></option>
		        	<option value='4' <?php selected('4',$visibility_level); ?> ><?php _e('By Users','wppcp'); ?></option>
		        
		        </select>
		    </div>

		    

		</div>

		<?php
			$role_display_panel = "display:none";
			if($visibility_level == '3'){
				$role_display_panel = "display:block";
			}
		?>
		<div class="wppcp-menu-role-display-panel description-wide" style="margin: 5px 0;<?php echo esc_attr($role_display_panel); ?>">
		    <span class="description"><?php _e( "Permitted user roles", 'wppcp' ); ?></span>
		    <br />

		    <?php
		    foreach ( $user_roles as $role => $name ) {

		        $checked = checked( true, in_array( $role, $roles ) , false );
		        
		        ?>

		        <div class="" style="">
		        	<input type="checkbox" name="wppcp_menu_roles[<?php echo esc_attr($item->ID) ;?>][]" id="wppcp_menu_roles<?php echo esc_attr($item->ID) ;?>" <?php echo esc_attr($checked); ?> value="<?php echo esc_attr($role); ?>" />
		        	<label for="nav_menu_role-<?php echo esc_attr($role); ?>-for-<?php echo esc_attr($item->ID) ;?>">
		        	<?php echo esc_html( $name ); ?>
		        </label>
		        </div>

		<?php } ?>

		</div>

		<?php
			$users_display_panel = "display:none";
			if($visibility_level == '4'){
				$users_display_panel = "display:block";
			}
		?>
		<div class="wppcp-menu-users-display-panel description-wide" style="margin: 5px 0;<?php echo esc_attr($users_display_panel); ?>">
		    <span class="description"><?php _e( "Permitted users", 'wppcp' ); ?></span>
		    <br />

		    <select class="wppcp-select2-full-setting wppcp_menu_user_restrictions" multiple name="wppcp_menu_users[<?php echo esc_attr($item->ID) ;?>][]" id="wppcp_menu_users<?php echo esc_attr($item->ID) ;?>">
		    	<?php foreach ($users as $user_id) { 
		    			if($user_id != '' &&  $user_id != '0'){
		    			$user = get_user_by('ID',$user_id);
		    			if($user){
		    	?>
		    		<option value='<?php echo esc_attr($user_id); ?>' selected ><?php echo esc_html($user->data->display_name); ?></option>
		    	<?php }}} ?>
		    </select>
		    <input type='hidden' class='wppcp_menu_user_restrictions_hidden' value='' name='wppcp_menu_users_hidden[<?php echo esc_attr($item->ID) ;?>]' />
		    

		</div>

		<?php 
	
	}

	/* Save restriction settings for menu items */
	public function update_nav_menu_item( $menu_id, $menu_item_db_id, $args ) {

		if( ( current_user_can('manage_options') || current_user_can('wppcp_manage_options') )
			&& isset($_POST['wppcp_nav_menu_page_nonce_field']) && wp_verify_nonce( $_POST['wppcp_nav_menu_page_nonce_field'], 'wppcp_nav_menu_page_nonce' )) {


			$visibility_level = get_post_meta( $menu_item_db_id, 'wppcp_nav_menu_visibility_level', true );
			$new_visibility_level = isset( $_POST['wppcp_menu_visibility_'.$menu_item_db_id] ) ? sanitize_text_field($_POST['wppcp_menu_visibility_'.$menu_item_db_id]) : '0';
		
			$visibility_roles = isset($_POST['wppcp_menu_roles'][$menu_item_db_id]) ? (array) $_POST['wppcp_menu_roles'][$menu_item_db_id] : array();
			$visibility_users = isset($_POST['wppcp_menu_users'][$menu_item_db_id]) ? (array) $_POST['wppcp_menu_users'][$menu_item_db_id] : array();

			$visibility_users_list = array();
			foreach ($visibility_users as $key => $value) {
				if($key !== '' && !in_array($value,$visibility_users_list)){
					$value = (int) $value;
					array_push($visibility_users_list, $value);
				}
			}

			$visibility_roles_list = array();
			foreach ($visibility_roles as $key => $value) {
				if($key !== '' && !in_array($value,$visibility_roles_list)){
					$value = sanitize_text_field($value);
					array_push($visibility_roles_list, $value);
				}
			}

			update_post_meta( $menu_item_db_id, 'wppcp_nav_menu_visibility_level', $new_visibility_level );
			update_post_meta( $menu_item_db_id, 'wppcp_nav_menu_roles', $visibility_roles_list );
			update_post_meta( $menu_item_db_id, 'wppcp_nav_menu_users', $visibility_users_list );
		}
	}

	

	/* Restrict menu items based on specified conditions */
	public function restrict_nav_menu_items( $items, $menu, $args ) {

		$hide_children_of = array();

		// Iterate over the items to search and destroy
		foreach ( $items as $key => $item ) {

			$visible = true;

			$visibility_level = get_post_meta( $item->ID, 'wppcp_nav_menu_visibility_level', true );
	
			if( in_array( $item->menu_item_parent, $hide_children_of ) ){
				$visible = false;
				$hide_children_of[] = $item->ID;
			}

			if( $visible && isset( $visibility_level ) ) {

				// check all logged in, all logged out, or role
				switch( $visibility_level ) {
					case '0' :
						$visible = true;
						break;
					case '1' :
						$visible = is_user_logged_in() ? true : false;
						break;
					case '2' :
						$visible = ! is_user_logged_in() ? true : false;
						break;
					case '3' :
						$visibility_roles = (array) get_post_meta( $item->ID, 'wppcp_nav_menu_roles', true );
						$visible = false;
						foreach ( $visibility_roles as $role ) {
							if ( current_user_can( $role ) ) 
								$visible = true;
						}
						break;
					case '4' :
						$visibility_users = (array) get_post_meta( $item->ID, 'wppcp_nav_menu_users', true );
						$visible = false;
						foreach ( $visibility_users as $user ) {
							if ( get_current_user_id() == $user ) 
								$visible = true;
						}
						break;
				}

			}

			// add filter to work with plugins that don't use traditional roles
			$visible = apply_filters( 'nav_menu_roles_item_visibility', $visible, $item );

			// unset non-visible item
			if ( ! $visible ) {
				$hide_children_of[] = $item->ID; // store ID of item 
				unset( $items[$key] ) ;
			}

		}

		return $items;
	}
}