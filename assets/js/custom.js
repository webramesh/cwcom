jQuery(function($) {  
    // Header button clicks
    $('.login-btn, .register-btn').on('click', function(e) {
        e.preventDefault();
        if (window.authModal) {
            const isRegister = $(this).hasClass('register-btn');
            window.authModal.openModal(isRegister ? 'register' : 'login');
        }
    });

    $("body").on("click", '#fancybox_form .log_link', function(event) {
        event.preventDefault();
        if (window.authModal) {
            window.authModal.switchTab('login');
        }
    });

    $("body").on("click", '.reg_link', function(event) {
        event.preventDefault();
        if (window.authModal) {
            window.authModal.switchTab('register');
        }
    });

    if ($('.register_form_fancy .gform_validation_errors').length) {
        if (window.authModal) {
            window.authModal.openModal('register');
        }
    }

    jQuery('form[name="login_form"] input[name="log"]').attr({"placeholder": "Email", "required": "required"});
    jQuery('form[name="login_form"] input[name="pwd"]').attr({"placeholder": "Password", "required": "required"});

    var client_link = $('.client_area_logout');
    $(client_link).appendTo('.client_area ul li:last-child').wrap('<li>');

    if(document.body.classList.contains( 'logged-in' )) {
        $('#block-110, #block-111, #block-113, #block-114').hide();
    } else {
    }

    var register_label = $('#register label[for="useremail"]');
    $(register_label).append(':');

    if(window.location.hash == '#subscribe_tender_main') {
        var id  = $('#subscribe_tender_main');
        var top = $(id).offset().top;
        $('body, html').animate({scrollTop: top-100}, 1000);
    }

    if(window.location.search == '?login=failed&reason=invalid_username' || window.location.search == '?login=failed&reason=incorrect_password') {
        $('.login_form_container').before('<div class="login_error">Please check your password and account name and try again or <a href="/wpsysfiles/wp-login.php?action=lostpassword" target="_blank" rel="noopener noreferrer">Recover your Password</a>.</div>');
        var id  = $('#login_form');
        var top = $(id).offset().top;
        $('body, html').animate({scrollTop: top-150}, 1000);  
    }

    if(window.location.search == '?login=failed&reason=authentication_failed') {
        $('.login_form_container').before('<div class="login_error">Please verify your account by email.</div>');
        var id  = $('#login_form');
        var top = $(id).offset().top;
        $('body, html').animate({scrollTop: top-150}, 1000);  
    }

    if(window.location.search == '?password=changed') {
        $('.login_form_container').before('<div class="login_error">Password has been successfully changed.</div>');
        var id  = $('#login_form');
        var top = $(id).offset().top;
        $('body, html').animate({scrollTop: top-150}, 1000);  
    }

    if(window.location.href.indexOf("error=password_reset_mismatch") > -1) {
        $('#password-reset-form').before('<div class="login_error">Password and confirm password does not match.</div>');
        var id  = $('#resetpassform');
        var top = $(id).offset().top;
        $('body, html').animate({scrollTop: top-150}, 1000);  
    }

    if(window.location.href.indexOf("error=password_reset_empty") > -1) {
        $('#password-reset-form').before('<div class="login_error">The password field is empty.</div>');
        var id  = $('#resetpassform');
        var top = $(id).offset().top;
        $('body, html').animate({scrollTop: top-150}, 1000);  
    }

    var terms = $('<div class="reg-form-group privacy"><input type="checkbox" id="privacy" name="scales" checked required><label for="privacy">By clicking Register, you accept our <a href="/privacy-policy/" target="_blank" rel="noopener">Privacy Policy</a></label></div>');
    $('#register .reg-form-group:last-child').before(terms);

    jQuery('#field_10_3, #field_10_10').wrapAll('<div class="form_10_wrap"></div>');
    jQuery('#field_10_5, .form_10_wrap').wrapAll('<div class="form_10_wrap_all"></div>');

    var isMobile = window.matchMedia("only screen and (max-width: 1024px)").matches;
    if (isMobile) {
        $('.header_login').appendTo('#mobile-menu');
        $('.header_logout').appendTo('#mobile-menu');
        // $('.header_flags').clone().appendTo('#mobile-menu');
    }
    
    // Tender modal functionality with 20-second delay
    $(document).ready(function() {
        // Get the modal element
        var modal = $('#tender-modal');
        
        // If modal exists and is meant to be shown (has display:block style)
        if (modal.length && modal.css('display') === 'block') {
            // Initially hide it
            modal.hide();
            
            // Show it after 20 seconds
            setTimeout(function() {
                modal.fadeIn(500);
            }, 30000); // 20000 milliseconds = 30 seconds
        }

        // Yes button - redirect to the "Submit interest request" tab
        $('#tender-yes').on('click', function() {
            // Hide the modal
            modal.fadeOut(300);
            
            // Find and click the "Submit interest request" tab
            var interestTab = $('.responsive-tabs__list__item[aria-controls*="tabpanel"][title="Submit interest request"]');
            if (interestTab.length) {
                // Activate the tab
                interestTab.trigger('click');
                interestTab.addClass('responsive-tabs__list__item--active').attr('aria-selected', 'true');
                
                // Show the tab panel
                var tabPanelId = interestTab.attr('aria-controls');
                $('#' + tabPanelId).addClass('responsive-tabs__panel--active').attr('aria-hidden', 'false');
                
                // Scroll to the form section
                $('html, body').animate({
                    scrollTop: $('#apply-for-tender').offset().top - 100
                }, 800);
            } else {
                // Fallback - just scroll to the section if tab not found
                $('html, body').animate({
                    scrollTop: $('#apply-for-tender').offset().top - 100
                }, 800);
            }
        });
        
        // No button - simply close the modal
        $('#tender-no').on('click', function() {
            modal.fadeOut(300);
        });
        
        // Allow clicking outside the modal to close it
        $('.cw-modal-overlay').on('click', function(e) {
            if (e.target === this) {
                $(this).fadeOut(300);
            }
        });
    });

    $('.countries_main .inner-column-1 .pciwgas-pdt-cat-grid:last-child .cat-name').each(function () {
        this.innerHTML = this.innerHTML.replace( /(\s+)(.*)/, '$1<span class="two_word">$2</span>' );
    });

    if (window.location.hash == '#fancybox_form') {
        var anchor = jQuery('#fancybox_form');
        jQuery('html, body').stop().animate({
            scrollTop: jQuery(anchor).offset().top - 130
        }, 1000);
    }

    jQuery('.tender_apply_footer').click(function (e) {
        if (window.authModal) {
            window.authModal.openModal('register');
        }
    });

    jQuery('.login_span').click(function (e) {
        e.preventDefault();
        if (window.authModal) {
            window.authModal.openModal('login');
        }
    });

    $('.tenders__intro .faq_main').appendTo('.tenders_container');

    $('#subs_form #login_form input[name="redirect_to"]').val('https://www.concealedwines.com/client-area/my-subscriptions/');

    var url = new URL(window.location);
    var searchParams = new URLSearchParams(url.search.substring(1));
    var product = searchParams.get("product"),
        country = searchParams.get("country");

    if (country != '') {
        $("#subs_region_form #subscribe-form-input-product option:contains('" + product + "')").prop('selected', true);
        $("#subs_region_form #subscribe-form-input-country option:contains('" + country + "')").prop('selected', true);

        $(`#subs_region_form #subscribe-form-input-markets option[value='0']`).prop('selected', true);
        $(`#subs_region_form .subs_region_form`).append(' ' + country);

        console.log("product=" + product + " country=" + country);
    }

    $('.faq_block_tenders .kt-blocks-accordion-header').click(function (e) {
        $(this).closest('.wp-block-kadence-pane').toggleClass('kt-accordion-panel-active');
    });

    $('#gform_page_21_1 .gform_page_footer').append('<div class="mess_21">Your form data will be temporarily saved so you can edit it later using a special link</div>');
    $('.mess_21').hide();
    $("#gform_save_21_2_link").mouseover(function () {
        $(".mess_21").fadeIn();
    });

    $("#gform_save_21_2_link").mouseleave(function () {
        $(".mess_21").fadeOut();
    });

    // Sticky footer scroll behavior
    $(window).on('scroll', function() {
        var footerBar = $('.sticky-footer-bar-container'); // Target the container
        if (footerBar.length) {
            var footerBarHeight = footerBar.outerHeight() || 75; 
            // Check if scrolled to the bottom of the page
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - footerBarHeight - 50) { 
                footerBar.hide(); // Hide the footer when at the bottom
            } else {
                footerBar.show(); // Show the footer otherwise
            }
        }
    });

    // Print PDF button functionality
    $('body').on('click', '.print-pdf-button', function(e) {
        e.preventDefault();
        window.print();
    });
});