<?php 
class WC_easypay_mobile_money extends WC_Payment_Gateway 
{
	protected $template;
	protected static $instance;
	private $payments 			= array();
	public  $clientkey 			= '';
	public  $clientsecret 		= '';
	private $statuses 			= array('processing' => 'Processing Payment', 'on-hold' => 'On Hold', 'completed' => 'Completed');
	private $mainplugin_url		= '';
	protected $adminpage;
	public function __construct(){
		global $easypayment;
		
		$this->id                 	= 'easypay_mobile_money';
		$this->mainplugin_url 		= admin_url("plugin-install.php?tab=search&s=");
		$this->method_title       	= __( '', 'EasyPay Mobile Money' );			
		$this->method_description  	= "<span>Pay using Mobile Money. Mtn, Airtel, Africell and M-sente(UTL) supported.</span>";
		$this->tip_description  	= "Tip: We are going to send a mobile money payment request to the phone number above from PEGASUS. Please approve transaction to complete the payment of this order.</br> Thank you.";
		$this->has_fields         	= false;

		$enabled = ((easypaymentWC_AFFILIATE_KEY=='easypayment' && $this->get_option('enabled')==='') || $this->get_option('enabled') == 'yes' || $this->get_option('enabled') == '1' || $this->get_option('enabled') === true) ? true : false;

		
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		$this->easypayment_settings();	
		
		// Hooks			
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_thankyou_easypay_mobile_money', array( $this, 'easypay_payment' ) );
		add_filter( 'woocommerce_thankyou_order_received_text', array($this,'espy_isa_order_received_text'), 10, 2 );						
		add_action( 'wp_enqueue_scripts', array($this,'easypay_enqueue_loadtimer_scripts' ));
		
		
		
		add_action( 'woocommerce_api_'. strtolower( get_class($this) ), array( $this, 'espy_callback_handler' ) );
		add_action( 'wp_footer',array($this,'easypay_woocommerce_pay_request'),100 );
		return true;
	}				 
	
	private function easypayment_settings(){
		$parent = new WMAMC_wc_easyPay();
		$setting = $parent->setting;
		$this->enabled      = $this->get_option( 'enabled' );
		$this->title        = $this->get_option( 'title' );
		$this->ipnaddress   = $this->get_option( 'ipnaddress' );
		$this->clientkey    = $setting['clientkey'];
		$this->clientsecret = $setting['clientsecret'];
		$this->username    = $setting['username'];
		$this->password = $setting['password'];
		$this->description  = $this->get_option( 'description' );
		$this->ipnwaittime  = $this->get_option( 'ipnwaittime' );
		if (!$this->title)	 $this->title 		= __('Easy payment ', easypaymentWC);
		return true;
	}
  
