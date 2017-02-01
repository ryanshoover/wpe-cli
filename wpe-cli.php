<?php
/*
Plugin Name: WPE CLI
Description: Provides wp-cli access to WPE installs. Definitely an unsupported feature of WP Engine.
Author: Ryan Hoover
Version: 0.1
Author URI: https://ryan.hoover.ws
*/

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

define( 'WPE_CLI', true );

/**
 * Some files need to be manually loaded
 */
require_once dirname( __FILE__ ) . '/autoload.php';
require_once dirname( __FILE__ ) . '/php/class-wpe-cli.php';
require_once dirname( __FILE__ ) . '/php/class-wpe-cli-command.php';
