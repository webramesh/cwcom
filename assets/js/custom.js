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
        $('.header_flags').appendTo('#mobile-menu');
    }

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

    $('#subs_form #login_form input[name="redirect_to"]').val('https://cw-com.bcat.tech/client-area/my-subscriptions/');

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

});
