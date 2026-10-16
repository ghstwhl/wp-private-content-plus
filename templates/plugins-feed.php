<?php
	global $wppcp,$wpexpert_plugins_data;
	extract($wpexpert_plugins_data);

	foreach($plugins as $plugin){ 
            $plugin = (array) $plugin;
            extract($plugin);   
?>

			<div class="wpexpert-plugins-panel-single">
				<div class="wpexpert-plugins-panel-single-image">
					<img src="<?php echo esc_url($plugin_image); ?>" />
				</div>
				<div class="wpexpert-plugins-panel-single-title">
				<?php echo esc_html($plugin_name); ?>
				</div>
				<div class="wpexpert-plugins-panel-single-desc">
				<?php echo wp_kses_post($plugin_desc); ?>		
				</div>
				<a class="wpexpert-plugins-panel-single-more" target="_blank" href="<?php echo esc_url($plugin_link); ?>"><?php _e('View More','wpexpert'); ?></a>
			</div>

<?php
	}
?>