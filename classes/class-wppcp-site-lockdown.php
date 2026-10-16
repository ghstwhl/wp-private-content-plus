<?php
class WPPCP_Site_Lockdown{

	public function __construct(){
		add_action('wppcp_admin_menu_pages',array($this, 'admin_menu_pages'));

		add_action('wppcp_custom_plugin_options_tab_content', array($this, 'tab_content'),10,3);
		
		add_action('wppcp_plugin_options_tabs', array($this, 'option_tabs'),10,2);

		add_action('wppcp_save_settings_page', array($this, 'save_settings'),10,2);

		add_action('template_redirect', array($this, 'check_site_lockdown'));
        

	}

	public function admin_menu_pages($params){
		add_submenu_page('wppcp-settings', __('Site Lockdown', 'wppcp' ), __('Site Lockdown', 'wppcp'), 'manage_options' ,'wppcp-site-lockdown-settings-page', array($this,'site_lockdown_settings'));
	}

	public function tab_content($tab,$private_content_settings,$params){
		global $wppcp_site_lockdown_settings_data,$wppcp;
		if($tab == 'wppcp_section_site_lockdown'){
			$data = isset($private_content_settings['site_lockdown']) ? $private_content_settings['site_lockdown'] : array();

	        $wppcp_site_lockdown_settings_data['tab'] = $tab;
	        
	        $wppcp_site_lockdown_settings_data['lockdown_status'] = isset($data['lockdown_status']) ? $data['lockdown_status'] : 'disabled';
	        $wppcp_site_lockdown_settings_data['lockdown_allowed_pages'] = isset($data['lockdown_allowed_pages']) ? (array) $data['lockdown_allowed_pages'] : array();
	        $wppcp_site_lockdown_settings_data['lockdown_allowed_posts'] = isset($data['lockdown_allowed_posts']) ? (array) $data['lockdown_allowed_posts'] : array();
	        $wppcp_site_lockdown_settings_data['allowed_urls'] = isset($data['allowed_urls']) ? $data['allowed_urls'] : '';
	        $wppcp_site_lockdown_settings_data['redirect_url'] = isset($data['redirect_url']) ? $data['redirect_url'] : site_url();

	        $wppcp_site_lockdown_settings_data = apply_filters('wppcp_site_lockdown_settings_data',$wppcp_site_lockdown_settings_data, array('data' => $data, 'section' => 'wppcp_section_site_lockdown' ) );



	        $wppcp->template_loader->get_template_part('site-lockdown-settings'); 
		}
		 
	}

	public function site_lockdown_settings(){
        global $wppcp,$wppcp_settings_data;
        
        add_settings_section( 'wppcp_section_site_lockdown', __('Site Lockdown','wppcp'), array( &$this, 'wppcp_section_general_desc' ), 'wppcp-site-lockdown' );
        
        
        $tab = isset( $_GET['tab'] ) ? sanitize_title ( $_GET['tab'] ) : 'wppcp_section_site_lockdown';
        $wppcp_settings_data['tab'] = $tab;
        
        $tabs = $wppcp->settings->plugin_options_tabs('site_lockdown',$tab);
   
        $wppcp_settings_data['tabs'] = $tabs;
        
        $tab_content = $wppcp->settings->plugin_options_tab_content($tab);
        $wppcp_settings_data['tab_content'] = $tab_content;
        
        $wppcp->template_loader->get_template_part( 'menu-page-container');

    }

    public function option_tabs($type,$params){
    	global $wppcp;

    	if($type == 'site_lockdown'){
    		$wppcp->settings->plugin_settings_tabs['wppcp_section_site_lockdown']  = __('Site Lockdown Settings','wppcp');
    	}
    }

