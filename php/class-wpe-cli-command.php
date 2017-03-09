<?php

class WPE_CLI_Command extends WP_CLI_Command {

	protected $base_url = '';
	protected $environment = '';

	/**
	 * Runs a command on WP Engine installs.
	 *
	 * ## OPTIONS
	 *
	 * <install>
	 * : The WP Engine install to run the command on.
	 *
	 * <command>
	 * : The command to run on the install.
	 *
	 * [<field>]
	 * : Any additional wp-cli commands needed
	 *
	 * [--staging]
	 * : Run the command on the staging environment.
	 *
	 * [--<field>=<value>]
	 * : Include any wp-cli specific requests.
	 *
	 * ## EXAMPLES
	 *
	 *     # Get the WordPress version of your install
	 *     $ wp wpe myinstall core version
	 *     Success: 4.7.2
	 *
	 *     # Update all of your install's plugins on staging
	 *     $ wp wpe myinstall plugin update --all --staging
	 *     Success:
	 *     wp wpe myinstall flush
	 *
	 * @when after_wp_load
	 */
	public function __invoke( $args, $assoc_args ) {
		$this->config = $this->get_config();

		$install = array_shift( $args );

		$this->base_url = "https://my.wpengine.com/installs/{$install}";

		$this->environment = \WP_CLI\Utils\get_flag_value( $assoc_args, 'staging' ) ? 'staging' : 'production';

		unset( $assoc_args['staging'] );

		if ( empty( $args ) ) {
			WP_CLI::error( 'Please provide a command to execute' );
		}

		switch ( $args[0] ) {
			case 'backup' :
				$settings = $this->get_backup_settings( $assoc_args );
			break;

			case 'flush' :
				$settings = $this->get_flush_settings();
			break;

			default :
				$settings = $this->get_wp_cli_settings( $args, $assoc_args );
		};

		$res = wp_remote_post( $settings['url'], $settings['post_args'] );

		// If the response is a WP_ERROR, then something went very wrong
		if ( is_wp_error( $res ) ) {
			WP_CLI::error( 'Something went wrong' . PHP_EOL . print_r( $res, true ) );
			return;
		}

		// If the response code is not in the 200s, something went wrong
		if ( 300 <= $res['response']['code'] ) {
			WP_CLI::error( 'Got an invalid response: ' . $res['response']['code'] . ' ' . $res['response']['message'] );
			return;
		}

		$json_res = json_decode( $res['body'] );

		if ( $json_res && ! empty( $json_res->response ) ) {
			WP_CLI::log( $json_res->response );
		} else {
			WP_CLI::success( 'Finished!' );
		}
	}

	protected function get_backup_settings( $assoc_args ) {
		$settings = array();

		$settings['url'] = "{$this->base_url}/backup_points";
		$settings['post_args'] = $this->get_default_post_args();

		$settings['post_args']['body'] = array(
			'checkpoint' => array(
				'environment' => $this->environment,
				'comment' => \WP_CLI\Utils\get_flag_value( $assoc_args, 'message', 'Triggered by wpe-cli' ),
				'notification_emails' => \WP_CLI\Utils\get_flag_value( $assoc_args, 'emails' ),
				),
			'commit' => "Create {$this->environment} backup",
			);

		return $settings;
	}

	protected function get_flush_settings() {
		$settings = array();

		$settings['url'] = "{$this->base_url}/utilities/clear_cache";
		$settings['post_args'] = $this->get_default_post_args();

		return $settings;
	}

	protected function get_wp_cli_settings( $args, $assoc_args ) {
		$command  = '';
		$command .= \WP_CLI\Utils\args_to_str( $args );
		$command .= \WP_CLI\Utils\assoc_args_to_str( $assoc_args );

		$settings = array();

		$settings['url'] = "{$this->base_url}/wp_cli?environment={$this->environment}";
		$settings['post_args'] = $this->get_default_post_args();

		$settings['post_args']['body'] = array( 'command' => $command );

		return $settings;
	}

	protected function get_default_post_args() {

		$config = $this->config;

		$cookies = array();

		// $cookies[] = new WP_Http_Cookie( [ 'name' => '__ar_v4', 'value' => $config['ar_v4'] ] );
		$cookies[] = new WP_Http_Cookie( [ 'name' => '_session_id', 'value' => $config['session_id'] ] );

		$post_args = array(
			'timeout' => 30,
			'headers' => array(
				'X-CSRF-Token' => $config['token'],
				),
			'cookies' => $cookies,
			);

		return $post_args;
	}

	protected function get_config() {
		$full_config = \WP_CLI::get_configurator()->to_array();

		$config = ! empty( $full_config[1]['wpe-cli'] ) ? $full_config[1]['wpe-cli'] : [];

		if ( empty( $config ) ) {
			WP_CLI::error( 'Please set the wpe-cli config values in your config.yml file' );
		}

		return $config;
	}
}

WP_CLI::add_command( 'wpe', 'WPE_CLI_Command' );
