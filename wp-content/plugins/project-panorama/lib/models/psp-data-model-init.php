<?php
/**
 * psp-data-model.php
 *
 * Simplifying, loads the data reorganized data models
 *
 * @category controller
 * @package psp-projects
 * @author Ross Johnson
 * @version 1.0
 * @since 1.3.6
 */

$libs = array(
	'psp-projects',
	'psp-project-types',
	'psp-teams',
	'psp-notifications',
	'psp-priorities'
);

foreach( $libs as $lib ) {

	require_once( $lib . '.php' );

}
