<?php
    global $menu, $submenu, $wppcp_settings_data,$wppcp;
    extract($wppcp_settings_data);

    $user_roles = $wppcp->roles_capability->wppcp_user_roles();

    $admin_menu_visibility = esc_html($admin_menu_visibility);
?>
<table class="form-table wppcp-settings-list">
  <tr>
    <th><?php echo __('Visibility','wppcp'); ?></th>
    <td>
      <select name='wppcp_admin_menu_item_visibility' id='wppcp_admin_menu_item_visibility'>
        <option <?php selected('0', $admin_menu_visibility); ?>  value="0"><?php echo __('Default Permissions','wppcp'); ?></option>
        <option <?php selected('user_roles', $admin_menu_visibility); ?>  value="user_roles"><?php echo __('By User Role','wppcp'); ?></option>
      </select>
    </td>
  </tr>
  <tr>
    <th><?php echo __('User Roles','wppcp'); ?></th>
    <td>
      <?php foreach($user_roles as $role_key => $role){
              $checked_val = ''; 
              if( in_array($role_key, $admin_menu_roles ) ){
                  $checked_val = ' checked ';   
              }
              if($role_key != 'administrator'){
          ?>
          <input type="checkbox" <?php echo esc_attr($checked_val); ?> class="wppcp_admin_menu_item_roles" id="wppcp_admin_menu_item_roles" name="wppcp_admin_menu_item_roles[]" value='<?php echo esc_attr($role_key); ?>'><?php echo esc_html($role); ?><br/>
          <?php } ?>  
      <?php } ?>
    </td>
  </tr>
  <tr>
    <th>&nbsp;</th>
    <td>
      <input type='hidden' name='wppcp_admin_menu_item_slug' id='wppcp_admin_menu_item_slug' value='<?php echo esc_html($admin_menu_slug); ?>' />
      <input type='button' name='wppcp_admin_menu_item_submit' id='wppcp_admin_menu_item_submit'
        value="<?php echo __('Save','wppcp'); ?>"  />        
    </td>
  </tr>
</table>
