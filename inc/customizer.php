<?php
/**
 * Multiselect option for WP Customizer
 *
 * @param $wp_customize
 */
add_action( 'customize_register',  'multiselect_customize_register' );
function multiselect_customize_register( $wp_customize ) {
    /**
     * Add Custom Theme Options
     */
    $wp_customize->add_panel( 'theme_options', array(
        'priority'       => 10,
        'capability'     => 'edit_theme_options',
        'theme_supports' => '',
        'title'          => __( 'Theme Options', 'textdomain' ),
        'description'    => __( 'Theme specific options', 'textdomain' ),
    ) );

    /**
     * Home Page
     */
    $wp_customize->add_section( 'filter', array(
        'priority'       => 10,
        'capability'     => 'edit_theme_options',
        'theme_supports' => '',
        'title'          => __( 'Filter options', 'kadence-child'),
        'description'    => 'Settings for the filter',
        'panel'          => 'theme_options',
    ) );
    $wp_customize->add_section( 'child-theme-general', array(
        'priority'       => 10,
        'capability'     => 'edit_theme_options',
        'theme_supports' => '',
        'title'          => __( 'General options', 'kadence-child'),
        'description'    => 'Settings for the child theme',
        'panel'          => 'theme_options',
    ) );

    /* Countries */
    $wp_customize->add_setting( 'filter-countries', array(
        'type'       => 'theme_mod',
        'capability' => 'edit_theme_options',
        'transport'  => '',
        'sanitize_callback' => 'tender_countries_list_sanitize'
    ) );
    /* Products */
    $wp_customize->add_setting( 'filter-products', array(
        'type'       => 'theme_mod',
        'capability' => 'edit_theme_options',
        'transport'  => '',
        'sanitize_callback' => 'tender_product_list_sanitize'
    ) );
    /* Base link for tenders by countries */
    $wp_customize->add_setting( 'base-country-link', array(
        'type'       => 'theme_mod',
        'capability' => 'edit_theme_options',
        'transport'  => '',
        'default'=>'',
        'sanitize_callback' => 'link_sanitize'
    ) );
    /* Base link for tenders by product */
    $wp_customize->add_setting( 'base-product-link', array(
        'type'       => 'theme_mod',
        'capability' => 'edit_theme_options',
        'transport'  => '',
        'default'=>'',
        'sanitize_callback' => 'link_sanitize'
    ) );
    /*Child theme options*/
     $wp_customize->add_setting( 'email-support', array(
         'type'       => 'theme_mod',
         'capability' => 'edit_theme_options',
         'transport'  => '',
         'default'=>'',
         'sanitize_callback' => 'link_sanitize'
     ) );

    /* Countries for harbour*/
    $wp_customize->add_setting( 'filter-countries-harbour', array(
        'type'       => 'theme_mod',
        'capability' => 'edit_theme_options',
        'transport'  => '',
        'sanitize_callback' => 'tender_countries_list_sanitize_harbour'
    ) );


    $wp_customize->add_control(
        new Sinfonia_Customize_Control_Multiple_Select(
            $wp_customize,
            'multiple_select_setting1',
            array(
                'settings' => 'filter-countries',
                'label'    => 'Select which countries you want to include to tender filter list',
                'section'  => 'filter', // Enter the name of your own section
                'type'     => 'multiple-select', // The $type in our class
                'choices'  => tender_countries_list() // Your choices
            )
        )
    );

    $wp_customize->add_control(
        new Sinfonia_Customize_Control_Multiple_Select(
            $wp_customize,
            'multiple_select_setting2',
            array(
                'settings' => 'filter-products',
                'label'    => 'Select which products you want to include to tender filter list',
                'section'  => 'filter', // Enter the name of your own section
                'type'     => 'multiple-select', // The $type in our class
                'choices'  => tender_product_list() // Your choices
            )
        )
    );

    $wp_customize->add_control(
        new Sinfonia_Customize_Control_Multiple_Select(
            $wp_customize,
            'multiple_select_setting3',
            array(
                'settings' => 'filter-countries-harbour',
                'label'    => 'Select which countries you want to add harbour',
                'section'  => 'child-theme-general', // Enter the name of your own section
                'type'     => 'multiple-select', // The $type in our class
                'choices'  => tender_countries_list_harbour() // Your choices
            )
        )
    );

    $wp_customize->add_control(
        'text_setting1', array(
        'label'=>'Base link for tenders by country',
        'type'=>'text',
        'section'=>'filter',
        'settings'=>'base-country-link'
         )
    );
    $wp_customize->add_control(
        'text_setting2', array(
            'label'=>'Base link for tenders by product',
            'type'=>'text',
            'section'=>'filter',
            'settings'=>'base-product-link'
        )
    );
    $wp_customize->add_control(
        'text_setting3', array(
            'label'=>'Support email',
            'type'=>'text',
            'section'=>'child-theme-general',
            'settings'=>'email-support'
        )
    );
}
/**
 * Multiple select customize control class.
 */
