<?php

class WPPCP_Groups{

	public function __construct(){
		add_action( 'init',array($this,'register_groups'));
        add_action( 'add_meta_boxes', array($this,'groups_meta_box'));
        add_action( 'wp_ajax_wppcp_load_group_setting_users', array($this, 'wppcp_load_group_setting_users'));
        add_action( 'save_post', array($this,'save_groups'), 10, 3 );
        add_action( 'wp_ajax_wppcp_remove_group_setting_users', array($this, 'wppcp_remove_group_setting_users'));
        
        add_filter( 'manage_edit-' . WPPCP_GROUPS_POST_TYPE . '_columns', array($this,'custom_columns'));
        add_action( 'manage_' . WPPCP_GROUPS_POST_TYPE . '_posts_custom_column', array( $this,'custom_column_values'), 10, 2 );

        add_action( 'delete_post', array($this,'delete_group_info'), 10 );
        add_action('restrict_manage_users', array($this,'add_group_filter_user_list'));
        add_filter('init', array($this,'add_user_group'));
        add_action('admin_notices', array($this,'bulk_admin_notices'));
	}

	public function register_groups(){
		register_post_type( WPPCP_GROUPS_POST_TYPE,
            array(
                'labels' => array(
                    'name'              => __('Groups','wppcp'),
                    'singular_name'     => __('Group','wppcp'),
                    'add_new'           => __('Add New','wppcp'),
                    'add_new_item'      => __('Add New Group','wppcp'),
                    'edit'              => __('Edit','wppcp'),
                    'edit_item'         => __('Edit Group','wppcp'),
                    'new_item'          => __('New Group','wppcp'),
                    'view'              => __('View','wppcp'),
                    'view_item'         => __('View Group','wppcp'),
                    'search_items'      => __('Search Group','wppcp'),
                    'not_found'         => __('No Group found','wppcp'),
                    'not_found_in_trash' => __('No Group found in Trash','wppcp'),
                ),

                'public' => true,
                'menu_position' => 100,
                'supports' => array( 'title','editor'),
                'has_archive' => true
            )
        );

	}

    public function groups_meta_box(){

        if(current_user_can('manage_options') || current_user_can('wppcp_manage_options') || apply_filters('wppcp_groups_setting_meta_box_visibility',false,array() ) ){
        
            add_meta_box(
                        'wppcp-groups-general',
                        __( 'WP Private Content Plus - Add New Users', 'wppcp' ),
                        array($this,'add_group_user'),
                        WPPCP_GROUPS_POST_TYPE,
                        'normal',
                        'high'
                    );

            add_meta_box(
                        'wppcp-groups-users',
                        __( 'WP Private Content Plus - Group Members', 'wppcp' ),
                        array($this,'list_group_user'),
                        WPPCP_GROUPS_POST_TYPE,
                        'normal',
                        'high'
                    );
        }
    }

    public function add_group_user($post, $metabox){
        global $wppcp;

        $wppcp->settings->load_wppcp_select2_scripts_style();

        $placeholder = __('Start typing username or email to Add New Members','wppcp');
        $display = "<div class='wppcp-select2-full-setting'><select data-group-id='".esc_attr($post->ID)."' multiple class='wppcp-select2-setting wppcp-select2-full-setting' style='width:100%' placeholder='".esc_attr($placeholder)."' name='wppcp_backend_group_add_new_member[]' id='wppcp_backend_group_add_new_member' ></select></div>";

        $display .= '<input type="hidden" name="wppcp_backend_group_add_new_member_nonce" value="'.wp_create_nonce( 'wppcp-backend-group-add-new-member-nonce' ).' " />';

        $allowed_html = array(
            'div' => array(
                'class' => array(),
            ),
            'select' => array(
                'data-group-id' => array(),
                'class' => array(),
                'style' => array(),
                'placeholder' => array(),
                'name' => array(),
                'id' => array(),
                'multiple' => array(),
            ),
            'input' => array(
                'type' => array(),
                'name' => array(),
                'value' => array(),
            ),
        );

        echo wp_kses($display, $allowed_html);

    }

