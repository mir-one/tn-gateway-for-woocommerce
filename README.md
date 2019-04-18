# TurtleNetwork Gateway for Woocommerce

Show prices in TN or any other token on TurtleNetwork and accept payments with that token your Woocommerce webshop

Display prices in TN or token and let your clients pay through the TurtleNetwork client software. Built on top of Ripple Gateway developed by Casper Mekel and uses Base58 library developed by Stephen Hill for encoding and decoding. 

* Display prices in TN or token in store and on checkout
* Prices are calculated based on TurtleNetwork DEX rate
* Links can be copied by clicking and a QR code is supplied which can be used in the TurtleNetwork wallet app Android
* Countdown refreshes form each 10 minutes, updating amounts using the most recent conversion reate
* Matches payments on (encoded) attachment and amount
* Checkout page is automatically refreshed after a successful payment 
* Dutch and Russian translations included. More translations are welcome.

### Install the plugin by uploading the zipfile in your WP admin interface or via FTP:

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Find the configuration in Woocommerce->Payment page and fill out your settings and preferences

### Screenshots

![Payment methods in Woocommerce](https://raw.githubusercontent.com/mir-one/tn-gateway-for-woocommerce/master/screenshot-1.png)
_Payment methods in Woocommerce_

![TurtleNetwork payments](https://raw.githubusercontent.com/mir-one/tn-gateway-for-woocommerce/master/screenshot-2.png)
_TurtleNetwork payments_

![QR-code/address/attachment for your order](https://raw.githubusercontent.com/mir-one/tn-gateway-for-woocommerce/master/screenshot-3.png)
_QR-code/address/attachment for your order_

![Order has been received](https://raw.githubusercontent.com/mir-one/tn-gateway-for-woocommerce/master/screenshot-4.png)
_Order has been received_

![Tx from explorer TurtleNetwork](https://raw.githubusercontent.com/mir-one/tn-gateway-for-woocommerce/master/screenshot-5.png)
_Tx from explorer TurtleNetwork_
