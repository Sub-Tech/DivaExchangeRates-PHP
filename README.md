# DivaExchangeRates-PHP
Diva Exchange Rates Library

```
$divaCache = new \DivaExchangeRates\Cache();
$divaCache->setCachePath( $_SERVER['DOCUMENT_ROOT'] . '/data/exchange_rate/' );
\DivaExchangeRates\Auth::setApiKey( 'yourkeyprovidedbydivaadmin' );
$convertedValue = new \DivaExchangeRates\Conversion( 'USD', 'GBP', 10 );
```
