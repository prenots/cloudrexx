 function cx_multisite_signup(defaultOptions) {
    var options = defaultOptions,
        ongoingRequest = false,
        submitRequested = false,
        submitButtonRequested = false,
        signUpForm = jQuery('#multisite_signup_form'),
        objModal = jQuery('#multisite_signup_form').parents('.modal'),
        objMail,
        objAddress,
        objTerms,
        objPayButton,
        isPaymentUrlRequested = false;

    var validatorDefaults = {
        objForm: signUpForm,
        objModal: objModal,
        systemErrorTxt: options.messageErrorTxt
    };
    var validator = new multisiteFormValidator(validatorDefaults);
    
    function initSignUpForm() {
        signUpForm
                .bootstrapValidator()
                .on('success.field.bv', function() {
                  if (submitButtonRequested) {
                    submitButtonRequested = false;
                    if (options.IsPayment) {
                      // jQuery('.multisite_pay').modal('show');
                    } else {
                      submitForm();
                    }
                  }
                })
                .on('error.field.bv', function() {
                  submitButtonRequested = false;
                });
        objModal.on('show.bs.modal', init);
        objModal.find('.multisite_cancel').on('click', cancelSetup);

        signUpForm.submit(submitForm);

        objMail = objModal.find('#multisite_email_address');
        objMail.bind('change', verifyEmail);

        objAddress = objModal.find('#multisite_address');
        objAddress.bind('change', verifyAddress);

        objTerms = objModal.find('#multisite_terms');
        objTerms.bind('change', verifyTerms);

        objModal.find('.multisite_submit').on('click', submitForm);
        objModal.find('.multisite_pay').on('click', setPaymentUrl);

        objPayButton = objModal.find('.multisite_pay_button');
        init();
    }

    function cancelSetup() {
        ongoingRequest = false;
        submitRequested = false;
    }

    function init() {
        if (ongoingRequest) {
            return;
        }

        validator.setFormHeader(options.headerInitTxt);
        validator.hideProgress();
        showForm();

        validator.clearFormStatus();

        if (typeof(options.email) == 'string' && !objMail.val()) {
            objMail.val(options.email);
        }
        //objMail.data('valid', false);
        objMail.data('verifyUrl', options.emailUrl);

        if (typeof(options.address) == 'string' && !objAddress.val()) {
            objAddress.val(options.address);
        }
        //objAddress.data('valid', false);
        objAddress.data('verifyUrl', options.addressUrl);

        //objTerms.data('valid', false);
        objTerms.change();

        validator.setFormButtonState('close', false);
        validator.setFormButtonState('cancel', true, true);
        if (options.IsPayment) {
            objPayButton.payrexxModal({
                hideObjects: ["#contact-details", ".contact"],
                show: function(e) {
                    //signup form validation and check valid payment
                    if (!formValidation() || !isPaymentUrlValid()) {
                        return e.preventDefault();
                    }

                    return true;
                },
                hidden: function(transaction) {
                    switch (transaction.status) {
                        case 'confirmed':
                            setFormButtonState('pay', false);
                            callSignUp();
                            break;
                        case 'waiting':
                        case 'cancelled':
                        default:
                            setFormButtonState('pay', false);
                            setFormButtonState('submit', true, true);
                            break;
                    }
                }
            });
            validator.setFormButtonState('submit', false);
            validator.setFormButtonState('pay', true, true);
        } else {
            validator.setFormButtonState('pay', false);
            validator.setFormButtonState('submit', true, true);
        }


        if (objTerms.length) {
            signUpForm.data('bootstrapValidator').updateStatus('agb', 'NOT_VALIDATED');
        }
        if (objMail.length) {
            signUpForm.data('bootstrapValidator').updateStatus('multisite_email_address', 'NOT_VALIDATED');
        }
        if (objAddress.val() == ''){
            signUpForm.data('bootstrapValidator').updateStatus('multisite_address', 'NOT_VALIDATED');
        } else {
            jQuery(objAddress).trigger('change');
        }
    }

    function verifyEmail() {
        validator.verifyInput(this, {multisite_email_address : jQuery(this).val()});
    }

    function verifyAddress() {
        validator.verifyInput(this, {multisite_address : jQuery(this).val().toLowerCase()});
    }

    function verifyTerms() {
        validator.verifyInput(this);
    }

    function formValidation() {
        signUpForm.data('bootstrapValidator').validate();
        if (!isFormValid() || !signUpForm.data('bootstrapValidator').isValid()) {
            return false;
        }

        return true;
    }

    function isPaymentUrlValid() {
        var urlPattern = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
        var url = objPayButton.data('href');

        jQuery('.alert-danger').remove();
        if (!urlPattern.test(url)) {
            jQuery('<div class="alert alert-danger" role="alert">Invalid Payrexx Form Url</div>').insertAfter(jQuery('#product_id'));
            return false;
        }

        return true;
    }

    function setPaymentUrl() {
        if (isPaymentUrlRequested) {
          return;
        }
        if (!formValidation()) {
            return;
        }
        isPaymentUrlRequested = true;
        try {
            jQuery.ajax({
                dataType: "json",
                url: options.paymentUrl,
                data: {
                    multisite_email_address : objMail.val(),
                    multisite_address : objAddress.val(),
                    product_id : jQuery("#product_id").val(),
                    renewalOption: options.renewalOption
                },
                type: "POST",
                beforeSend: function (xhr, settings) {
                    objModal.find('.multisite_pay').button('loading');
                    objModal.find('.multisite_pay').prop('disabled', true);
		    jQuery('.multisite_pay').removeClass('btn-primary');
                },
                success: function(response) {
                    isPaymentUrlRequested = false;
                    if (response.status == 'error') {
                        return;
                    }

                    if (response.status == 'success' && response.data.link) {
                        objPayButton.data('href', response.data.link);
                        objPayButton.trigger('click');
                    }
                },
                complete: function (xhr, settings) {
                    objModal.find('.multisite_pay').button('reset');
                    objModal.find('.multisite_pay').prop('disabled', false);
                }
            });
        } catch (e) {
            console.log(e);
        }
    }
    
    function verifyForm() {
        isFormValid();
    }

    function submitForm() {
        try {
            
            if (!formValidation()) {
                submitButtonRequested = true;
                return;
            }

            validator.setFormButtonState('submit', false);

            if (submitRequested) {
                return;
            }
            //signUpForm.find(':input').prop('disabled', true);
            submitRequested = true;
            callSignUp();
        } catch (e) {

        }

        // always return false. We don't want to form to get actually submitted
        // as everything is done using AJAX
        return false;
    }

    function isFormValid() {
        if (objMail.length && !objMail.data('valid')) {
            return false;
        }

        if (objAddress.length && !objAddress.data('valid')) {
            return false;
        }

        if (objTerms.length && !objTerms.data('valid')) {
            return false;
        }

        return true;

    }

    function callSignUp() {
        try {
            ongoingRequest = true;
            validator.setFormButtonState('close', true, true);
            validator.setFormButtonState('cancel', false, false);
            validator.setFormHeader(options.headerSetupTxt);
            validator.hideForm();
            
            var message = options.messageBuildTxt;
            message = message.replace('%1$s', '<a href="mailto:' + objMail.val() + '">' + objMail.val() + '</a>');
            message = message.replace('%2$s', '<a href="https://' + objAddress.val() + '.' + options.multisiteDomain + '" target="_blank">https://' + objAddress.val() + '.' + options.multisiteDomain + '</a>');
            validator.showProgress(message);

            trackConversions();

            var postData = {
                multisite_email_address : objMail.val(),
                multisite_address : objAddress.val(),
                product_id : jQuery("#product_id").val(),
                renewalOption: options.renewalOption
            };

            var subscriptionField = objModal.find('#multisite_subscription');
            if (subscriptionField.length) {
                postData['subscription_id'] = subscriptionField.val();
            }
            jQuery.ajax({
                dataType: "json",
                url: options.signUpUrl,
                data: postData,
                type: "POST",
                success: function(response){
                    var message, errorObject,errorMessage,errorType;
                    validator.hideProgress();
                    
                    if (!response.status) {
                        validator.showSystemError();
                        return;
                    }
                    // handle signup
                    switch (response.status) {
                        case 'success':
                            // this is a workaround for 
                            if (!response.message && !response.data) {
                                validator.showSystemError();
                                return;
                            }
                            if (options.callBackOnSuccess && typeof  options.callBackOnSuccess === 'function') {
                                options.callBackOnSuccess(response.data);
                            }
                            // fetch message
                            message = response.data.message;

                            // redirect to website, in case auto-login is active
                            if (message == 'auto-login') {
                                validator.setFormButtonState('close', false);
                                validator.setFormButtonState('cancel', false);
                                validator.setFormButtonState('submit', false);
                                validator.setFormHeader(options.headerSuccessTxt);
                                validator.setFormStatus('success', options.messageRedirectTxt);
                                window.location.href = response.data.loginUrl;
                                return;
                            } else if(response.data.reload){
                                location.reload();
                            }

                            setMessage(message, 'success');
                            break;

                        case 'error':
                        default:
                            errorObject = null;
                            errorType = 'danger';
                            errorMessage = response.message;
                            if (typeof(response.message) == 'object') {
                                errorObject = typeof(response.message.object) != null ? response.message.object : null;
                                errorMessage = typeof(response.message.message) != null ? response.message.message : null;
                                errorType = typeof(response.message.type) != null ? response.message.type : null;
                            }
                            setMessage(errorMessage, errorType, errorObject);
                            break;
                    }
                },
                error: function() {
                    validator.showSystemError();
                }
            });
        } catch (e) {
            console.log(e);
        }
    }
    
    function setMessage(message, type, errorObject) {
        var objElement;
        if (!type) type = 'info';
        objElement = null;

        switch (errorObject) {
            case 'email':
                objElement = objMail;
                /* FALLTHROUGH */
            case 'address':
                if (!objElement) objElement = objAddress;

                validator.setFormHeader(options.headerInitTxt);
                validator.setFormButtonState('close', false);
                validator.setFormButtonState('cancel', true, true);
                validator.hideProgress();
                showForm();
                jQuery('<div class="alert alert-' + type + '" role="alert">' + message + '</div>').insertAfter(objElement);
                objElement.data('valid', false);
                cancelSetup();

                jQuery("#multisite_signup_form").data('bootstrapValidator').updateStatus('multisite_address', 'NOT_VALIDATED');
                break;

            case 'form':
                validator.setFormHeader(options.headerErrorTxt);
                validator.setFormButtonState('close', false);
                validator.setFormButtonState('cancel', true, true);
                validator.hideForm();
                validator.hideProgress();
                validator.setFormStatus(type, message);
                cancelSetup();
                break;

            default:
                validator.setFormHeader(options.headerSuccessTxt);
                validator.setFormButtonState('close', true, true);
                validator.setFormButtonState('cancel', false);
                validator.hideForm();
                validator.hideProgress();
                validator.setFormStatus(type, message);
                cancelSetup();
                break;
        }
    }
    
    function showForm() {
        objModal.find('.multisite-form').show();
        jQuery('#multiSiteSignUp').find('.modal-body').css({'min-height': jQuery('#multiSiteSignUp').find('.multisite-form').height()});
    }
    
    function trackConversions() {
        // check if conversion tracking shall be done
        if (!options.conversionTrack) {
            return;
        }
        
        price = options.productPrice;
        currency = options.orderCurrency;
        trackGoogleConversion(price, currency);
        trackFacebookConversion(price, currency);
    }

    function trackGoogleConversion(price, currency) {
        // check if google conversion tracking shall be done
        if (!options.trackGoogleConversion) {
            return;
        }

        jQuery.getScript('//www.googleadservices.com/pagead/conversion_async.js', function() {
            goog_snippet_vars = function() {
                var w = window;
                w.google_conversion_id = options.googleConversionId;
                w.google_conversion_label = "ujWnCMeCvF4Q3eSdxgM";
                w.google_remarketing_only = false;
                w.google_conversion_value  = price;
                w.google_conversion_currency = currency;
            }
            // DO NOT CHANGE THE CODE BELOW.
            goog_report_conversion = function(url) {
                goog_snippet_vars();
                window.google_conversion_format = "3";
                window.google_is_call = true;
                var opt = new Object();
                opt.onload_callback = function() {
                    if (typeof(url) != 'undefined') {
                        window.location = url;
                    }
                }
                var conv_handler = window['google_trackConversion'];
                if (typeof(conv_handler) == 'function') {
                    conv_handler(opt);
                }
            }
            goog_report_conversion();
        });
    }

    function trackFacebookConversion(price, currency) {
        // check if facebook conversion tracking shall be done
        if (!options.trackFacebookConversion) {
            return;
        }

        var _fbq = window._fbq || (window._fbq = []);
        jQuery.getScript('//connect.facebook.net/en_US/fbds.js', function() {
            _fbq.loaded = true;
        });

        window._fbq = window._fbq || [];
        window._fbq.push(['track', options.facebookConversionId, {'value':price,'currency':currency}]);
    }


    initSignUpForm();
}

