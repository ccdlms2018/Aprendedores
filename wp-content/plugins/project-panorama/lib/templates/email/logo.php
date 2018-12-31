<?php
/**
 * Logo email template.
 *
 * @since {{VERSION}}
 *
 * @var string $logo Logo image URI.
 */

defined( 'ABSPATH' ) || die();

$settings = get_option('psp_settings');

if ( $logo && $settings['psp_include_logo'] == 1 ) : ?>

	<?php
	$styles = array(
		'display'    => 'block',
		'max-width'  => '200px',
		'text-align' => 'center',
		'height'     => 'autor',
		'margin'     => '0 auto 30px auto',
	);
	?>

	<img src="<?php echo $logo; ?>" style="<?php echo psp_build_style( $styles ); ?>" alt="">

<?php endif;
