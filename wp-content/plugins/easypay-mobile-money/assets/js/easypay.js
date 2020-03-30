function copyToClipboard(element) {
  var temp = jQuery("<input>");
  jQuery("body").append(temp);
  temp.val(jQuery(element).text()).select();
  document.execCommand("copy");
  alert('Copied !!!');
  temp.remove();
  
}
jQuery(document).ready(function(){
	
	jQuery('.ipncopytoclip').click(function(e){
		copyToClipboard('#ipn_addressfield');
	});

});
