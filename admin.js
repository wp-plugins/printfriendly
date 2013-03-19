// Throughout the admin I chose to use slow animations to make it clear that stuff is being hidden or shown depending on settings.
jQuery(document).ready(function() {
  jQuery('.show_list').change(function() {
    if (jQuery('.show_list:checked').length == 0) {
      jQuery('.content_placement').hide('slow');
      jQuery('.content_placement input').attr('disabled','disabled');
    } else {
      jQuery('.content_placement').show('slow');
      jQuery('.content_placement input').removeAttr('disabled');
    }
  }).change();

  jQuery('#colorSelector').ColorPicker({
    color: jQuery('#text_color').val(),
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
      jQuery('#text_color').val('#' + hex);
      jQuery('.printfriendly-text2').css('color','#' + hex);
    }
  });

  jQuery('#text_size').change(function(){
    size = jQuery('#text_size').val();
    jQuery('.printfriendly-text2').css('font-size',parseInt(size));
  }).change();

  jQuery('#css input').change(function() {
    if(jQuery(this).attr('checked')) {
      jQuery(this).val('off');
      jQuery('#margin, #txt-color, #txt-size').hide('slow');
      pf_reset_style();
    } else {
      jQuery(this).val('on');
      jQuery('#margin, #txt-color, #txt-size').show('slow');
      pf_apply_style();
    }
  }).change();

  jQuery('#custom_text').change(function(){
    pf_custom_text_change();
  }).change();

  jQuery('#custom_text').keyup(function(){
    pf_custom_text_change();
  });

  function pf_custom_text_change(){
    jQuery('#buttongroup3 span:not(.printandpdf)').text( jQuery('#custom_text').val() );
    jQuery('#custom span.printfriendly-text2').text( jQuery('#custom_text').val() );
  }

  function pf_initialize_preview(urlInputSelector, previewSelector) {
    var el = jQuery(urlInputSelector);
    var imgUrl = jQuery.trim(el.val());
    var preview = jQuery(previewSelector + '-preview');
    var error = jQuery(previewSelector + '-error');
    el.bind('input paste change keyup', function() {
      setTimeout(function() {
        // ie shows error if we try to merge the two below into a single statement
        var img = jQuery('<img/>');
        var imgUrl = jQuery.trim(el.val());
        img.load(function() {
            error.html('');
            preview.html('').append(img);})
          .error(function() {
            preview.html('');
            if(img.attr('src') != '') {
              error.html('<div class="error settings-error"><p><strong>Invalid Image URL</strong></p></div>');
            }
          }).attr('src', imgUrl);
        // hide error for empty url
        if(imgUrl == '')
          error.html('');
      }, 100);
    });
  }

  pf_initialize_preview('#custom_image', '#pf-custom-button');
  pf_initialize_preview('#upload-an-image', '#pf-image');
  jQuery('#custom_image, #upload-an-image').change();
  jQuery('#custom_image').bind('focus', function() {
    jQuery('#custom-image').attr('checked', 'checked');
    jQuery('#pf-custom-button-error').show();
  });
  jQuery('#button-style input.radio').bind('change', function() {
    if(jQuery('#custom-image').attr('checked')) {
      jQuery('#pf-custom-button-error').show();
    } else {
      jQuery('#pf-custom-button-error').hide();
    }
  }).change();

  jQuery('#pf-logo').bind('change', function() {
    if(jQuery(this).val() == 'favicon') {
      jQuery('.custom-logo, #image-preview').hide();
    } else {
      jQuery('.custom-logo').css('display', 'inline-block');
      jQuery('#image-preview').show();
    }
  }).change();

  jQuery('#website_protocol').bind('change', function() {
    if(jQuery(this).val() == 'https') {
      jQuery('#https-beta-registration').show('slow');
    } else {
      jQuery('#https-beta-registration').hide('slow');
    }
  }).change();

  jQuery('#password_protected').bind('change', function() {
    if(jQuery(this).val() == 'yes') {
      jQuery('#javascript').val('yes').change();
      pf_on_javascript();
      jQuery('#javascript').attr('disabled', 'disabled');
      jQuery('#pf-javascript-container').hide('slow');
    } else {
      jQuery('#javascript').removeAttr('disabled');
      jQuery('#pf-javascript-container').show('slow');
    }
  }).change();

  jQuery('#javascript').bind('change', function() {
    if(jQuery(this).val() == 'yes') {
      pf_on_javascript();
    } else {
      pf_on_no_javascript();
    }
  }).change();

  function pf_on_javascript() {
    jQuery('.javascript').show();
    jQuery('.no-javascript').hide();
    jQuery('#print-options').show('slow');
  }

  function pf_on_no_javascript() {
    jQuery('.javascript').hide();
    jQuery('.no-javascript').show();
    jQuery('#print-options').hide('slow');
  }

  function pf_reset_style() {
    jQuery('.printfriendly-text2').css('font-size',14);
    jQuery('.printfriendly-text2').css('color','#000000');
  }

  function pf_apply_style() {
    jQuery('.printfriendly-text2').css('color', jQuery('#text_color').val() );
    size = jQuery('#text_size').val();
    jQuery('.printfriendly-text2').css('font-size',parseInt(size));
  }

});
