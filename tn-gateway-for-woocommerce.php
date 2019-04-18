<?php

/**
 * TurtleNetwork Gateway for Woocommerce
 *
 * Plugin Name: TurtleNetwork Gateway for Woocommerce (also for other TurtleNetwork assets)
 * Plugin URI: https://t.me/mir_dev
 * Description: Show prices in TurtleNetwork (or asset) and accept TurtleNetwork payments in your woocommerce webshop
 * Version: 0.0.1
 * Author: Roman inozemtsev
 * Author URI:   https://t.me/inozemtsev_roman
 * License: GPLv2 or later
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 * Text Domain: tn-gateway-for-woocommerce
 * Domain Path: /languages/
  *
 * Copyright 2019 mir.one
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WcTurtleNetwork')) {

    class WcTurtleNetwork
    {

        private static $instance;
        public static $version = '0.0.1';
        public static $plugin_basename;
        public static $plugin_path;
        public static $plugin_url;

        protected function __construct()
        {
        	self::$plugin_basename = plugin_basename(__FILE__);
        	self::$plugin_path = trailingslashit(dirname(__FILE__));
        	self::$plugin_url = plugin_dir_url(self::$plugin_basename);
            add_action('plugins_loaded', array($this, 'init'));
        }
        
        public static function getInstance()
        {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function init()
        {
            $this->initGateway();
        }

        public function initGateway()
        {

            if (!class_exists('WC_Payment_Gateway')) {
                return;
            }

            if (class_exists('WC_TurtleNetwork_Gateway')) {
	            return;
	        }

	        /*
	         * Include gateway classes
	         * */
	        include_once plugin_basename('includes/base58/src/Base58.php');
	        include_once plugin_basename('includes/base58/src/ServiceInterface.php');
	        include_once plugin_basename('includes/base58/src/GMPService.php');
	        include_once plugin_basename('includes/base58/src/BCMathService.php');
	        include_once plugin_basename('includes/class-tn-gateway.php');
	        include_once plugin_basename('includes/class-tn-api.php');
	        include_once plugin_basename('includes/class-tn-exchange.php');
	        include_once plugin_basename('includes/class-tn-settings.php');
	        include_once plugin_basename('includes/class-tn-ajax.php');

	        add_filter('woocommerce_payment_gateways', array($this, 'addToGateways'));
            add_filter('woocommerce_currencies', array($this, 'TurtleNetworkCurrencies'));
            add_filter('woocommerce_currency_symbol', array($this, 'TurtleNetworkCurrencySymbols'), 10, 2);

	        add_filter('woocommerce_get_price_html', array($this, 'TurtleNetworkFilterPriceHtml'), 10, 2);
	        add_filter('woocommerce_cart_item_price', array($this, 'TurtleNetworkFilterCartItemPrice'), 10, 3);
	        add_filter('woocommerce_cart_item_subtotal', array($this, 'TurtleNetworkFilterCartItemSubtotal'), 10, 3);
	        add_filter('woocommerce_cart_subtotal', array($this, 'TurtleNetworkFilterCartSubtotal'), 10, 3);
	        add_filter('woocommerce_cart_totals_order_total_html', array($this, 'TurtleNetworkFilterCartTotal'), 10, 1);

	    }

	    public static function addToGateways($gateways)
	    {
	        $gateways['tn'] = 'WcTurtleNetworkGateway';
	        return $gateways;
	    }

        public function TurtleNetworkCurrencies( $currencies )
        {
            $currencies['TN'] = __( 'TurtleNetwork', 'tn' );
            $currencies['HN'] = __( 'Hellenic Node ', 'hn' );
            return $currencies;
        }

        public function TurtleNetworkCurrencySymbols( $currency_symbol, $currency ) {
            switch( $currency ) {
                case 'TN': $currency_symbol = 'TN'; break;
                case 'HN': $currency_symbol = 'HN'; break;
            }
            return $currency_symbol;
        }

	    public function TurtleNetworkFilterCartTotal($value)
	    {
	        return $this->convertToTurtleNetworkPrice($value, WC()->cart->total);
	    }

	    public function TurtleNetworkFilterCartItemSubtotal($cart_subtotal, $compound, $that)
	    {
	        return $this->convertToTurtleNetworkPrice($cart_subtotal, $that->subtotal);
	    }

	    public function TurtleNetworkFilterPriceHtml($price, $that)
	    {
	        return $this->convertToTurtleNetworkPrice($price, $that->price);
	    }

	    public function TurtleNetworkFilterCartItemPrice($price, $cart_item, $cart_item_key)
	    {
	        $item_price = ($cart_item['line_subtotal'] + $cart_item['line_subtotal_tax']) / $cart_item['quantity'];
	        return $this->convertToTurtleNetworkPrice($price,$item_price);
	    }

	    public function TurtleNetworkFilterCartSubtotal($price, $cart_item, $cart_item_key)
	    {
	        $subtotal = $cart_item['line_subtotal'] + $cart_item['line_subtotal_tax'];
	        return $this->convertToTurtleNetworkPrice($price, $subtotal);
	    }

	    private function convertToTurtleNetworkPrice($price_string, $price)
	    {
            $options = get_option('woocommerce_tn_settings');
            if(!in_array(get_woocommerce_currency(), array("TN","HN")) && $options['show_prices'] == 'yes') {
                $tn_currency = $options['asset_code'];
                if(empty($tn_currency)) {
                    $tn_currency = 'TurtleNetwork';
                }
                $tn_assetId = $options['asset_id'];
                if(empty($tn_assetId)) {
                    $tn_assetId = null;
                }
                $tn_price = TurtleNetworkExchange::convertToAsset(get_woocommerce_currency(), $price,$tn_assetId);
                if ($tn_price) {
                    $price_string .= '&nbsp;(<span class="woocommerce-price-amount amount">' . $tn_price . '&nbsp;</span><span class="woocommerce-price-currencySymbol">'.$tn_currency.')</span>';
                }
            }
	        return $price_string;
	    }
    }

}

WcTurtleNetwork::getInstance();

function tnGateway_textdomain() {
    load_plugin_textdomain( 'tn-gateway-for-woocommerce', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
        
add_action( 'plugins_loaded', 'tnGateway_textdomain' );