<?php
/*
Plugin Name: 		Easypay Mobile Money
Plugin URI: 	
Description: 		Allow mobile money (MTN,Airtel,M-Sente & Africell Money), Visa & Mastercard payments within your woocommerce stores and wordpress. Easypay combines the open mobile money api, open visa api to bring you the latest in Payments. EasyPay Plugin for <a href="https://wordpress.org/plugins/woocommerce/">WooCommerce 3.0+</a>. 
Version: 		  	1.1.7
Author: 			Easypay Mobile Wallet
Text Domain: 		easypay-mobile-money
WC requires at least: 3.1
WC tested up to: 	3.6
License: 		    GPLv2
License URI: 		http://www.gnu.org/licenses/gpl-2.0.html
*/

$active_plugins = get_option( 'active_plugins', array() );
if( !in_array( 'woocommerce/woocommerce.php',$active_plugins ) ){
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
	deactivate_plugins( plugin_basename( __FILE__ ) );
	if( isset( $_GET['activate'] ))
      unset( $_GET['activate'] );
}

class WMAMC_wc_easyPay {
  
	protected static $instance;
	protected $template;	
	public  $clientkey 			= '';
	public  $clientsecret 		= '';

	public function __construct() {
		DEFINE('easypaymentWC', 'easypayment-woocommerce');	
		//EASY PAY API USER NAME
		DEFINE('easypaymentUSER', 'rYBtHpja2Du6E3Qtwaz4avDNCRJyAzDd');	
		//EASY PAY API PASSWORD
		DEFINE('easypaymentPASS', 'W3rGcpGvQEkEpn5vDVabWeYjBsyGXb8F');	
		register_activation_hook(__FILE__, array($this,'easypay_plugin_activate'));
		add_action( 'admin_menu', array($this,'easypay_set_submenu' ));
		add_action( 'admin_enqueue_scripts',array($this,'easypay_admineasypay_enqueue_scripts') );
		
		add_action( 'wp_enqueue_scripts',array($this,'easypay_enqueue_scripts') );
		
		add_action('wp_ajax_espay_api_render', array($this,'espay_api_render'));
		add_action('plugins_loaded', array($this,'easypayment_wc_gateway_load'), 20);
		add_filter('woocommerce_payment_gateways', array($this,'add_easypay_payment_gateway'));

		// add_filter('woocommerce_payment_gateways', 'add_easypay_payment_gateway');
		add_action( 'woocommerce_after_checkout_validation', array($this,'easypayment_checkout_validation'));
		
		add_action( 'wp_ajax_easypay_update_order_status', array($this,'easypay_update_order_status' ));
		add_action( 'wp_ajax_nopriv_easypay_update_order_status', array($this,'easypay_update_order_status' ));
		
		add_action( 'wp_ajax_easypay_chk_order_status', array($this,'easypay_chk_order_status' ));
		add_action( 'wp_ajax_nopriv_easypay_chk_order_status', array($this,'easypay_chk_order_status' ));
		
		add_action( 'wp_ajax_espy_resendeasypayrequest', array($this,'espy_resendeasypayrequest' ));
		add_action( 'wp_ajax_nopriv_espy_resendeasypayrequest', array($this,'espy_resendeasypayrequest' ));
	
	
		add_action( 'wp_ajax_easypay_visa_order_process', array($this,'easypay_visa_order_process' ));
		add_action( 'wp_ajax_nopriv_easypay_visa_order_process', array($this,'easypay_visa_order_process' ));
		add_action( 'admin_init', array($this,'easypay_plugin_redirect' ));
		$this->setting = $this->espy_getPayment_settings();
	}
	
	public function easypay_plugin_activate() {
		add_option('easypay_do_activation_redirect', true);
	}

	public function easypay_plugin_redirect() {
		if (get_option('easypay_do_activation_redirect', false)) {
			delete_option('easypay_do_activation_redirect');
			if(!isset($_GET['activate-multi'])){
				wp_redirect( admin_url( 'admin.php?page=espay_setup_wizard' ) );
			}
		}
	}
	
