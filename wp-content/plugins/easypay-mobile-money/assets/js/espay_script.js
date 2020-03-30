jQuery( window ).load(function() {

});
jQuery( document ).ready(function() {
  
	jQuery('.tab1').show();
	jQuery('body').on('click','.main_tabs .login',function(e){
		jQuery('.alert_msg_box').html('');
		if (!jQuery(this).hasClass('active')) {
			jQuery(this).addClass('active');
			jQuery('.main_tabs .register').removeClass('active');
		
		}
		if (!jQuery('#LoginForm').hasClass('active')) {
			jQuery('#LoginForm').addClass('active');
			jQuery('#regForm').removeClass('active');
		}
	});

	jQuery('body').on('click','.main_tabs .register',function(e){
		jQuery('.alert_msg_box').html('');
		if (!jQuery(this).hasClass('active')) {
			jQuery(this).addClass('active');
			jQuery('.main_tabs .login').removeClass('active');
		}
		if (!jQuery('#regForm').hasClass('active')) {
			jQuery('#regForm').addClass('active');
			jQuery('#LoginForm').removeClass('active');
		}
	});

	jQuery('body').on('submit','.espy_login_verify_form',function(e){
		e.preventDefault();
		jQuery('.alert_msg_box').html('');
	/* 	 if (!validateForm()) return false;alert('jj'); exit(); */
		 
		/* var username = jQuery('.espy_login_verify_form input[name=username]').val();
		var passwords = jQuery('.espy_login_verify_form input[name=password]').val();*/
		var phone = jQuery('.espy_login_verify_form input[name=phone]').val();
		
		var form_data = jQuery('.espy_login_verify_form').serializeArray();
		var countryData = jQuery(".espy_login_verify_form input[name=phone]").intlTelInput("getSelectedCountryData"); // get country data as obj 
		var dialCode = countryData.dialCode
		// console.log(countryData.iso2);
		// console.log(countryCode);
		form_data.push({name: 'action', value: 'espay_api_render'});
		form_data.push({name: 'step', value: 'verify'});
		form_data.push({name: 'dialCode', value: dialCode});
		jQuery('#regForm input[name=hidden-parameter]').attr('dialCode',dialCode).attr('iso2',countryData.iso2);
		jQuery.ajax({
			
			type: "POST",
			url: ajaxurl,
			datatype : "json",
			data: form_data,
			success: function(response){
				if(!response){
					swal({
					  title: 'Error',
					  text: "Try Again!",
					  type: 'error',
					  showCancelButton: false,
					  confirmButtonColor: '#3085d6',
					  cancelButtonColor: '#52D3C7',
					  confirmButtonText: 'OK',
					  cancelButtonText: 'Cancel'
					}).then(function(result) {
					})
					return false;
				}
				//var data = jQuery.parseJSON(d);
				// var data = d;
				if(response.success==1){
					// jQuery('.alert_msg_box').html('Phone number verified successfully.');
					
					// jQuery('.espy_login_form').show();
					// jQuery('.espy_login_verify_form').hide();
					swal({
					  title: 'Successful',
					  text: "Your phone number already exist!",
					  type: 'success',
					  showCancelButton: true,
					  confirmButtonColor: '#3085d6',
					  cancelButtonColor: '#52D3C7',
					  confirmButtonText: 'Login',
					  cancelButtonText: 'Cancel'
					}).then(function(result) {
						// console.log(result.value);
					  if (result){
						jQuery('.espy_login_form').show();
						var verifyPhone = jQuery('.espy_login_verify_form input[name=phone]').val();
						jQuery('.espy_login_form input[name=phone]').val(verifyPhone);
						jQuery('.espy_login_verify_form').hide();
						jQuery(".espy_login_form input[name=phone]").intlTelInput({
							initialCountry : countryData.iso2,
							separateDialCode: true,
							preferredCountries: ["ug", "ke", 'cd'],
						});
					  }
					})
					//jQuery('.wizard_container .register').click();
				}else{
					swal({
					  title: 'Error',
					  text: "Your phone number doesn't exist!",
					  type: 'error',
					  showCancelButton: true,
					  confirmButtonColor: '#3085d6',
					  cancelButtonColor: '#52D3C7',
					  confirmButtonText: 'Register',
					  cancelButtonText: 'Cancel'
					}).then(function(result) {
						// console.log(result.value);
					  if (result){
						jQuery('.wizard_container .register').click();
					  }
					})
				}
				return;
				
		   }
		   
		}); 
		
	});
	
	jQuery('body').on('submit','.espy_login_form',function(e){
		e.preventDefault();
		jQuery('.alert_msg_box').html('');
		var form_data = jQuery('.espy_login_form').serializeArray();
		var phone = jQuery('.espy_login_verify_form input[name=phone]').val();
		var countryData = jQuery(".espy_login_verify_form input[name=phone]").intlTelInput("getSelectedCountryData"); // get country data as obj 
		var dialCode = countryData.dialCode;
		form_data.push({name: 'action', value: 'espay_api_render'});
		form_data.push({name: 'step', value: 'login'});
		form_data.push({name: 'phone', value: phone});
		form_data.push({name: 'dialCode', value: dialCode});
		
		jQuery.ajax({
			
			type: "POST",
			url: ajaxurl,
			datatype : "json",
			data: form_data,
			success: function(response){
				//var data = jQuery.parseJSON(d);
				// var data = d;
				if(response.success==1){
					// jQuery('.alert_msg_box').html('Login sucessfully.');
					swal({
					  title: 'Successful',
					  text: "Login sucessfully!",
					  type: 'success',
					  showCancelButton: false,
					  confirmButtonColor: '#3085d6',
					  cancelButtonColor: '#52D3C7',
					  confirmButtonText: 'OK',
					  cancelButtonText: 'Cancel'
					}).then(function(result) {
						window.location.href = response.sendto;
					})
					jQuery('.espy_login_verify_form').show();
					jQuery('.espy_login_form').hide();
				}else{
					// jQuery('.alert_msg_box').html('Authantication failed.');
					swal({
					  title: 'Error',
					  text: response.errormsg,
					  type: 'error',
					  showCancelButton: false,
					  confirmButtonColor: '#3085d6',
					  cancelButtonColor: '#52D3C7',
					  confirmButtonText: 'OK',
					  cancelButtonText: 'Cancel'
					}).then(function(result) {
					})
				}
				return;
		    }
		   
		}); 
		
	});
	jQuery(".espy_login_verify_form input[name=phone], #regForm .espy_reg_form input[name=phone]").intlTelInput({
		separateDialCode: true,
		initialCountry: "auto",
		preferredCountries: ["ug", "ke", 'cd'],
		geoIpLookup: function(callback) {
			if (localStorage.getItem('countryCode'))
			{	
				callback(localStorage.getItem('countryCode'));
			} else
			{
				jQuery.get('https://www.easypay.co.ug/api/geoip.php', function() {}, "json").always(function(resp) {
				var countryCode = (resp && resp.country) ? resp.country : "";
				localStorage.setItem('countryCode',countryCode);
				callback(countryCode);
			});
			}
		},
	});
});

