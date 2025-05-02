<?php

/**
 * Enqueue child styles.
 */
define('CH_KADENCE_VERSION', '1.1.3');
function child_enqueue_styles()
{
	wp_enqueue_style('child-theme', get_stylesheet_directory_uri() . '/style.css', array(), CH_KADENCE_VERSION);
	wp_enqueue_style('cw-style', get_stylesheet_directory_uri() . '/css/cw.min.css', array(), CH_KADENCE_VERSION);
	if(!empty($_GET['country']) ||  !empty($_GET['wpvtender-products'])) {
		wp_enqueue_style('accordion-css', home_url() . '/wpsysfiles/wp-content/plugins/kadence-blocks/dist/blocks/accordion.style.build.css', array(), CH_KADENCE_VERSION);
		wp_enqueue_script('kt-accordion', home_url() . '/wpsysfiles/wp-content/plugins/kadence-blocks/dist/kt-accordion-min.js', array(), false, true);
	}

    wp_enqueue_script('custom', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('jquery'), false, true);
}

add_action('wp_enqueue_scripts', 'child_enqueue_styles'); // Remove the // from the beginning of this line if you want the child theme style.css file to load on the front end of your site.

/**
 * Add custom functions here*/
require_once  get_stylesheet_directory() . '/assets/assets.php'; 
require_once  get_stylesheet_directory() . '/inc/child-theme-functions.php';

// Load auth module
require_once get_stylesheet_directory() . '/modules/auth/auth.php';

//multilanguage subdomain check
function set_language_from_url() {
    if (isset($_GET['lang'])) {
        $lang = sanitize_text_field($_GET['lang']);
        $allowed_languages = ['en', 'fr', 'es', 'de']; // Add more as needed

        if (in_array($lang, $allowed_languages)) {
            echo '<script type="text/javascript">
                function gtSetLanguage() {
                    console.log("gtSetLanguage function called");

                    if (typeof doGTranslate === "function") {
                        console.log("doGTranslate function is available");

                        // Get the current language from the googtrans cookie
                        var currentLang = GTranslateGetCurrentLang();
                        console.log("Current Language: " + currentLang);

                        // Set the googtrans cookie directly to ensure the language change
                        var domain = document.domain.startsWith("www.") ? document.domain.substring(4) : document.domain;
                        document.cookie = "googtrans=/" + currentLang + "/' . $lang . '; path=/; domain=" + domain;
                        document.cookie = "googtrans=/" + currentLang + "/' . $lang . '; path=/";

                        // Change the language
                        doGTranslate(currentLang + "|' . $lang . '");

                        // Remove lang parameter from URL
                        setTimeout(function() {
                            console.log("Removing lang parameter from URL");
                            history.replaceState({}, "", window.location.pathname);
                        }, 1000); // Wait a bit before removing the lang parameter
                    } else {
                        console.log("doGTranslate function is not available, retrying...");
                        setTimeout(gtSetLanguage, 500);
                    }
                }

                function GTranslateGetCurrentLang() {
                    var keyValue = document.cookie.match("(^|;) ?googtrans=([^;]*)(;|$)");
                    return keyValue ? keyValue[2].split("/")[2] : "en";
                }

                document.addEventListener("DOMContentLoaded", function() {
                    console.log("DOMContentLoaded event triggered");
                    gtSetLanguage();
                });
            </script>';
        } else {
            echo '<script>console.log("Invalid language parameter: ' . $lang . '");</script>';
        }
    } else {
        echo '<script>console.log("No lang parameter in URL");</script>';
    }
}
add_action('wp_footer', 'set_language_from_url', 100);

// Custom shortcode to retrieve multiple Toolset custom fields and format dates
function get_custom_fields_value_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'post_id' => '',
        'fields'  => 'wpcf-tender-start-date, wpcf-tender-offer-deadline',
    ), $atts, 'custom_fields' );

    if ( empty( $atts['post_id'] ) || empty( $atts['fields'] ) ) {
        return 'No post ID or fields provided.';
    }

    $fields = explode(',', $atts['fields']);
    $output = '';

    foreach ( $fields as $field ) {
        $field = trim( $field );
        $custom_field_value = get_post_meta( $atts['post_id'], $field, true );

        if ( ! empty( $custom_field_value ) ) {
            // Check if the value is a Unix timestamp and format it
            if ( is_numeric( $custom_field_value ) && (int)$custom_field_value == $custom_field_value ) {
                $formatted_value = date( 'F j, Y', $custom_field_value ); // Change the date format as needed
            } else {
                $formatted_value = esc_html( $custom_field_value );
            }
            $output .= sprintf( '<p>%s: %s</p>', ucwords(str_replace(array('wpcf-', '-'), array('', ' '), $field)), $formatted_value );
        } else {
            $output .= sprintf( '<p>%s: No value found</p>', ucwords(str_replace(array('wpcf-', '-'), array('', ' '), $field)) );
        }
    }

    return $output;
}
add_shortcode( 'custom_fields', 'get_custom_fields_value_shortcode' );
function set_custom_language_attribute($lang) {
    $current_url = $_SERVER['REQUEST_URI'];
    
    $lang_map = array(
        '/profil-compagie' => 'fr',
        '/espanol/' => 'es',
        '/portugues' => 'pt',
        '/deutsch' => 'de',
        '/italiano' => 'it'
    );
    
    foreach ($lang_map as $path => $code) {
        if (strpos($current_url, $path) !== false) {
            return $code;
        }
    }
    
    return $lang; // Return the original language if no match is found
}

add_filter('language_attributes', 'custom_language_attributes');

function custom_language_attributes($output) {
    $lang = set_custom_language_attribute(get_bloginfo('language'));
    return "lang=\"$lang\"";
}


// Add custom dashboard widget( latest user signup)
function latest_user_signups_dashboard_widget() {
    wp_add_dashboard_widget(
        'latest_user_signups_widget',
        'Latest User Signups',
        'display_latest_user_signups'
    );
}
add_action('wp_dashboard_setup', 'latest_user_signups_dashboard_widget');

// Display latest user signups
function display_latest_user_signups() {
    $users = get_users(array(
        'orderby' => 'user_registered',
        'order' => 'DESC',
        'number' => 20
    ));

    if (empty($users)) {
        echo '<p>No recent signups.</p>';
        return;
    }

    echo '<ul>';
    foreach ($users as $user) {
        $signup_date = date('F j, Y', strtotime($user->user_registered));
        echo "<li>{$user->user_login} - {$signup_date}</li>";
    }
    echo '</ul>';
}

//auto populate taste sytle description for pdf

function auto_populate_taste_style_description($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (get_post_type($post_id) != 'tenders') return;

    $content = get_post_field('post_content', $post_id);
    update_post_meta($post_id, 'wpcf-taste-style-description', $content);
}
add_action('save_post', 'auto_populate_taste_style_description');


// user column

add_filter('http_request_args', function($args) {
    $args['timeout'] = 30; // Increase timeout to 30 seconds
    return $args;
}, 100);

// Also add this to increase cURL timeout specifically
add_action('http_api_curl', function($handle) {
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($handle, CURLOPT_TIMEOUT, 30);
});

//pop up for consumer websites
function enqueue_country_popup_script() {
    wp_enqueue_script('jquery');
    wp_enqueue_script(
        'country-popup-script',                                       
        get_stylesheet_directory_uri() . '/js/country-popup.js',    
        array('jquery'),                                              
        '1.0.0',                                                      
        true                                                          
    );
}
add_action('wp_enqueue_scripts', 'enqueue_country_popup_script');