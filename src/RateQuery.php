<?php
namespace DivaExchangeRates;

/**
 * Class RateQuery
 * @package DivaExchangeRates
 */
class RateQuery extends Rate
{
    protected $query;
    protected $data = false;

    public function __construct($fromCurrency, $toCurrency)
    {
      
        $this->query = new Query();
        $data = $this->query->execute($fromCurrency, $toCurrency)->getData();

	    if (!$data) {
		    $this->data = null;
		    throw new \Exception('No Exchange Rate Data Received!');
	    }

	    if( !isset($data->status) || $data->status != "ok"){
		    $this->data = null;
		    $message    = 'Error in received exchange rate data - ' . var_export($data,true);
		    throw new \Exception( $message );
	    }

	    $this->data = $data;
    }
}