<?php 
class WC_easypay_visa extends WC_Payment_Gateway {
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
		
		$this->id                 	= 'easypay_visa';
		$this->mainplugin_url 		= admin_url("plugin-install.php?tab=search&s=");
		$this->method_title       	= __( '', 'Card Payment - Visa/Mastercard' );
		$this->method_description  	= "<span>Have your customers pay with VISA.</span>";
		$this->has_fields         	= false;

		$enabled = ((easypaymentWC_AFFILIATE_KEY=='easypayment' && $this->get_option('enabled')==='') || $this->get_option('enabled') == 'yes' || $this->get_option('enabled') == '1' || $this->get_option('enabled') === true) ? true : false;

		
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		$this->easypayment_settings();	
		// Hooks
		add_action( 'woocommerce_thankyou_easypay_visa', array( $this, 'easypay_payment' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );	
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
		if (!$this->title)	 $this->title 		= __('Easy payment ', easypaymentWC);
		return true;
	}
  
	public function init_form_fields(){
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
								'default'   => __('Easypay Visa', 'woocommerce' )
								),
			'description' => array(
				'title' => __( 'Customer Message', 'woocommerce-other-payment-gateway' ),
				'type' => 'textarea',
				'css' => 'width:500px;',
				'default' => 'Payment by easypay wallet.',
				'description' 	=> __( 'The message which you want it to appear to the customer in the checkout page.', 'woocommerce-other-payment-gateway' ),
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
		$parent = new WMAMC_wc_easyPay();
		$order = new WC_Order( $order_id );
		$order_data = $order->get_data();
		
		$order_id    = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->id          : $order->get_id();
		$userID      = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->user_id     : $order->get_user_id();
		$order_total = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->order_total : $order->get_total();
		
		$user_phone   = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order_data['billing']['phone']  : $order_data['billing']['phone'];
		$billingCountry   = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? WC()->countries->countries[ $order->shipping_country ]  : WC()->countries->countries[ $order->get_shipping_country() ];
		
		$billingCustomerName =  $order_data['billing']['first_name'].' '.$order_data['billing']['last_name'];
		$billingEmail =  $order_data['billing']['email'];
		$billing_address =  $order_data['billing']['address_1'].' '.$order_data['billing']['address_2'];
		


		if(!$user_phone){
			$user_phone   = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order_data['shipping']['phone']  : $order_data['shipping']['phone'];
		}
		

		$cart_total =get_post_meta($order_id,'_order_total',true);	
		$invoice_url = site_url().'/checkout/order-received/'.$order_id.'/?key='.$order->order_key; 
		
		$currency = '';
		if (function_exists('get_woocommerce_currency')) $currency = get_woocommerce_currency();
		
		// Mark as pending (we're awaiting the payment)
		$order->update_status('pending', __('Awaiting payment notification from easypay', easypaymentWC));
		$r_token = $parent->easypay_gximnytkey($this->clientkey,$this->clientsecret,$this->clientkey .'!'.$this->clientsecret .'!!'.$order_id,'e');
		update_post_meta($order_id,'easypayorder_token',$r_token);	
		
		$card_holder = trim($_POST[$this->id.'-card_holder']);
		$card_number = trim($_POST[$this->id.'-card_number']);
		$card_cvv = trim($_POST[$this->id.'-card_cvv']);
		$expiry_month = trim($_POST[$this->id.'-expiry_month']);
		$expiry_year = trim($_POST[$this->id.'-expiry_year']);
		
		$url = 'https://www.easypay.co.ug/api/';
		$para = array(
					"username"	=>	$this->clientkey,
					"password"	=>	$this->clientsecret,
					"action"=>"cardpayment",
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
		
		$paymentResponse = json_decode($parent->easyPayHTTPPost($url,$para));
		//LOG VISA PROCESS
		$dir = $parent->espy_get_plugin_dir();
		file_put_contents($dir."/log/VISA-".$order_id.".txt",print_r($paymentResponse, true), FILE_APPEND);
		/* $paymentResponse = (object)array(
							'success'=>1,
							'data'=>(object)array(
								'requiresPIN'=>0,
								'requiresOTP'=>1,
								'otpInfo'=>(object)array(
									'chargeResponseMessage'=>'Pending, Validation',
									'authModelUsed'=>'VBVSECURECODE',
									'authurl'=>'https://coreflutterwaveprod.com/flwmpgs/trxauth?hid=FLW97453f51c9b944c5b2c4738a9c183700',
									'paymentType'=>'card'
								),
							'success'=>1)
						); */
		if($paymentResponse){
			if(!empty($paymentResponse->success) && !empty($paymentResponse->data)){
				if(!empty($paymentResponse->data->success)){
				// $order->update_status('completed', __($paymentResponse->data, easypaymentWC));
				update_post_meta( $order_id, '_easypayment_visa_requiresPIN', $paymentResponse->data->requiresPIN );
				update_post_meta( $order_id, '_easypayment_visa_requiresOTP', $paymentResponse->data->requiresOTP );
				update_post_meta( $order_id, '_easypayment_visa_suggestedAUTH', $paymentResponse->data->suggestedAUTH );
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
					$order->update_status('failed', __('failed', easypaymentWC));
				}
			} else {
				$order->update_status('failed', __($paymentResponse->errormsg, easypaymentWC));
			}
			update_post_meta( $order_id, '_easypayment_order_response', $paymentResponse ); 
		}
		// Payment Page
		$payment_link = $this->get_return_url($order);
		// Remove cart
		WC()->cart->empty_cart();
		// Return redirect
 		return array(
			'result' 	=> 'success',
			'redirect'	=> $payment_link
		); 
	}					
				
	public function payment_fields(){ ?>
		<fieldset>
			<div class="" style="text-align: center;">
				<a href="https://www.easypay.co.ug/" target="_blank" style="box-shadow:none;">
					<img src="https://www.easypay.co.ug/wp-content/uploads/thegem-logos/logo_0f000f4e3223b628f93bdeccd9993d2c_1x.png" title="Easy Pay" style="box-shadow:none;" />
				</a>
			</div>
			<p style="padding-top:10px;"><small><?php echo $this->description; ?> <a href="https://www.easypay.co.ug/" target="_blank" style="box-shadow:none;text-decoration: underline;">Easypay</a></small></p>

			<p class="form-row form-row-wide" style="margin-bottom: 0;">
				<label for="<?php echo $this->id; ?>-card_number">Card Number<span class="required">*</span></label>
				<div class="input-wrapped full" id="validateCard">
					<input name="<?php echo $this->id; ?>-card_number" type="text" id="<?php echo $this->id; ?>-card_number" class="input-text" autocomplete="off" data-creditcard="true" style="padding-left:55px;" />
				</div>
			</p>
			<p class="form-row form-row-wide">
				<label for="<?php echo $this->id; ?>-card_holder">Card Holder Name<span class="required">*</span></label>
				<input name="<?php echo $this->id; ?>-card_holder" type="text" id="<?php echo $this->id; ?>-card_holder" class="input-text" />
			</p>
			<p class="form-row form-row-wide">
				<label for="<?php echo $this->id; ?>-expiry_month">Expiry Date<span class="required">*</span></label>
				<select name="<?php echo $this->id; ?>-expiry_month" id="<?php echo $this->id; ?>-expiry_month" class="input-text" style="width: 15%;">
					<option value="">Month</option>
					<?php for($m=1;$m<=12;$m++){ ?>
					<option value="<?php echo $m; ?>"><?php echo date("M", mktime(0, 0, 0, $m, 10)); ?></option>
					<?php } ?>
				</select>
				<select name="<?php echo $this->id; ?>-expiry_year" id="<?php echo $this->id; ?>-expiry_year" class="input-text" style="width: 13%;">
					<option value="">Year</option>
					<?php for($y=2015;$y<=2030;$y++){ ?>
					<option value="<?php echo $y; ?>"><?php echo $y; ?></option>
					<?php } ?>
				</select>
			</p>
			<p class="form-row form-row-wide">
				<label for="<?php echo $this->id; ?>-card_cvv">CVV<span class="required">*</span></label>
				<input name="<?php echo $this->id; ?>-card_cvv" type="password" id="<?php echo $this->id; ?>-card_cvv" class="input-text" autocomplete="off" />
			</p>
			<div class="clear"></div>
		</fieldset>
		<script>
			jQuery( document ).ready(function() {				
				var creditCard = jQuery('#<?php echo $this->id; ?>-card_number');
				function validateCard() {

				  creditCard.after('<i class="icon-ok"></i>');
				  creditCard.before('<span class="card"></span>');
				  var cardHolder = jQuery('span.card');


				  creditCard.validateCreditCard(function(result) {

					  console.log('test');
					var ele = jQuery(this),
						paymentIcons = ele.hasClass('*[class*="card-"]'),
						checkmark = ele.siblings('.icon-ok');


					var removeIcon = ele.removeClass(function(index, css) {
					  return (css.match (/\bcard-\S+/g) || []).join(' ');
					});


					  if (result.card_type !== null) {
						// // background image didn't work w/ cc autofill mobile
						//ele.addClass('card-'+result.card_type.name);
						//ele.before('<span class="card-'+result.card_type.name+'"></span>');
						cardHolder.html('<span class="card-'+result.card_type.name+'"></span>');
					  }
					  else {
						//ele.addClass('card-generic');
						//ele.before('<span class="card-generic"></span>');
						cardHolder.html('<span class="card-generic"></span>');
					  }


					if (result.valid) return checkmark.addClass('valid');
					else return checkmark.removeClass('valid');

					}, {
					  accept: ['visa', 'mastercard', 'discover', 'amex']
					});

				}

				if (creditCard.data('creditcard') == true) {
					validateCard();
					// creditCard.on('change', function() {
					//   var timer = setTimeout(function() {
					//     validateCard();
					//   },400);
					// });
				}
			});
		</script>
		<?php
	}
	public function easypay_payment($order_id){
		global $easypayment;
		
		$order = new WC_Order( $order_id );		
		$order_data = $order->get_data();
		
		$order_id       = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->id             : $order->get_id();
		$order_status   = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->status         : $order->get_status();
		$post_status    = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->post_status    : get_post_status( $order_id );
		$userID         = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->user_id        : $order->get_user_id();
		$order_currency = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->order_currency : $order->get_currency();
		$order_total    = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->order_total    : $order->get_total();
		$user_phone   = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order_data['billing']['phone']  : $order_data['billing']['phone'];
		
		if(!$user_phone){
			$user_phone   = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order_data['shipping']['phone']  : $order_data['shipping']['phone'];;
		}
		
		$visaProcessing = get_post_meta($order_id,'_easypayment_visa_processing',true);
		$visaRequiresPIN = get_post_meta($order_id,'_easypayment_visa_requiresPIN',true);
		$visaRequiresOTP = get_post_meta($order_id,'_easypayment_visa_requiresOTP',true);
		$visaAuthurl = get_post_meta($order_id,'_easypayment_visa_authurl',true);
					
		if ($order === false) {
			echo '<br><h2>' . __( 'Information', easypaymentWC ) . '</h2>' . PHP_EOL;
			echo "<div class='woocommerce-error'>". sprintf(__( 'The easypayment payment plugin was called to process a payment but could not retrieve the order details for orderID %s. Cannot continue!', easypaymentWC ), $order_id)."</div>";
		} elseif ($order_status == "cancelled" || $post_status == "wc-cancelled"){
			echo '<br><h2>' . __( 'Information', easypaymentWC ) . '</h2>' . PHP_EOL;
			echo "<div class='woocommerce-error'>". __( "This order's status is 'Cancelled' - it cannot be paid for. Please contact us if you need assistance.", easypaymentWC )."</div>";
		} else {
			$plugin          = "easypaymentwoocommerce";
			$amount          = $order_total; 	
			$currency        = '';	
					
			// try to use original readonly order values
			$original_orderID     = get_post_meta( $order_id, '_easypayment_orderid', true );			
			
			if ($original_orderID && $original_orderID == $order_id ) $userID = $original_userID;
			else $original_orderID = $original_createtime = $original_userID = '';
			
			
			if (!$userID) $userID = "guest"; // allow guests to make checkout (payments)
			
			if (!$userID){
				echo '<br><h2>' . __( 'Information', easypaymentWC ) . '</h2>' . PHP_EOL;
				echo "<div align='center'><a href='".wp_login_url(get_permalink())."'>
						<img style='border:none;box-shadow:none;' title='".__('You need first to login or register on the website to make Payments', easypaymentWC )."' vspace='10'
						src='".$easypayment->box_image()."' border='0'></a></div>";
			} elseif ($amount <= 0){
				echo '<br><h2>' . __( 'Information', easypaymentWC ) . '</h2>' . PHP_EOL;
				echo "<div class='woocommerce-error'>". sprintf(__( "This order's amount is %s - it cannot be paid for. Please contact us if you need assistance.", easypaymentWC ), $amount ." " . $currency)."</div>";
			} else {
				if ($amount > 0 && $order_status=='pending'){
					if($visaProcessing){
						if($visaRequiresPIN==0 && $visaRequiresOTP==0){
							delete_post_meta($order_id, '_easypayment_visa_processing'); 
								// echo $requirePinHtml;
						} elseif($visaRequiresOTP==1 && $visaAuthurl){
							/* echo "<script>
							window.open('".$visaAuthurl."','winname','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=800,height=500');
								  </script>"; */
							echo "<section class='woocommerce-easypay-pin'>
									<iframe width='650' height='400' src='".$visaAuthurl."'></iframe>
								</section>";
						} elseif($visaRequiresPIN==1){
				$requirePinHtml = '<div class="payment_box payment_method_easypay_visa">
										<fieldset>
											<p class="form-row form-row-wide woocommerce-validated">
											  <label for="'.$this->id.'-card_pin">PIN<span class="required">*</span></label>
											  <input name="'.$this->id.'-card_pin" id="'.$this->id.'-card_pin" class="input-text" autocomplete="off" type="password">
											</p>
											<button class="'.$this->id.'-submit_pin" data-id="'.$order_id.'">Submit</button>
											<button class="'.$this->id.'-cancel_order" data-id="'.$order_id.'">Cancel Order</button>
										</fieldset>
									  </div>';
								echo $requirePinHtml;
						}
					}
					// echo 'Processing';
				}
			}
		}
		echo "<br>";
		return true;
	}
		
	public function easypay_woocommerce_pay_request(){	?>			
			<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery('body').on('click','.<?php echo $this->id; ?>-cancel_order',function(){
					var orderid = parseInt(jQuery(this).data('id'));
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
				jQuery('body').on('click','.<?php echo $this->id; ?>-submit_pin',function(){
					var orderid = parseInt(jQuery(this).data('id'));
					var pin = jQuery('#<?php echo $this->id; ?>-card_pin').val();
					var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
					<?php $ajax_nonce = wp_create_nonce( "easypay_visa_order_process" ); ?>
					jQuery.ajax({
						  type: "POST",
						  url: ajaxurl,
						  dataType: "json",
						  data: {action:'easypay_visa_order_process','orderid':orderid,'pin':pin,security: '<?php echo $ajax_nonce; ?>',},
						  success: function(response){
							  window.location.reload();
						  }
					});
				});
			});
			</script>
<?php }
	public function espay_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}