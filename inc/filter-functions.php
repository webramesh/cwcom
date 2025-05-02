<?php
// Register AJAX actions for logged-in and non-logged-in users
add_action('wp_ajax_prodfilter', 'true_filter_function'); 
add_action('wp_ajax_nopriv_prodfilter', 'true_filter_function');

function true_filter_function() {
    $paged = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);
    $params = array(
        'post_type'      => 'tenders',
        'post_status'    => 'publish',
        'posts_per_page' => get_option('posts_per_page'),
        'paged'          => $paged,
        'no_found_rows'  => false,
        'meta_query'     => array(),
    );

    // Handling tender type and status
    if($_POST['tender_type'] && $_POST['tender_type'] === "past-tenders"){
        $params["meta_query"] = array(
            array(
                'key'     => 'wpcf-tender-archive-date',
                'value'   => time(),
                'compare' => '<=',
            )
        );
        $params["order"] = 'ASC';
        $params["orderby"] = 'meta_value';
        $params["meta_key"] = 'wpcf-tender-archive-date';
    } elseif ($_POST['tender_type'] && $_POST['tender_type'] === "tenders"){
        if ($_POST['tender_status'] == 'upcoming'){
            $params["meta_query"] = array(
                'relation' => 'AND',
                array(
                    'key'     => 'wpcf-tender-archive-date',
                    'value'   => time(),
                    'compare' => '>=',
                ),
                array(
                    'key'     => 'wpcf-tender-launch-plan',
                    'value'   => 1,
                    'compare' => '=',
                ),
            );
            $params["order"] = 'ASC';
            $params["orderby"] = 'meta_value';
            $params["meta_key"] = 'wpcf-tender-start-date';
        } elseif ($_POST['tender_status'] == 'closed'){
            $params['meta_query'] = array(
                array(
                    'key'     => 'wpcf-tender-archive-date',
                    'value'   => time(),
                    'compare' => '<=',
                )
            );
            $params['order'] = 'DESC';
            $params["orderby"] = 'meta_value';
            $params["meta_key"] = 'wpcf-tender-archive-date';
        } elseif ($_POST['tender_status'] == 'current'){
            $params['meta_query'] = array(
                'relation' => 'AND',
                array(
                    'key'     => 'wpcf-tender-archive-date',
                    'value'   => time(),
                    'compare' => '>=',
                ),
                array(
                    'key'     => 'wpcf-tender-launch-plan',
                    'value'   => 0,
                    'compare' => '=',
                ),
            );
            $params['order'] = 'ASC';
            $params["orderby"] = 'meta_value';
            $params["meta_key"] = 'wpcf-tender-offer-deadline';
        } else {
            $params['meta_query'] = array(
                array(
                    'key'     => 'wpcf-tender-archive-date',
                    'value'   => time(),
                    'compare' => '>',
                )
            );
            $params['order'] = 'DESC';
            $params["orderby"] = 'meta_value';
            $params["meta_key"] = 'wpcf-tender-archive-date';
        }
    }

    // Date filter handling
    if (!empty($_POST['from_date']) || !empty($_POST['to_date'])) {
        $date_query = array('relation' => 'AND');
        
        if (!empty($_POST['from_date'])) {
            $from_timestamp = strtotime($_POST['from_date']);
            $date_query[] = array(
                'relation' => 'OR',
                array(
                    'key'     => 'wpcf-tender-start-date',
                    'value'   => $from_timestamp,
                    'compare' => '>=',
                    'type'    => 'NUMERIC'
                ),
                array(
                    'key'     => 'wpcf-tender-offer-deadline',
                    'value'   => $from_timestamp,
                    'compare' => '>=',
                    'type'    => 'NUMERIC'
                )
            );
        }
        
        if (!empty($_POST['to_date'])) {
            $to_timestamp = strtotime($_POST['to_date']);
            $date_query[] = array(
                'relation' => 'OR',
                array(
                    'key'     => 'wpcf-tender-start-date',
                    'value'   => $to_timestamp,
                    'compare' => '<=',
                    'type'    => 'NUMERIC'
                ),
                array(
                    'key'     => 'wpcf-tender-offer-deadline',
                    'value'   => $to_timestamp,
                    'compare' => '<=',
                    'type'    => 'NUMERIC'
                )
            );
        }
        
        $params['meta_query'][] = $date_query;
    }

    // Taxonomy filters
    $filter_val = 0;
    $tax_q2 = array();
    $tax_q = array('relation' => 'AND');

    if($_POST['tender-market']){ 
        $filter_val++;
        array_push($tax_q2, array(
            'taxonomy' => 'tender-market',
            'field' => 'id',
            'terms' => $_POST['tender-market']
        ));
    }

    if($_POST['tender-countries']){	 
        $filter_val++;
        array_push($tax_q2, array(
            'taxonomy' => 'tender-countries',
            'field'    => 'term_id',
            'terms'    => $_POST['tender-countries'],
        ));
    }

    if($_POST['tender-products']){ 
        $filter_val++;
        array_push($tax_q2, array(
            'taxonomy' => 'tender-products',
            'field'    => 'term_id',
            'terms'    => $_POST['tender-products'],
        ));
    }

    if ($filter_val > 1) {
        array_push($tax_q, $tax_q2);
        $params["tax_query"] = $tax_q;
    } elseif ($filter_val > 0) {
        $params["tax_query"] = $tax_q2;
    }

    query_posts($params);

    global $wp_query;
    ob_start();

    get_template_part('template-parts/content/archive_main', 'tenders');	 
    $posts_html = ob_get_contents();
    ob_end_clean();

    echo json_encode(array(
        'posts' => json_encode($wp_query->query_vars),
        'max_page' => $wp_query->max_num_pages,
        'found_posts' => $wp_query->found_posts,
        'content' => $posts_html
    ));

    die();
}

