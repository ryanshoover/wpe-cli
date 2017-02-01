<?php

class WPE_CLI {

	private static $instance;

	private $states = array();

	/**
	 * Get the instance
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new Dictator;
		}
		return self::$instance;

	}
}
