/**
 * Client Area Page Specific JavaScript
 * Handles form interactions and error handling for embedded Gravity Forms
 */
(function($) {
    'use strict';
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        // Set up the gf_global object with spinner URL from PHP
        if (typeof window.gf_global === 'undefined') {
            window.gf_global = {
                "gfcaptcha": {},
                "spinnerUrl": typeof gfClientVars !== 'undefined' ? gfClientVars.spinnerUrl : "",
                "spinnerAlt": "Loading",
                "formId": 0
            };
        }
        
        // Fix for missing gformInitSpinner function
        if (typeof window.gformInitSpinner !== 'function') {
            window.gformInitSpinner = function(formId, spinnerUrl) {
                if (!spinnerUrl) spinnerUrl = gf_global.spinnerUrl;
                
                // Check if the spinner function exists
                if (typeof gformShowSpinner === 'function') {
                    gformShowSpinner(formId);
                } else {
                    // Fallback if the function doesn't exist
                    $('#gform_submit_button_' + formId).after('<img id="gform_ajax_spinner_' + formId + '" class="gform_ajax_spinner" src="' + spinnerUrl + '" alt="Loading..." />');
                }
            };
        }
        
        // Handle form switching in client area
        $('.switch-to-register, .switch-to-login').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Prevent event bubbling to prevent modal from opening
            
            const loginSection = $('#client-login-section');
            const registerSection = $('#client-register-section');
            
            if ($(this).hasClass('switch-to-register')) {
                loginSection.hide();
                registerSection.show();
                
                // Initialize Gravity Forms when switching to registration
                if (typeof window.gform !== 'undefined' && typeof window.gform.initializeOnLoaded === 'function') {
                    setTimeout(function() {
                        window.gform.initializeOnLoaded();
                    }, 100);
                }
            } else {
                registerSection.hide();
                loginSection.show();
            }
        });
        
        // Special handling for registration form errors
        $(document).on('gform_confirmation_loaded', function(event, formId) {
            // Form submission completed - either success or validation errors
            $('.gform_button').prop('disabled', false).val('Register');
            
            // If there's a confirmation message, it was successful
            $('.gform_confirmation_message').each(function() {
                $(this).show();
            });
        });
        
        // Handle Gravity Forms render events 
        $(document).on('gform_post_render', function(event, formId, currentPage) {
            console.log('Form rendered:', formId);
            
            // Reset button state
            $('.gform_button').prop('disabled', false).val('Register');
            
            // Make sure validation error messages are visible
            $('.gform_validation_errors, .validation_message').show();
            
            // For the client page specifically
            if (window.location.pathname.includes('client-area')) {
                // If errors exist, ensure the register form section is visible
                if ($('.gform_validation_errors').length > 0) {
                    $('#client-login-section').hide();
                    $('#client-register-section').show();
                    
                    // Scroll to the errors
                    setTimeout(function() {
                        const errorElement = $('.gform_validation_errors').first();
                        if (errorElement.length) {
                            $('html, body').animate({
                                scrollTop: errorElement.offset().top - 100
                            }, 300);
                        }
                    }, 100);
                }
            }
        });
        
        // Direct monitoring of AJAX form submission to detect errors
        $(document).ajaxComplete(function(event, xhr, settings) {
            if (settings.url && settings.url.includes('gform_ajax')) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    
                    // If we have form validation errors
                    if (response && response.formErrors) {
                        // Reset all submit buttons
                        $('.gform_button').prop('disabled', false).val('Register');
                        
                        // For client area page
                        if (window.location.pathname.includes('client-area')) {
                            // Show registration form if there are errors
                            $('#client-login-section').hide();
                            $('#client-register-section').show();
                        }
                    }
                } catch (e) {
                    // Not JSON or other error, just reset the buttons
                    $('.gform_button').prop('disabled', false).val('Register');
                }
            }
        });
        
        // Initialize based on URL hash
        if (window.location.hash === '#register') {
            $('#client-login-section').hide();
            $('#client-register-section').show();
        }
    });
})(jQuery);