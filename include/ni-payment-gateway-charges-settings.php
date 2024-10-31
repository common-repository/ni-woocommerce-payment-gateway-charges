<?php
if ( ! defined( 'ABSPATH' ) ) { exit;}
if( !class_exists( 'ni_payment_gateway_charges_settings' ) ) {
	class ni_payment_gateway_charges_settings{
		var $options = array();
		
		
		function __construct() {
			 add_action( 'admin_menu', array( $this, 'add_setting_page' ) );
			 add_action( 'admin_init', array( $this, 'admin_init' ),110 );
			 add_action( 'admin_init', array( $this, 'admin_init_save'),100 );
		}
		function add_setting_page(){
			add_submenu_page( "ni-payment-gateway-charges-dashboard", 'Setting', 'Setting', 'manage_options', 'ni-payment-gateway-charges-setting', array( $this, 'setting_page' ) );
			add_submenu_page('ni-payment-gateway-charges-dashboard', 'Other Plugins', 'Other Plugins', 'manage_options', 'niwoopgc-other-plugins' , array(&$this,'add_page'));
		}
		function admin_init_save(){
			if (isset($_REQUEST["ni-payment-gateway-charges-option"])){	
				update_option('ni-payment-gateway-charges-option',$_REQUEST["ni-payment-gateway-charges-option"]);
			}
		}
		function add_page(){
			if (isset($_REQUEST["page"])){
				$page  = sanitize_text_field($_REQUEST["page"]);
				if ($page =="niwoopgc-other-plugins"){
					include_once("niwoopgc-other-plugins.php");	
					$obj = new NiWooPGC_Other_Plugins();
					$obj->page_init();
				}
			}
		}
		function setting_page(){
			
			
			
			    // Set class property
			$this->options = get_option( 'ni-payment-gateway-charges-option' );
			//$this->options = get_option( 'invoice_setting_option' );
			?>
			<div class="wrap">
				<?php //screen_icon(); ?>
			  <!--  <h2>My Settings</h2>           -->
				<form method="post">
				<?php
					// This prints out all hidden setting fields
					settings_fields( 'ni-payment-gateway-charges-option-group' );   
					do_settings_sections( 'ni-payment-gateway-charges-admin' );
					submit_button(); 
				?>
				</form>
			</div>
			<?php
		}
		function admin_init(){
			register_setting(
				'ni-payment-gateway-charges-option-group', // Option group
				'ni-payment-gateway-charges-option', // Option name
				array( $this, 'sanitize' ) // Sanitize
			);
			
			add_settings_section(
				'setting_section_id', // ID
				'Payment Gateway Charges Settings', // Title
				array( $this, 'print_section_info' ), // Callback
				'ni-payment-gateway-charges-admin' // Page
			);
			
			/*Email To Customer*/
			add_settings_field(
				'add_extra_charges', 
				'Enable extra charges', 
				array( $this, 'add_extra_charges' ), 
				'ni-payment-gateway-charges-admin', 
				'setting_section_id'
			);  
			
			/*Extra charges*/
			add_settings_field(
				'calculate_extra_charges', 
				'Extra charges option', 
				array( $this, 'calculate_extra_charges' ), 
				'ni-payment-gateway-charges-admin', 
				'setting_section_id'
			);  
			
			/*Extra charges*/
			add_settings_field(
				'add_extra_charges_to_payment', 
				'Add extra charges to payemnt', 
				array( $this, 'add_extra_charges_to_payemnt' ), 
				'ni-payment-gateway-charges-admin', 
				'setting_section_id'
			);  
			
		
		}
		function add_extra_charges() {
			$html = '<input type="checkbox" id="add_extra_charges" name="ni-payment-gateway-charges-option[add_extra_charges]" value="1"' . checked(isset( $this->options['add_extra_charges'] ), true, false) . '/>';
			$html .= '<label for="add_extra_charges">Add extra charges to payment gateway</label>';
			echo $html;
	
		}
		function calculate_extra_charges(){
			//print_r( $this->options);
			$intervals = array('exclude_tax'=>'Exclude Tax','include_tax'=>'Include Tax');
			// $intervals = array('12 Hours','daily');
			$html = "";
			$html .= "<select  style=\"width:300px\" name='ni-payment-gateway-charges-option[calculate_extra_charges]'>";
			foreach ($intervals as $k=>$v)
			{
				if (isset($this->options['calculate_extra_charges'])) 
				{
					if ($this->options['calculate_extra_charges']==$k)
						$html .=  "<option value='{$k}' selected>{$v}</option>";
					else
						$html .= "<option value='{$k}'>{$v}</option>";	
					}
					else
					{
						$html .= "<option value='{$k}'>{$v}</option>";
					}
			}
			$html .= "</select>";
			echo $html;
				
		}
		function print_section_info(){
    		print 'Enter your payment gateway charges settings below:';
		}
		function sanitize( $input ){
			if( !is_numeric( $input['id_number'] ) )
				$input['id_number'] = '';  
		
			if( !empty( $input['title'] ) )
				$input['title'] = sanitize_text_field( $input['title'] );
				
			if( !empty( $input['color'] ) )
				$input['color'] = sanitize_text_field( $input['color'] );
			return $input;
		}
		function add_extra_charges_to_payemnt(){
			$admin_url =admin_url("admin.php")."?page=wc-settings&tab=checkout";
			echo "<a href=\"".$admin_url."\">Go to payement tab</a>";
		}
	}
}
?>