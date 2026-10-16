<?php
	global $wppcp,$post_page_restriction_params;
	extract($post_page_restriction_params);

	$user_roles = $wppcp->roles_capability->wppcp_user_roles();
	$post_type = $post->post_type;

  $visibility = get_post_meta( $post->ID, '_wppcp_post_page_author_visibility', true );
  $post_page_author_redirection_url = get_post_meta( $post->ID, '_wppcp_post_page_author_redirection_url', true );

?>

<div class="wppcp_post_meta_row">
	<div class="wppcp_post_meta_row_label"><strong><?php _e('Limit Access to Author','wppcp'); ?></strong></div>
	<div class="wppcp_post_meta_row_field">
		<select id="wppcp_post_page_author_visibility" name="wppcp_post_page_author_visibility" class="wppcp-select2-setting">
			<option value='no' <?php selected('no',$visibility); ?> ><?php _e('No','wppcp'); ?></option>
			<option value='yes' <?php selected('yes',$visibility); ?> ><?php _e('Yes','wppcp'); ?></option>			
		</select>
	</div>
</div>
<div class="wppcp-clear"></div>

<div class="wppcp_post_meta_row">
	<div class="wppcp_post_meta_row_label"><strong><?php _e('Redirection URL','wppcp'); ?></strong></div>
	<div class="wppcp_post_meta_row_field">
		<input type='text' id="wppcp_post_page_author_redirection_url" name="wppcp_post_page_author_redirection_url" value="<?php echo esc_url($post_page_author_redirection_url); ?>" />
			
	</div>
</div>
<div class="wppcp-clear"></div>

<?php wp_nonce_field( 'wppcp_restriction_settings', 'wppcp_restriction_settings_nonce' ); ?>

