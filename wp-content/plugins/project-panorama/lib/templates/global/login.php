<?php
do_action( 'login_head' );
do_action( 'login_enqueue_scripts' );
?>

<div id="overview" class="psp-comments-wrapper">

	<?php if( ( psp_get_option( 'psp_logo' ) != '' ) && ( psp_get_option( 'psp_logo' ) != 'http://' ) ) { ?>
		<div class="psp-login-logo">
			<img src="<?php echo psp_get_option( 'psp_logo' ); ?>">
		</div>
	<?php } ?>

	<?php do_action( 'psp_login_form_before' ); ?>

	<div id="psp-login">
		<h2><?php apply_filters( 'psp_login_form_title', psp_the_login_title() ); ?></h2>

		<?php if( ( isset($_GET['login']) && ( $_GET['login'] == 'failed' ) ) ) { ?>
			<div class="psp-login-error">
				<p><?php esc_html_e( 'Incorrect username or password.', 'psp_projects'); ?><br> <?php esc_html_e( 'Please try again', 'psp_projects' ); ?></p>
			</div>
		<?php } ?>

		<?php do_action( 'psp_login_form_content' ); ?>

		<?php
		if( !is_user_logged_in() ):

			$password_required = post_password_required();

			echo panorama_login_form( $password_required );

		else:

			echo "<p>" . __( 'You don\'t have permission to access this project' , 'psp_projects' ) . "</p>";

		endif; ?>
	</div>

	<?php do_action( 'psp_login_form_after' ); ?>

	<p class="psp-text-center"><a href="<?php echo esc_url(wp_lostpassword_url(site_url().$_SERVER['REQUEST_URI'])); ?>"><?php esc_html_e( 'Lost your password?', 'psp_projects' ); ?></a></p>

</div>
<?php do_action( 'login_footer' ); ?>
