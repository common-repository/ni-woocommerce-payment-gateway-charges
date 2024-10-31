<?php 
if ( ! defined( 'ABSPATH' ) ) { exit;}
if( !class_exists( 'ni_payment_gateway_charges_init' ) ) {
class ni_payment_gateway_charges_init{
	function __construct() {
		
		add_action('admin_menu',  array(&$this,'admin_menu' ));	
		add_action('init', array($this, 'admin_init'));
		add_action('wp_enqueue_scripts',array($this,'wp_enqueue_scripts'));
		add_action('woocommerce_cart_calculate_fees', array($this,'woocommerce_cart_calculate_fees') );
		add_action( 'admin_enqueue_scripts',  array(&$this,'admin_enqueue_scripts' ));
		$this->add_enquiry_setting_page();
	}
	function admin_menu(){
		add_menu_page('Payement','Payement','manage_options','ni-payment-gateway-charges-dashboard',array(&$this,'add_page'),plugins_url( '../images/icon2.png', __FILE__ ),59.46);
		 	add_submenu_page('ni-payment-gateway-charges-dashboard', 'Dashboard', 'Dashboard', 'manage_options', 'ni-payment-gateway-charges-dashboard' , array(&$this,'add_page'));
			
		
	}
	function add_enquiry_setting_page()
	{
		include_once("ni-payment-gateway-charges-settings.php");	
		$obj = new ni_payment_gateway_charges_settings();
		
			
	}
	function add_page(){
		if (isset($_REQUEST["page"])){
			$page  = sanitize_text_field($_REQUEST["page"]);
			if ($page == "ni-payment-gateway-charges-dashboard"){
				include_once("ni-payment-gateway-charges-dashboard.php");	
				$obj = new ni_payment_gateway_charges_dashboard();
				$obj->page_init();
			}
			if ($page =="niwoopgc-other-plugins"){
				include_once("niwoopgc-other-plugins.php");	
				$obj = new NiWooPGC_Other_Plugins();
				$obj->page_init();
			}
		}
	} 
	function admin_enqueue_scripts(){
		if (isset($_REQUEST["page"])){
			$page  = sanitize_text_field($_REQUEST["page"]);
			if ($page =="ni-payment-gateway-charges-dashboard" ||$page  == "niwoopgc-other-plugins"){
					
					wp_register_style( 'niwoopgc-style', plugins_url( '../admin/css/niwoopgc.css', __FILE__ ));
		 			wp_enqueue_style( 'niwoopgc-style' );
					
					wp_register_style( 'niwoopgc-font-awesome-css', plugins_url( '../admin/css/font-awesome.css', __FILE__ ));
		 			wp_enqueue_style( 'niwoopgc-font-awesome-css' );
					
					wp_register_script( 'niwoopgc-amcharts-script', plugins_url( '../admin/js/amcharts/amcharts.js', __FILE__ ) );
					wp_enqueue_script('niwoopgc-amcharts-script');
				
		
					wp_register_script( 'niwoopgc-light-script', plugins_url( '../admin/js/amcharts/light.js', __FILE__ ) );
					wp_enqueue_script('niwoopgc-light-script');
				
					wp_register_script( 'niwoopgc-pie-script', plugins_url( '../admin/js/amcharts/pie.js', __FILE__ ) );
					wp_enqueue_script('niwoopgc-pie-script');
					
					
					wp_register_style('niwoopgc-bootstrap-css', plugins_url('../admin/css/lib/bootstrap.min.css', __FILE__ ));
		 			wp_enqueue_style('niwoopgc-bootstrap-css' );
				
					wp_enqueue_script('niwoopgc-bootstrap-script', plugins_url( '../admin/js/lib/bootstrap.min.js', __FILE__ ));
					wp_enqueue_script('niwoopgc-popper-script', plugins_url( '../admin/js/lib/popper.min.js', __FILE__ ));
			}
		}
	}
	function woocommerce_cart_calculate_fees() {
		global $woocommerce;
		$payment_option = get_option( 'ni-payment-gateway-charges-option' );
		$taxes = 0;
		$shipping_taxes =0 ;
		$fees_total =0 ;
		
		/*Comment on 03-Jan-2018*/
		//$taxes = isset($woocommerce->cart->taxes[2])?$woocommerce->cart->taxes[2] :0;
		//$shipping_taxes = isset($woocommerce->cart->shipping_taxes[2])?$woocommerce->cart->shipping_taxes[2] :0;
		/*End Comment on 03-Jan-2018*/
			
		//echo '<pre>',print_r($taxes[2],1),'</pre>';	
		// echo '<pre>',print_r($woocommerce->cart,1),'</pre>';	
		 $add_extra = isset($payment_option["add_extra_charges"])?"yes" :"no";
		 $calculate_extra_charges = isset($payment_option["calculate_extra_charges"])?$payment_option["calculate_extra_charges"] :"exclude_tax";
		if ( is_admin() && ! defined( 'DOING_AJAX' ))
		return;
		
		if ($add_extra =="no" )
		return ;
		
		
		$extra_charges_title ='';
		//echo get_option('woocommerce_currency');
		 $currency_symbol = get_woocommerce_currency_symbol(get_option('woocommerce_currency'));
		
		
		/*Local Variable for extra charges*/
		$extra_charges = 0;
		$extra_charges_type = "percent";
		
		
		$extra_charges_option =  array();
		/*All Payment Gateways*/
		$payment_gateways = $this->get_payment_gateways();
		
		$custom_fee = 0;
		
		/*Selected Payment Gateways*/
		$selected_payment_gateways = $woocommerce->session->chosen_payment_method;
		if (isset($payment_gateways [$selected_payment_gateways])){
			//error_log($selected_payment_gateways);
			/*Get Payment Gateway value*/
			$extra_charges_option  = get_option("woocommerce_".$selected_payment_gateways ."_settings");
			/*Get Charges*/
			$extra_charges = isset($extra_charges_option["ic_extra_charges"])?$extra_charges_option["ic_extra_charges"]:0;
			/*Get Charges Type*/
			$extra_charges_type =  isset( $extra_charges_option["ic_extra_charges_type"])?$extra_charges_option["ic_extra_charges_type"] :'' ;
			
			if ($extra_charges_type=="percent"){
				 //$custom_fee =  round(($subtotal*$extra_charges)/100,2);
				 
				//$taxes = array_sum($woocommerce->cart->taxes);
				$fees_total = $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total;
				
    			$custom_fee  = (( $fees_total ) * $extra_charges)/100;   
    
				$extra_charges_title = $extra_charges."% charge on ".$currency_symbol . $fees_total."  for ".$selected_payment_gateways ." payment gateway";
				
				//$custom_fee = ($subtotal*$extra_charges)/100;
			}else{
				$fees_total = $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total;
				$extra_charges_title = $currency_symbol .$extra_charges." charge on ".$currency_symbol  . $fees_total." for ".$selected_payment_gateways ." payment gateway";
				$custom_fee  =  $extra_charges ;
				
			}
			
		}
		
		if ($custom_fee >0){
			if ("include_tax" == $calculate_extra_charges){
				/*Added Fess*/
				$woocommerce->cart->add_fee($extra_charges_title, 	$custom_fee , true, '' );
			}else{
				/*Added Fess*/
				$woocommerce->cart->add_fee($extra_charges_title, 	$custom_fee , false, '' );
			}
			
		}
			//$woocommerce->cart->add_fee($extra_charges_title, 	$custom_fee , true, '' );
		
	}
	function wp_enqueue_scripts(){
		wp_enqueue_script( 'ni-payment-gateway-charges-script', plugins_url( '../js/ni-payment-gateway-charges-script.js', __FILE__ ), array('jquery') );	
	}
	
	function admin_init(){
		
		$page = isset($_GET['page']) ? $_GET['page'] : '';
		if($page != 'wc-settings'){
			return false;
		}
		
		if(!is_admin()){
			return false;
		}
		$gateways = $this->get_payment_gateways();
			
		foreach($gateways  as $key=>$value){
			add_filter('woocommerce_settings_api_form_fields_'.$key, array($this, 'add_extra_payment_fields' ),101,2);
		}
		
	}
	function add_extra_payment_fields($settings,$form_fields = ''){
		
		//print_r($form_fields);
		
		$settings["ic_extra_charges"] = array(
				'title'       => __( 'Extra Charges', 'woocommerce' ),
				'type'        => 'number',
				'description' => __( 'Extra fess for payment gatways ', 'woocommerce' ),
				'default'     => __( '0', 'woocommerce' ),
				'desc_tip'    => true,
				'custom_attributes' => array(
						'step' => 'any',
					),
		);
		
		$settings["ic_extra_charges_type"] = array(
				'title'       => __( 'Charges Type', 'woocommerce' ),
				'type'        => 'select',
				'description' => __( 'Extra fess for payment gatways ', 'woocommerce' ),
				'default'     => __( 'Direct Bank Transfer', 'woocommerce' ),
				'desc_tip'    => true,
				'options'     => array(
					'fixed'   => __( 'Fixed amount', 'woocommerce' ),
					'percent' => __( 'Percentage', 'woocommerce' ),
				),
		);
			
		return $settings;
		
	}
	function get_payment_gateways(){
		$payment_gateways =  array();
		$available_gateways = WC()->payment_gateways->payment_gateways();
		foreach($available_gateways  as $key=>$value){
			$payment_gateways[$key] = $value->title;
		}
		return $payment_gateways;
	}
}
}
?>