    public function list_group_user($post, $metabox){
        global $wpdb;

        $group_list_page = isset($_GET['group_list_page']) ? (int) $_GET['group_list_page'] : 1;
        $limit = 10;
        $group_list_next = ($group_list_page)*$limit;
        $group_list_start = ($group_list_page - 1)*$limit;
        $limit_str = " limit $group_list_start,$limit ";

        $sql_total  = $wpdb->prepare( "SELECT usr.*,gru.user_id,gru.group_id FROM {$wpdb->prefix}users as usr inner join {$wpdb->prefix}wppcp_group_users as gru on usr.ID=gru.user_id WHERE group_id = %d  ", $post->ID );
        $result_total = $wpdb->get_results($sql_total);
        if($result_total){

            $sql  = $wpdb->prepare( "SELECT usr.*,gru.user_id,gru.group_id  FROM {$wpdb->prefix}users as usr inner join {$wpdb->prefix}wppcp_group_users as gru on usr.ID=gru.user_id WHERE group_id = %d  $limit_str", $post->ID );
            $result = $wpdb->get_results($sql);

            $display = "";
            $display .= "<div class='wppcp-admin-group-list-header' >
                            <div class='wppcp-admin-group-list-header-item' >".__('ID','wppcp')."</div>
                            <div class='wppcp-admin-group-list-header-item' >".__('Name','wppcp')."</div>
                            <div class='wppcp-admin-group-list-header-item' ></div>
                            <div class='wppcp-clear' ></div>
                        </div>";
            $group_users = array();
            if($result && is_array($result)){  
                foreach($result as $row){
                    $display .= "<div class='wppcp-admin-group-list-values' >
                                    <div class='wppcp-admin-group-list-value wppcp-admin-group-list-id' >".$row->user_id."</div>
                                    <div class='wppcp-admin-group-list-value ' ><span>".get_avatar($row->user_id, 30)."</span><span class='wppcp-admin-group-list-name'>".esc_html($row->display_name)."</span></div>
                                    <div class='wppcp-admin-group-list-value wppcp-admin-group-list-control' ><a href='javascript:void(0)' data-group-id='".$row->group_id."' data-user-id='".$row->user_id."' class='wppcp-admin-group-list-remove'>".__('Remove from Group','wppcp')."</a></div>
                                    <div class='wppcp-clear' ></div>
                                </div>";
                }
            }

            if($group_list_page > 1){
                $plnk = ($group_list_page-1);
                $display .= "<a style='float:left' class='button button-primary button-large' href='".esc_attr(get_edit_post_link( $post->ID))."&group_list_page=".esc_attr($plnk)."'>". __('Previous','wppcp')."</a>";
            }


            if(count($result_total) > $group_list_next){
                $nlnk = ($group_list_page+1);
                $display .= "<a style='float:right' class='button button-primary button-large' href='".esc_attr(get_edit_post_link( $post->ID))."&group_list_page=".esc_attr($nlnk)."'>".__('Next','wppcp')."</a>";
            }

            $display .= "<div class='wppcp-clear' ></div>";
            $allowed_html = wppcp_admin_templates_allowed_html();
             
            $allowed_html['a']['data-user-id'] = array();
            $allowed_html['a']['data-group-id'] = array();
            echo wp_kses($display, $allowed_html); 
        }else{
            $display  = "<div class='wppcp-group-empty-users' >".__('No Users Found','wppcp')."</div>";
            $display .= "<div class='wppcp-clear' ></div>";
            echo wp_kses_post($display);
        }

        
    }