var multisiteFormValidator = function(options) {
    var defaults = {
        objForm: jQuery("#multisite_signup_form"),
        objModal: jQuery('#multisite_signup_form').parents('.modal'),
        systemErrorTxt: ''
    };
    var settings    = jQuery.extend( {}, defaults, options );
    var statusDiv   = settings.objModal.find('.multisite-status');
    var progressDiv = settings.objModal.find('.multisite-progress');
    return {
        setStatusDiv: function(obj) {
            statusDiv = obj;
        },
        setProgressDiv: function(obj) {
            progressDiv = obj;
        },
        verifyInput: function(domElement, data) {
            that = this;
            jQuery(domElement).data('server-msg', '');
            settings.objForm.data('bootstrapValidator').validateField('multisite_address');
            jQuery(domElement).data('valid', false);
            jQuery(domElement).prop('disabled', true);
            if (jQuery(domElement).data('verifyUrl')) {
                jQuery.ajax({
                    dataType: "json",
                    url: jQuery(domElement).data('verifyUrl'),
                    data: data,
                    type: "POST",
                    success: function(response){that.parseResponse(response, domElement);}
                });
            } else {
                that.parseResponse({status:'success',data:{status:'success'}}, domElement);
            }
        },
        setFormHeader: function(headerTxt) {
            settings.objModal.find('.modal-header .modal-title').html(headerTxt);
        },
        setFormButtonState: function(btnName, show, active) {
            var btn = settings.objModal.find('.multisite_' + btnName);
            show ? btn.show() : btn.hide();
            btn.prop('disabled', !active);
        },
        /**
        * @param {{data:{loginUrl}}} response The url to which the user gets redirected if auto-login is active.
        * @param {jQuery} objCaller
        */
        parseResponse: function(response, objCaller) {
            var type, message;

            if (!response.status) {
                this.showSystemError();
                return;
            }

            // handle form validation
            if (objCaller) {
                jQuery(objCaller).prop('disabled', false);

                // fetch verification state of form element
                if (response.status == 'success') {
                    jQuery(objCaller).data('server-msg', '');
                    jQuery(objCaller).data('valid', true);

                    settings.objForm.data('bootstrapValidator').revalidateField(jQuery(objCaller).attr('name'));
                    return true;
                } else {
                    type = 'danger';
                    message = response.message;
                    if (typeof(response.message) == 'object') {
                        message = typeof(response.message.message) != null ? response.message.message : null;
                        type = typeof(response.message.type) != null ? response.message.type : null;
                    }
                    jQuery(objCaller).data('server-msg', message);
                }

                settings.objForm.data('bootstrapValidator').revalidateField(jQuery(objCaller).attr('name'));

                return;
            }
        },
        setMessage: function(message, type) {
            this.setFormButtonState('close', true, true);
            this.setFormButtonState('cancel', false);
            this.hideForm();
            this.hideProgress();
            this.setFormStatus(type, message);
        },
        showSystemError: function() {
            this.hideProgress();
            this.setMessage(settings.systemErrorTxt, 'danger');
        },
        hideForm: function() {
            settings.objModal.find('.multisite-form').hide();
        },
        showProgress: function(message) {
            progressDiv.html(message);
            progressDiv.show();
        },
        hideProgress: function() {            
            progressDiv.hide();
        },
        clearFormStatus: function() {
            statusDiv.hide();
            statusDiv.children().remove();
        },
        setFormStatus : function(type, message) {
            this.clearFormStatus();
            statusDiv.append('<div class="alert alert-' + type + '" role="alert">' + message + '</div>');
            statusDiv.show();
        }
    };
};
jQuery(document).ready(function() {
    // trigger signup only on  signup form exists
    if (typeof(cx_multisite_options) != 'undefined' && jQuery('#multisite_signup_form').length) {
        cx_multisite_signup(cx_multisite_options);
    }
});