if ( class_exists( 'WP_Customize_Control' ) ) {
    class Sinfonia_Customize_Control_Multiple_Select extends WP_Customize_Control
    {

        /**
         * The type of customize control being rendered.
         */
        public $type = 'multiple-select';

        /**
         * Displays the multiple select on the customize screen.
         */
        public function render_content()      {

              if ($this->type === 'multiple-select') {
                  if (empty($this->choices)) {
                      return;
                  }
               ?>
               <label>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                 <select <?php $this->link(); ?> multiple="multiple" style="height: 100%;" size="10">
                    <?php
                      foreach ($this->choices as $value => $label) {
                          $selected = '';
                          if (!empty($this->value())) {
                             $selected = (in_array($value, $this->value())) ? selected(1, 1, false) : '';
                         }
                         echo '<option value="' . esc_attr($value) . '"' . $selected . '>' . $label . '</option>';
                      }
                    ?>
                  </select>
                 </label>
        <?php }

        }

    }
}


/**
 * Get all  countries
 *
 * @return array
 */
function tender_countries_list() {
    $cats    = array();
    $cats[0] = 'None';
    foreach ( get_terms_by_post_type( 'tender-countries', 'tenders' ) as $categories => $category ) {
        $cats[ $category->term_id ] = $category->name;
    }

    return $cats;
}

/**
 * Validate the options against the existing categories
 *
 * @param  string[] $input
 *
 * @return string
 */
function tender_countries_list_sanitize( $input ) {
    $valid = tender_countries_list();

    foreach ( $input as $value ) {
        if ( ! array_key_exists( $value, $valid ) ) {
            return [];
        }
    }

    return $input;
}

/**
 * Get all  products
 *
 * @return array
 */
function tender_product_list() {
    $cats    = array();
    $cats[0] = 'None';
    foreach ( get_terms_by_post_type( 'tender-products', 'tenders' ) as $categories => $category ) {
        $cats[ $category->term_id ] = $category->name;
    }

    return $cats;
}


/**
 * Get all  countries for harbour
 *
 * @return array
 */
function tender_countries_list_harbour() {
    $cats    = array();
    $cats[0] = 'None';
    foreach ( get_terms_by_post_type( 'tender-countries', 'tenders' ) as $categories => $category ) {
        $cats[ $category->term_id ] = $category->name;
    }

    return $cats;
}


/**
 * Validate the options against the existing categories for harbour
 *
 * @param  string[] $input
 *
 * @return string
 */
function tender_countries_list_sanitize_harbour( $input ) {
    $valid = tender_countries_list_harbour();

    foreach ( $input as $value ) {
        if ( ! array_key_exists( $value, $valid ) ) {
            return [];
        }
    }

    return $input;
}



/**
 * Validate the options against the existing categories
 *
 * @param  string[] $input
 *
 * @return string
 */
function tender_product_list_sanitize( $input ) {
    $valid = tender_product_list();

    foreach ( $input as $value ) {
        if ( ! array_key_exists( $value, $valid ) ) {
            return [];
        }
    }

    return $input;
}
function link_sanitize ($input) {
    $valid_input = strip_tags($input);
    return $valid_input;
}
?>