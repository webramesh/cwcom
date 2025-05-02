// Calendly Popup functionality
function initCalendlyPopup() {
    jQuery(document).ready(function($) {
        // Check if we're on a tender page
        if (window.location.href.indexOf('https://www.concealedwines.com/tenders/') !== -1) {
            // Create popup HTML structure
            var popupHTML = `
                <div id="calendly-popup" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999; overflow-y: auto;">
                    <div style="position: relative; width: 90%; max-width: 650px; margin: 50px auto; background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0 0 20px rgba(0,0,0,0.3);">
                        <button id="close-calendly-popup" style="position: absolute; top: -5px; right: 0px; background: none; border: none; font-size: 30px; cursor: pointer; color: #333; width: 30px; height: 30px; line-height: 30px; text-align: center;">&times;</button>
                        
                        <h2 style="text-align: center; color: #f79942; margin: 30px 0 20px; font-size: 22px;">Would you like to discuss about this tender in person? Let me assist you!</h2>
                        <hr>
                        <div class="calendly-inline-widget" data-url="https://calendly.com/calle-nilsson/30min?hide_event_type_details=1&hide_gdpr_banner=1&primary_color=f79942" style="min-width:320px;height:500px;"></div>
                        <script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async></script>
                    </div>
                </div>
            `;
            
            // Append popup to body
            $('body').append(popupHTML);
            
            // Fix for layout shift - measure scrollbar width
            var scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
            $('head').append(`
                <style>
                    body.calendly-popup-open {
                        overflow: hidden;
                        padding-right: ${scrollbarWidth}px;
                    }
                    .sticky-wrapper, .navbar-fixed-top, .fixed-header {
                        padding-right: ${scrollbarWidth}px;
                    }
                    #calendly-popup .calendly-inline-widget {
                        margin: 0 auto;
                    }
                </style>
            `);
            
            // Set a cookie function to prevent showing the popup repeatedly
            function setCookie(name, value, days) {
                var expires = "";
                if (days) {
                    var date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "") + expires + "; path=/";
            }
            
            // Get a cookie function
            function getCookie(name) {
                var nameEQ = name + "=";
                var ca = document.cookie.split(';');
                for(var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
                }
                return null;
            }
            
            // Show popup after 30 seconds if the cookie isn't set
            if (!getCookie('calendlyPopupShown')) {
                setTimeout(function() {
                    $('#calendly-popup').fadeIn(300);
                    $('body').addClass('calendly-popup-open');
                    // setCookie('calendlyPopupShown', 'true', 1); // Set cookie for 1 day
                }, 30000); // 30 seconds
            }
            
            // Close popup when clicking close button or outside the popup
            $('#close-calendly-popup, #calendly-popup').on('click', function(e) {
                if (e.target.id === 'close-calendly-popup' || e.target.id === 'calendly-popup') {
                    $('#calendly-popup').fadeOut(300);
                    setTimeout(function() {
                        $('body').removeClass('calendly-popup-open');
                        $('.sticky-wrapper, .navbar-fixed-top, .fixed-header').css('padding-right', '');
                    }, 300);
                }
            });
        }
    });
}

// Export the function for WordPress
if (typeof module !== 'undefined' && module.exports) {
    module.exports = initCalendlyPopup;
} else {
    // Call the function directly if not using modules
    initCalendlyPopup();
}