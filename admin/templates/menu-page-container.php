<?php 
    global $wppcp_settings_data; 
    extract($wppcp_settings_data);

    $allowed_html = wppcp_admin_templates_allowed_html();
        echo wp_kses($display, $allowed_html); 
?>

<div class="wrap">
    <?php echo wp_kses($tabs, $allowed_html); ?> 
    <?php echo wp_kses($tab_content, $allowed_html); ?> 
</div>

