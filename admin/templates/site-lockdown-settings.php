<?php 
    global $wppcp,$wppcp_site_lockdown_settings_data;
    extract($wppcp_site_lockdown_settings_data);    

?>
<form method="post" action=""  class="wppcp-settings-form">

    <table class="form-table wppcp-settings-list">               

        <tr>
            <th><label for=""><?php echo __('Site Lockdown Status','wppcp'); ?></label></th>
            <td style="width:500px;">
                <select name="wppcp_site_lockdown[lockdown_status]" id="wppcp_site_lockdown_status" class="wppcp-select2-setting" placeholder="<?php _e('Select','wppcp'); ?>" >
                    
                    <option <?php echo selected('disabled',$lockdown_status,false); ?> value="disabled"><?php echo __('Disabled','wppcp'); ?></option>
                    <option <?php echo selected('enabled',$lockdown_status,false); ?> value="enabled"><?php echo __('Enabled','wppcp'); ?></option>
                    
                </select>
                <div class='wppcp-settings-help'><?php _e('This setting is used to define status of entire site lockdown.','wppcp'); ?></div>
            </td>
            
        </tr>

        <tr>
            <th><label for=""><?php echo __('Allowed Posts','wppcp'); ?></label></th>
            <td style="width:500px;">
                <select name="wppcp_site_lockdown[lockdown_allowed_posts][]" id="wppcp_lockdown_allowed_posts" multiple class="wppcp-select2-setting" placeholder="<?php _e('Select','wppcp'); ?>" >
                            
                            <?php 
                                if(is_array($lockdown_allowed_posts)){
                                    foreach ($lockdown_allowed_posts as $post_id) { ?>
                                    <option selected value="<?php echo esc_attr($post_id); ?>"><?php echo esc_html(get_the_title($post_id)); ?></option>
                            <?php }}  ?>
                        </select>
                <div class='wppcp-settings-help'><?php _e('This setting is used to define the posts allowed to visit after locking down entire site.','wppcp'); ?></div>
            </td>
            
        </tr>

        <tr>
            <th><label for=""><?php echo __('Allowed Pages','wppcp'); ?></label></th>
            <td style="width:500px;">
                <select name="wppcp_site_lockdown[lockdown_allowed_pages][]" id="wppcp_lockdown_allowed_pages" multiple class="wppcp-select2-setting" placeholder="<?php _e('Select','wppcp'); ?>" >
                            
                            <?php 
                                if(is_array($lockdown_allowed_pages)){
                                    foreach ($lockdown_allowed_pages as $post_id) { ?>
                                    <option selected value="<?php echo esc_attr($post_id); ?>"><?php echo esc_html(get_the_title($post_id)); ?></option>
                            <?php }}  ?>
                        </select>
                <div class='wppcp-settings-help'><?php _e('This setting is used to define the pages allowed to visit after locking down entire site.','wppcp'); ?></div>
            </td>
            
        </tr>

        <tr>
            <th><label for=""><?php echo __('Allowed URL\'s','wppcp'); ?></label></th>
            <td style="width:500px;">
                <textarea rows="5" style="width:350px" name="wppcp_site_lockdown[allowed_urls]" id="wppcp_site_lockdown_allowed_urls"  ><?php echo esc_html($allowed_urls); ?></textarea>
                <div class='wppcp-settings-help'><?php _e('This setting is used to define the URL\'s allowed to acccess the site without restrictions. Use new line for each URL','wppcp'); ?></div>
            </td>
            
        </tr>  
        <tr>
            <th><label for=""><?php echo __('Redirect URL','wppcp'); ?></label></th>
            <td style="width:500px;">
                <input value="<?php echo esc_url($redirect_url); ?>" type='text' name="wppcp_site_lockdown[redirect_url]" id="wppcp_site_lockdown_redirect_url"  />
                <div class='wppcp-settings-help'><?php _e('This setting is used to define the URL to redirect the users with unauthorised access.','wppcp'); ?></div>
            </td>
            
        </tr>  
                
                                         
                
    <input type="hidden" name="wppcp_site_lockdown[common_status]"  value="1" />                   
    <input type="hidden" name="wppcp_tab" value="<?php echo esc_html($tab); ?>" />    
</table>
    <?php wp_nonce_field( 'wppcp_settings_page_nonce', 'wppcp_settings_page_nonce_field' );  ?>
    <?php submit_button(); ?>
</form>