<?php

$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'getting_started';

$title = sprintf( __( 'Welcome to WP Private Content Plus %s', 'wppcp' ), WPPCP_VERSION ) ;
$desc = __( 'Thank you for choosing WP Private Content Plus.','wppcp');
$desc .= "<a href='https://www.wpexpertdeveloper.com/wp-private-content-plus/' target='_blank'>".__('Visit Plugin Home Page','wppcp')."</a>";

?>

<div class="wrap about-wrap">
	<h1><?php echo esc_html($title); ?></h1>
	<div class="about-text">
		<?php echo wp_kses_post($desc); ?>
	</div>

	<h2 class="nav-tab-wrapper">
		<a class="nav-tab <?php echo ($tab == 'getting_started') ? 'nav-tab-active' : '' ; ?>" href="<?php echo esc_url(admin_url( 'admin.php?page=wppcp-help&tab=getting_started' )); ?>">
			<?php _e( 'Getting Started', 'wppcp' ); ?>
		</a>
		<a class="nav-tab <?php echo ($tab == 'support_docs') ? 'nav-tab-active' : '' ; ?>" href="<?php echo esc_url(admin_url( 'admin.php?page=wppcp-help&tab=support_docs' )); ?>">
			<?php _e( 'Documentation and Support', 'wppcp' ); ?>
		</a>
		
		
	</h2>

	<?php if($tab == 'new_version'){ ?> 
	<div class="wpexpert-help-tab">
		<div class="feature-section">
			<h2><?php _e( 'What\'s New in 1.10', 'wppcp' );?></h2>
			<p><?php  _e('Restrict menu items for specific users', 'wppcp' );?></p>
			<p><?php  _e('Create user groups and add members', 'wppcp' );?></p>
			<p><?php  _e('Restrict post/page/custom post type content by user groups', 'wppcp' );?></p>
		</div>
		
	</div>
	<?php } ?>


	<?php if($tab == 'getting_started'){ ?> 
	<div class="wpexpert-help-tab">
		<div class="feature-section">

			
			<h2><?php _e( 'Private Content Shortcodes', 'wppcp' );?></h2>

			<p><?php _e( 'First, you have to enable Private Content Module by going into the <b>General Settings</b> section. 
			This settings allows you to use content restriction features of this plugin.', 'wppcp' ); ?></p>

			<div class="wpexpert-help-screenshot">
				<img src="<?php echo esc_url( WPPCP_PLUGIN_URL . 'images/docs/settings_1.png'); ?>" />
			</div>


			<h4><?php _e( 'Using Shortcodes', 'wppcp' );?></h4>
			<p><?php _e( 'You can use the private content shortcodes within any post/page/custom post type to retrict content. Place the
			restricted content within opening and closing shortcode tags as shown in the screen. Change the shortcode and parameters based on 
			your requirements', 'wppcp' );?></p>

			<div class="wpexpert-help-screenshot">
				<img src="<?php echo esc_url( WPPCP_PLUGIN_URL . 'images/docs/shortcodes_1.png'); ?>" class="wpexpert-help-screenshot"/>
			</div>

			<h4><?php _e( 'Features and Usage', 'wppcp' );?></h4>
			<p>
				<ul class="wpexpert-help-list">
					<li><a target="_blank" href="https://www.wpexpertdeveloper.com/restrict-posts-pages-custom-post-types/"><?php  _e('Restrict entire posts/pages/custom post types', 'wppcp' );?></a></li>
					<li><a target="_blank" href="https://www.wpexpertdeveloper.com/restrict-content-by-user-roles/"><?php  _e('Restrict content by User roles', 'wppcp' );?></a></li>
					<li><a target="_blank" href="https://www.wpexpertdeveloper.com/restrict-content-guests-members/"><?php  _e('Restrict content for Guest or Members', 'wppcp' );?></a></li>
					<li><a target="_blank" href="https://www.wpexpertdeveloper.com/restrict-content-by-user-role-levels/"><?php  _e('Restrict content by User role Levels', 'wppcp' );?></a></li>
					<li><a target="_blank" href="https://www.wpexpertdeveloper.com/restrict-content-by-capabilities/"><?php  _e('Restrict content by WordPress capabilities', 'wppcp' );?></a></li>
					<li><a target="_blank" href="https://www.wpexpertdeveloper.com/private-page-for-users/"><?php  _e('Private Page for user profiles', 'wppcp' );?></a></li>
					<li><a target="_blank" href="https://www.wpexpertdeveloper.com/restrict-menu-items/"><?php  _e('Restrict menu for members, guests, user roles', 'wppcp' );?></a></li>
					<li><a target="_blank" href="https://www.wpexpertdeveloper.com/restrict-widgets/"><?php  _e('Restrict widgets for members, guests, user roles', 'wppcp' );?></a></li>
					<li><a target="_blank" href="https://www.wpexpertdeveloper.com/restrict-post-attachments-downloads/"><?php  _e('Restrict post attachments and downloads to for members, guests', 'wppcp' );?></a></li>
					<li><a target="_blank" href="https://www.wpexpertdeveloper.com/restrict-content-by-multiple-user-meta-keys/"><?php  _e('Restrict content by multiple user meta keys', 'wppcp' );?></a></li>
					<li><a target="_blank" href="https://www.wpexpertdeveloper.com/restrict-content-by-multiple-user-meta-values/"><?php  _e('Restrict content by multiple user meta values', 'wppcp' );?></a></li>
					<li><a target="_blank" href="https://www.wpexpertdeveloper.com/site-search-restrictions/"><?php  _e('Restrict search content by user types', 'wppcp' );?></a></li>
					<li><a href="https://www.wpexpertdeveloper.com/user-profiles-made-easy-integration/"><?php  _e('Integration with User Profiles Made Easy', 'wppcp' );?></a></li>
					<li><a target="_blank" href="https://www.wpexpertdeveloper.com/protect-entire-site-single-password/"><?php  _e('Global Site Protection with Single Password', 'wppcp' );?></a></li>

				</ul>
			</p>

			<h4><?php _e( 'Video Documentation', 'wppcp' );?></h4>
			<p><ul class="wpexpert-help-list">
				<li><a href="https://www.youtube.com/channel/UCTORWi9WD_qbAotycppJd7A" target="_blank"
				>Youtube Channel</a></li></ul></p>

			

			
		</div>
		
	</div>
	<?php } ?>

	<?php if($tab == 'support_docs'){ ?>
	<div class="wpexpert-help-tab">

		<div class="feature-section">
			<h2><?php _e( 'Documentation', 'wpexpert' );?></h2>

			<p style='text-align:center'>
				<?php _e( 'Complete documentation for this plugin is available at ', 'wppcp' ); ?>
				<a target="_blank" href="https://www.wpexpertdeveloper.com/wp-private-content-plus/"><?php _e('WP Expert Developer','wppcp'); ?></a>.
			</p>

			<h2><?php _e( 'Video Documentation', 'wppcp' );?></h2>

			<p style='text-align:center'>
				<?php _e( 'Video documentation for this plugin is available at ', 'wppcp' ); ?>
				<a target="_blank" href="https://www.youtube.com/channel/UCTORWi9WD_qbAotycppJd7A">WP Expert Developer Youtube Channel</a>.
			</p>

			<h2><?php _e( 'Support', 'wppcp' );?></h2>

			<p style='text-align:center'><?php _e('You can get free support fot this plugin at '); ?>
				<a target="_blank" href="https://wordpress.org/support/plugin/wp-private-content-plus"><?php _e('wordpress.org','wppcp');?></a>.
			</p>


		</div>
	</div>
	<?php } ?>

	

</div>
