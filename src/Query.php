<?php

namespace DivaExchangeRates;

/**
 * Class Query
 * @package DivaExchangeRates
 */
class Query {

	/**
	 * @var bool
	 */
	private $fromCurrency = false;
	/**
	 * @var bool
	 */
	private $toCurrency = false;
	/**
	 * @var string
	 */
	protected $url = '';
	/**
	 * @var string
	 */
	protected $params = '';
	/**
	 * @var string
	 */
	protected $rawData = '';
	/**
	 * @var CurrencyCodes
	 */
	protected $codes;

	/**
	 * @param bool|string $fromCurrency
	 * @param bool|string $toCurrency
	 * @param string $format
	 */
	public function __construct( $fromCurrency = false, $toCurrency = false ) {

		$this->codes = new CurrencyCodes();
		$this->setFromCurrency( $fromCurrency );
		$this->setToCurrency( $toCurrency );

	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @return string
	 */
	public function getParams() {
		return $this->params;
	}

	/**
	 * @return string
	 */
	public function getToCurrency() {
		return $this->toCurrency;
	}

	/**
	 * @param $currency
	 */
	public function setToCurrency( $currency ) {
		if ( $this->codes->isValid( $currency ) ) {
			$this->toCurrency = $currency;
		}
	}


	/**
	 * @return string
	 */
	public function getFromCurrency() {
		return $this->fromCurrency;
	}

	/**
	 * @param string $currency
	 */
	public function setFromCurrency( $currency ) {
		if ( $this->codes->isValid( $currency ) ) {
			$this->fromCurrency = $currency;
		}else{
			throw new \Exception("Not a valid currency for Diva!");
		}
	}

	/**
	 * @param bool $fromCurrency
	 * @param bool $toCurrency
	 *
	 * @return $this
	 */
	protected function buildQuery( $fromCurrency = false, $toCurrency = false ) {

		$fromCurrencyDefault = $this->getFromCurrency();
		if ( isset( $fromCurrency ) ) {
			$fromCurrency = $this->codes->isValid( $fromCurrency );
			if ( $fromCurrency ) {
				$finalFromCurrency = $fromCurrency;
			} else {
				$finalFromCurrency = $fromCurrencyDefault;
			}
		}
		$toCurrencyDefault = $this->getToCurrency();
		if ( isset( $toCurrency ) ) {
			$toCurrency = $this->codes->isValid( $toCurrency );
			if ( $toCurrency ) {
				$finalToCurrency = $toCurrency;
			} else {
				$finalToCurrency = $toCurrencyDefault;
			}
		}
		$url = 'http://diva.stechga.co.uk/api/currency/exchange_rate';


		$this->url    = $url;
		$this->params = "apikey=" . Auth::getApiKey() . "&from=" . $finalFromCurrency . "&to=" . $finalToCurrency;

		return $this;
	}

	/**
	 * @param bool $fromCurrency
	 * @param bool $toCurrency
	 */
	public function execute( $fromCurrency = false, $toCurrency = false ) {
		$this->buildQuery( $fromCurrency, $toCurrency );
		$url           = $this->getUrl();
		$params        = $this->getParams();
		$rawData       = $this->curl( $url, $params );
		$this->rawData = $rawData;

		return $this;
	}

	public function getData() {
		return json_decode( $this->getRawData() );
	}

	/**
	 * @param null $url
	 * @param null $params
	 * @param null $header
	 * @param int $connecttimeout
	 * @param int $timeout
	 * @param int $followloc
	 * @param int $redir
	 * @param string $user_agent
	 * @param bool $convert_params_array
	 *
	 * @return bool|mixed
	 */
	protected function curl(
		$url = null,
		$params = null,
		$header = null,
		$connecttimeout = 180,
		$timeout = 180,
		$followloc = 1,
		$redir = 3,
		$user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)',
		$convert_params_array = true
	) {


		if ( ! function_exists( 'curl_init' ) ) {
			die( "\n no curl" );
		}

		if ( ! isset( $url ) ) {
			return false;
		}

		$postfields = '';
		$i          = 0;
		if ( isset( $params ) && is_array( $params ) && $convert_params_array == true ) {
			foreach ( $params as $key => $value ) {
				$and        = ( $i > 0 ) ? '&' : '';
				$postfields .= $and . $key . '=' . $value;
				$i ++;
			}
		} else {
			$postfields = $params;
		}
		$ch = curl_init( $url );

		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		if ( isset( $header ) && is_array( $header ) ) {
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $header );
		}
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_USERAGENT, $user_agent );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $connecttimeout );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
		if ( ! ini_get( 'open_basedir' ) && ! ini_get( 'safe_mode' ) ) {

			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, $followloc );
			curl_setopt( $ch, CURLOPT_MAXREDIRS, $redir );
		}
		curl_setopt( $ch, CURLOPT_HEADER, 0 );

		if ( isset( $params ) ) {

			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $postfields );
		}

		$response = curl_exec( $ch );

		curl_close( $ch );

		return $response;
	}

	/**
	 * @return string
	 */
	public function getRawData() {
		return $this->rawData;
	}


}