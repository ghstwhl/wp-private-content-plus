<?php   global $wppcp_addon_template_data;
        extract($wppcp_addon_template_data); 
?>
<div id="wppcp-addons-feed">
    
    <?php 

        foreach($addons as $addon){ 
            $addon = (array) $addon;
            extract($addon);           
            
            if(in_array($name,$active_plugins)){
                $status = __('Activated','wppcp');
                $status_class = 'activated';
            }else{
                $status = __('Deactivated','wppcp');
                $status_class = 'deactivated'; 
            }
    ?>
            <div class="wppcp-addon-single">
                <div class="wppcp-addon-single-title"><?php echo esc_html($title); ?></div>
                <div class="wppcp-addon-single-image">
                    <img src="<?php echo esc_url($image); ?>" />
                </div>
                <div class="wppcp-addon-single-desc"><?php echo wp_kses_post($desc); ?></div>
                <div class="wppcp-addon-single-buttons">
                    <div class="wppcp-addon-single-status <?php echo esc_attr($status_class); ?> "><?php echo esc_html($status); ?></div>
                    <div class="wppcp-addon-single-type"><?php echo esc_html($type); ?></div>
                    <div class="wppcp-addon-single-get"><a href="<?php echo esc_url($download); ?>"><?php echo __('Purchase','wppcp'); ?></a></div>
                    <div class="wppcp-clear"></div>
                </div>
                
            </div>
    <?php } ?>
    
    
</div>