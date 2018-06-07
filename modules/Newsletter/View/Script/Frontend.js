cx.jQuery(document).ready(function(){
  jQuery('input[name="recipient_save"]').click(function(e){
    var errorMsg = cx.variables.get('NEWSLETTER_AGB_ERROR', 'Newsletter'),
        form     = jQuery('form[name="newsletter"]');
    if (
        form.find('#agbPrivacyStatement').length &&
        !jQuery('#agbPrivacyStatement').is(':checked')
        
    ) {
      e.preventDefault();
      if (jQuery('#termsConditionsError').length === 0) {
        form.parent('div').before('<div class="form-group" id="termsConditionsError"><div class="text-danger">'+ errorMsg +'</div></div>');
      }
    }
  });
});