    /* Get the users for the private page content form */
    public function wppcp_load_group_setting_users(){
        global $wpdb,$post;

        if( (current_user_can('manage_options') || current_user_can('wppcp_manage_options'))
         && check_ajax_referer( 'wppcp-admin', 'verify_nonce',false )  ){

            $search_text  = isset($_POST['q']) ? sanitize_text_field( $_POST['q'] )  : '';
            $group_id = isset($_POST['group_id']) ? (int) $_POST['group_id'] : '';

            $sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wppcp_group_users WHERE group_id = %d", $group_id );
            $result = $wpdb->get_results($sql);

            $group_users = array();
            if($result && is_array($result)){  
                foreach($result as $row){
                    array_push($group_users, intval($row->user_id));
                }
            }
            
            $args = array('number' => 20);
            if($search_text != ''){
                $args['search'] = "*".$search_text."*";
            }
            
            $user_results = array();
            $user_json_results = array();
            
            $user_query = new WP_User_Query( $args );
            $user_results = $user_query->get_results();

            foreach($user_results as $user){
                if($user->ID != $this->current_user && !in_array($user->ID, $group_users)){
                    array_push($user_json_results , array('id' => $user->ID, 'name' => $user->data->display_name." (".$user->data->user_email.")") ) ;
                }                           
            }

        }else{
            $user_json_results = array();
        }
    
        echo json_encode(array('items' => $user_json_results ));exit;
    }  

	public function save_groups($post_id, $post, $update){
        global $wpdb;

        if ( WPPCP_GROUPS_POST_TYPE != $post->post_type ) {
            return;
        }

        $nonce = isset($_POST['wppcp_backend_group_add_new_member_nonce']) ? 
            sanitize_text_field($_POST['wppcp_backend_group_add_new_member_nonce']) : '';

        if ( ! isset($_POST['wppcp_backend_group_add_new_member_nonce']) || ! wp_verify_nonce( $nonce, 'wppcp-backend-group-add-new-member-nonce' ) ) {
            return;
        } 

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! ( current_user_can( 'manage_options', $post_id ) || current_user_can( 'wppcp_manage_options', $post_id ) ) ) {
            return;
        }

        if ( isset( $_REQUEST['wppcp_backend_group_add_new_member'] ) ) { 
            $group_members =  array_map( 'sanitize_text_field',  $_REQUEST['wppcp_backend_group_add_new_member'] );
            foreach ($group_members as $key => $group_member) {
                $group_member = (int) $group_member;
                $sql  = $wpdb->prepare( "Insert into {$wpdb->prefix}wppcp_group_users(group_id,user_id,updated_at) values(%d,%d,'%s')", $post_id , $group_member, date("Y-m-d H:i:s"));
                
                $result = $wpdb->get_results($sql);
            }
        }