	public function espy_getPayment_settings(){
		$settings = get_option('woocommerce_easypayments_settings');
		return $settings;
	}
	
	public function easypay_enqueue_scripts(){
		wp_enqueue_script('easypay_input-script', plugins_url('/assets/js/intlTelInput.min.js', __FILE__ ),'','',true  );
		wp_enqueue_style('eeasypay_input-style', plugins_url('/assets/css/intlTelInput.css', __FILE__ ) );
		wp_enqueue_style('eeasypay_creditCardValidator-style', plugins_url('/assets/css/card.css', __FILE__ ) );
		wp_enqueue_script('easypay_creditCardValidator-script', plugins_url('/assets/js/jquery.creditCardValidator.js', __FILE__ ),'','',true  );
	}
	
	public function easypay_admineasypay_enqueue_scripts(){
		wp_enqueue_style('easypay_order-style', plugins_url('/assets/css/easypay.css', __FILE__ ) );	
		if(!empty($_GET['page']) && $_GET['page']=='espay_setup_wizard'){
			
			wp_enqueue_script('espy_admineasypay_orderjs-script', plugins_url('/assets/js/easypay.js', __FILE__ ),'','',true  );	
			wp_enqueue_script('espy_admineasypay_sweet-script', plugins_url('/assets/js/sweetalert2.min.js', __FILE__ ),'','',true  );	
			wp_enqueue_script('espy_admineasypay_input-script', plugins_url('/assets/js/intlTelInput.min.js', __FILE__ ),'','',true  );	
			wp_enqueue_style('espy_admineasypay_sweet-style', plugins_url('/assets/css/sweetalert2.min.css', __FILE__ ) );
			wp_enqueue_style('espy_admineasypay_input-style', plugins_url('/assets/css/intlTelInput.css', __FILE__ ) );
			
			wp_enqueue_style( 'espay_wizard-css', plugins_url('/assets/css/espay_wizard.css', __FILE__),array(),'1' );	
			wp_enqueue_script('espay_script-script',  plugins_url('/assets/js/espay_script.js', __FILE__ ) , array('jquery'), '1', true);
		}
	}

	public function easypay_set_submenu() {
		if($this->setting['clientkey'] && $this->setting['clientsecret']){
			$menuText = 'Easypay Account';
		} else {
			$menuText = 'Easypay Setup';
		}
		add_submenu_page(
		'woocommerce',
			'Easypay Setup Wizard',
			$menuText,
			'manage_woocommerce',
			'espay_setup_wizard',
			array( $this, 'espay_setup_render' )
		);
		
	}
	
	public function espay_setup_render() {
		if(!empty($_GET['woocommerce_easypayments_unlink']) && $_GET['woocommerce_easypayments_unlink']==1){
			$payment_settings['enabled'] = '';
			$payment_settings['clientkey'] = '';
			$payment_settings['clientsecret'] = '';
			$payment_settings['username'] = '';
			$payment_settings['password'] = '';
			$payment_settings['phone'] = '';
			$payment_settings['title'] = '';
			$payment_settings['pin'] = '';
			$payment_settings['website'] = '';
			$payment_settings['ipn'] = '';
			$payment_settings['unlink'] = '';
			update_option( 'woocommerce_easypayments_settings', $payment_settings );
			add_filter( 'woocommerce_available_payment_gateways',array($this,'disable_easypay_gateways') );
			wp_redirect( admin_url( 'admin.php?page=espay_setup_wizard' ) );
		}
		if($this->setting['phone']){
			$payment_settings = $this->espy_getPayment_settings();	
			$userName = ($payment_settings ? $payment_settings['clientkey'] : '');
			$password = ($payment_settings ? $payment_settings['clientsecret'] : '');
			
			$this->limit =  25;
			
			$this->pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
			
			$this->offset = ( $this->pagenum - 1 ) * $this->limit;
			
			$url = 'https://www.easypay.co.ug/api/';
			$payload = array(
						"username"=> $userName,
						"password"=> $password,
					);
			$paraBal = array(
						"action"=> "checkbalance"
					);
			$paraStat = array(
							"action"=> "getstatements",
							"page"=>$this->pagenum,
							"count"=>$this->limit 
						);
					
			$payloadBal = array_merge($payload,$paraBal);
			$payloadStat = array_merge($payload,$paraStat);
			$balance = json_decode($this->easyPayHTTPPost($url,$payloadBal));
			$statement = json_decode($this->easyPayHTTPPost($url,$payloadStat));
			// $totalPages = ceil($totalPosts/$limit);
			// print_r($statement);
			$this->balance = $balance;
			$this->statement = $statement;
			// $this->totalPages = $totalPages;
			
			$this->template = $this->espy_get_template('statement');
		} else {
			$this->template = $this->espy_get_template('register');
		}
	
	}
	
