<?php 
/*
Plugin Name: Ni WooCommerce Payment Gateway Charges
Description: Ni WooCommerce Payment Gateway Charges plugin provide the option to charge extra payment amoount or fees based on customer selected payment gateway.
Version:1.6.0
Author: anzia
Author URI: http://naziinfotech.com/
Plugin URI: https://wordpress.org/plugins/ni-woocommerce-payment-gateway-charges/
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/agpl-3.0.html
Requires at least: 4.7
Tested up to: 6.4.3
WC requires at least: 3.0.0
WC tested up to: 8.7.0
Last Updated Date: 24-March-2024
Requires PHP: 7.0
*/
if ( ! defined( 'ABSPATH' ) ) { exit;}
if( !class_exists( 'ni_woocommerce_payment_gateway_charges' ) ) {
	class ni_woocommerce_payment_gateway_charges {
		function __construct() {
			
			add_action( 'activated_plugin',  array(&$this,'nipgc_activation_redirect' ));
			
			include_once("include/ni-payment-gateway-charges-init.php");
			$obj = new  ni_payment_gateway_charges_init();
			
		}
		static   function nipgc_activation_redirect($plugin){
			 if( $plugin == plugin_basename( __FILE__ ) ) {
				exit( wp_redirect( admin_url( 'admin.php?page=ni-payment-gateway-charges-setting' ) ) );
			}
	    }
	}
	$obj  = new  ni_woocommerce_payment_gateway_charges();
}
?>