	public function init_form_fields(){
		// print_r( $this->get_option('clientsecret'));
		// exit;
		$fields = array(
		  'enabled' 	=>  array(
							'title'     => __( 'Enable / Disable ', 'woocommerce' ),
							'desc_tip' => __( 'Enable / Disable EasyPay', 'woocommerce' ),
							'type'     => 'checkbox',							
							'description'     => __( '  ', 'woocommerce' ),
						),
		'title'    => array(
							'title'     => __( 'Method Name', 'woocommerce' ),
							'desc_tip' => __( 'Payment method name', 'woocommerce' ),			
							'type'     => 'text',
							'description'     => __('Payment method name' , 'woocommerce' ),
							'default'   => __('Easypay Mobile Money', 'woocommerce' )
							),
		'description' => array(
			'title' => __( 'Customer Message', 'woocommerce-other-payment-gateway' ),
			'type' => 'textarea',
			'css' => 'width:500px;',
			'default' => $this->method_description,
			'description' 	=> __( 'The message which you want it to appear to the customer in the checkout page.', 'woocommerce-other-payment-gateway' ),
		),
		'tip_description' => array(
			'title' => __( 'Tip Message', 'woocommerce-other-payment-gateway' ),
			'type' => 'textarea',
			'css' => 'width:500px;',
			'default' => $this->tip_description,
			'description' 	=> __( 'The message which you want it to appear to the customer in the checkout page.', 'woocommerce-other-payment-gateway' ),
		),
		'ipnwaittime'    => array(
							'title'     => __( 'Ipn Waiting Time', 'woocommerce' ),
							'desc_tip' => __( 'Maximum waiting time for User to enter IPN', 'woocommerce' ),			
							'type'     => 'number',
							'description'     => __('Maximum waiting time for User to enter IPN e.g 180 (in seconds)' , 'woocommerce' ),
							'default'   => __(180, 'woocommerce' )
							)
		);
		$this->form_fields = $fields;
		return true;
	}
	 
	 
	/*
	 * Forward to WC Checkout Page
	 */
	public function process_payment( $order_id ){
		global $woocommerce;        
		 
		// New Order
		$order = new WC_Order( $order_id );
		$order_data = $order->get_data();
		
		$order_id    = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->id          : $order->get_id();
		$userID      = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->user_id     : $order->get_user_id();
		$order_total = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->order_total : $order->get_total();
		$user_phone   = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order_data['billing']['phone']  : $order_data['billing']['phone'];;
		
		if(!$user_phone){
			$user_phone   = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order_data['shipping']['phone']  : $order_data['shipping']['phone'];;
		}

		// Mark as pending (we're awaiting the payment)
		$order->update_status('pending', __('Awaiting payment notification from easypay', easypaymentWC));
	
			
		// Payment Page
		$payment_link = $this->get_return_url($order);
		$invoice_url = site_url().'/checkout/order-received/'.$order_id.'/?key='.$order->order_key; 
		$total = ($order_total >= 1000 ? number_format($order_total) : $order_total)." ".$arr["user"];
		$orderpage = $order->get_checkout_order_received_url();
		if (!get_post_meta( $order_id, '_easypayment_orderid', true ))
		{
			update_post_meta( $order_id, '_easypayment_orderid', $order_id ); 
			update_post_meta( $order_id, '_easypayment_genratepin', 'pending' );
			update_post_meta( $order_id, '_easypayment_ordertime', time() );
						 
		}
		
		$website = site_url();			
		$url = "https://www.easypay.co.ug/api/";
		
		if($this->clientkey =='' || $this->clientsecret == ''){
			$notice_check = wc_has_notice( 'Client key or Secret Key is not valid.', 'error' );
			if($notice_check == false){
				wc_add_notice( __('Client key or Secret Key is not valid.',easypaymentWC), 'error' );
			}	
		}
		
		
		/** order token **/
		$parent = new WMAMC_wc_easyPay();
		$r_token = $parent->easypay_gximnytkey($this->clientkey,$this->clientsecret,$this->clientkey .'!'.$this->clientsecret .'!!'.$order_id,'e');
		/** Update token for order **/
		update_post_meta($order_id,'easypayorder_token',$r_token);	
		$cart_total =get_post_meta($order_id,'_order_total',true);	
	
		$currency = '';
		if (function_exists('get_woocommerce_currency')) $currency = get_woocommerce_currency();
		
		// $user_phone = '256787491745';
		$user_phone = preg_replace('/\s+/', '', $_POST[$this->id.'-full_phone']);
		$params = array( 			
			"username"=> $this->clientkey,				
			"password"=> $this->clientsecret,
			"action"=>"mmdeposit",
			"currency"	=>	$currency,
			"amount"=>$cart_total,
			"phone"=>$user_phone,
			"reference"=>$r_token,
			"reason"=> "Web Order:$order_id,Invoice URL:$invoice_url,Phone:$user_phone,Amount:$cart_total,Currency:$currency" );
		$postData = json_encode($params);
		$response = wp_remote_post( $url, array(
				'method' => 'POST',
				'timeout' => 0.1,				
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
		$body 			  = json_decode( wp_remote_retrieve_body( $response ));
		$dir = $parent->espy_get_plugin_dir();
		file_put_contents($dir."/log/MOBILE-MONEY-".$order_id.".txt",print_r($response, true), FILE_APPEND);
		if ( 200 != $response_code && ! empty( $response_message ) ) {
			 wc_add_notice( $res['msg'], 'error' );
			 $res =  array('status'=>false,'msg'=>$response_message) ;
		} elseif ( 200 != $response_code ) {
			wc_add_notice( $res['msg'], 'error' );
			$res = array('status'=>false,'msg'=>'Unknown error occurred') ;
		} elseif($body->success == 0){
			wc_add_notice( $body->errormsg, 'error' );
			$res = array('status'=>false,'msg'=>$body->errormsg) ;			
		}else {
			$res =  wp_remote_retrieve_body( $response );
		}
		
		if($res['status']==true){
			 update_post_meta( $order_id, '_easypayment_ordertime', 'success' );

		}			

		// Remove cart
		WC()->cart->empty_cart();
	
		// Return redirect
		return array(
			'result' 	=> 'success',
			'redirect'	=> $payment_link
		);
	}    
	
	/*
	 *  WC Order Checkout Page
	 */
	public function easypay_payment( $order_id ){
		
		global $easypayment;
		
		$order = new WC_Order( $order_id );		
		$order_data = $order->get_data();
		
		$order_id       = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->id             : $order->get_id();
		$order_status   = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->status         : $order->get_status();
		$post_status    = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->post_status    : get_post_status( $order_id );
		$userID         = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->user_id        : $order->get_user_id();
		$order_currency = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->order_currency : $order->get_currency();
		$order_total    = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->order_total    : $order->get_total();
		$user_phone   = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order_data['billing']['phone']  : $order_data['billing']['phone'];;
		if(!$user_phone){
			$user_phone   = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order_data['shipping']['phone']  : $order_data['shipping']['phone'];;
		}
		
		if ($order === false)
		{
			echo '<br><h2>' . __( 'Information', easypaymentWC ) . '</h2>' . PHP_EOL;
			echo "<div class='woocommerce-error'>". sprintf(__( 'The easypayment payment plugin was called to process a payment but could not retrieve the order details for orderID %s. Cannot continue!', easypaymentWC ), $order_id)."</div>";
		}
		elseif ($order_status == "cancelled" || $post_status == "wc-cancelled")
		{
			echo '<br><h2>' . __( 'Information', easypaymentWC ) . '</h2>' . PHP_EOL;
			echo "<div class='woocommerce-error'>". __( "This order's status is 'Cancelled' - it cannot be paid for. Please contact us if you need assistance.", easypaymentWC )."</div>";
		}
		
		else 
		{ 	
			
			$plugin          = "easypaymentwoocommerce";
			$amount          = $order_total; 	
			$currency        = '';
			$period          = "NOEXPIRY";
			$language        = $this->deflang;
		
					
			// try to use original readonly order values
			$original_orderID     = get_post_meta( $order_id, '_easypayment_orderid', true );			
			
			if ($original_orderID && $original_orderID == $order_id ) $userID = $original_userID;
			else $original_orderID = $original_createtime = $original_userID = '';
			
			
			if (!$userID) $userID = "guest"; // allow guests to make checkout (payments)
			
			if (!$userID) 
			{
				echo '<br><h2>' . __( 'Information', easypaymentWC ) . '</h2>' . PHP_EOL;
				echo "<div align='center'><a href='".wp_login_url(get_permalink())."'>
						<img style='border:none;box-shadow:none;' title='".__('You need first to login or register on the website to make Payments', easypaymentWC )."' vspace='10'
						src='".$easypayment->box_image()."' border='0'></a></div>";
			}
			elseif ($amount <= 0)
			{
				echo '<br><h2>' . __( 'Information', easypaymentWC ) . '</h2>' . PHP_EOL;
				echo "<div class='woocommerce-error'>". sprintf(__( "This order's amount is %s - it cannot be paid for. Please contact us if you need assistance.", easypaymentWC ), $amount ." " . $currency)."</div>";
			}
			else
			{
		
				// Payment Box
				if ($amount > 0)
				{	
			
					$current_time = date('Y-m-d H:i:s');
					//$timelimit = strtotime($current_time)+200;
					
					$now = time();
					$two_minutes = $now + (1.5 * 60);  /*added 1.5 min*/
					$timelimit = $two_minutes;					
					
					$payment_status  = $this->easypay_cron_payment_check($timelimit,$order_id);				

				}	
			}
		}

		echo "<br>";
				
		return true;
	}
	
	
	public function espy_request_ordereasypay($website,$orderId,$userID,$amount,$phone){
			
		$subtotal = $woocommerce->cart->subtotal;
		
		$order = new WC_Order( $orderId );		
		
		$order_total = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->order_total : $order->get_total();
		
		$currency = '';
		if (!$currency && function_exists('get_woocommerce_currency')) $currency = get_woocommerce_currency();
		
		
		/** order token **/
		$parent = new WMAMC_wc_easyPay();
		$r_token = $parent->easypay_gximnytkey($this->clientkey,$this->clientsecret,$this->clientkey .'!'.$this->clientsecret .'!!'.$orderId,'e');
		/** Update token for order **/
		update_post_meta($orderId,'easypayorder_token',$r_token);
		$cart_total =get_post_meta($orderId,'_order_total',true);
				
		$url = "https://www.easypay.co.ug/api/";
		$params = array( 
			
			"username"=> $this->clientkey,				
			"password"=> $this->clientsecret,
			"action"=>"mmdeposit",
			"amount"=>$cart_total,				
			"phone"=>$phone,
			"reference"=>$r_token,
			"reason"=> "Web Order:$orderId,Phone:$phone,Amount:$cart_total,Currency:$currency" );
		
		$postData = json_encode($params);
		
		$response = wp_remote_post( $url, array(
				'method' => 'POST',
				'timeout' => 10,
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

			$response_code    = wp_remote_retrieve_response_code( $response );
			$response_message = wp_remote_retrieve_response_message( $response );
			if ( 200 != $response_code && ! empty( $response_message ) ) {
				return (array('status'=>false,'msg'=>$response_message)) ;
			} elseif ( 200 != $response_code ) {
				return (array('status'=>false,'msg'=>'Unknown error occurred')) ;
			} else {
				return ( wp_remote_retrieve_body( $response ));
			}				
	}	    
	
	
	public function easypay_cron_payment_check($limit,$orderId){
		$c_time = $now = time();	
		if( $c_time < $limit){
			$res = $this->easypay_payment_status($orderId);
			$res = (int)$res;
			if($res == 1){	
				echo "<div class='woocommerce-message'>". __('Your payment has been received successfully','woocommerce') ."</div>";
			}else{ /*waiting time process*/
				$paymentgateway = get_post_meta( $orderId, '_payment_method', true );
				if($paymentgateway == "easypay_mobile_money"){
					$order = new WC_Order($orderId);
					$order_date = $order->order_date;
					$invoice_url = site_url().'/checkout/order-received/'.$orderId.'/?key='.$order->order_key;
					$get_subtotal = $order->get_subtotal();
					$order_total = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->order_total : $order->get_total();
					
					$order_data = $order->get_data();
					$ordertimer = get_post_meta( $orderId,'_easypayment_ordertime'); /*in sec*/
					$ordertimer = $ordertimer[0];
					
					if($this->ipnwaittime > 0){
						$orderwaittimer = ( $this->ipnwaittime ) / 60; /*converted to min*/
					}else{
						$orderwaittimer = 3; /*in min*/
					}
					$user_phone   = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order_data['billing']['phone']  : $order_data['billing']['phone'];;
	
					if(!$user_phone){
						$user_phone   = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order_data['shipping']['phone']  : $order_data['shipping']['phone'];;
					}
					$c_time = date('Y-m-d H:i:s');
					$c_time = strtotime($c_time);
				
					//$order_date = strtotime($order_date);
					$order_date = $ordertimer;
					$totalmins = round(($c_time - $order_date) / 60,2);
					$totalmins = max($totalmins,0);
					
					if($totalmins < $orderwaittimer){
						//$totalmins = 10;
						echo __("<p id='countdown_easypay_maincount' totalminsleft='".$orderwaittimer."' startdate='".$order_date."' currdate='".$c_time."' orderid='".$orderId."' orderamt='".$order_total."' orderphn='".$user_phone."' ordertimer='".$ordertimer."'></p>",easypaymentWC);
						echo __("<div class='woocommerce-waiting-message'><h3>Your order is pending for approval</h3><p>Your order requires you to approve the payment request sent to your phone number. Please approve transaction to complete the transaction. Tap retry if you didnot receive the payment request.</br>Thank You</p></div>",easypaymentWC);
						echo __("<div class='woocommerce-message'>Order Status: <span id='chkingordrstatus'>".ucfirst($order->status)."</span></div>",easypaymentWC);
						echo __("<div class='order_received_payment_wrapper' id='order_received_payment_wrapper'>
								<button class='cancelwooc-orderhere'>Cancel Order</button>
								<button class='resendeasypayrequest'>Retry</button>								
							</div>",easypaymentWC);
						
						echo __('<div class="countdown_easypay_wrapper"><div class="countdown countdown-container container">
								<div class="clock row">
									<div class="clock-item clock-days countdown-time-value col-sm-6 col-md-3">
										<div class="wrap">
											<div class="inner">
												<div id="canvas-days" class="clock-canvas"></div>

												<div class="text">
													<p class="val">0</p>
													<p class="type-days type-time">DAYS</p>
												</div>
											</div>
										</div>
									</div>

									<div class="clock-item clock-hours countdown-time-value col-sm-6 col-md-3">
										<div class="wrap">
											<div class="inner">
												<div id="canvas-hours" class="clock-canvas"></div>

												<div class="text">
													<p class="val">0</p>
													<p class="type-hours type-time">HOURS</p>
												</div>
											</div>
										</div>
									</div>

									<div class="clock-item clock-minutes countdown-time-value col-sm-6 col-md-3">
										<div class="wrap">
											<div class="inner">
												<div id="canvas-minutes" class="clock-canvas"></div>

												<div class="text">
													<p class="val">0</p>
													<p class="type-minutes type-time">MINUTES</p>
												</div>
											</div>
										</div>
									</div>

									<div class="clock-item clock-seconds countdown-time-value col-sm-6 col-md-3">
										<div class="wrap">
											<div class="inner">
												<div id="canvas-seconds" class="clock-canvas"></div>

												<div class="text">
													<p class="val">0</p>
													<p class="type-seconds type-time">SECONDS</p>
												</div>
											</div>
										</div>
									</div>
								</div>
							  </div>
							</div>',easypaymentWC);

					}else{
							echo __("<div class='woocommerce-error'>Order Status: ".ucfirst($order->status)."</div>",easypaymentWC);
					}
				}	
			}
		}else{
			echo "<div class='woocommerce-error'>". __('Order Payment Timeout',easypaymentWC) ."</div>";
					echo "<div class='order_received_payment_wrapper'>
						<button class='resendeasypayrequest'>". __('Resend',easypaymentWC) ."</button>			
					</div>";
					
		} 
	}
	
	public function easypay_payment_status($orderId){
		global $wpdb;
		$order_status = $wpdb->get_row( "SELECT $wpdb->posts.post_status  FROM $wpdb->posts WHERE  $wpdb->posts.ID = $orderId ", ARRAY_A );
		
		if( $order_status['post_status'] == 'wc-completed' || $order_status['post_status'] == 'completed' ){
			return 1;
		}else{
			return 0;
		}
		
	}
	
	public function easypay_woocommerce_pay_request(){	?>			
		<script type="text/javascript">
		jQuery(document).ready(function(){
			
				jQuery(function($){
					jQuery("form[name='checkout']").submit(function (e) { 
					 e.preventDefault();
						if(jQuery("#payment_method_easypay_mobile_money").is(":checked")) { 
							
							var billing_phone = jQuery('.woocommerce-checkout input[name="billing_phone"]').val();	
							jQuery("body").addClass("easypay_loader").prepend("<div style='top:30%;'><p class='cust-pin-notification-text'>We have sent a mobile money debit request to "+billing_phone+" from PAYLEO. Please approve it to complete transaction. This request will expire within 2 minutes.</p></div>");								  
							jQuery("body div:first").addClass("easypay_waiting");
						}
					});
				});	
				
		 });
		 jQuery( document.body ).on( 'checkout_error', function() {

			var error_text = jQuery('.woocommerce-error').find('li').first().text();
			if ( error_text != '' ) {
				if(jQuery("#payment_method_easypay_mobile_money").is(":checked")) { 
					if(jQuery('.woocommerce-error').length != 0){
						jQuery("body").removeClass("easypay_loader");								  
						jQuery("body div.easypay_waiting").remove();
					}
				};
			}

		});
		
		if(jQuery("#countdown_easypay_maincount").length != 0) {
		  //it doesn't exist
		  var ipnwaittime = '<?php echo $this->ipnwaittime; ?>';
		  if(ipnwaittime == '' || ipnwaittime <= 0 ){
			  ipnwaittime = 180;
		  } 
		  
		  jQuery('.countdown').final_countdown({
				'start': 000,
				'end': ipnwaittime,
				'now': 001       
			}); 
	
		
			var countDownDate = new Date();
			var totalminsleft = parseFloat(jQuery("#countdown_easypay_maincount").attr('totalminsleft'));
			countDownDate = countDownDate.getTime() + ( totalminsleft * 60 *1000);

			var cntr = 1;
			// Update the count down every 1 second
			var x = setInterval(function() {
									
				if(cntr == 1){
					
					jQuery("#chkingordrstatus").html('checking order status..');
					var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
					<?php $ajax_nonce = wp_create_nonce( "easypay_chk_order_status" ); ?>
					
					var orderid = parseInt(jQuery("#countdown_easypay_maincount").attr('orderid'));
				
					 jQuery.ajax({
						  type: "POST",
						  url: ajaxurl,
						  dataType: "json",
						  data: {action:'easypay_chk_order_status','orderid':orderid,security: '<?php echo $ajax_nonce; ?>',},
						  success: function(response){
							  jQuery("#chkingordrstatus").html(response.status);
							  if(response.status == "Wc-completed" || response.status == "Completed"){
								  window.location.reload();
							  }else{
								  jQuery("#chkingordrstatus").html(response.status);
							  }
						  }
					 });
				}
			   
			   cntr++;
			   
			   if(cntr >= 5){
				   cntr = 1;
			   }
				
			  // Get todays date and time			
			  var now = new Date();
				
			  // Find the distance between now an the count down date
			  var distance = countDownDate - now;

			  // Time calculations for days, hours, minutes and seconds
			  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
			  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
				
			  // If the count down is finished, write some text 
			  if (distance < 0) {
				clearInterval(x);
				
				var orderid = parseInt(jQuery("#countdown_easypay_maincount").attr('orderid'));
				var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
				<?php $ajax_nonce = wp_create_nonce( "easypay_update_order_status" ); ?>
				
				jQuery.ajax({
						  type: "POST",
						  url: ajaxurl,
						  dataType: "json",
						  data: {action:'easypay_update_order_status','orderid':orderid,security: '<?php echo $ajax_nonce; ?>',},
						  success: function(response){
							  window.location.reload();
						  }
				});					
			
			  }
			}, 1000);
				
		}
		
		
		jQuery('body').on('click','.cancelwooc-orderhere',function(){					
				var orderid = parseInt(jQuery("#countdown_easypay_maincount").attr('orderid'));
				var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
				<?php $ajax_nonce = wp_create_nonce( "easypay_update_order_status" ); ?>
				
				jQuery.ajax({
						  type: "POST",
						  url: ajaxurl,
						  dataType: "json",
						  data: {action:'easypay_update_order_status','orderid':orderid,security: '<?php echo $ajax_nonce; ?>',},
						  success: function(response){
							  window.location.reload();
						  }
				});
				
		});
		
		/*resend IPN request*/
		jQuery('body').on('click','.resendeasypayrequest',function(){
			 var ipnwaittime = '<?php echo $this->ipnwaittime; ?>';
			 if(ipnwaittime == '' || ipnwaittime <= 0 ){
				  ipnwaittime = 180;
			 }
			var orderid = parseInt(jQuery("#countdown_easypay_maincount").attr('orderid'));
			var orderamt = parseInt(jQuery("#countdown_easypay_maincount").attr('orderamt'));
			var orderphn = parseInt(jQuery("#countdown_easypay_maincount").attr('orderphn'));
			<?php $ajax_nonce = wp_create_nonce( "espy_resendeasypayrequest" ); ?>
			
			var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
			
				jQuery.ajax({
					  type: "POST",
					  url: ajaxurl,
					  dataType: "json",
					  data: {action:'espy_resendeasypayrequest','orderid':orderid,'orderamt':orderamt,'orderphn':orderphn,security: '<?php echo $ajax_nonce; ?>',},
					  success: function(response){
						 if(response.success == true ){
							  jQuery("#chkingordrstatus").parent().addClass('woocommerce-info');
							  jQuery("#chkingordrstatus").html('Request Sent !!');
							  setTimeout(function(){ 										
									jQuery("#chkingordrstatus").parent().removeClass('woocommerce-info'); 
									window.location.reload();										 
							  },1000);
						 }else{
							  var errorout = '';
							 if(typeof response.data === "undefined"){
								 errorout = response.errormsg;
							 }else if(typeof response.errormsg === "undefined"){
								 errorout = response.data;
							 }else{
								 errorout = 'Unknown error. Try again';
							 }
							 jQuery("#chkingordrstatus").parent().addClass('woocommerce-error');
							  jQuery("#chkingordrstatus").html( errorout);
							  window.location.reload();
						 }
					  }
				 });
				
		});
		</script>			
		<?php	
	}	
	
	public function espy_api_phone_request($action,$phone){
		
		 if($this->clientkey =='' || $this->clientsecret == ''){
			$notice_check = wc_has_notice( 'Client key or Secret Key is not valid.', 'error' );
			if($notice_check == false){
				wc_add_notice( __('Client key or Secret Key is not valid.',easypaymentWC), 'error' );
				return json_encode(array('success'=>false,'errormsg'=>'Client key or Secret Key is not valid.'));
			}	
		 }
			
		$url = "https://www.easypay.co.ug/api/";
		$params = array( 			
			"username"=> $this->clientkey,				
			"password"=> $this->clientsecret,
			"action"=>$action,					
			"phone"=>$phone,
			 );
		
		$postData = json_encode($params);
		
		$response = wp_remote_post( $url, array(
				'method' => 'POST',
				'timeout' => 10,
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
	
		return $response_body = wp_remote_retrieve_body( $response ); 		
	}
		
	public function easypay_enqueue_loadtimer_scripts(){
		wp_enqueue_style('easypay_ordertimer-style', plugins_url('/assets/css/easypay_timer.css', __FILE__ ) );	
		wp_enqueue_script('easypay_ordertimerload-script', plugins_url('/assets/js/kinetic.js', __FILE__ ),'','',true );
		wp_enqueue_script('easypay_ordercountdownjs-script', plugins_url('/assets/js/jquery.final-countdown.js', __FILE__ ),'','',true );
	}
	
	public function espy_isa_order_received_text( $text, $order ) {
		
		$de_order = json_decode($order);
		$status = $this->easypay_payment_status($de_order->id);
		if($status != true){
			$text = '';
		}			
		return $text;
	}
	
	public function espy_callback_handler(){
		
		$post = file_get_contents('php://input',true); 		if(empty($post)){			$response = array('status'=>0,'message'=>'No Post Data');			wp_send_json($response);		}
		$data = json_decode($post);		$parent = new WMAMC_wc_easyPay();
		$dir = $parent->espy_get_plugin_dir();
		file_put_contents($dir."/log/IPN-".date('Y-m-d').".txt",print_r($data, true), FILE_APPEND);
		// echo 'hello';
		if($data->reference){
				/** order token **/
				
				$d_token = $parent->easypay_gximnytkey($this->clientkey,$this->clientsecret,$data->reference,'d');
				$de_token = explode('!!',$d_token);
				$order_id = $de_token['1'];
				
				/** get token for order **/
				$getTokenMeta = get_post_meta($order_id,'easypayorder_token',true);
				
				if($getTokenMeta == $data->reference){
			
					global $woocommerce;
					$order = new WC_Order( $order_id);
					$order->update_status('completed', "payment completed for order id $order_id ");
					
				update_post_meta( $order_id, '_easypayment_reference', $data->reference );
				update_post_meta( $order_id, '_easypayment_transactionId', $data->transactionId );
				update_post_meta( $order_id, '_easypayment_amount', $data->amount );
				if(!empty($data->PaymentType) && strtolower($data->PaymentType)=='card'){
					update_post_meta( $order_id, '_easypayment_currencyCode', $data->currency );
				} else {
					update_post_meta( $order_id, '_easypayment_currencyCode', $data->currencyCode );
				}
				update_post_meta( $order_id, '_easypayment_order_ipn_response', $data );
					/** remove token **/
					delete_post_meta($order_id, 'easypayorder_token'); 					
				}
				
		}

		die();
	}
	
	public function payment_fields(){ ?>
		<fieldset>
			<div class="" style="text-align: center;">
				<a href="https://www.easypay.co.ug/" target="_blank" style="box-shadow:none;">
					<img src="https://www.easypay.co.ug/mobilemoney.jpg" title="Easy Pay" style="box-shadow:none;" width="300" />
				</a>
			</div>
			<p><small><?php echo $this->description; ?> <a href="https://www.easypay.co.ug/" target="_blank" style="box-shadow:none;text-decoration: underline;">Easypay</a></small></p>
			<p class="form-row form-row-wide">
				<label for="<?php echo $this->id; ?>-phone">Phone<span class="required">*</span></label>
				<input name="<?php echo $this->id; ?>-phone" type="text" id="<?php echo $this->id; ?>-phone" class="input-text" />
				<input name="<?php echo $this->id; ?>-full_phone" type="hidden" id="<?php echo $this->id; ?>-full_phone" class="input-text" />
			</p>	
			<p><small><?php echo $this->tip_description; ?></small></p>
			<div class="clear"></div>
		</fieldset>
		<script>
			jQuery( document ).ready(function() {
				jQuery("form[name='checkout']").submit(function (e) {
					var walletPhone = jQuery("#<?php echo $this->id; ?>-phone").val();
					var countryData = jQuery("#<?php echo $this->id; ?>-phone").intlTelInput("getSelectedCountryData");
					jQuery("#<?php echo $this->id; ?>-full_phone").val(countryData.dialCode+''+walletPhone);
					 e.preventDefault();
				});
				jQuery("#<?php echo $this->id; ?>-phone").intlTelInput({
					separateDialCode: true,
					allowDropdown:false,
					autoHideDialCode: false,
					onlyCountries:["ug"],
					initialCountry: "ug",
				});
			});
		</script>
		<?php
	}
	
	public function disable_easypay_gateways( $gateways ) {
		// Remove EasyPay payment gateway
		unset( $gateways['easypay_mobile_money'] );
		return $gateways;
	}

	public function espay_instance() {
		
		if ( is_null( self::$instance ) ) {
			
			self::$instance = new self();
			
		}
		
		return self::$instance;
		
	}
	
}