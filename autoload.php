<?php

/**
 * Auto-load Dictator classes
 */

function wpe_cli_autoloader( $class ) {

	if ( $class[0] === '\\') {
		$class = substr( $class, 1 );
	}

	if ( 0 !== strpos( $class, 'WPE_CLI' ) ) {
		return;
	}

	$file_parts = explode( '\\', str_replace( '_', '-', strtolower( $class ) ) );
	array_shift( $file_parts );
	$file_name = array_pop( $file_parts );
	$file_name = 'class-' . $file_name . '.php';

	$file_path = dirname( __FILE__ ) . '/php/' . implode( '/', $file_parts ) . '/' . $file_name;
	if ( is_file( $file_path ) ) {
		require $file_path;
	}

}

spl_autoload_register( 'wpe_cli_autoloader' );
