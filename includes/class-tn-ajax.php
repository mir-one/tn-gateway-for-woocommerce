<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ajax class
 */
class TurtleNetworkAjax
{

    private static $instance;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        add_action('wp_ajax_check_tn_payment', array(__CLASS__, 'checkTurtleNetworkPayment'));
    }

    public function checkTurtleNetworkPayment()
    {
        global $woocommerce;
        $woocommerce->cart->get_cart();

        $options = get_option('woocommerce_tn_settings');

        $payment_total   = WC()->session->get('tn_payment_total');
        $destination_tag = WC()->session->get('tn_destination_tag');

        $ra     = new TurtleNetworkApi($options['address']);
        $result = $ra->findByDestinationTag($destination_tag);

        $result['match'] = ($result['amount'] == $payment_total ) ? true : false;

        echo json_encode($result);
        exit();
    }

} 

TurtleNetworkAjax::getInstance();
