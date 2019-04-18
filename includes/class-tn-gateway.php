<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Gateway class
 */
class WcTurtleNetworkGateway extends WC_Payment_Gateway
{
    public $id;
    public $title;
    public $form_fields;
    public $addresses;
    private $assetId;
    private $assetCode;
    private $currencyIsTurtleNetwork = false;

    public function __construct()
    {

        $this->id          			= 'tn';
        $this->title       			= $this->get_option('title');
        $this->description 			= $this->get_option('description');
        $this->address   			= $this->get_option('address');
        $this->secret   			= $this->get_option('secret');
        $this->order_button_text 	= __('Awaiting transfer..','tn-gateway-for-woocommerce');
        $this->has_fields 			= true;

        // assetCode+id if woocommerce_currency is set to TurtleNetwork-like currency
        $this->currencyIsTurtleNetwork = in_array(get_woocommerce_currency(), array("TN","HN"));
        if($this->currencyIsTurtleNetwork) {
            if (get_woocommerce_currency() == "TurtleNetwork") {
                $this->assetCode = 'TurtleNetwork';
                $this->assetId = null;
            } else if (get_woocommerce_currency() == "HN") {
                $this->assetCode = 'HN';
                $this->assetId = '3GvqjyJFBe1fpiYnGsmiZ1YJTkYiRktQ86M2KMzcTb2s';
				}
        } else {
            $this->assetId              = $this->get_option('asset_id');
            $this->assetCode            = $this->get_option('asset_code');
        }
        if(empty($this->assetId)) {
            $this->assetId = null;
        }
        if(empty($this->assetCode)) {
            $this->assetCode = 'TurtleNetwork';
        }

        $this->initFormFields();

        $this->initSettings();

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(
            $this,
            'process_admin_options',
        ));
        add_action('wp_enqueue_scripts', array($this, 'paymentScripts'));