    public function save_settings($tab,$params){
        global $wppcp;
        $this->settings = array();
        if(isset($_POST['wppcp_site_lockdown'])){
            
            foreach($_POST['wppcp_site_lockdown'] as $k=>$v){
                switch ($k) {
                    case 'lockdown_allowed_pages':
 					case 'lockdown_allowed_posts':
                        if(is_array($v)){
                            $post_arr = array();
                            foreach ($v as $post_ids) {
                                array_push($post_arr, (int) $post_ids);
                            }
                            $this->settings[$k] = $post_arr;
                        }
                        break;
                    case 'lockdown_status':
                    	$v = sanitize_text_field($v);
                        $this->settings[$k] = $v;
                        break;
                    case 'allowed_urls':
                        $v = sanitize_textarea_field($v);
                        $this->settings[$k] = $v;
                        break;
                    case 'redirect_url':
                        $v = esc_url_raw($v);
                        $this->settings[$k] = $v;
                        break;
                }
                
            }     
                     
        }
        
        $wppcp_options = get_option('wppcp_options');
        $wppcp_options['site_lockdown'] = $this->settings;

        update_option('wppcp_options',$wppcp_options);
        add_action( 'admin_notices', array( $wppcp->settings, 'admin_notices' ) ); 
    }

    public function check_site_lockdown(){
        global $wppcp,$pagenow;

        $private_content_settings  = get_option('wppcp_options');
        if(!isset($private_content_settings['general']['private_content_module_status'])){
            return;        
        }

        $lockdown_settings = isset($wppcp->settings->wppcp_options['site_lockdown']) ? $wppcp->settings->wppcp_options['site_lockdown'] : array();
        $lockdown_settings['lockdown_status'] = isset($lockdown_settings['lockdown_status']) ? $lockdown_settings['lockdown_status'] : 'disabled';

        if( $lockdown_settings['lockdown_status'] != 'enabled'){
            return;
        }

        if(is_feed()){
            return;
        }

        if (is_user_logged_in ()) {
            return;
        }else{
            $this->user_id = 0;
        }

        $redirect_url = isset($lockdown_settings['redirect_url']) ? $lockdown_settings['redirect_url'] : '';
        if(trim($redirect_url) == ''){
            $redirect_url = site_url();
        }

        // Add globally skipped URL's, pages and posts
        $skipped_urls = array( strtok(rtrim($redirect_url,"/"), '?') , strtok(rtrim(wp_login_url(),"/"),'?'), strtok(rtrim(wp_registration_url(),"/"),'?') , strtok(rtrim(wp_lostpassword_url(),"/"),'?') );
        $skipped_pages = isset($lockdown_settings['lockdown_allowed_pages']) ? (array) $lockdown_settings['lockdown_allowed_pages'] : array();
        
        foreach ($skipped_pages as $page_id) {
           if($page_id != '0' && $page_id != ''){
                array_push($skipped_urls, strtok(rtrim(get_permalink( $page_id ),"/"),'?') );
           }
        }

        $skipped_posts = isset($lockdown_settings['lockdown_allowed_posts']) ? (array) $lockdown_settings['lockdown_allowed_posts'] : array() ;
        foreach ($skipped_posts as $page_id) {
           if($page_id != '0' && $page_id != ''){
                array_push($skipped_urls, strtok(rtrim(get_permalink( $page_id ),"/"),'?') );
           }
        }

        $lockdown_settings_allowed_urls = isset($lockdown_settings['allowed_urls']) ? $lockdown_settings['allowed_urls'] : '';
        $skipped_custom_urls = explode(PHP_EOL, $lockdown_settings_allowed_urls);
        foreach ($skipped_custom_urls as $url) {
            if($url != ''){
                array_push($skipped_urls, rtrim($url,"/"));
            }
        }

        $current_page_url = rtrim(wppcp_current_page_url(),"/");

        $parsed_url = parse_url($current_page_url);
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
 
        $current_page_url = rtrim($scheme.$user.$pass.$host.$port.$path,"/");
        if(in_array($current_page_url, $skipped_urls)){
            return;
        }else{

            // Check for URL exceptions in admin area                
            if('wp-login' == $redirect_url){
                $url = add_query_arg( 'redirect_to', $current_page_url, wp_login_url() );
                wp_redirect(esc_url($url));
                
            }else{
                
                $url = add_query_arg( 'redirect_to', $current_page_url, ($redirect_url) );
                wp_redirect(esc_url($url));
            }             
            exit;
           
        }        

    }
}