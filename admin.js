jQuery(document).ready(function() {
	jQuery('#upload_image_button').click(function() {
	 formfield = jQuery('#upload_image').attr('name');
	 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
	 return false;
	});

	window.send_to_editor = function(html) {
	 imgurl = jQuery('img',html).attr('src');
	 jQuery('#upload_image').val(imgurl);
	 tb_remove();
	}
	
	jQuery('.pf_show_list').change(function(){
		if(jQuery(this).val()=='manual'){
			jQuery('.pf_content_placement').attr('disabled','disabled');
		}else{
			jQuery('.pf_content_placement').removeAttr('disabled');
		}
	});
	
	jQuery('#colorSelector').ColorPicker({
		color: jQuery('#pf_text_color').val(),
		onShow: function (colpkr) {
			jQuery(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			jQuery(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			jQuery('#colorSelector div').css('backgroundColor', '#' + hex);
			jQuery('#pf_text_color').val('#' + hex);
			jQuery('.preview_button span:not(.pf_tip)').css('color','#' + hex)
		}
	});
	jQuery('#pf_text_size').change(function(){
		pf_size_preview();
	});
	
	jQuery('#pf_text_size').keyup(function(){
		pf_size_preview();
	});
	
	function pf_size_preview(){
		size = jQuery('#pf_text_size').val();
		jQuery('.preview_button span:not(.pf_tip)').css('font-size',parseInt(size));
	}
	
	jQuery('#pf_custom_text').change(function(){
		pf_custom_text_change();
	});
	
	jQuery('#pf_custom_text').keyup(function(){
		pf_custom_text_change();
	});
	
	function pf_custom_text_change(){
		if(text==''){
			text ='Print Friendly';
		}else{
			if(jQuery('#pf_custom_image_label').find('span:not(.pf_tip)').length == 0){
				color = jQuery('#pf_text_color').val();
				size = jQuery('#pf_text_size').val();
				jQuery('#pf_custom_image_label').find('img').after('<span style="marigin-left:3px; color: '+color+'; font-size:'+size+';"></span>');	
			}
		}
		text = jQuery('#pf_custom_text').val();
		jQuery('.preview_button span:not(.pf_tip):not(.printandpdf)').text(text);
	}
});