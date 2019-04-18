=== TurtleNetwork Gateway for Woocommerce ===
Contributors: Roman Inozemtsev
Donate link: TN - 3Jd9EQX1yqtYhACBydpjNd43pNnFJFX8z3o, WAVES - 3PA8GR3PLpGmwYhSdQjTSK7sGnjeC2XQQS2
Tags: billing, invoicing, woocommerce, payment, tn, turtlenetwork
Requires at least: 5.1.1
Tested up to: 5.1.1
Stable tag: 0.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show prices in TN or any other token on TurtleNetwork and accept payments with that token your woocommerce webshop

== Description ==

Display prices in TN or token and let your clients pay through the TurtleNetwork client software. Built on top of Ripple Gateway developed by Casper Mekel and uses Base58 library developed by Stephen Hill for encoding and decoding. 

* Display prices in TN or token in store and on checkout
* Prices are calculated based on TurtleNetwork DEX rate
* Links can be copied by clicking and a QR code is supplied which can be used in the TurtleNetwork wallet app Android
* Countdown refreshes form each 10 minutes, updating amounts using the most recent conversion reate
* Matches payments on (encoded) attachment and amount
* Checkout page is automatically refreshed after a successful payment 
* Dutch and Russian translations included. More translations are welcome.

== Installation ==

Install the plugin by uploading the zipfile in your WP admin interface or via FTP:

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Find the configuration in Woocommerce->Payment page and fill out your settings and preferences

== Frequently Asked Questions ==

== Screenshots ==

1. Payment methods in Woocommerce
2. TurtleNetwork payments
3. QR-code/address/attachment for your order
4. Order has been received
5. Tx from explorer TurtleNetwork

== Changelog ==

- 0.0.1
* Initial release

== Upgrade Notice ==

No upgrade notices apply.