        update_post_meta( $post_id , 'wppcp_group_type' , 'Administrative');
        
    }

    public function wppcp_remove_group_setting_users(){
        global $wpdb,$post;

        if( ( current_user_can('manage_options') || current_user_can('wppcp_manage_options') )
         && check_ajax_referer( 'wppcp-admin', 'verify_nonce',false ) ){

            $group_id = isset($_POST['group_id']) ? (int) $_POST['group_id'] : '0';
            $user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : '0';

            $sql  = $wpdb->prepare( "Delete FROM {$wpdb->prefix}wppcp_group_users WHERE group_id = %d AND user_id=%d", $group_id, $user_id );
            $result = $wpdb->get_results($sql);

            echo json_encode(array('status' => 'success' ));exit;

        }else{
            echo json_encode(array('status' => 'error' ));exit;
        }
        
    }

    public function custom_columns( $columns ) {

        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Title','wppcp'),
            'wppcp_group_id' => __( 'Group ID','wppcp' ),
            'date' => __( 'Dates','wppcp' )
        );

        return $columns;
    }

    public function custom_column_values($column, $post_id ) {
        global $post;

        switch( $column ) {

          case 'wppcp_group_id' :
                echo (int) $post_id;
                break;

          default :
            break;
       }
        
    }

    public function get_user_groups_by_id($user_id){
        global $wpdb;

        $sql  = $wpdb->prepare( "Select * FROM {$wpdb->prefix}wppcp_group_users WHERE user_id=%d", $user_id );
        $result = $wpdb->get_results($sql);

        $user_groups = array();

        if($result){
            foreach ($result as $key => $value) {
               array_push($user_groups, $value->group_id );
            }
        }

        return $user_groups;
    }


    public function delete_group_info($post_id){
        global $wpdb;

        if ( ! ( current_user_can( 'manage_options', $post_id ) ||
            current_user_can( 'wppcp_manage_options', $post_id ) ) ) {
            return;
        }

        $sql  = $wpdb->prepare( "Delete FROM {$wpdb->prefix}wppcp_group_users WHERE group_id=%d", $post_id );
        $result = $wpdb->get_results($sql);

    }

    public function add_group_filter_user_list($name){

        $group_select = '<select name="wppcp_user_list_group_%s" style="float:none;margin-left:10px;">
    <option value="">%s</option>%s</select>';


        $query = new WP_Query( array( 
            'post_type' => WPPCP_GROUPS_POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page'=>-1    ) );

        $options = '';
        if ( $query->have_posts() ) {
            while ($query->have_posts()) : $query->the_post();            
                 $options .= '<option value="'.get_the_ID().'">'.get_the_title().'</option>';
            endwhile;
            wp_reset_query();
        }


        $select = sprintf( $group_select, $name, __( 'Select Group...', 'wppcp' ), $options );

        $allowed_html = wppcp_admin_templates_allowed_html();
        echo wp_kses($select, $allowed_html);  

        echo wp_nonce_field( 'wppcp_user_assign_group_nonce', 'wppcp_user_assign_group_nonce_field' );
        submit_button(__( 'Assign Group','wppcp' ), null, 'wppcp_user_list_group_submit' , false);
    }

    public function add_user_group($query){
         global $pagenow,$wpdb;
         if ( (current_user_can('manage_options') || (current_user_can('wppcp_manage_options') ) ) 
            && isset($_POST['wppcp_user_assign_group_nonce_field']) && wp_verify_nonce( $_POST['wppcp_user_assign_group_nonce_field'], 'wppcp_user_assign_group_nonce' ) && is_admin() && 'users.php' == $pagenow 
            && isset($_REQUEST['wppcp_user_list_group_submit']) ) {

            $users = isset($_GET['users']) ?   (array) $_GET['users'] : array() ;
            $user_list_group = isset($_GET['wppcp_user_list_group_top']) ? (int) $_GET['wppcp_user_list_group_top'] : 0;

            if($user_list_group != 0) {
                foreach ($users as $user_id ) {
                    $user_id = (int) $user_id;
                    
                    $sql  = $wpdb->prepare( "Delete from {$wpdb->prefix}wppcp_group_users where group_id=%d and user_id=%d", $user_list_group , $user_id);
                    $result = $wpdb->get_results($sql);

                    $sql  = $wpdb->prepare( "Insert into {$wpdb->prefix}wppcp_group_users(group_id,user_id,updated_at) values(%d,%d,'%s')", $user_list_group , $user_id, date("Y-m-d H:i:s"));
                    $result = $wpdb->get_results($sql);
                }        
            }
         }
    }

    public function bulk_admin_notices(){

        $screen = get_current_screen();
        if ( $screen->id != "users" )   // Only add to users.php page
            return;

        $message = '';

        if((isset($_REQUEST['wppcp_user_list_group_submit'])) ) {
            $users = isset($_GET['users']) ?   wp_parse_id_list($_GET['users']) : array() ;
            $user_list_group = isset($_GET['wppcp_user_list_group_top']) ? (int) $_GET['wppcp_user_list_group_top'] : 0;
            if($user_list_group != 0 && count($users) > 0 ) {
                $message = __( 'Users added to group.','wppcp');
            }

        }

        if('' != $message){
            $html = '<div class="updated">
                        <p>'.esc_html($message).'</p>
                    </div>';

            $allowed_html = wppcp_admin_templates_allowed_html();
            echo wp_kses($html, $allowed_html);        
        }
        
    }
        
}


