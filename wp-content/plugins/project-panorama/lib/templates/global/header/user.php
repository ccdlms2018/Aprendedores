<?php if( is_user_logged_in() ): ?>
	<aside class="psp-masthead-user">
		<?php
		$cuser = wp_get_current_user(); ?>
		<p><?php esc_html_e('Hello','psp_projects'); ?> <?php esc_html_e($cuser->display_name); ?>!</p>
		<?php echo get_avatar($cuser->ID); ?>
	</aside>
	<?php
endif; ?>