        add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyouPage'));        

    }

    public function initFormFields()
    {
        parent::init_form_fields();
        $this->form_fields = TurtleNetworkSettings::fields();
    }

    public function initSettings()
    {
    	// sha1( get_bloginfo() )
        parent::init_settings();
    }
   
    public function payment_fields()
    {
    	global $woocommerce;
    	$woocommerce->cart->get_cart();
        $total_converted = $this->get_order_total();
        $rate = null;
        if(!$this->currencyIsTurtleNetwork) {
            $total_converted = TurtleNetworkExchange::convertToAsset(get_woocommerce_currency(), $total_converted,$this->assetId);
            $rate = $total_converted / $this->get_order_total();
        }
		
		// Set decimals for tokens other than default value 8
		if (get_woocommerce_currency() == "HN") {
		$total_tn = $total_converted * 100;
		}
		else {
			$total_tn = $total_converted * 100000000;
		}


        $destination_tag = hexdec( substr(sha1(current_time(timestamp,1) . key ($woocommerce->cart->cart_contents )  ), 0, 7) );
        $base58 = new StephenHill\Base58();
        $destination_tag_encoded = $base58->encode(strval($destination_tag));
        // set session data 
        WC()->session->set('tn_payment_total', $total_tn);
        WC()->session->set('tn_destination_tag', $destination_tag_encoded);
        WC()->session->set('tn_data_hash', sha1( $this->secret . $total_converted ));
        //QR uri
        $url = "tn://". $this->address ."?amount=". $total_tn."&attachment=".$destination_tag;
        if($this->assetId) {
            $url .= "&asset=".$this->assetId;
        }?>
        <div id="tn-form">
            <div class="tn-container">
            <div>
                <?if ($this->description) { ?>
                <div class="separator"></div>
                <div id="tn-description">
                    <?=apply_filters( 'wc_tn_description', wpautop(  $this->description ) )?>
                </div>
                <?}?>
                <div class="separator"></div>
                <div class="tn-container">
                <?if($rate!=null){?>
                <label class="tn-label">
                    (1<?=get_woocommerce_currency()?> = <?=round($rate,6)?> <?=$this->assetCode?>)
                </label>
                <?}?>
                <p class="tn-amount">
                    <span class="copy" data-success-label="<?=__('copied','tn-gateway-for-woocommerce')?>"
                          data-clipboard-text="<?=esc_attr($total_converted)?>"><?=esc_attr($total_converted)?>
                    </span> <strong><?=$this->assetCode?></strong>
                </p>
                </div>
            </div>
            <div class="separator"></div>
            <div class="tn-container">
                <label class="tn-label"><?=__('destination address', 'tn-gateway-for-woocommerce')?></label>
                <p class="tn-address">
                    <span class="copy" data-success-label="<?=__('copied','tn-gateway-for-woocommerce')?>"
                          data-clipboard-text="<?=esc_attr($this->address)?>"><?=esc_attr($this->address)?>
                    </span>
                </p>
            </div>
            <div class="separator"></div>
            <div class="tn-container">
                <label class="tn-label"><?=__('attachment', 'tn-gateway-for-woocommerce')?></label>
                <p class="tn-address">
                    <span class="copy" data-success-label="<?=__('copied','tn-gateway-for-woocommerce')?>"
                          data-clipboard-text="<?=esc_attr($destination_tag)?>"><?=esc_attr($destination_tag)?>
                    </span>
                </p>
            </div>
            <div class="separator"></div>
            </div>
            <div id="tn-qr-code" data-contents="<?=$url?>"></div>
            <div class="separator"></div>
            <div class="tn-container">
                <p>
                    <?=sprintf(__('Send a payment of exactly %s to the address above (click the links to copy or scan the QR code). We will check in the background and notify you when the payment has been validated.', 'tn-gateway-for-woocommerce'), '<strong>'. esc_attr($total_converted).' '.$this->assetCode.'</strong>' )?>
                </p>
                <strong>DO NOT FORGET THE ATTACHMENT IF YOU USE MANUAL PAYMENT! </strong>
                <p>
                    <?=sprintf(__('Please send your payment within %s.', 'tn-gateway-for-woocommerce'), '<strong><span class="tn-countdown" data-minutes="10">10:00</span></strong>' )?>
                </p>
                <p class="small">
                    <?=__('When the timer reaches 0 this form will refresh and update the attachment as well as the total amount using the latest conversion rate.', 'tn-gateway-for-woocommerce')?>
                </p>
            </div>
            <input type="hidden" name="tx_hash" id="tx_hash" value="0"/>
        </div>
        <?
    }

    public function process_payment( $order_id ) 
    {
    	global $woocommerce;
        $this->order = new WC_Order( $order_id );
        
	    $payment_total   = WC()->session->get('tn_payment_total');
        $destination_tag = WC()->session->get('tn_destination_tag');

	    $ra = new TurtleNetworkApi($this->address);
	    $transaction = $ra->getTransaction( $_POST['tx_hash']);
	    
        if($transaction->attachment != $destination_tag) {
	    	exit('destination');
	    	return array(
		        'result'    => 'failure',
		        'messages' 	=> 'attachment mismatch'
		    );
	    }
		
		if($transaction->assetId != $this->assetId ) {
			return array(
		        'result'    => 'failure',
		        'messages' 	=> 'Wrong Asset'
		    );
		}
		
	    if($transaction->amount != $payment_total) {
	    	return array(
		        'result'    => 'failure',
		        'messages' 	=> 'amount mismatch'
		    );
	    }
	    
        $this->order->payment_complete();

        $woocommerce->cart->empty_cart();
	   
        return array(
            'result' => 'success',
            'redirect' => $this->get_return_url($this->order)
        );
	}

    public function paymentScripts()
    {
        wp_enqueue_script('qrcode', plugins_url('assets/js/jquery.qrcode.min.js', WcTurtleNetwork::$plugin_basename), array('jquery'), WcTurtleNetwork::$version, true);
        wp_enqueue_script('initialize', plugins_url('assets/js/jquery.initialize.js', WcTurtleNetwork::$plugin_basename), array('jquery'), WcTurtleNetwork::$version, true);
        
        wp_enqueue_script('clipboard', plugins_url('assets/js/clipboard.js', WcTurtleNetwork::$plugin_basename), array('jquery'), WcTurtleNetwork::$version, true);
        wp_enqueue_script('woocommerce_tn_js', plugins_url('assets/js/tn.js', WcTurtleNetwork::$plugin_basename), array(
            'jquery',
        ), WcTurtleNetwork::$version, true);
        wp_enqueue_style('woocommerce_tn_css', plugins_url('assets/css/tn.css', WcTurtleNetwork::$plugin_basename), array(), WcTurtleNetwork::$version);

        // //Add js variables
        $tn_vars = array(
            'wc_ajax_url' => WC()->ajax_url(),
            'nonce'      => wp_create_nonce("tn-gateway-for-woocommerce"),
        );

        wp_localize_script('woocommerce_tn_js', 'tn_vars', apply_filters('tn_vars', $tn_vars));

    }

}
