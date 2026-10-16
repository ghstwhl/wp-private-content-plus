<?php
	global $wppcp,$woo_tabs_restriction_params;
	extract($woo_tabs_restriction_params);

	$user_roles = $wppcp->roles_capability->wppcp_user_roles();
	$post_type = $post->post_type;

    $visibility = esc_html(get_post_meta( $post->ID, '_wppcp_woo_tabs_visibility', true ));
    $redirection_url = get_post_meta( $post->ID, '_wppcp_woo_tabs_redirection_url', true );

    $visible_roles = get_post_meta( $post->ID, '_wppcp_woo_tabs_roles', true );
    if(!is_array($visible_roles)){
    	$visible_roles = array();
    }
    

    $show_role_field = '';
    if( $visibility == 'role'){
    	$show_role_field = " style='display:block;' ";
    }

?>

<div class="wppcp_post_meta_row">
	<div class="wppcp_post_meta_row_label"><strong><?php _e('Visibility','wppcp'); ?></strong></div>
	<div class="wppcp_post_meta_row_field">
		<select id="wppcp_woo_tabs_visibility" name="wppcp_woo_tabs_visibility" class="wppcp-select2-setting">
			<option value='none' <?php selected('none',$visibility); ?> ><?php _e('Please Select','wppcp'); ?></option>
			<option value='all' <?php selected('all',$visibility); ?> ><?php _e('Everyone','wppcp'); ?></option>
			<option value='guest' <?php selected('guest',$visibility); ?> ><?php _e('Guests','wppcp'); ?></option>
			<option value='member' <?php selected('member',$visibility); ?> ><?php _e('Members','wppcp'); ?></option>
			<option value='role' <?php selected('role',$visibility); ?> ><?php _e('Selected User Roles','wppcp'); ?></option>
			
		</select>
	</div>
</div>
<div class="wppcp-clear"></div>

<div id="wppcp_woo_tabs_role_panel" class="wppcp_post_meta_row" <?php echo esc_attr($show_role_field); ?> >
	<div class="wppcp_post_meta_row_label"><strong><?php _e('Allowed User Roles','wppcp'); ?></strong></div>
	<div class="wppcp_post_meta_row_field">
		<?php foreach($user_roles as $role_key => $role){
				$checked_val = ''; 

				if(in_array($role_key, $visible_roles)  ){
					$checked_val = ' checked '; 
	
				}
				if($role_key != 'administrator'){
			?>
			<input type="checkbox" <?php echo esc_attr($checked_val); ?> name="wppcp_woo_tabs_roles[]" value='<?php echo esc_attr($role_key); ?>'><?php echo esc_html($role); ?><br/>
			<?php } ?>	
		<?php } ?>		
	</div>

</div>


<div class="wppcp-clear"></div>

<?php wp_nonce_field( 'wppcp_restriction_settings', 'wppcp_restriction_settings_nonce' ); ?>

