jQuery(document).ready(function($) {
    // Create popup HTML structure
    var popupHTML = `
        <div id="country-popup" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999; overflow-y: auto;">
            <div style="position: relative; width: 90%; max-width: 600px; margin: 50px auto; background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0 0 20px rgba(0,0,0,0.3);">
                <button id="close-popup" style="position: absolute; top: -10px; right: 5px; background: none; border: none; font-size: 32px; cursor: pointer; color: #333; padding: 10px;">&times;</button>
                
                <h2 style="text-align: center; color: #f79942; padding-top: 10px; margin-bottom: 30px;">Go to country consumer websites</h2>
                
                <div style="display: flex; justify-content: center; gap: 50px;  margin-bottom: 30px;">
                    <a href="https://www.concealedwines.no/" style="text-decoration: none; text-align: center;">
                        <img src="https://www.concealedwines.com/wpsysfiles/wp-content/uploads/2025/04/no-flag.webp" alt="Norway" style="width: 70px; height: 70px; border-radius: 50%; transition: transform 0.3s;">
                        <p style="margin-top: 10px; color: #333;">Norway</p>
                    </a>
                    <a href="https://www.concealedwines.se/" style="text-decoration: none; text-align: center;">
                        <img src="https://www.concealedwines.com/wpsysfiles/wp-content/uploads/2025/04/se-flag.webp" alt="Sweden" style="width: 70px; height: 70px; border-radius: 50%; transition: transform 0.3s;">
                        <p style="margin-top: 10px; color: #333;">Sweden</p>
                    </a>
                    <a href="https://www.concealedwines.fi/" style="text-decoration: none; text-align: center;">
                        <img src="https://www.concealedwines.com/wpsysfiles/wp-content/uploads/2025/04/fi-flag.webp" alt="Finland" style="width: 70px; height: 70px; border-radius: 50%; transition: transform 0.3s;">
                        <p style="margin-top: 10px; color: #333;">Finland</p>
                    </a>
                </div>
                <h3 style="text-align: center; margin: 30px 0 0px; color: #f79942;">Some of our Partners</h3>
                
                <hr>
                <div id="partner-logos" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin-top: 20px">
                    <!-- Partner logos will be added here dynamically -->
                </div>
            </div>
        </div>
    `;
    
    // Append popup to body
    $('body').append(popupHTML);
    
    // Add hover effect to flag images
    $('#country-popup a img').hover(
        function() { $(this).css('transform', 'scale(1.1)'); },
        function() { $(this).css('transform', 'scale(1)'); }
    );
    
    // Generate partner logos (32 random logos from range 1-85)
    var partners = [];
    var usedNumbers = new Set();
    var totalLogos = 32; // Number of logos to show

    // Generate unique random numbers between 1 and 85
    while (usedNumbers.size < totalLogos) {
    var randomNum = Math.floor(Math.random() * 85) + 1;
    usedNumbers.add(randomNum);
    }

    // Convert the Set to an Array for easier iteration
    var randomNumbers = Array.from(usedNumbers);

    // Create the logos HTML
    for (var i = 0; i < totalLogos; i++) {
    partners.push(`
        <div style="text-align: center;">
            <img src="/wpsysfiles/wp-content/uploads/2025/04/partner-logo${randomNumbers[i]}.webp" alt="Partner ${randomNumbers[i]}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 1px solid #f79942; transition: transform 0.2s;">
        </div>
    `);
    }
    $('#partner-logos').html(partners.join(''));
    
    
    // // Version 1Generate partner logos (30 logos)
    // var partners = [];
    // for (var i = 1; i <= 30; i++) {
    //     partners.push(`
    //         <div style="text-align: center;">
    //             <img src="/wp-content/uploads/2025/03/partner-logo${i}.webp" alt="Partner ${i}" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 1px solid #eee; transition: transform 0.2s;">
    //         </div>
    //     `);
    // }
    $('#partner-logos').html(partners.join(''));
    
    // Add hover effect to partner logos
    $('#partner-logos img').hover(
        function() { $(this).css('transform', 'scale(1.1)'); },
        function() { $(this).css('transform', 'scale(1)'); }
    );
    // Open popup when clicking on header_flags
    $('.header_flags').on('click', function(e) {
    e.preventDefault();
    
    // Save the current scrollbar width before opening popup
    var scrollWidth = window.innerWidth - document.documentElement.clientWidth;
    
    // // Add padding to the body equal to scrollbar width to prevent content shift
    // $('body').css({
    //     'overflow': 'hidden',
    //     'paddingRight': scrollWidth + 'px'
    // });
    
    $('#country-popup').fadeIn(300);
    });

    // Close popup when clicking close button or outside the popup
    $('#close-popup, #country-popup').on('click', function(e) {
    if (e.target.id === 'close-popup' || e.target.id === 'country-popup') {
        $('#country-popup').fadeOut(300);
        
        // Remove the padding and restore scrolling
        $('body').css({
            'overflow': 'auto',
            'paddingRight': '0'
        });
    }
    });
}); 