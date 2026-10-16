<?php 
    global $wppcp_settings_data,$wppcp; 
    extract($wppcp_settings_data);
    $user_roles = $wppcp->roles_capability->wppcp_user_roles();

   
?>
vfvbfd
<div id="wppcp-main-settings-panel">
<form method="post" action="" class="wppcp-settings-form">
    <table class="form-table wppcp-settings-list">
        <tr id="">
            <th><label for=""><?php echo __('Type','wppcp'); ?></label></th>
            <td style="width:500px;">
                <select id="wppcp_bulk_private_page_upload_type" name="wppcp_bulk_private_page_upload_type" class="wppcp-select2-setting">
                    <option value='none'  ><?php _e('Please Select','wppcp'); ?></option>
                    <option value='users' ><?php _e('Selected Users','wppcp'); ?></option>
                    
            
                </select>
                
                <div class='wppcp-settings-help'><?php _e('This setting is used to specify the users type adding same content for the private user page.','wppcp'); ?></div>
           
        </td>
        </tr> 
        <tr id="wppcp_bulk_private_page_users_panel">
            <th><label for=""><?php echo __('Select Users','wppcp'); ?></label></th>
            <td style="width:500px;">
                <select name="wppcp_bulk_private_page_upload_users[]" id="wppcp_bulk_private_page_users" multiple class="wppcp-select2-setting" placeholder="<?php _e('Select','wppcp'); ?>" >
                    
                </select>   
                
                <div class='wppcp-settings-help'><?php _e('This setting is used to specify the users for adding same content for the private user page.','wppcp'); ?></div>
           
        </td>
        </tr>
        <tr id="">
            <th><label for=""><?php echo __('Private Page Content','wppcp'); ?></label></th>
            <td style="width:500px;">        
                <?php 

                wp_editor("", 'wppcp_bulk_private_page_upload_content'); ?>
                <div class='wppcp-settings-help'><?php _e('This setting is used to specify the content for the private user page.','wppcp'); ?></div>
           
            </td>
        </tr>          
                
    <input type="hidden" name="wppcp_bulk_private_page_upload_mod"  value="1" />                        
       
</table>
    <?php wp_nonce_field( 'wppcp_settings_page_nonce', 'wppcp_settings_page_nonce_field' );  ?> 
    <?php submit_button(); ?>
</form>

</div>
<div id="wppcp-main-settings-sidebar">
    <?php echo wp_kses_post(wppcp_display_pro_sidebar_info_box()); ?>
</div>
<div class="wppcp-clear"></div>