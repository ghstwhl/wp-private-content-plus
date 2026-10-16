<?php

    global $menu, $submenu, $wppcp_settings_data,$wppcp;
    extract($wppcp_settings_data);
    $user_roles = $wppcp->roles_capability->wppcp_user_roles();
    if ( ! isset( $menu ) || empty( $menu ) ) {
      return;
    }

?>

<div id="wppcp-admin-menu-permission-panel">
	<div id="wppcp-admin-menu-permission-list">
		<?php 

			foreach ( $menu as $key => $item ) { 
				
				if ( isset( $item[ 2 ] ) ) {
          $menu_slug = $item[ 2 ];
        }

        if($item[0] != ''){
				?>
				<div data-key="<?php echo esc_attr($key); ?>" class="wppcp-admin-menu-permission-level1" data-slug="<?php echo esc_attr($menu_slug); ?>" >
					<?php echo wp_kses_post($item[0]); ?>
				</div>
		<?php
				}

				if ( isset( $submenu ) && ! empty( $submenu[ $menu_slug ] ) ) { 
    ?>
        <div id="wppcp-admin-menu-<?php echo esc_attr($key); ?>" class="wppcp-admin-menu-permission-level1-panel">
    <?php
        foreach ( (array) $submenu[ $menu_slug ] as $subindex => $subitem ) { 
    ?>

          <div class="wppcp-admin-menu-permission-level2" data-slug="<?php echo esc_attr($subitem[2]); ?>" >
						<?php echo wp_kses_post($subitem[0]); ?>
					</div>
			<?php			
	        }
      ?>
        </div>
      <?php
	      }
			}
		?>
	</div>
	<div id="wppcp-admin-menu-permissions">
    <div id="wppcp-admin-menu-permissions-info"><?php echo __("Click on the menu items to assign permissions. This feature only allows you to restrict backend 
      features to users based on the existing permissions. You can't use it to assign features to users.","wppcp"); ?></div>
	</div>
</div>

    

