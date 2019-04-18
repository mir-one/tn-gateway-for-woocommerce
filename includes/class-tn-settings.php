<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Settings class
 */
if (!class_exists('TurtleNetworkSettings')) {

    class TurtleNetworkSettings
    {

        public static function fields()
        {

            return apply_filters('wc_tn_settings',

                array(
                    'enabled'     => array(
                        'title'   => __('Enable/Disable', 'tn-gateway-for-woocommerce'),
                        'type'    => 'checkbox',
                        'label'   => __('Enable TurtleNetwork payments', 'tn-gateway-for-woocommerce'),
                        'default' => 'yes',
                    ),
                    'title'       => array(
                        'title'       => __('Title', 'tn-gateway-for-woocommerce'),
                        'type'        => 'text',
                        'description' => __('This controls the title which the user sees during checkout.', 'tn-gateway-for-woocommerce'),
                        'default'     => __('Pay with TurtleNetwork', 'tn-gateway-for-woocommerce'),
                        'desc_tip'    => true,
                    ),
                    'description' => array(
                        'title'   => __('Customer Message', 'tn-gateway-for-woocommerce'),
                        'type'    => 'textarea',
                        'default' => __('Ultra-fast and secure checkout with TurtleNetwork'),
                    ),
                    'address'     => array(
                        'title'       => __('Destination address', 'tn-gateway-for-woocommerce'),
                        'type'        => 'text',
                        'default'     => '',
                        'description' => __('This addresses will be used for receiving funds.', 'tn-gateway-for-woocommerce'),
                    ),
                    'show_prices' => array(
                        'title'   => __('Convert prices', 'tn-gateway-for-woocommerce'),
                        'type'    => 'checkbox',
                        'label'   => __('Add prices in TurtleNetwork (or asset)', 'tn-gateway-for-woocommerce'),
                        'default' => 'no',

                    ),
                    'secret'      => array(
                        'type'    => 'hidden',
                        'default' => sha1(get_bloginfo() . Date('U')),

                    ),
                    'asset_id'     => array(
                        'title'       => __('Asset ID', 'tn-gateway-for-woocommerce'),
                        'type'        => 'text',
                        'default'     => null,
                        'description' => __('This is the asset Id used for transactions.', 'tn-gateway-for-woocommerce'),
                    ),
                    'asset_code'     => array(
                        'title'       => __('Asset code (short name = currency code = currency symbol)', 'tn-gateway-for-woocommerce'),
                        'type'        => 'text',
                        'default'     => null,
                        'description' => __('This is the Asset Currency code for exchange rates. If omitted TurtleNetwork will be used', 'tn-gateway-for-woocommerce'),
                    ),
                    'asset_description'     => array(
                        'title'       => __('Asset description', 'tn-gateway-for-woocommerce'),
                        'type'        => 'text',
                        'default'     => null,
                        'description' => __('Asset full name', 'tn-gateway-for-woocommerce'),
                    ),
                )
            );
        }
    }

}
