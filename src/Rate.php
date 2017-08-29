<?php

namespace DivaExchangeRates;

/**
 * Class Rate
 * @package ExchangeRates
 */
class Rate {
	protected $query;
	protected $data = false;

	public function __construct( $data = false ) {
		$this->data = $data;
	}

	public function get() {

		if ( isset( $this->data->rate ) ) {
			return $this->data->rate;
		}
		throw new \Exception( 'No Exchange Rate Data Found!' );
	}

	/**
	 * @return bool|data
	 */
	public function getData() {
		return $this->data;
	}

}