// jQuery('body').on('click','.nextBtn_cs',function(e){
jQuery('body').on('submit','.espy_reg_form, .espy_reg_verify_form, .espy_create_ac',function(e){
	 //var btn = jQuery(this).find("button[type=button]:focus");
	  var submit = jQuery("button[type=submit]:focus",this); 
	  console.log(submit.attr('class'));
	if(submit.hasClass('nextBtn')){
		jQuery('.alert_msg_box').html('');
		var n = submit.attr('step');
		// e.preventDefault();
		if (n == 1 && !validateForm()) return false;
		sendto_api(n);
	}
	e.preventDefault();
})
/* jQuery('body').on('click','input[name=verification_type]',function(e){
	e.preventDefault();
	
}); */
jQuery('body').on('click','.prevbtn_cs',function(e){
	
	e.preventDefault();
	jQuery('.alert_msg_box').html('');
	var n = jQuery(this).attr('step');
	jQuery('.tab.tab'+n).hide();
	var next_n = parseInt(n)-1;
							
	jQuery('.tab.tab'+next_n).show();
	
})



/* function showTab(n,btn) {
	
	if(n!=0 && btn=='next'){
		sendto_api(n);
	}
  // This function will display the specified tab of the form ...
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "block";
  // ... and fix the Previous/Next buttons:
  if (n == 0) {
    document.getElementById("prevBtn").style.display = "none";
  } else {
    document.getElementById("prevBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
    document.getElementById("nextBtn").innerHTML = "Submit";
  } else {
    document.getElementById("nextBtn").innerHTML = "Next";
  }
  // ... and run a function that displays the correct step indicator:
  fixStepIndicator(n,btn)
}
 */
/* var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab,''); // Display the current tab



function nextPrev(n,btn) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && !validateForm()) return false;
  // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  x[currentTab].style.display = "block";
  // if you have reached the end of the form... :
  if (currentTab >= x.length) {
    //...the form gets submitted:
    document.getElementById("regForm").submit();
    return false;
  }
  // Otherwise, display the correct tab:
  showTab(currentTab,btn);
}
 */
 var currentTab = 0;
function validateForm() {
  // This function deals with validation of the form fields
  var x, y, i, valid = true;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByTagName("input");
  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {
    // If a field is empty...
    if (y[i].value == "") {
      // add an "invalid" class to the field:
      y[i].className += " invalid";
      // and set the current valid status to false:
      valid = false;
    }
  }
  // If the valid status is true, mark the step as finished and valid:
  if (valid) {
    document.getElementsByClassName("step")[currentTab].className += " finish";
  }
  return valid; // return the valid status
}

/* function fixStepIndicator(n,btn) {
	//console.log(btn);
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName("step");
  y = document.getElementsByClassName("tab");
  console.log(x);
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
    y[i].className = y[i].className.replace(" active", "");
  }
  //... and adds the "active" class to the current step:
	x[n].className += " active";
	y[n].className += " active";
	
} */

