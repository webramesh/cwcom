<?php
/**
 * Add custom columns to the 'tenders' custom post type admin list
 */

// Add custom columns for taxonomies
function tenders_custom_columns($columns) {
    $new_columns = array();
    
    // Insert columns after 'title'
    foreach($columns as $key => $title) {
        $new_columns[$key] = $title;
        
        if ($key == 'title') {
            $new_columns['tender_countries'] = __('Countries', 'kadence-child');
            $new_columns['tender_products'] = __('Products', 'kadence-child');
            $new_columns['tender_market'] = __('Market', 'kadence-child');
            $new_columns['tender_regions'] = __('Regions', 'kadence-child');
        }
    }
    
    return $new_columns;
}
add_filter('manage_tenders_posts_columns', 'tenders_custom_columns');

// Populate custom columns with taxonomy terms
function tenders_custom_column_content($column_name, $post_id) {
    switch ($column_name) {
        case 'tender_countries':
            echo get_the_term_list($post_id, 'tender-countries', '', ', ', '');
            break;
        
        case 'tender_products':
            echo get_the_term_list($post_id, 'tender-products', '', ', ', '');
            break;
            
        case 'tender_market':
            echo get_the_term_list($post_id, 'tender-market', '', ', ', '');
            break;
            
        case 'tender_regions':
            echo get_the_term_list($post_id, 'tender-regions', '', ', ', '');
            break;
    }
}
add_action('manage_tenders_posts_custom_column', 'tenders_custom_column_content', 10, 2);

// Make the custom columns sortable
function tenders_sortable_columns($columns) {
    $columns['tender_countries'] = 'tender_countries';
    $columns['tender_products'] = 'tender_products';
    $columns['tender_market'] = 'tender_market';
    $columns['tender_regions'] = 'tender_regions';
    
    return $columns;
}
add_filter('manage_edit-tenders_sortable_columns', 'tenders_sortable_columns');

// Handle the sorting logic for custom columns based on taxonomy
function tenders_custom_columns_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'tenders') {
        return;
    }

    $orderby = $query->get('orderby');
    
    // Add sorting for taxonomy columns
    if ('tender_countries' === $orderby) {
        $query->set('orderby', 'tax');
        $query->set('tax_query', array(
            array(
                'taxonomy' => 'tender-countries'
            )
        ));
    } elseif ('tender_products' === $orderby) {
        $query->set('orderby', 'tax');
        $query->set('tax_query', array(
            array(
                'taxonomy' => 'tender-products'
            )
        ));
    } elseif ('tender_market' === $orderby) {
        $query->set('orderby', 'tax');
        $query->set('tax_query', array(
            array(
                'taxonomy' => 'tender-market'
            )
        ));
    } elseif ('tender_regions' === $orderby) {
        $query->set('orderby', 'tax');
        $query->set('tax_query', array(
            array(
                'taxonomy' => 'tender-regions'
            )
        ));
    }
}
add_action('pre_get_posts', 'tenders_custom_columns_orderby');