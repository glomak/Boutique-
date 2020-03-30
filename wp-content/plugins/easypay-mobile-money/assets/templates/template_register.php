<?php session_start(); ?>
<div class="step_container wrap woocommerce">	
	<div class="">
		<div class="header_section_cs">
		

			<legend> <?php // _e("Enter cart limit values","wmamc-cart-limit");?></legend>
		</div>
		
		<div class="wizard_container">
			<?php if(empty($this->clientkey) && empty($this->clientsecret)){ ?>
			<div class="alert_msg_box"></div>
			<div class="main_tabs">
				<span class="login active">Login</span><span class="register">Register</span>
			</div>
			<div id="LoginForm" class="active">
				<form class="espy_login_verify_form" name="espy_login_verify_form">
					<h3>What is your phone number?</h3>
					<p><input required name="phone" placeholder="Phone" oninput="this.className = ''"></p>
					<p><input type="submit" value="Verify Number"/></p>
				</form>
				<form class="espy_login_form" name="espy_login_form" style="display:none;">
					<h3>Please enter your phone and pin to login</h3>
					<p><input name="phone" placeholder="Phone" disabled></p>
					<p><input type="number" style="-webkit-text-security: disc;height:40px" required name="pin" placeholder="Pin"></p>
					<p><input type="submit" value="Login"/></p>
				</form>
			</div>
			<div id="regForm" >
				<?php //print_r($_SESSION); ?>
				<input name="hidden-parameter" type="hidden" value="" />
				<!-- One "tab" for each step in the form: -->
				<div class="tab1 tab" step="1">
					<h1 >Create Account:</h1>
					<form class="espy_reg_form" name="espy_reg_form">
						<h3>What is your phone number?</h3>
						<p><input name="phone" value="" placeholder="Phone" oninput="this.className = ''"></p>
						<h3>How would you like us to verify your phone number?</h3>
						<p><label>By Call: <input type="radio" <?php if($_SESSION['verification_type']==1){ echo "checked"; }?> name="verification_type" value="1" required /></label></p>
						<p><label>By Sms: <input type="radio" <?php if($_SESSION['verification_type']==2){ echo "checked"; }?> name="verification_type" value="2" required /></label></p>
								
						<div style="overflow:auto;">
						  <div style="float:right;">
							<button type="submit" class="nextBtn_cs nextBtn" step="1">Next</button>
						  </div>
						</div>
					</form>
				</div>

				<div class="tab2 tab" step="2">
					<h3 class="misscall-verify" style="display:none;">Please enter the last four digits of the missed call we have sent you.</h3>
					<h3 class="sms-verify" style="display:none;">Please enter the four digit code we have send you as an SMS.</h3>
					<form class="espy_reg_verify_form" name="espy_reg_verify_form">
						<p><input name="pin" placeholder="Pin" oninput="this.className = ''"></p>
						
						<div style="overflow:auto;">
						  <div style="float:right;">
							<button type="button" class="prevbtn_cs prevBtn" step="2">Previous</button>
							<button type="submit" class="nextBtn_cs nextBtn" step="2">Next</button>
						  </div>
						</div>
					</form>
				</div>

				<div class="tab3 tab" step="3">	<h1>Personal Detail: </h1>
					<form class="espy_create_ac" name="espy_create_ac">
						<small>Please enter your name and email address.</small>
						<p><input value="" name="name" placeholder="Name" oninput="this.className = ''"></p>
						<p><input value="" name="email" placeholder="Email" oninput="this.className = ''"></p>
						<p><input value="" disabled name="phone" placeholder="Phone" oninput="this.className = ''"></p>
						<h3>What PIN would you like to use?</h3>
						<small>Your PIN is your password to login into Easypay Wallet. Remember it. It should be numeric and a minimum of 6 digits.</small>
						<p><input name="pin" placeholder="Pin" oninput="this.className = ''"></p>
						<p><input value="" name="city" placeholder="City" oninput="this.className = ''"></p>
						<p><input value="" name="address" placeholder="Address" oninput="this.className = ''"></p>
						<h3>Pick a question below and answer it</h3>
						<small>You will need to remember this question and answer in case you need to recover your account. Write it down somewhere and keep it safe.</small>
						<p id="question_list"></p>
						<h3>Answer to above question</h3>
						<p><input name="answer" placeholder="Answer" oninput="this.className = ''"></p>
						
						<div style="overflow:auto;">
						  <div style="float:right;">
							<button type="button" class="prevbtn_cs prevBtn" step="3">Previous</button>
							<button type="submit" class="nextBtn_cs nextBtn" step="3">Next</button>
						  </div>
						</div>
					</form>
				</div>
				<div class="tab4 tab" step="4">
					<h1>Account created.</h1>
					<p>Your account has been activated, you can login with your details.</p>
					<p class="warn-message" style="display:none;"></p>
					<p>Thanks for sign up.</p>
				</div>
				<!-- Circles which indicates the steps of the form: -->
				<div style="opacity: 0; text-align:center;margin-top:40px;">
				  <span class="step"></span>
				  <span class="step"></span>
				  <span class="step"></span>
				  <span class="step"></span>
				</div> <!---->
			</div> 
			<?php } else { ?>
				<h3 class="alert_msg_box">Account is linked. <a href="<?php echo admin_url('admin.php?page=wc-settings&tab=checkout&section='.$this->id); ?>">Setting</a></h3>
			<?php } ?>
		</div>
	</div>		
</div>