<?php do_action( 'psp_before_primary_bar' ); ?>
<div id="psp-primary-header" class="psp-grid-row cf">

	<div class="psp-primary-bar">

		<?php
		do_action( 'psp_primary_bar_before_logo' );

		if( psp_get_option('psp_logo') != '' && psp_get_option('psp_logo') != 'http://' ):

			$link = ( psp_get_option('psp_logo_link') ? psp_get_option('psp_logo_link') : get_post_type_archive_link('psp_projects') ); ?>

			<div class="psp-masthead-logo">
				<a href="<?php echo esc_url($link); ?>" class="psp-single-project-logo"><img src="<?php echo psp_get_option('psp_logo'); ?>"></a>
			</div>
		<?php endif; ?>

		<?php
		do_action( 'psp_primary_bar_before_menu' ); ?>

		<nav class="nav psp-masthead-nav" id="psp-main-nav">
			<ul>
				<li id="nav-menu"><a href="#">Menu</a></li>
			</ul>
		</nav>

		<?php
		do_action( 'psp_primary_bar_before_user' );

		include( psp_template_hierarchy( 'global/header/user' ) );

		do_action( 'psp_primary_bar_before_edit' );

		do_action( 'psp_primary_bar_after_edit' ); ?>

	</div>

</div>
<?php
do_action( 'psp_after_primary_bar'); ?>
