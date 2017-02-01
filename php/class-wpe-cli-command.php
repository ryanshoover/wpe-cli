<?php

class WPE_CLI_Command extends WP_CLI_Command {

	/**
     * Runs a command on WP Engine installs
     *
     * ## OPTIONS
     *
     * <install>
     * : The name of the install to run the command on
     *
     * <command>
     * : The actual wp-cli command to run
     *
     * [--environment=<type>]
     * : Whether to run this in production or staging
     * ---
     * default: production
     * options:
     *   - production
     *   - staging
     * ---
     *
     * ## EXAMPLES
     *
     *     wp wpe myinstall core version
     *     wp wpe myinstall plugin update --all --environment=staging
     *
     * @when after_wp_load
     */
	public function __invoke( $args, $assoc_args ) {
		$config = $this->get_config();

		if ( empty( $config ) ) {
			WP_CLI::error( 'Please set the wpe-cli config values in your config.yml file' );
		}

		$install = array_shift( $args );

		$environment = \WP_CLI\Utils\get_flag_value( $assoc_args, 'environment', 'production' );

		unset( $assoc_args['environment'] );

		$command = \WP_CLI\Utils\args_to_str( $args );

		$command .= \WP_CLI\Utils\assoc_args_to_str( $assoc_args );

		$url = "https://my.wpengine.com/installs/{$install}/wp_cli?environment={$environment}";

		$cookies = array();

		$cookies[] = new WP_Http_Cookie( [ 'name' => '__ar_v4', 'value' => $config['ar_v4'] ] );
		$cookies[] = new WP_Http_Cookie( [ 'name' => '_session_id', 'value' => $config['session_id'] ] );

		$post_args = array(
			'timeout' => 30,
			'headers' => array(
				'X-CSRF-Token' => $config['token'],
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

	protected function get_config() {
		$full_config = \WP_CLI::get_configurator()->to_array();

		$config = ! empty( $full_config[1]['wpe-cli'] ) ? $full_config[1]['wpe-cli'] : [];

		return $config;
	}
}

WP_CLI::add_command( 'wpe', 'WPE_CLI_Command' );
