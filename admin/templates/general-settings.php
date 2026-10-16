<?php 
    global $wppcp_settings_data; 
    extract($wppcp_settings_data);


    $checked_private_content_module_status = '';
    if(isset($private_content_module_status)){
        $checked_private_content_module_status = checked( '1', $private_content_module_status, false );
    }

    

    $checked_search_restrictions_status = '';
    if(isset($search_restrictions_module_status)){
        $checked_search_restrictions_status = checked( '1', $search_restrictions_module_status, false );
    }

    $checked_dashboard_restrictions_widget_status = '';
    if(isset($dashboard_restrictions_widget_status)){
        $checked_dashboard_restrictions_widget_status = checked( '1', $dashboard_restrictions_widget_status, false );
    }

    $checked_author_post_page_restrictions_status = '';
    if(isset($author_post_page_restrictions_status)){
        $checked_author_post_page_restrictions_status = checked( '1', $author_post_page_restrictions_status, false );
    }


    
?>

<div id="wppcp-main-settings-panel">
<form method="post" action="" class="wppcp-settings-form">
<table class="form-table wppcp-settings-list">

                <tr >
                    <th colspan='2' style="width:500px;">
                        <div class='wppcp-settings-section-header'><?php _e('License Details','wppcp'); ?></div>
                    </th>
                    
                </tr> 

                <tr>
                    <th><label for=""><?php echo __('License Key','wppcp'); ?></label>
                            </th>
                    <td style="width:500px;">
                        <div><?php _e('You are using <strong>WP Private Content Plus</strong>. No license key is required.','wppcp'); ?></div>
                        <div><?php _e('To unlock PRO features, pro updates and priority support, consider.','wppcp'); ?>
                            <br/><a class='wppcp-upgrading-pro-button' href="https://www.wpexpertdeveloper.com/wp-private-content-pro/#plus_license_key"><strong><?php _e('Upgrading to PRO License','wppcp'); ?></strong></a></div>
                    </td>
                    
                </tr> 

                <tr >
                    <th colspan='2' style="width:500px;">
                        <div class='wppcp-settings-section-header'><?php _e('General Settings','wppcp'); ?></div>
                    </th>
                    
                </tr>

                <tr>
                    <th><label for=""><?php echo __('Enable Private Content Module','wppcp'); ?></label></th>
                    <td style="width:500px;">
                        <input type="checkbox" name="wppcp_general[private_content_module_status]" <?php echo esc_attr($checked_private_content_module_status); ?> value="1" /><br/>
                        <div class='wppcp-settings-help'><?php _e('This setting is used to enable/disable features of this plugin. Once its disabled, all the restrictions and 
                    shortcodes applied from this plugin will be disabled.','wppcp'); ?></div>
                    </td>
                    
                </tr> 


                <tr>
                    <th><label for=""><?php echo __('Post/Page Restriction Redirect URL','wppcp'); ?></label></th>
                    <td style="width:500px;">
                        <input type="text" name="wppcp_general[post_page_redirect_url]"  value="<?php echo esc_url($post_page_redirect_url); ?>" /><br/>
                        <div class='wppcp-settings-help'><?php _e('This setting is used to specify the redirect URL for restrictions setup in posts/pages/custom post types meta box.','wppcp'); ?></div>
                    </td>
                    
                </tr>

                    
                <tr>
                    <th><label for=""><?php echo __('Enable Search Restrictions Module','wppcp'); ?></label></th>
                    <td style="width:500px;">
                        <input type="checkbox" name="wppcp_general[search_restrictions_module_status]" <?php echo esc_attr($checked_search_restrictions_status); ?> value="1" /><br/>
                        <div class='wppcp-settings-help'><?php _e('This setting is used to enable/disable search restriction settings of this plugin. Once its disabled, all the search restrictions and 
                    shortcodes applied from this plugin will be disabled.','wppcp'); ?></div>
                    </td>
                    
                </tr> 

                <tr>
                    <th><label for=""><?php echo __('Enable Dasboard Restriction Stats','wppcp'); ?></label></th>
                    <td style="width:500px;">
                        <input type="checkbox" name="wppcp_general[dashboard_restrictions_widget_status]" <?php echo esc_attr($checked_dashboard_restrictions_widget_status); ?> value="1" /><br/>
                        <div class='wppcp-settings-help'><?php _e('This setting is used to enable/disable restriction stats widget on dashboard.','wppcp'); ?></div>
                    </td>
                    
                </tr> 

                <tr>
                    <th><label for=""><?php echo __('Enable Author Post/Page Restrictions','wppcp'); ?></label></th>
                    <td style="width:500px;">
                        <input type="checkbox" name="wppcp_general[author_post_page_restrictions_status]" <?php echo esc_attr($checked_author_post_page_restrictions_status); ?> value="1" /><br/>
                        <div class='wppcp-settings-help'><?php _e('This setting is used to enable/disable restrictions for post/page authors.','wppcp'); ?></div>
                    </td>
                    
                </tr> 
                        
                
    <input type="hidden" name="wppcp_general[private_mod]"  value="1" />                        
    <input type="hidden" name="wppcp_tab" value="<?php echo esc_html($tab); ?>" />    
</table>
    <?php wp_nonce_field( 'wppcp_settings_page_nonce', 'wppcp_settings_page_nonce_field' );  ?> 
    <?php submit_button(); ?>
</form>

</div>
<div id="wppcp-main-settings-sidebar">
    <?php echo wp_kses_post(wppcp_display_pro_sidebar_info_box()); ?>
</div>
<div class="wppcp-clear"></div>