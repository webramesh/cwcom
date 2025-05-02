<?php
defined( 'ABSPATH' ) || exit;

/**
 * Add Files to Admin Panelа
 */
add_action( 'admin_enqueue_scripts', 'kadence_tender_regions_media_files' );
function kadence_tender_regions_media_files()
{

    $screen = get_current_screen();

    if ( $screen->id == 'edit-tender-countries' )
    {

        /**
         * MultiSelect
         */
        wp_enqueue_style( 'tender-countries-multiselect', get_stylesheet_directory_uri() . '/assets/plugins/slim-select/slimselect.min.css' );
        wp_enqueue_script( 'tender-countries-multiselect', get_stylesheet_directory_uri() . '/assets/plugins/slim-select/slimselect.min.js', [ 'jquery' ] );

    }

}

/**
 * Script Footer
 */
add_action('admin_footer', 'kadence_tender_regions_admin_footer_function', 99999);
function kadence_tender_regions_admin_footer_function($data)
{
    $screen = get_current_screen();

    /**
     * Countries Page
     */
    if ( $screen->id == 'edit-tender-countries' )
    {
        ?>
        <style>
            .ss-main {
                width: 95%;
            }
            .ss-main .ss-multi-selected {
                border: 1px solid #8c8f94;
            }
            .ss-main .ss-multi-selected .ss-values .ss-value {
                background-color: #2271b1;
            }
        </style>
        <script type='text/javascript'>

            jQuery(function ()
            {

                new SlimSelect({
                    select: '#tender-countries-select',
                    placeholder: '<?php _e( 'Choose Region', 'kadence' ) ?>',
                });

            });

        </script>
        <?php
    }
}

/**
 * Register Term Meta
 */
add_action( 'init', 'kadence_register_term_meta_tender_regions' );
function kadence_register_term_meta_tender_regions()
{

    register_meta( 'term', '_term_tender_regions', 'kadence_sanitize_term_meta_tender_regions' );
}

/**
 * Sanitize Term Meta
 * @param $value
 * @return string
 */
function kadence_sanitize_term_meta_tender_regions ( $value )
{
    return $value;
}

/**
 * Get Term Meta
 * @param $term_id
 * @return string
 */
function kadence_get_term_meta_tender_regions( $term_id )
{

    $value = get_term_meta( $term_id, '_term_tender_regions', true );
    $value = kadence_sanitize_term_meta_tender_regions( $value );

    return $value;

}

/**
 * Get Regions
 */
function kadence_get_regions()
{

    $taxonomy = 'tender-regions';
    $terms = get_terms( [
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    ] );

    return $terms;

}

/**
 * Save and Edit Term
 */
add_action( 'edit_tender-countries',   'kadence_save_term_meta_tender_regions' );
add_action( 'create_tender-countries', 'kadence_save_term_meta_tender_regions' );
function kadence_save_term_meta_tender_regions( $term_id )
{

    if ( ! isset( $_POST['term_meta_tender_regions_nonce'] ) || ! wp_verify_nonce( $_POST['term_meta_tender_regions_nonce'], basename( __FILE__ ) ) )
        return;

    $old_value  = kadence_get_term_meta_tender_regions( $term_id );
    $new_value = isset( $_POST['_term_tender_regions'] ) ? kadence_sanitize_term_meta_tender_regions ( $_POST['_term_tender_regions'] ) : '';


    if ( $old_value && '' === $new_value )
        delete_term_meta( $term_id, '_term_tender_regions' );

    else if ( $old_value !== $new_value )
        update_term_meta( $term_id, '_term_tender_regions', $new_value );

}

/**
 * Add Meta to Taxonomy
 */
add_action( 'tender-countries_add_form_fields', 'kadence_add_form_field_term_meta_tender_regions' );
function kadence_add_form_field_term_meta_tender_regions()
{

    $terms = kadence_get_regions();

    if( $terms )
    {
        wp_nonce_field( basename( __FILE__ ), 'term_meta_tender_regions_nonce' );
        ?>

            <div class="form-field term-meta-text-wrap">

                <label for="term-meta-text">
                    <?php _e( 'Regions', 'kadence' ); ?>
                </label>

                <select multiple id="tender-countries-select" name="_term_tender_regions[]">

                    <?php foreach ( $terms as $term ) { ?>

                        <option value="<?= $term->term_id ?>">
                            <?= $term->name ?>
                        </option>

                    <?php } ?>

                </select>

            </div>

        <?php
    }

}

/**
 * Edit Term
 */
add_action( 'tender-countries_edit_form_fields', 'kadence_edit_form_field_term_meta_tender_regions' );
function kadence_edit_form_field_term_meta_tender_regions( $term )
{

    $terms = kadence_get_regions();

    $value = (array) kadence_get_term_meta_tender_regions( $term->term_id );

    if ( ! $value ) $value = "";

    if( $terms ) {
        wp_nonce_field( basename( __FILE__ ), 'term_meta_tender_regions_nonce' );
        ?>

        <tr class="form-field term-meta-text-wrap">

            <th scope="row">

                <label for="term-meta-text">

                    <?php _e( 'Regions', 'kadence' ); ?>

                </label>

            </th>

            <td>

                <select multiple id="tender-countries-select" name="_term_tender_regions[]">

                    <?php foreach ( $terms as $term ) { ?>

                        <option value="<?= $term->term_id ?>" <?= (in_array( $term->term_id, $value )) ? 'selected="selected"' : '' ?>>
                            <?= $term->name ?>
                        </option>

                    <?php } ?>

                </select>

            </td>

        </tr>

        <?php
    }
}