	public function espy_get_template($template){
		$template_name = 'assets/templates/template_'.$template.'.php';
		include  $this->espy_get_plugin_dir().$template_name;
	}
	
	public function espy_get_plugin_dir(){
		return plugin_dir_path( __FILE__ );
	}
	
	public function easyPayRegisterStep1($url, $response){
		$url = 'https://www.easypay.co.ug/api/api.php';
		$para = array(
			"username"=> easypaymentUSER,
			"password"=> easypaymentPASS,
			"controller"=>"Api",
			"action"=>"fetchsecurityquestion"
		);
		$result = json_decode($this->easyPayHTTPPost($url,$para));
		$html ="";
		if($result->success==1){
			$questions = $result->data;
			foreach($questions as $question){
				$html = $html.'<p><input value="'.$question->id.'" name="question_id" type="radio"/><label>'.$question->qn.'</label></p>';						
			}
		}				
		$result->questions = $html;
		if($result->questions){
			$result->success==0;
		}
		return $result;
		// print_r($response);
	}
			
	public function easyPayHTTPPost($url, $params){
		$postData = json_encode($params);
		$response = wp_remote_post( $url, array(
			'method' => 'POST',
			'timeout' => 50,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($postData)
			),
			'body' => $postData,
			'cookies' => array()
			)
		);
		$result = wp_remote_retrieve_body($response);
		return $result;
	}
	
	public function espay_api_render(){
		session_start();
		$data = $_REQUEST;
		
		$payload = array(
			"username"=> easypaymentUSER,
			"password"=> easypaymentPASS,
		);
		if($data['step']=='verify' && isset($data['phone']) && $data['phone']!="" && !empty($data['dialCode'])){
			// $this->template = $this->espy_get_api('verify_phone');
			$url = 'https://www.easypay.co.ug/api/api.php';
			$para = array(
				"controller"=>"Api",
				"action"=>"ismember",
				"phone"=> $data['dialCode'].$data['phone']
			);
			$payload = array_merge($payload,$para);
			$this->template = json_decode($this->easyPayHTTPPost($url,$payload));
		} elseif($data['step']=='login' && isset($data['pin']) && $data['pin']!="" && !empty($data['phone']) && !empty($data['dialCode'])){
			// $this->template = $this->espy_get_api('login');
			 $url = 'https://www.easypay.co.ug/api/api.php'; 
			 $ipnurl = site_url('wc-api/wc_easypay_mobile_money');
			$para = array(
				 "controller"=>"Api",
				 "action"=>"wppluginlogin",
				 "phone"=> $data['dialCode'].$data['phone'],
				 "pin"=> $data['pin'],
				 "title"=> get_bloginfo( 'name' ),
				 "website"=> site_url(),
				 "ipn"=> $ipnurl
			 );
			$payload = array_merge($payload,$para);
			$response = json_decode($this->easyPayHTTPPost($url,$payload));
			if($response->success==1){
				$this->easyPayLoginSave($response,$payload);
				$response->sendto = admin_url('admin.php?page=wc-settings&tab=checkout&section='.$this->id);
			}
			$this->template = $response;
		} else {
			if( isset($data['step']) && $data['step']==1 && isset($data['phone']) && $data['phone']!="" && !empty($data['dialCode'])){
				$url = 'https://www.easypay.co.ug/api/api.php';
				$paraVefify = array(
					"controller"=>"Api",
					"action"=>"ismember",
					"phone"=> $data['dialCode'].$data['phone']
				);
				$payloadVerify = array_merge($payload,$paraVefify);
				$verifyPhone = json_decode($this->easyPayHTTPPost($url,$payloadVerify));
				// print_r($url);
				// print_r($verifyPhone);
				// print_r($payloadVerify);
				// exit;
				if($verifyPhone && $verifyPhone->success==0){
					if($data['verification_type']==1){
						// $this->template = $this->espy_get_api('verify_call');
					 $para = array(
									"controller"=> "Api",
									"phone"=> $data['dialCode'].$data['phone'],
									"action"=> "sendmissedcall"
								);
					} else {
						// $this->template = $this->espy_get_api('send_sms');
						 $para = array(
										"controller"=> "Api",
										"phone"=> $data['dialCode'].$data['phone'],
										"action"=> "sendregsms"
									);
					}
					$payload = array_merge($payload,$para);
					$response = json_decode($this->easyPayHTTPPost($url,$payload));
					$this->template = $response;
				} else {
					$this->template = array('success'=>2,'errormsg'=>'Already Registered');
				}
			} elseif( isset($data['step']) && $data['step']==2 && isset($data['pin']) && $data['pin']!="" ){
				// $this->template = $this->espy_get_api('verify_code');
				$url = 'https://www.easypay.co.ug/api/api.php';
				$para = array(
					"controller"=> "Api",
					"pin"=> $data['pin'], //Last 4 digits of missed call
					"id"=> $data['id']
				);
				if($data['vType']==1){
					$para['action'] = 'verifymissedcall';
				} else {
					$para['action'] = 'verifyregsms';
				}
				$payload = array_merge($payload,$para);
				$response = json_decode($this->easyPayHTTPPost($url,$payload));
				if($response->success==1){
					$response = ($this->easyPayRegisterStep1($url,$response));
				}
				$this->template = $response;
			} elseif( !empty($data['step']) && $data['step']==3 && !empty($data['name']) && !empty($data['email']) && !empty($data['phone']) && !empty($data['dialCode']) && !empty($data['pin']) && !empty($data['city'])){
				$url = 'https://www.easypay.co.ug/api/api.php';
				 $para = array(
					  "controller"=> "Api",
					  "action"=> "registeruser",
					  "name"=> $data['name'],
					  "email"=> $data['email'],
					  "phone"=> $data['dialCode'].$data['phone'],
					  "pin"=> $data['pin'],
					  "city"=> $data['city'],
					  "address"=> $data['address'],
					  "question_id"=> $data['question_id'],
					  "answer"=> $data['answer']
				  );
				$payload = array_merge($payload,$para);
				// print_r($payload);
				$response = json_decode($this->easyPayHTTPPost($url,$payload));
				// $this->template = $this->espy_get_api('create_ac');
				$this->template = $response;
			} else {
				$this->template = array('success'=>0,'errormsg'=>'Try Again.');
			}
		}
		wp_send_json($this->template);
		exit;
	}

	public function easyPayLoginSave($response,$payload){
		$payment_settings = $this->espy_getPayment_settings();	
		$espy_clientkey = $response->data->clientKey;
		$espy_clientsecret = $response->data->clientSecret;
		
		// $payment_settings['espy_ipnurl'] = $payload['ipn'];
		$payment_settings['clientkey'] = $espy_clientkey;
		$payment_settings['clientsecret'] = $espy_clientsecret;
		$payment_settings['username'] = $payload['username'];
		$payment_settings['password'] = $payload['password'];
		$payment_settings['phone'] = $payload['phone'];
		$payment_settings['title'] = $payload['title'];
		$payment_settings['pin'] = $payload['pin'];
		$payment_settings['website'] = $payload['website'];
		$payment_settings['ipn'] = $payload['ipn'];
		update_option( 'woocommerce_easypayments_settings', $payment_settings );
	}

	public function easypayment_wc_gateway_load(){
		if (!class_exists('WC_Payment_Gateway')) return;
		require 'class-woocommerce-mobile-money-payment-gateway.php';
		require 'class-woocommerce-wallet-payment-gateway.php';
		require 'class-woocommerce-visa-payment-gateway.php';
	}

	public function add_easypay_payment_gateway( $methods ){
		if (!class_exists('WC_Payment_Gateway')) return;
		// print_r($methods);
		if ($this->setting['phone'] && !in_array('WC_easypay_wallet', $methods)) {
			$methods[] = 'WC_easypay_wallet';
		}
		if ($this->setting['phone'] && !in_array('WC_easypay_visa', $methods)) {
			$methods[] = 'WC_easypay_visa';
		}
		if ($this->setting['phone'] && !in_array('WC_easypay_mobile_money', $methods)) {
			$methods[] = 'WC_easypay_mobile_money';
		} 
		return $methods; 
	}

	public function disable_easypay_gateways( $gateways ) {
		// Remove EasyPay payment gateway
		unset( $gateways['easypay_visa'] );
		unset( $gateways['easypay_wallet'] );
		unset( $gateways['easypay_mobile_money'] );
		return $gateways;
	}
	
	public function easypayment_checkout_validation($posted) {
		global $woocommerce;
		if ($posted['payment_method']== 'easypay_wallet' || $posted['payment_method']== 'easypay_mobile_money'){
			$easypay_phone = preg_replace('/\s+/', '', $_REQUEST[$posted['payment_method'].'-full_phone']);
			$url = 'https://www.easypay.co.ug/api/api.php';
			$payload = array(
				"username"=> easypaymentUSER,
				"password"=> easypaymentPASS,
			);
			/*$paraVefify = array(
				"controller"=>"Api",
				"action"=>"ismember",
				"phone"=> $easypay_phone
			);
			$payloadVerify = array_merge($payload,$paraVefify);
			$verifyPhone = json_decode($this->easyPayHTTPPost($url,$payloadVerify));
			if($verifyPhone && $verifyPhone->success == 0){
				$msg = $verifyPhone->errormsg;
				$notice_check = wc_has_notice( $msg, 'error' );
				if($notice_check == false)
					wc_add_notice( __( $msg ), 'error' );
			}*/
		}
	
		if ($posted['payment_method']== 'easypay_wallet' ){
			if (!empty($_REQUEST[$posted['payment_method'].'-full_phone'])){
					$para = array(
						 "controller"=>"Api",
						 "action"=>"wppluginlogin",
						 "title"=> get_bloginfo( 'name' ),
						 "website"=> site_url(),
						 "ipn"=> '',
						 "phone"=> $easypay_phone,
						 "pin"=> $_REQUEST[$posted['payment_method'].'-pin']
					 );
					$payloadLogin = array_merge($payload,$para);
					$loginResponse = json_decode($this->easyPayHTTPPost($url,$payloadLogin));
					if($loginResponse && $loginResponse->success){
						$loginData = $loginResponse->data;
						$clientKey = $loginData->clientKey;
						 $payloadBal = array(
									"username"=> $loginData->clientKey,
									"password"=> $loginData->clientSecret,
									"action"=> "checkbalance"
								);
						$balanceResonse = json_decode($this->easyPayHTTPPost('https://www.easypay.co.ug/api/',$payloadBal)); 
						if($balanceResonse){
							if($balanceResonse->success == 1){
								if(!$balanceResonse->data){
									$notice_check = wc_has_notice( 'Wallet amount is Zero', 'error' );
									if($notice_check == false)
										wc_add_notice( __( 'Wallet amount is Zero' ), 'error' );
								}
							} else {
								$msg = $balanceResonse->errormsg;
								$notice_check = wc_has_notice( $msg, 'error' );
								if($notice_check == false)
									wc_add_notice( __( $msg ), 'error' );
							}
						}
					} else {
						$msg = $loginResponse->errormsg;
						$notice_check = wc_has_notice( $msg, 'error' );
						if($notice_check == false)
							wc_add_notice( __( $msg ), 'error' );
					}
			} else {
				$notice_check = wc_has_notice( 'Your Eaypay phone & PIN required.', 'error' );
				if($notice_check == false)
				wc_add_notice( __( 'Your Eaypay phone & PIN required.' ), 'error' );
			}
		}
		
		if ($posted['payment_method']== 'easypay_visa' ){
			if (empty($_REQUEST[$posted['payment_method'].'-card_number'])){
				$notice_check = wc_has_notice( 'Card Number is required.', 'error' );
				if($notice_check == false)
				wc_add_notice( __( 'Card Number is required.' ), 'error' );
			}
			if (empty($_REQUEST[$posted['payment_method'].'-card_holder'])){
				$notice_check = wc_has_notice( 'Card holder name is required.', 'error' );
				if($notice_check == false)
				wc_add_notice( __( 'Card holder name is required.' ), 'error' );
			}
			if (empty($_REQUEST[$posted['payment_method'].'-expiry_month']) && empty($_REQUEST[$posted['payment_method'].'-expiry_year'])){
				$notice_check = wc_has_notice( 'Card expiry date is required.', 'error' );
				if($notice_check == false)
				wc_add_notice( __( 'Card expiry date is required.' ), 'error' );
			}
			if (empty($_REQUEST[$posted['payment_method'].'-card_cvv']) && empty($_REQUEST[$posted['payment_method'].'-expiry_year'])){
				$notice_check = wc_has_notice( 'Card CVV is required.', 'error' );
				if($notice_check == false)
				wc_add_notice( __( 'Card CVV is required.' ), 'error' );
			}
		}
	}
	
	public function easypay_update_order_status(){
		$chk  = check_ajax_referer( 'easypay_update_order_status', 'security',false );
		if($chk){
			$orderid = intval($_POST["orderid"]);
			$order = new WC_Order($orderid);
			if($order->status){
				$order->update_status('cancelled', '');
			}
			$arr = array("status" => true);
		} else {
			$arr = array("status" => 'Nonce is invalid. Please try again');
		}
		wp_send_json($arr);
	}

	public function easypay_chk_order_status(){
			$chk  = check_ajax_referer( 'easypay_chk_order_status', 'security',false );
			if( $chk ){
				$orderid = intval($_POST["orderid"]);
				$order = new WC_Order($orderid);
				$status =  ucfirst($order->status);
				$arr = array("status" => $status);	
			}else{
				$arr = array("status" => 'Nonce is invalid. Please try again');
			}			
			wp_send_json($arr);
	}

	public function espy_resendeasypayrequest(){
		 $chk  = check_ajax_referer( 'espy_resendeasypayrequest', 'security',false );
		// print_r($this->setting);
		if($chk){
			$order_id   = intval($_POST['orderid']); 
			$user_phone = intval($_POST['orderphn']);
						
			$url = "https://www.easypay.co.ug/api/";		
			
			// $payment_settings = espy_getPayment_settings();	
			
			$clientkey = $this->setting['clientkey']; 
			$clientsecret = $this->setting['clientsecret'];   
			
			/** order token **/
			$r_token = $this->easypay_gximnytkey($clientkey,$clientsecret,$clientkey.'!'.$clientsecret.'!!'.$order_id,'e');
			/** Update token for order **/
			update_post_meta($order_id,'easypayorder_token',$r_token);
			$cart_total =get_post_meta($order_id,'_order_total',true);
			
			/* clientkey and clientsecret check */
			if($clientkey =='' || $clientsecret == ''){
				$notice_check = wc_has_notice( 'Client key or Secret Key is not valid.', 'error' );
				if($notice_check == false){
					wc_add_notice( __('Client key or Secret Key is not valid.',easypaymentWC), 'error' );
					echo  json_encode(array('success'=>false,'errormsg'=>'Client key or Secret Key is not valid.'));
					exit;
				}
			 }
			 
			$currency = '';
			$orderKey = $this->espy_get_orderkey($order_id); 			
			$invoice_url = site_url().'/checkout/order-received/'.$order_id.'/?key='.$orderKey; 
			if (function_exists('get_woocommerce_currency')) $currency = get_woocommerce_currency();
			
			$params = array( 			
				"username"=> $clientkey,				
				"password"=> $clientsecret,
				"action"=>"mmdeposit",
				"currency"	=>	$currency,
				"amount"=>$cart_total,
				"phone"=>$user_phone,
				"reference"=>$r_token,
				"reason"=> "Web Order:$order_id,Invoice URL:$invoice_url,Phone:$user_phone,Amount:$cart_total,Currency:$currency" );
			
			$postData = json_encode($params);
			
			$response = wp_remote_post( $url, array(
					'method' => 'POST',
					'timeout' => 5,					
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => array(
								'Content-Type: application/json',
								'Content-Length: ' . strlen($postData)
							),
					'body' => $postData,
					'cookies' => array()
					)
				);
			$response_code    = wp_remote_retrieve_response_code( $response );
			$response_message = wp_remote_retrieve_response_message( $response );
			$body			  = json_decode(wp_remote_retrieve_body( $response )); 
			$dir = $this->espy_get_plugin_dir();
			file_put_contents($dir."/log/MMDEPOSIT-RESEND-".$order_id.".txt",print_r($body, true), FILE_APPEND);
			if ( 200 != $response_code && ! empty( $response_message ) ) {					
					echo  $res =  json_encode( array('success'=>false,'errormsg'=>$response_message)) ;
					exit;
			} elseif ( 200 != $response_code ) {					
					echo $res = json_encode( array('success'=>false,'errormsg'=>$response_message) );
					exit;
			} elseif($body->success == false){			
					echo $res = json_encode( array('success'=>false,'errormsg'=>$body->errormsg) );
					exit;
			}else{
				//reset the order and timer
				$current_tim = time();
				update_post_meta( $order_id, '_easypayment_ordertime', $current_tim );
				
				echo $res =  wp_remote_retrieve_body( $response ); 
				exit;
			}
		}else{
			echo $res = json_encode( array('success'=>false,'errormsg'=>'Nonce is invalid. Please try again') );
			exit;
		}
	}
	
	public function easypay_visa_order_process(){
		$chk  = check_ajax_referer( 'easypay_visa_order_process', 'security',false );
		if($chk){
	
			if(!empty($_POST["orderid"]) && !empty($_POST["pin"])){
				$order_id = intval($_POST["orderid"]);
				$pin = trim($_POST["pin"]);
				$visaRequiresPIN = get_post_meta($order_id,'_easypayment_visa_requiresPIN',true);
				if($visaRequiresPIN){
					$order = new WC_Order($order_id);
					$order_data = $order->get_data();
					if($order->status=='pending'){
						// $order->update_status('cancelled', '');
						// $arr = array("status" => true);
						$billingEmail =  $order_data['billing']['email'];
						$billing_address =  $order_data['billing']['address_1'].' '.$order_data['billing']['address_2'];
						$billingCountry   = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? WC()->countries->countries[ $order->shipping_country ]  : WC()->countries->countries[ $order->get_shipping_country() ];
						$user_phone   = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order_data['billing']['phone']  : $order_data['billing']['phone'];
						
						$r_token = $this->easypay_gximnytkey($this->setting['clientkey'],$this->setting['clientsecret'],$this->setting['clientkey'] .'!'.$this->setting['clientsecret'] .'!!'.$order_id,'e');
						update_post_meta($order_id,'easypayorder_token',$r_token);	
						
						$order_total = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->order_total : $order->get_total();
						
						$currency = '';
						if (function_exists('get_woocommerce_currency')) $currency = get_woocommerce_currency();
						
						$invoice_url = site_url().'/checkout/order-received/'.$order_id.'/?key='.$order->order_key;
						
						$cart_total =get_post_meta($order_id,'_order_total',true);	
						
						$card_holder = trim(get_post_meta($order_id,'_easypayment_visa_card_holder',true));
						$suggestedAUTH = trim(get_post_meta($order_id,'_easypayment_visa_suggestedAUTH',true));
						$card_number = trim(get_post_meta($order_id,'_easypayment_visa_card_number',true));
						$card_cvv = trim(get_post_meta($order_id,'_easypayment_visa_card_cvv',true));
						$expiry_month = trim(get_post_meta($order_id,'_easypayment_visa_expiry_month',true));
						$expiry_year = trim(get_post_meta($order_id,'_easypayment_visa_expiry_year',true));
						
						$url = 'https://www.easypay.co.ug/api/';
						$para = array(
									"username"	=>	$this->setting['clientkey'],
									"password"	=>	$this->setting['clientsecret'],
									"action"=>"cardpayment",
									"auth"=>$suggestedAUTH,
									"pin"=>$pin,
									"name"=>$card_holder,
									"cardno"=>$card_number,
									"cvv"=>$card_cvv,
									"month"=>$expiry_month,
									"year"=>$expiry_year,
									"email"=>$billingEmail,
									"address"=>$billing_address,
									"country"=>$billingCountry,
									"phone"=>$user_phone,
									"reference"=>$r_token,
									"amount"	=>	$order_total,
									"currency"	=>	$currency,
									"reason"	=>	"Web Order:$order_id,Method:{$this->id},Invoice URL:$invoice_url,Phone:$user_phone,Amount:$cart_total,Currency:$currency"
						);
						$paymentResponse = json_decode($this->easyPayHTTPPost($url,$para));
						$dir = $this->espy_get_plugin_dir();
						file_put_contents($dir."/log/VISA-PIN-".$order_id.".txt",print_r($paymentResponse, true), FILE_APPEND);
						if($paymentResponse){
							if(!empty($paymentResponse->success) && !empty($paymentResponse->data)){
								if(!empty($paymentResponse->data->success)){
								// $order->update_status('completed', __($paymentResponse->data, easypaymentWC));
								update_post_meta( $order_id, '_easypayment_visa_requiresPIN', $paymentResponse->data->requiresPIN );
								update_post_meta( $order_id, '_easypayment_visa_requiresOTP', $paymentResponse->data->requiresOTP );
								update_post_meta( $order_id, '_easypayment_visa_processing', 1 );
									if(!empty($paymentResponse->data->requiresOTP) && !empty($paymentResponse->data->otpInfo) && !empty($paymentResponse->data->otpInfo->authurl)){
										update_post_meta( $order_id, '_easypayment_visa_authurl', $paymentResponse->data->otpInfo->authurl );
									}
									if(!empty($paymentResponse->data->requiresPIN)){
										update_post_meta( $order_id, '_easypayment_visa_card_holder', $card_holder );
										update_post_meta( $order_id, '_easypayment_visa_card_number', $card_number );
										update_post_meta( $order_id, '_easypayment_visa_card_cvv', $card_cvv );
										update_post_meta( $order_id, '_easypayment_visa_expiry_year', $expiry_year );
										update_post_meta( $order_id, '_easypayment_visa_expiry_month', $expiry_month );
									}
								} else {
									$order->update_status('failed', __($paymentResponse->errormsg, easypaymentWC));
								}
							} else {
								$order->update_status('failed', __($paymentResponse->errormsg, easypaymentWC));
							}
							update_post_meta( $order_id, '_easypayment_order_response', $paymentResponse ); 
						}
					} else {
						$arr = array("status" => true,'msg'=>'Order not in pending state.');
					}
				} else {
					$arr = array("status" => true,'msg'=>'Invalid Request.');
				}
			} else {
				$arr = array("status" => true,'msg'=>'Invalid Request.');
			}
		} else {
			$arr = array("status" => true,'msg'=>'Nonce is invalid. Please try again.');
		}
		wp_send_json($arr);
	}
	
	public function easypay_gximnytkey($secret_key='',$secret_iv='',$string, $action = 'e'){
			if(!$secret_key) {
				$secret_key = 'easypay_123qwerty2345dfgvb';
			}
			if(!$secret_iv) {
				$secret_iv = 'easypay_123qwerty2345dfgvb';
			}
			
			$output = false;
			$encrypt_method = "AES-256-CBC";
			$key = hash( 'sha256', $secret_key );
			$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
		 
			if( $action == 'e' ) {
				$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
			}
			else if( $action == 'd' ){
				$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
			}
		 
			return $output;
	}
	
	public function espy_get_orderkey($order_id){
		global $woocommerce;
		$orderobj = new WC_Order($order_id);
		return $orderobj->order_key;
	}

}


new WMAMC_wc_easyPay();