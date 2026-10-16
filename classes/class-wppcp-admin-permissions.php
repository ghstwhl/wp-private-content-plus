<?php

class WPPCP_Admin_Permissions{

	public function __construct(){
		add_action( 'init' , array( $this, 'init') );
		add_action( 'wp_ajax_wppcp_load_admin_menu_permission', array( $this, 'load_admin_menu_permission') );
		add_action( 'wp_ajax_wppcp_update_admin_menu_permission', array( $this, 'update_admin_menu_permission') );
		add_filter( 'admin_menu', array( $this, 'restrict_admin_menus') , 9999 );
	}

	public function init(){
		global $wppcp;
		$this->wppcp_options = $wppcp->settings->wppcp_options; 
	}

	public function load_admin_menu_permission(){
		global $wppcp, $wppcp_settings_data;

		if( current_user_can('manage_options') ){			

			$slug = isset( $_POST['slug'] ) ? sanitize_text_field( $_POST['slug'] ) : '';

			if( check_ajax_referer( 'wppcp-admin', 'verify_nonce',false ) ){

				$admin_menu_settings = isset( $this->wppcp_options['admin_menu_visibility'][$slug] ) ? $this->wppcp_options['admin_menu_visibility'][$slug] : '';
				$wppcp_settings_data['admin_menu_visibility'] = isset($admin_menu_settings['visibility']) ? $admin_menu_settings['visibility'] : 0;
				$wppcp_settings_data['admin_menu_roles'] = isset($admin_menu_settings['user_roles']) ? $admin_menu_settings['user_roles'] : array() ;
				$wppcp_settings_data['admin_menu_slug'] = $slug;

				ob_start();
	      		$wppcp->template_loader->get_template_part('admin-menu-visibility');    
	      		$display = ob_get_clean();  


				$result = array( 'msg' => $display , 'status' => 'success' );
			}else{
				$result = array( 'msg' => __('Invalid request.','wppcp'), 'status' => 'error' );
			}
		}else{
			$result = array( 'msg' => __('Permission denied.','wppcp'), 'status' => 'error' );
		}

		echo json_encode( $result );exit;
		exit;
	}

	public function update_admin_menu_permission(){
		global $wppcp, $wppcp_settings_data;

		if( current_user_can('manage_options') ){	

			$visibility = isset( $_POST['visibility'] ) ? sanitize_text_field( $_POST['visibility'] ) : 0;
			$slug = isset( $_POST['slug'] ) ? sanitize_text_field( $_POST['slug'] ) : '';
			$user_roles = isset( $_POST['user_roles'] ) ? (array)( $_POST['user_roles'] ) : array();
			$user_roles_filtered = array();
			foreach ($user_roles as $key => $value) {
				$user_roles_filtered[sanitize_text_field($key)] = sanitize_text_field($value);
			}
			if($visibility == '0'){
				$user_roles_filtered = array();
			}

			if( check_ajax_referer( 'wppcp-admin', 'verify_nonce',false ) ){

				$this->wppcp_options['admin_menu_visibility'][$slug] = array('visibility' => $visibility, 'user_roles' => $user_roles_filtered);

				update_option('wppcp_options',$this->wppcp_options);
			
				$admin_menu_settings = get_option('wppcp_options');
				// echo "<pre>";print_r($admin_menu_settings);exit;
				$admin_menu_settings = isset( $admin_menu_settings['admin_menu_visibility'][$slug] ) ? $admin_menu_settings['admin_menu_visibility'][$slug] : '';
				
				$wppcp_settings_data['admin_menu_visibility'] = isset($admin_menu_settings['visibility']) ? $admin_menu_settings['visibility'] : 0;
				$wppcp_settings_data['admin_menu_roles'] = isset($admin_menu_settings['user_roles']) ? $admin_menu_settings['user_roles'] : array() ;
				$wppcp_settings_data['admin_menu_slug'] = $slug;

				ob_start();
	      		$wppcp->template_loader->get_template_part('admin-menu-visibility');    
	      		$display = ob_get_clean(); 

				$result = array( 'msg' => $display , 'status' => 'success' );
			}else{
				$result = array( 'msg' => __('Invalid request.','wppcp'), 'status' => 'error' );
			}

		}else{
			$result = array( 'msg' => __('Permission denied.','wppcp'), 'status' => 'error' );
		}

		echo json_encode( $result );exit;
		exit;
	}
	
	public function restrict_admin_menus() {
	    if ( current_user_can('manage_options') ) {
	        return ;
	    }

	    global $menu, $submenu;
	    if ( ! isset( $menu ) || empty( $menu ) ) {
	      return;
	    }

	    $user              = wp_get_current_user();
	    $user_roles        = $user->roles;

	    $admin_menu_settings = get_option('wppcp_options');
		$admin_menu_settings = isset( $admin_menu_settings['admin_menu_visibility']) ? (array) $admin_menu_settings['admin_menu_visibility'] : array();
		$restricted_slugs = array();
		foreach ($admin_menu_settings as $slug => $menu_data) {
			$restricted_status = true;
			if($menu_data['visibility'] == 'user_roles'){
				$allowed_roles = (array) $menu_data['user_roles'];
				foreach ($user_roles as $role) {
					if(in_array($role, $allowed_roles)){
						$restricted_status = false;
					}
				}

				if($restricted_status){
					$restricted_slugs[] = $slug;
				}
			}
		}
			

	    foreach ( $menu as $key => $item ) {
	        if ( isset( $item[ 2 ] ) ) {
	            $menu_slug = $item[ 2 ];            
	            if ( in_array( $menu_slug, $restricted_slugs , false ) ) {                
	                $this->restrict_menu_access( $menu_slug , 'menu' );
	            }

	            if ( isset( $submenu ) && ! empty( $submenu[ $menu_slug ] ) ) {
	                foreach ( (array) $submenu[ $menu_slug ] as $subindex => $subitem ) {
	                    if ( in_array($subitem[ 2 ], $restricted_slugs)  ) {                         
	                        $this->restrict_menu_access( $menu_slug  , 'submenu' , $subitem[ 2 ]);
	                    }
	                }
	            }
	        }
	    }
	}

	public function restrict_menu_access( $menu_slug , $menu_type , $sub_menu_slug = '' ){

		if($menu_type == 'menu'){
			remove_menu_page( $menu_slug );
			$remove_slug = $menu_slug;
		}else{
			remove_submenu_page( $menu_slug, $sub_menu_slug );
			$remove_slug = $sub_menu_slug;
		}

		$url = basename( sanitize_url( $_SERVER[ 'REQUEST_URI' ] ) );
		$url = htmlspecialchars( $url );
		$uri = parse_url( $url );

		if ( $remove_slug === $url ) {
			add_action( 'load-' . basename( $uri[ 'path' ] ), array($this, 'block_page_access') );
			return TRUE;
		}
	}

	public function block_page_access(){
		global $wp_query;
	    $wp_query->set_404();
	    status_header( 404 );
	    get_template_part( 404 ); 
	    exit();
	}


}

?>