<?php 
class WC_easypay_wallet extends WC_Payment_Gateway {
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
		
		$this->id                 	= 'easypay_wallet';
		$this->mainplugin_url 		= admin_url("plugin-install.php?tab=search&s=");
		$this->method_title       	= __( '', 'EasyPay Wallet' );			
		$this->method_description  	= "<span>Have your customers pay with Easypay Wallet.</span>";
		$this->has_fields         	= false;

		$enabled = ((easypaymentWC_AFFILIATE_KEY=='easypayment' && $this->get_option('enabled')==='') || $this->get_option('enabled') == 'yes' || $this->get_option('enabled') == '1' || $this->get_option('enabled') === true) ? true : false;

		
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		$this->easypayment_settings();	
		// Hooks			
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );				
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
								'default'   => __('Easypay Mobile Money', 'woocommerce' )
								),
			'description' => array(
				'title' => __( 'Customer Message', 'woocommerce-other-payment-gateway' ),
				'type' => 'textarea',
				'css' => 'width:500px;',
				'default' => 'Payment by easypay wallet.',
				'description' 	=> __( 'The message which you want it to appear to the customer in the checkout page.', 'woocommerce-other-payment-gateway' ),
			)
		);
		if($this->get_option( 'clientkey' ) && $this->get_option( 'clientsecret' )){
		$unlinkField['unlink'] = array(
									'title'     => __( 'Unlink Active Account', 'woocommerce' ),
									'desc_tip' => __( 'Unlink Active Account', 'woocommerce' ),
									'type'     => 'checkbox',
									'description'     => __( '  ', 'woocommerce' ),
								);
			$fields = array_merge($fields,$unlinkField);
		}
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
		$user_phone   = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order_data['billing']['phone']  : $order_data['billing']['phone'];;
		
		if(!$user_phone){
			$user_phone   = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order_data['shipping']['phone']  : $order_data['shipping']['phone'];;
		}
		

		$cart_total =get_post_meta($order_id,'_order_total',true);	
		$invoice_url = site_url().'/checkout/order-received/'.$order_id.'/?key='.$order->order_key; 
		
		$currency = '';
		if (function_exists('get_woocommerce_currency')) $currency = get_woocommerce_currency();
		
		// Mark as pending (we're awaiting the payment)
		$order->update_status('pending', __('Awaiting payment notification from easypay', easypaymentWC));
	
		$easypay_phone = preg_replace('/\s+/', '', $_POST[$this->id.'-full_phone']);
		$url = 'https://www.easypay.co.ug/api/';
		$para = array(
					"username"	=>	$this->clientkey,
					"password"	=>	$this->clientsecret,
					"action"	=>	"pay",
					"phone"		=>	$easypay_phone,
					// "phone"		=>	'917888422038',
					"pin"		=>	$_POST[$this->id.'-pin'],
					// "pin"		=>	'1991',
					// "amount"	=>	"0.1",
					"amount"	=>	$order_total,
					"currency"	=>	$currency,
					"reason"	=>	"Web Order:$order_id,Method:{$this->id},Invoice URL:$invoice_url,Phone:$easypay_phone,Amount:$cart_total,Currency:$currency"
		);
		
		$paymentResponse = json_decode($parent->easyPayHTTPPost($url,$para));
		$dir = $parent->espy_get_plugin_dir();
		file_put_contents($dir."/log/WALLET-".$order_id.".txt",print_r($paymentResponse, true), FILE_APPEND);
		if($paymentResponse){
			if($paymentResponse->success){
				$order->update_status('completed', __($paymentResponse->data, easypaymentWC));
				update_post_meta( $order_id, '_easypayment_txid', $paymentResponse->txid );
				update_post_meta( $order_id, '_easypayment_finalTxId', $paymentResponse->finalTxId );
				update_post_meta( $order_id, '_easypayment_finalAmt', $paymentResponse->finalAmt );
				update_post_meta( $order_id, '_easypayment_finalCurrency', $paymentResponse->finalCurrency );
			} else {
				$order->update_status('failed', __($paymentResponse->errormsg, easypaymentWC));
			}
			if (!get_post_meta( $order_id, '_easypayment_orderid', true )){
				update_post_meta( $order_id, '_easypayment_orderid', $order_id ); 
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
			<p><small><?php echo $this->description; ?> <a href="https://www.easypay.co.ug/" target="_blank" style="box-shadow:none;text-decoration: underline;">Easypay</a></small></p>
			<p class="form-row form-row-wide">
				<label for="<?php echo $this->id; ?>-phone">Phone<span class="required">*</span></label>
				<input name="<?php echo $this->id; ?>-phone" type="text" id="<?php echo $this->id; ?>-phone" class="input-text" />
				<input name="<?php echo $this->id; ?>-full_phone" type="hidden" id="<?php echo $this->id; ?>-full_phone" class="input-text" />
			</p>	
			<p class="form-row form-row-wide">
				<label for="<?php echo $this->id; ?>-pin">PIN<span class="required">*</span></label>
				<input name="<?php echo $this->id; ?>-pin" type="password" id="<?php echo $this->id; ?>-pin" class="input-text" />
			</p>
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
					autoHideDialCode: false,
					// hiddenInput: "<?php echo $this->id; ?>-full_phone",
					// utilsScript:'https://s3-us-west-2.amazonaws.com/s.cdpn.io/32471/utils.js',
					initialCountry: "auto",
					preferredCountries: ["ug", "ke", 'cd'],
					geoIpLookup: function(callback) {
						if (localStorage.getItem('countryCode')){	
							callback(localStorage.getItem('countryCode'));
						} else {
							jQuery.get('https://www.easypay.co.ug/api/geoip.php', function() {}, "json").always(function(resp) {
								var countryCode = (resp && resp.country) ? resp.country : "";
								localStorage.setItem('countryCode',countryCode);
								callback(countryCode);
							});
						}
					}
				});
			});
		</script>
		<?php
	}
	
	public function espay_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}