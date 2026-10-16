<?php

class WPPCP_Posts{

	public function __construct(){
		add_action('wp_ajax_wppcp_load_published_posts', array($this, 'load_published_posts'));
        add_action('wp_ajax_wppcp_load_published_pages', array($this, 'load_published_pages'));
        add_action('wp_ajax_wppcp_load_published_cpt', array($this, 'load_published_cpt'));
        
	}

	public function load_published_pages(){
        global $wpdb;

        $post_json_results = array();
        if( ( current_user_can('manage_options') || current_user_can('wpppcp_manage_options') )
        && check_ajax_referer( 'wppcp-admin', 'verify_nonce',false ) ){
            $search_text  = isset($_POST['q']) ? sanitize_text_field($_POST['q']) : '';
            $search_text = '%' . $wpdb->esc_like( $search_text ) . '%';
            $post_json_results = array();

            $query  = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE $wpdb->posts.post_title like %s  && $wpdb->posts.post_status='publish'  && $wpdb->posts.post_type='page' order by $wpdb->posts.post_date desc limit 20", $search_text );
            
            $result = $wpdb->get_results($query);
            if($result){
                foreach($result as $post_row){
                    array_push($post_json_results , array('id' => $post_row->ID, 'name' => esc_html($post_row->post_title)) ) ;
                }
            }
        }       
        
        echo json_encode(array('items' => $post_json_results ));exit;
    }

    public function load_published_posts(){
        global $wpdb;

        $post_json_results = array();
        if( ( current_user_can('manage_options') || current_user_can('wpppcp_manage_options') )
        && check_ajax_referer( 'wppcp-admin', 'verify_nonce',false ) ){
            $search_text  = isset($_POST['q']) ? sanitize_text_field($_POST['q']) : '';
            $search_text = '%' . $wpdb->esc_like( $search_text ) . '%';
            $post_json_results = array();

            $query  = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE $wpdb->posts.post_title like %s  && $wpdb->posts.post_status='publish'  && $wpdb->posts.post_type='post' order by $wpdb->posts.post_date desc limit 20", $search_text );
            $result = $wpdb->get_results($query);
            if($result){
                foreach($result as $post_row){
                    array_push($post_json_results , array('id' => $post_row->ID, 'name' => esc_html($post_row->post_title)) ) ;
                }
            }
        }        
        
        echo json_encode(array('items' => $post_json_results ));exit;
    }

    public function load_published_cpt(){
    	global $wpdb;
        

        $post_json_results = array();
        if( ( current_user_can('manage_options') || current_user_can('wpppcp_manage_options') )
        && check_ajax_referer( 'wppcp-admin', 'verify_nonce',false ) ){
            $search_text  = isset($_POST['q']) ? sanitize_text_field($_POST['q']) : '';
            $search_text = '%' . $wpdb->esc_like( $search_text ) . '%';
            $post_type  = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : '';

            $post_json_results = array();

            

            $query  = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE $wpdb->posts.post_title like %s  && $wpdb->posts.post_status='publish'  && $wpdb->posts.post_type='%s' order by $wpdb->posts.post_date desc limit 20", $search_text, $post_type );

            $result = $wpdb->get_results($query);
            if($result){
                foreach($result as $post_row){
                    array_push($post_json_results , array('id' => $post_row->ID, 'name' => esc_html($post_row->post_title) )) ;
                }
            }

        }        
        
        echo json_encode(array('items' => $post_json_results ));exit;
    }

    public function get_post_types(){

    	$skipped_types = array('post','page','attachment','revision','nav_menu_item');
    	$allowed_post_types = array();

    	$args = array();
    	$output = 'objects'; 
    	$post_types = get_post_types( $args, $output );
    	foreach ($post_types as $post_type => $post_type_data) {
    		if(!in_array($post_type, $skipped_types)){
    			$allowed_post_types[$post_type] = $post_type_data->label;
    		}
    	}
    	

    	return $allowed_post_types;
    }
}