<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Exchange class
 */
class TurtleNetworkExchange
{
    private static function getBodyAsJson($url,$retries=1) {
        $response = wp_remote_get( $url );
        $result = json_decode(wp_remote_retrieve_body($response));
        if(!$result && $retries>0) {
            return TurtleNetworkExchange::getBodyAsJson($url,--$retries);
        }
        return $result?$result:null;
    }

    private static function getExchangePrice($currency1,$currency2) {
        $pair = $currency1."/".$currency2;
        $result = wp_cache_get($pair,'exchangePrices');
        if (false === $result ) {
            $result = TurtleNetworkExchange::getBodyAsJson("https://bot.blackturtle.eu/api/ticker/".$pair);
            $result = isset($result->{'24h_vwap'})?$result->{'24h_vwap'}:false;
            wp_cache_set( $pair, $result, 'exchangePrices', 3600);
        }
        return $result;
    }

    private static function exchange($currency,$price,$currencyTo) {
        $exchange_price = TurtleNetworkExchange::getExchangePrice($currencyTo,$currency);
        if(!$exchange_price || $exchange_price==0 || $price==null) {
            return null;
        }
        return round($price / $exchange_price, $currencyTo=='tn'?2:0, PHP_ROUND_HALF_UP);
    }

    public static function convertToAsset($currency, $price,$assetId) {
        $price_in_tn = TurtleNetworkExchange::exchange(strtolower($currency),$price,'tn');
        if($assetId==null) {
            return $price_in_tn;
        }
        return TurtleNetworkExchange::exchange('tn',$price_in_tn,$assetId);
    }
}