/* Filter form on archive page */
add_action('kadence_before_main_content', 'filter_form_func', 10);
function filter_form_func() {
	
    if (false !== get_transient('country')) { $tr_query['country'] = get_transient('country'); }
    if (!empty($_GET['wpvtender-products'])) { $tr_query['product-slug'] = $_GET['wpvtender-products']; }
    if (false !== get_transient('market')) { $tr_query['market'] = get_transient('market'); }
    if (false !== get_transient('product')) { $tr_query['product'] = get_transient('product'); }
    if (false !== get_transient('tender-status')) { $tr_query['tender-status'] = get_transient('tender-status'); }

    delete_transient('country');
    delete_transient('market');
    delete_transient('product');
    delete_transient('tender-status');

    if ((get_query_var('post_type') == 'tenders' || get_query_var('tender-countries') || get_query_var('tender-market') || get_query_var('tender-products') || get_query_var('tender-regions')) && !is_single()) { 
        su_query_asset('js', 'ajax-filter');
        su_query_asset('css', 'filter');
        echo '<div class="filters-panel">';
        echo '<div id="product-ajax-filer"><div class="filter-head">' . __('Filter', 'kadence-child') . '
        <span class="dropdown-filter-toggle"><span class="kadence-svg-iconset svg-baseline"><svg aria-hidden="true" class="kadence-svg-icon kadence-arrow-down-svg" fill="currentColor" version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><title>Expand</title><path d="M5.293 9.707l6 6c0.391 0.391 1.024 0.391 1.414 0l6-6c0.391-0.391 0.391-1.024 0-1.414s-1.024-0.391-1.414 0l-5.293 5.293-5.293-5.293c-0.391-0.391-1.024-0.391-1.414 0s-0.391 1.024 0 1.414z"></path>
                </svg></span></span>
            </div>';
        echo '<div class="form-wrap"><form method="POST" id="filter" data-load="' . __('Loading...', 'kadence-child') . '">';
    	echo '<div class="filter-wrap">';

         // Tender markets list
    $market_terms = get_terms_by_post_type('tender-market', 'tenders');
    if ($market_terms && !is_wp_error($market_terms)) {
        echo '<select name="tender-market"><option value="">' . __('All markets', 'kadence-child') . '</option>';
        foreach ($market_terms as $term) {
            echo '<option value="' . $term->term_id . '">' . $term->name . ' (' . $term->count . ')</option>';
        }
        echo '</select>';
    }

    // Country list
    $country_terms = get_terms_by_post_type('tender-countries', 'tenders');
    if ($country_terms && !is_wp_error($country_terms)) {
        echo '<select name="tender-countries"><option value="">' . __('All countries', 'kadence-child') . '</option>';
        foreach ($country_terms as $term) {
            echo '<option value="' . $term->term_id . '">' . $term->name . ' (' . $term->count . ')</option>';
        }
        echo '</select>';
    }

    // Product type list
    $product_terms = get_terms_by_post_type('tender-products', 'tenders');
    if ($product_terms && !is_wp_error($product_terms)) {
        echo '<select name="tender-products"><option value="">' . __('All products', 'kadence-child') . '</option>';
        foreach ($product_terms as $term) {
            echo '<option value="' . $term->term_id . '">' . $term->name . ' (' . $term->count . ')</option>';
        }
        echo '</select>';
    }

        // Tender status
        if ((get_post_type() == 'tenders' || get_page_template_slug() === 'tenders-by-product.php') && empty($tr_query['tender-status'])) {
            echo '<select name="tender_status"><option value="opened">' . __('Opened tenders', 'kadence-child') . '</option>';
            echo '<option value="current">' . __('- current tenders', 'kadence-child') . '</option>';
            echo '<option value="upcoming">' . __('- upcoming tenders', 'kadence-child') . '</option>';
            echo '<option value="closed">' . __('Closed tenders', 'kadence-child') . '</option>';
            echo '</select>';
        } elseif (!empty($tr_query['tender-status'])) {
            $opened = $current = $upcoming = $closed = '';
            switch ($tr_query['tender-status']) {
                case 'opened':
                    $opened = 'selected';
                    break;
                case 'current':
                    $current = 'selected';
                    break;
                case 'upcoming':
                    $upcoming = 'selected';
                    break;
                case 'closed':
                    $closed = 'selected';
                    break;
            }
            echo '<select name="tender_status">
                  <option value="opened" ' . $opened . '>' . __('Opened tenders', 'kadence-child') . '</option>';
            echo '<option value="current" ' . $current . '>' . __('- current tenders', 'kadence-child') . '</option>';
            echo '<option value="upcoming" ' . $upcoming . '>' . __('- upcoming tenders', 'kadence-child') . '</option>';
            echo '<option value="closed" ' . $closed . '> ' . __('Closed tenders', 'kadence-child') . '</option>';
            echo '</select>';
        } else {
            echo '<input type="hidden" name="tender_status" value="closed">';
        }

        // New date range filter
        echo '<div class="date-range-filter">';
        echo '<label for="from_date">' . __('From: ', 'kadence-child') . '</label>';
        echo '<input type="date" id="from_date" name="from_date">';
		echo '<label for="to_date">' . __('To: ', 'kadence-child') . '</label>';
        echo '<input type="date" id="to_date" name="to_date">';
        echo '</div>';

        // Hidden fields and buttons
        echo '<input type="hidden" name="url" value="' . $_SERVER['REQUEST_URI'] . '">';
        echo '</div>';
        echo '<div class="filter button-wrap">';
        echo '<button type="reset" class="reset button" id="clear_all_filters">' . __('Clear filter', 'kadence-child') . '</button>';	   
        echo '<button type="submit" class="submit button">' . __('Apply filter', 'kadence-child') . '</button>';
        
        echo '</div>';
        echo '<input type="hidden" name="action" value="prodfilter">';
        echo '</form></div></div></div>';

        // Add inline CSS and JavaScript
        echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        var clearAllButton = document.getElementById("clear_all_filters");
        var fromDate = document.getElementById("from_date");
        var toDate = document.getElementById("to_date");

        clearAllButton.addEventListener("click", function(e) {
            e.preventDefault(); // Prevent the default reset behavior
            
            // Clear all form inputs
            var form = this.closest("form");
            var inputs = form.getElementsByTagName("input");
            var selects = form.getElementsByTagName("select");

            for (var i = 0; i < inputs.length; i++) {
                switch(inputs[i].type) {
                    case "text":
                    case "date":
                        inputs[i].value = "";
                        break;
                    case "checkbox":
                    case "radio":
                        inputs[i].checked = false;
                        break;
                }
            }

            for (var i = 0; i < selects.length; i++) {
                selects[i].selectedIndex = 0;
            }

            // Trigger form submission to refresh results
            form.submit();
        });
    });
    </script>';
    }
}

