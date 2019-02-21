/**
 * This file is loaded by the abstract SystemComponentBackendController
 * You may add own JS files using
 * \JS::registerJS(substr($this->getDirectory(false, true) . '/View/Script/FileName.css', 1));
 * or remove this file if you don't need it
 */

jQuery(document).ready(function(){
    if ( cx.jQuery( '#form-0-emailAccess' ).length ) {
        var $value = cx.jQuery( "#form-0-emailAccess" ).val();

        cx.jQuery( '[name="emailAccess"]' ).val($value);
    }

    if ( cx.jQuery( '#form-0-backendLangId' ).length ) {
        var $value = cx.jQuery( '#form-0-backendLangId' ).val();

        cx.jQuery( '[name="backendLangId"]' ).val($value);
    }

    if ( cx.jQuery( '#form-0-twoFaActive' ).length ) {
        validate2faActiveRadio();

        cx.jQuery( '#form-0-twoFaActive' ).click(function() {
            validate2faActiveRadio();
        });
    }

    if ( cx.jQuery( '#user-btn-verify-code' ).length ) {
        cx.jQuery( '#user-btn-verify-code' ).click(function() {
            event.preventDefault();
            var value = cx.jQuery( '#user-input-code' ).val();
            var secret = cx.jQuery( '#user-secret-wrapper' ).text();
            verifyCode(value, secret);
        });
    }

});

/**
 * If the twoFaActive radiobutton is true, show the corresponding authentications div
 */
function validate2faActiveRadio() {
    var twoFaRadioTrue = cx.jQuery( '#form-0-twoFaActive_yes' );
    var authenticationsDiv = cx.jQuery( '#form-0-authentications' ).closest( '.group' );

    if ( cx.jQuery( twoFaRadioTrue ).is( ':checked' ) ) {
        cx.jQuery( authenticationsDiv ).show();
    }else {
        cx.jQuery( authenticationsDiv ).hide();
    }
}

/**
 * 
 */
function verifyCode(value, secret) {
    cx.ajax(
        'JsonUser',
        'verifyCode',
        {
            data: {
                code: value,
                secret: secret
            },
            success: function(json) {
                cx.jQuery('#user-response-wrapper').html(json['data']['content']);
            }
        }
    );
}
