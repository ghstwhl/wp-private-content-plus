<?php

class WPPCP_Woocommerce_Tab_Manager{

	public function __construct(){
		add_action( 'init',array($this,'register_woo_tabs'));
        add_action( 'add_meta_boxes', array($this,'woo_tabs_meta_box'));
        add_action( 'save_post', array($this,'save_woo_tabs'), 10, 3 );
        add_filter( 'woocommerce_product_tabs', array($this,'add_frontend_woo_tabs') );
	}

	public function register_woo_tabs(){
		register_post_type( WPPCP_WOO_TABS_POST_TYPE,
            array(
                'labels' => array(
                    'name'              => __('Woo Product Tabs','wppcp'),
                    'singular_name'     => __('Woo Product Tab','wppcp'),
                    'add_new'           => __('Add New','wppcp'),
                    'add_new_item'      => __('Add New Woo Product Tab','wppcp'),
                    'edit'              => __('Edit','wppcp'),
                    'edit_item'         => __('Edit Woo Product Tab','wppcp'),
                    'new_item'          => __('New Woo Product Tab','wppcp'),
                    'view'              => __('View','wppcp'),
                    'view_item'         => __('View Woo Product Tab','wppcp'),
                    'search_items'      => __('Search Woo Product Tab','wppcp'),
                    'not_found'         => __('No Woo Product Tab found','wppcp'),
                    'not_found_in_trash' => __('No Woo Product Tab found in Trash','wppcp'),
                ),

                'public' => true,
                'menu_position' => 100,
                'supports' => array( 'title','editor'),
                'has_archive' => true
            )
        );

	}


	public function save_woo_tabs($post_id){

        $skipped_types = array('attachment','revision','nav_menu_item');

        if ( ! isset( $_POST['wppcp_restriction_settings_nonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['wppcp_restriction_settings_nonce'], 'wppcp_restriction_settings' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! ( current_user_can( 'manage_options', $post_id ) || current_user_can( 'wppcp_manage_options', $post_id ) )) {
            return;
        }

        $visibility = isset( $_POST['wppcp_woo_tabs_visibility'] ) ? sanitize_text_field( $_POST['wppcp_woo_tabs_visibility'] ) : 'none';        
        $visible_roles = isset( $_POST['wppcp_woo_tabs_roles'] ) ? (array) $_POST['wppcp_woo_tabs_roles'] : array();

        $visible_roles_filtered = array();
        foreach ($visible_roles as $key => $value) {
            $visible_roles_filtered[$key] = sanitize_text_field($value);
        }


        update_post_meta( $post_id, '_wppcp_woo_tabs_visibility', $visibility );
        update_post_meta( $post_id, '_wppcp_woo_tabs_roles', $visible_roles_filtered );
        

    }   

    public function woo_tabs_meta_box(){

        if(current_user_can('manage_options') || current_user_can('wppcp_manage_options') || apply_filters('wppcp_restriction_setting_meta_box_visibility',false,array() ) ){
                add_meta_box(
                    'wppcp-woo-product-tab-restrictions',
                    __( 'WP Private Content Plus - WooCommrce Product Tab Settings', 'wppcp' ),
                    array($this,'add_woo_tabs_restrictions'),
                    'wppcp_fproduct_tabs',
                    'normal',
                    'low'
                );
        }

    } 

    public function add_woo_tabs_restrictions($post){
        global $wppcp,$woo_tabs_restriction_params;

        $wppcp->settings->load_wppcp_select2_scripts_style();

        $woo_tabs_restriction_params['post'] = $post;

        $wppcp->template_loader->get_template_part('woo-tabs-restriction-meta');   

    }       

    
    public function add_frontend_woo_tabs( $tabs ) {
        global $wppcp;

        $query = new WP_Query( array( 
            'post_type' => WPPCP_WOO_TABS_POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 25,
             ) );

        if ( $query->have_posts() ) {
            while ($query->have_posts()) : $query->the_post();

                $product_tab_id = get_the_ID();
                if($this->protection_status($product_tab_id)){
                    $tabs['wppcp_woo_'.$product_tab_id] = array(
                        'title'     => __( get_the_title() , 'wppcp' ),
                        'priority'     => 150,
                        'callback'  => array($this, 'woo_new_product_tab_content')
                    );
                }
                
            endwhile;
            wp_reset_query();

            
        }

        return $tabs;
    }

    public function woo_new_product_tab_content($key,$tab)  {
        $post_id = str_replace("wppcp_woo_", "", $key);
        $product_tab = get_post($post_id);
        echo wp_kses_post(do_shortcode($product_tab->post_content));
    }

    public function protection_status($post_id){
        global $wppcp;

        $visibility = get_post_meta( $post_id, '_wppcp_woo_tabs_visibility', true );
        $visible_roles = get_post_meta( $post_id, '_wppcp_woo_tabs_roles', true );
        if(!is_array($visible_roles)){
            $visible_roles = array();
        }

        switch ($visibility) {
            case 'all':
                return TRUE;
                break;
            
            case 'guest':
                if(is_user_logged_in()){
                    return FALSE;
                }else{
                    return TRUE;
                }
                break;

            case 'member':
                if(is_user_logged_in()){
                    return TRUE;
                }else{
                    return FALSE;
                }
                break;

            case 'role':
                if(is_user_logged_in()){
                    if(count($visible_roles) == 0){
                        return FALSE;
                    }else{
                        $user_roles = $wppcp->roles_capability->get_user_roles_by_id(get_current_user_id());
                        foreach ($visible_roles as  $visible_role ) {
                            if(in_array($visible_role, $user_roles)){
                                return TRUE;
                            }
                        }
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
                
                break;
                
            

            default:
                return "none";
                break;
        }

        return TRUE;
    }
}


