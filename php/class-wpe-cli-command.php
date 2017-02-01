<?php

class WPE_CLI_Command extends WP_CLI_Command {

	public function cli( $args, $assoc_args ) {
		$install = array_shift( $args );

		$environment = ! empty( $assoc_args['environment'] ) ? $assoc_args['environment'] : 'production';

		$command = implode( ' ', $args );

		foreach ( $assoc_args as $key => $val ) {
			if ( true === $val ) {
				$command .= " --{$key}";
			} else {
				$command .= " --{$key}={$val}";
			}
		}

		$url = "https://my.wpengine.com/installs/{$install}/wp_cli?environment={$environment}";

		$token = '7mLdItRR5J2CpPexM1FPmnJ9YhjOXvPy8AAMdm5ys1n28W9NJX/JxhbhUbTJMawpi4UtZ5vzod9s90XPAfhe9w==';

		$arv4 = 'TAHWBEST55E5TJYIHVPHVJ%3A20170201%3A25%7C5CW3DDC2HFD6PG3HGA4GUM%3A20170201%3A25%7C66DS7TWRAJCDVGEJKPFSTO%3A20170201%3A6%7CO52ALOLRLRBPBEREO22RZS%3A20170201%3A19';

		$session = '17db8c633de93b2abc34fc21a539e43f';

		$cookies = array();

		$cookies[] = new WP_Http_Cookie( [ 'name' => '__ar_v4', 'value' => $arv4 ] );
		$cookies[] = new WP_Http_Cookie( [ 'name' => '_session_id', 'value' => $session ] );

		$post_args = array(
			'headers' => array(
				'X-CSRF-Token' => $token,
				),
			'cookies' => $cookies,
			'body'    => [ 'command' => $command ],
			);

		$res = wp_remote_post( $url, $post_args );

		if ( is_wp_error( $res ) ) {
			WP_CLI::error( 'Something went wrong' . PHP_EOL . print_r( $res, true ) );
			return;
		}

		if ( 300 < $res['response']['code'] ) {
			WP_CLI::error( 'Got an invalid response: ' . $res['response']['code'] . ' ' . $res['response']['message'] );
			return;
		}

		$message = json_decode( $res['body'] );

		WP_CLI::success( $message->response );
	}

	public function save( $args, $assoc_args ) {

	}

}

WP_CLI::add_command( 'wpe', 'WPE_CLI_Command' );
