<?php
/* Gravity form overrides */
/* If all required fields is filled in then do not render form 'Required Vendor Profile Details'*/
if (class_exists('GFAPI')) {
  function get_form_id_by_name($name) {
	$field_email_id = $form_id = 0;
	$result_forms = GFAPI::get_forms(true, false, 'id', 'ASC');
	foreach ($result_forms as $result_form) { 

		if ($result_form['title'] === $name) {
			$form_id = $result_form['id'];			
			}
		}
	
	return $form_id;
   }
	$re_ven_det_form_id=get_form_id_by_name('Required Vendor Profile Details');
	$current_user = wp_get_current_user();
	$user_url = $current_user->user_url;
	$field_score = array();
	$user_id = $current_user->ID;
	$keys = ['first_name', 'last_name', 'wpcf-vendor-user-company', 'wpcf-vendor-user-role', 'wpcf-vendor-user-phone', 'wpcf-vendor-user-country'];
	foreach ($keys as $key) {
		$user_meta_val = get_user_meta($user_id, $key);
		if ($user_meta_val) {
			$field_score[] = 1;
		}
	}
	if ($user_url) {
		array_push($field_score, 1);
	}

	if (count($field_score) === 7) {
		add_filter('gform_pre_render_' . $re_ven_det_form_id, 'limit_form_by_user_entry', 10, 3);
	function limit_form_by_user_entry($form, $ajax, $field_values)
		{
			$form['limitEntries'] = true;
			$form['limitEntriesCount'] = 1;
			$form['limitEntriesMessage'] =  __('All necessary profile information already filled in!', 'kadence-child');
			return $form;
		}
	}


/* Populate country, product, market values in Tender form  ($tender_form_id=   )*/
   $tender_form_id = get_form_id_by_name('Tender Form');
   add_filter('gform_pre_render_'.$tender_form_id, 'lc_populate_member_dropdown');
   function lc_populate_member_dropdown($form)
   {
	global $post;
	$terms = get_the_terms($post->ID, 'tender-countries');
	$product_type = get_the_terms($post->ID, 'tender-products');
	$product_market = get_the_terms($post->ID, 'tender-market');

	//Creating drop down item array.
	$items = array();
	$items_type = array();
	$items_market = array();
	$items_regions[] = array("text" => "", "value" => "");


	//Adding post titles to the items array
    if ($terms) {
        foreach ($terms as $term) {
            $items[] = array(
                "value" => $term->term_id,
                "text" => $term->name
            );

            foreach ($form["fields"] as &$field) {

                if ($field["id"] == 141) {
                    $field["type"] = "select";
                    $field["choices"] = $items;
                }
            }
        }
    }

    if ($product_type) {
	   foreach ($product_type as $term_type) {
	     	$items_type[] = array(
			  "value" => $term_type->term_id,
		    	"text" =>  $term_type->name
		    );

		   foreach ($form["fields"] as &$field) {

		    	if ($field["id"] == 15) {
			    	$field["type"] = "select";
				    $field["choices"] = $items_type;
			   }
		   }
	   }
    }

    if ($product_market) {
        foreach ($product_market as $term_market) {
            $items_market[] = array(
                "value" => $term_market->term_id,
                "text" => $term_market->name
            );

            foreach ($form["fields"] as &$field) {

                if ($field["id"] == 81) {
                    $field["type"] = "select";
                    $field["choices"] = $items_market;
                }
            }
        }
    }


	return $form;
  }
}