function sendto_api(n){
	jQuery('.alert_msg_box').html('');
	if(n==1){
		var form_data = jQuery('.espy_reg_form').serializeArray();
	}else if(n==2){
		var form_data = jQuery('.espy_reg_verify_form').serializeArray();
		var id = jQuery('#regForm input[name=hidden-parameter]').attr('data-id');
		var vType = jQuery('#regForm input[name=hidden-parameter]').attr('data-verification_type');
		form_data.push({name: 'id', value: id});
		form_data.push({name: 'vType', value: vType});
	}else if(n==3){
		var form_data = jQuery('.espy_create_ac').serializeArray();
		var id = jQuery('#regForm input[name=hidden-parameter]').attr('data-id');
		var vType = jQuery('#regForm input[name=hidden-parameter]').attr('data-verification_type');
		var phone = jQuery('#regForm .espy_reg_form input[name=phone]').val();
		form_data.push({name: 'id', value: id});
		form_data.push({name: 'phone', value: phone});
		form_data.push({name: 'vType', value: vType});
	}else{
		var form_data = "";
	}
	//console.log(form_data);
	var countryData = jQuery("#regForm .espy_reg_form input[name=phone]").intlTelInput("getSelectedCountryData"); // get country data as obj 
	var dialCode = countryData.dialCode;
	
	form_data.push({name: 'action', value: 'espay_api_render'});
	form_data.push({name: 'step', value: n});
	form_data.push({name: 'dialCode', value: dialCode});
	
	jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		datatype : "json",
		data: form_data,
		success: function(response){
			
			//var data = jQuery.parseJSON(d);
			if(!response){
				swal({
				  title: 'Error',
				  text: "Try Again!",
				  type: 'error',
				  showCancelButton: false,
				  confirmButtonColor: '#3085d6',
				  cancelButtonColor: '#52D3C7',
				  confirmButtonText: 'OK',
				  cancelButtonText: 'Cancel'
				}).then(function(result) {
				})
				return false;
			}
			if(response.success==0){
				//jQuery('.alert_msg_box').html(data.errormsg);
				swal({
				  title: 'Error',
				  text: response.errormsg,
				  type: 'error',
				  showCancelButton: false,
				  confirmButtonColor: '#3085d6',
				  cancelButtonColor: '#52D3C7',
				  confirmButtonText: 'OK',
				  cancelButtonText: 'Cancel'
				}).then(function(result) {
				})
				return false;
			}
			// var data = d;
			//alert(n);
			if(n==1){
				 if(response.success==2){
					swal({
					  title: 'Error',
					  text: "Your phone number already exist!",
					  type: 'error',
					  showCancelButton: true,
					  confirmButtonColor: '#3085d6',
					  cancelButtonColor: '#52D3C7',
					  confirmButtonText: 'Login',
					  cancelButtonText: 'Cancel'
					}).then(function(result) {
						// console.log(result.value);
					  if (result){
						jQuery('.wizard_container .login').click();
					  }
					})
					return false;
				} else {
					jQuery('.tab.tab'+n).hide();
					var next_n = parseInt(n)+1;
					jQuery('.tab.tab'+next_n).show();
					var verification_type = jQuery('.espy_reg_form input[name=verification_type]:checked').val();
					if(verification_type==1){
						var verifyId = response.data.id;
					} else {
						var verifyId = response.data;
					}
					jQuery('#regForm input[name=hidden-parameter]').attr('data-id',verifyId);
					jQuery('#regForm input[name=hidden-parameter]').attr('data-verification_type',verification_type);
					jQuery('#regForm input[name=hidden-parameter]').attr('data-step',1);
					if(verification_type==1){
						jQuery('#regForm .misscall-verify').show();
						jQuery('#regForm .sms-verify').hide();
					} else {
						jQuery('#regForm .misscall-verify').hide();
						jQuery('#regForm .sms-verify').show();
					}
				}
			
			}
			if(n=='2'){
				jQuery("#regForm .espy_create_ac input[name=phone]").intlTelInput({
					separateDialCode: true,
					initialCountry: countryData.iso2,
					preferredCountries: ["ug", "ke", 'cd']
				});
				jQuery('#question_list').html(response.questions);
				jQuery('.tab.tab'+n).hide();
				var next_n = parseInt(n)+1;
				jQuery('.tab.tab'+next_n).show();
				jQuery('#regForm input[name=hidden-parameter]').attr('data-step',2);
				var phone = jQuery('#regForm .espy_reg_form input[name=phone]').val();
				jQuery('#regForm .espy_create_ac input[name=phone]').val(phone);
			}
			if(n=='3'){
				// jQuery('#question_list').html(response.questions);
				if(response.accountWarnMsg){
					jQuery('#regForm .tab4 .warn-message').html(response.accountWarnMsg).show();
				}
				jQuery('.tab.tab'+n).hide();
				var next_n = parseInt(n)+1;
				jQuery('.tab.tab'+next_n).show();			
			}
			return;
	   }
	}); 
}