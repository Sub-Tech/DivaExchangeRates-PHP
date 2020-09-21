<?php

namespace DivaExchangeRates;
/**
 * Class Conversion
 * @package DivaExchangeRates
 */
class Conversion
{
    /**
     * @var Cache
     */
    protected $cache;
    /**
     * @var RateQuery
     */
    protected $rateQuery;

    /**
     * @var Rate
     */
    protected $__rate;
    /**
     * @var int
     */
    protected $rate = 0;
    /**
     * @var int
     */
    protected $value = 0;

	/**
	 * Conversion constructor.
	 *
	 * @param $fromCurrency
	 * @param $toCurrency
	 * @param $value
	 * @param int $ttl
	 * @param null $apiKey
	 */
    public function __construct($fromCurrency, $toCurrency, $value, $ttl = 86400, $apiKey=null)
    {
    	if(isset($apiKey)){
			Auth::setApiKey($apiKey);
	    }
        $this->init($fromCurrency, $toCurrency, $value, $ttl);
    }

    protected function init($fromCurrency, $toCurrency, $value, $ttl = 86400, $old = false)
    {
    	if($fromCurrency==$toCurrency){
    		$this->rate = 1;
    		$this->value = $value;
    		return;
	    }
        $this->cache = new Cache();
        $cacheFilename = $fromCurrency . $toCurrency . '-divaratedata';
        if ($old) {
            $cachedRateData = $this->cache->getOldCacheData($cacheFilename);
        } else {
            $cachedRateData = $this->cache->getCacheData($cacheFilename, $ttl);
        }
        if (!$cachedRateData) {
            try {
                $this->rateQuery = new RateQuery($fromCurrency, $toCurrency);
	            $rateData = $this->rateQuery->getData();
	            if($rateData){
                    $this->cache->saveToCache($cacheFilename, $rateData);
	            }
                $cachedRateData = $rateData;
            } catch (\Exception $e) {
                if ($old == false) {
                    return $this->init($fromCurrency, $toCurrency, $value, $ttl, true);
                } else {
                    throw new \Exception("Could Not get data from RateQuery", 0, $e);
                }
            }
        }
        $this->__rate = new Rate($cachedRateData);
        try {
            $this->rate = $this->__rate->get();
        } catch (\Exception $e) {
            if ($old == false) {
                return $this->init($fromCurrency, $toCurrency, $value, $ttl, true);
            } else {
                throw new \Exception("Could Not get data from Old Cache File", 0, $e);
            }
        }

        $this->value = $value;
    }

    /**
     * @return int
     */
    public function get()
    {
        return ($this->value * $this->rate);
    }
}