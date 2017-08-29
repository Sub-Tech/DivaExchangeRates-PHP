<?php

namespace DivaExchangeRates;

/**
 * Class Auth
 * @package DivaExchangeRates
 */
class Auth {
	/**
	 * @var null
	 */
	protected static $apiKey = null;

	public function __construct( $apiKey ) {
		$this->setApiKey( $apiKey );
	}

	/**
	 * @return null
	 */
	public static function getApiKey() {
		return static::$apiKey;
	}

	/**
	 * @param null $apiKey
	 */
	public static function setApiKey( $apiKey ) {
		static::$apiKey = $apiKey;
	}

}