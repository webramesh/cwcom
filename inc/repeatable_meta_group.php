<?php

/**
 * Repeatable Custom Fields in a Metabox
 * Author: Helen Hou-Sandi
 *
 * From a bespoke system, so currently not modular - will fix soon
 * Note that this particular metadata is saved as one multidimensional array (serialized)
 */
Class ThemeMetabox
{
public $slug = array();
    function __construct() {
        add_action('admin_init', array($this, 'hhs_add_meta_boxes'), 1);
        add_action('save_post',  array($this,'hhs_repeatable_meta_box_save'));
        add_action( 'admin_enqueue_scripts', array($this,'metabox_page_style') );
    }

public function metabox_page_style(){
        wp_register_style( 'admin_theme_style', get_stylesheet_directory_uri() . '/inc/admin-assets/css/style.css', array(), CH_KADENCE_VERSION);
        wp_enqueue_style( 'admin_theme_style' );
    }

public function hhs_get_sample_options()
{

    $terms = get_terms_by_post_type('tender-countries', 'tenders');
    $chosen_countries = array();
    $chosen_countries = get_theme_mod('filter-countries');
    if ($terms) :
        foreach ($terms as $term) :
            if (empty($chosen_countries) || $chosen_countries[0] === 0 || in_array($term->term_id, $chosen_countries)) {
                $options[$term->name] = 'tender-countries:' . $term->term_id;
                $this->slug['tender-countries:' . $term->term_id]=$term->slug;
            }
        endforeach;
    endif;

    $terms = get_terms_by_post_type('tender-products', 'tenders');
    $chosen_products = array();
    $chosen_products = get_theme_mod('filter-products');

    if ($terms) :
        foreach ($terms as $term) :
            if (empty($chosen_products) || $chosen_products[0] === 0 || in_array($term->term_id, $chosen_products)) {
                $options[$term->name] = 'tender-products:' . $term->term_id;
                $this->slug['tender-products:' . $term->term_id]=$term->slug;
            }
        endforeach;
    endif;


    return $options;
}


public function hhs_add_meta_boxes()
{
    add_meta_box('repeatable-fields', 'Additional page data', array($this,'hhs_repeatable_meta_box_display'), 'page', 'normal', 'default');
}

public function hhs_repeatable_meta_box_display()
{
    global $post;
    $subtitle = get_post_meta($post->ID, 'subtitle', true);
    $page_class = get_post_meta($post->ID, 'page_class', true);
?>
    <span><?php _e('Page Subtitle', 'kadence-child'); ?></span>
    <div class="td"><input type="text" class="widefat" name="subtitle" value="<?php if ($subtitle) echo $subtitle; ?>" /></div>
    <span><?php _e('Page Class', 'kadence-child'); ?></span>
    <div class="td"><input type="text" class="widefat" name="page_class" value="<?php if ($page_class) echo $page_class; ?>" /></div>
    <?php
    wp_nonce_field('hhs_repeatable_meta_box_nonce', 'hhs_repeatable_meta_box_nonce');
    if ($post->post_name === 'tenders-for-country') {
        $repeatable_fields = get_post_meta($post->ID, 'repeatable_fields', true);
        $options = $this->hhs_get_sample_options();
        $tags_h1 = __('h1', 'kadence-child');
        $tags_h2 = __('h2', 'kadence-child');
        $meta_titles = __('meta_title', 'kadence-child');
        $meta_descrs = __('meta_descr', 'kadence-child');
        $faq_title = __('faq_title', 'kadence-child');
        $q1s = __('Question_1', 'kadence-child');
        $a1s = __('Answer_1', 'kadence-child');
        $q2s = __('Question_2', 'kadence-child');
        $a2s = __('Answer_2', 'kadence-child');
        $q3s = __('Question_3', 'kadence-child');
        $a3s = __('Answer_3', 'kadence-child');
        $q4s = __('Question_4', 'kadence-child');
        $a4s = __('Answer_4', 'kadence-child');
        $q5s = __('Question_5', 'kadence-child');
        $a5s = __('Answer_5', 'kadence-child');

    ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#add-row').on('click', function() {
                    var row = $('.empty-row.screen-reader-text').clone(true);
                    row.removeClass('empty-row screen-reader-text');
                    row.insertAfter('#repeatable-fieldset-one >.tr:last');
                    return false;
                });

                $('.remove-row').on('click', function() {
                    $(this).parents('div.tr').remove();
                    return false;
                });
            });
        </script>


        <br><strong><?php _e('Country/product related meta and FAQ', 'kadence-child'); ?> </strong><br>
        <section id="repeatable-fieldset-one" width="100%" class="page_<?php echo $post->post_name; ?>">

            <?php

            if ($repeatable_fields) :

                foreach ($repeatable_fields as $field) {
            ?>
                    <div class="tr">
                        <span>country/product</span>
                        <div class="td">
                            <select name="select[]" class=" hjh">
                                <option value=""><?php echo "Select"; ?></option>
                                <?php foreach ($options as $label => $value) : ?>
                                    <option value="<?php echo $value; ?>" <?php selected($field['select'], $value); ?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="link"> <?php echo home_url().'/current-tenders/tenders-for-country/?country='.$this->slug[$field['select']]; ?></span>
                        </div>
                        <div class="repeatable_values">
                            <div class="wrap-group">
                                <div class="wrap-elem">
                                   <span><?php echo $tags_h1; ?></span>
                                   <div class="td"><input type="text" class="widefat" name="tag_h1[]" value="<?php if ($field['tag_h1'] != '') echo esc_attr($field['tag_h1']); ?>" /></div>
                                </div>
                                <div class="wrap-elem">
                                  <span><?php echo $tags_h2; ?></span>
                                   <div class="td"><input type="text" class="widefat" name="tag_h2[]" value="<?php if ($field['tag_h2'] != '') echo esc_attr($field['tag_h2']); ?>" /></div>
                                </div>
                            </div>
                            <div class="repeatable_q_value wrap-group">
							  <div class="wrap-elem">
                                 <span><?php echo $meta_titles; ?></span>
                                <div class="td"><input type="text" class="widefat" name="meta_title[]" value="<?php if ($field['meta_title'] != '') echo esc_attr($field['meta_title']);  ?>" /></div>
							  </div>
							  <div class="wrap-elem">
                                <span><?php echo $meta_descrs; ?></span>
                                <div class="td"><textarea class="widefat" name="meta_descr[]"><?php if ($field['meta_descr'] != '') echo esc_attr($field['meta_descr']);  ?></textarea></div>
							  </div>  
                            </div>
                            <span><?php echo $faq_title; ?></span>
                            <div class="td"><input type="text" class="widefat" name="faq_title[]" value="<?php if ($field['faq_title'] != '') echo esc_attr($field['faq_title']); ?>" /></div>
                            <div class="repeatable_q">
                                <div class="repeatable_q_value">
                                    <span><?php echo $q1s; ?></span>
                                    <div class="td"><textarea class="widefat" name="q1[]" rows="4" cols="90"><?php if ($field['q1'] != '') echo esc_attr($field['q1']);  ?></textarea></div>
                                    <span><?php echo $a1s; ?></span>
                                    <div class="td"><textarea class="widefat" name="a1[]" rows="4" cols="90"><?php if ($field['a1'] != '') echo esc_attr($field['a1']);  ?></textarea></div>
                                </div>
                                <div class="repeatable_q_value">
                                    <span><?php echo $q2s; ?></span>
                                    <div class="td"><textarea class="widefat" name="q2[]" rows="4" cols="90"><?php if ($field['q2'] != '') echo esc_attr($field['q2']);  ?></textarea></div>
                                    <span><?php echo $a2s; ?></span>
                                    <div class="td"><textarea class="widefat" name="a2[]" rows="4" cols="90"><?php if ($field['a2'] != '') echo esc_attr($field['a2']);  ?></textarea></div>
                                </div>
                                <div class="repeatable_q_value">
                                    <span><?php echo $q3s; ?></span>
                                    <div class="td"><textarea class="widefat" name="q3[]" rows="4" cols="90"><?php if ($field['q3'] != '') echo esc_attr($field['q3']);  ?></textarea></div>
                                    <span><?php echo $a3s; ?></span>
                                    <div class="td"><textarea class="widefat" name="a3[]" rows="4" cols="90"><?php if ($field['a3'] != '') echo esc_attr($field['a3']);  ?></textarea></div>
                                </div>
                                <div class="repeatable_q_value">
                                    <span><?php echo $q4s; ?></span>
                                    <div class="td"><textarea class="widefat" name="q4[]" rows="4" cols="90"><?php if ($field['q4'] != '') echo esc_attr($field['q4']);  ?></textarea></div>
                                    <span><?php echo $a4s; ?></span>
                                    <div class="td"><textarea class="widefat" name="a4[]" rows="4" cols="90"><?php if ($field['a4'] != '') echo esc_attr($field['a4']);  ?></textarea></div>
                                </div>
                                <div class="repeatable_q_value">
                                    <span><?php echo $q5s; ?></span>
                                    <div class="td"><textarea class="widefat" name="q5[]" rows="4" cols="90"><?php if ($field['q5'] != '') echo esc_attr($field['q5']);  ?></textarea></div>
                                    <span><?php echo $a5s; ?></span>
                                    <div class="td"><textarea class="widefat" name="a5[]" rows="4" cols="90"><?php if ($field['a5'] != '') echo esc_attr($field['a5']);  ?></textarea></div>
                                </div>
                            </div>
                        </div>
                        <div class="td"><a class="button remove-row" href="#">Remove</a></div>
                    </div>
                <?php
                }
            else :
                // show a blank one
                ?>
                <div class="tr">
                    <span>country/product</span>
                    <div class="td">
                        <select name="select[]">
                            <option value="" selected><?php echo "Select"; ?></option>
                            <?php foreach ($options as $label => $value) : ?>
                                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="repeatable_values">
					  <div class="wrap-group">
					    <div class="wrap-elem">
                            <span><?php echo $tags_h1; ?></span>
                            <div class="td"><input type="text" class="widefat" name="tag_h1[]" /></div>
						</div>	
						<div class="wrap-elem">
                            <span><?php echo $tags_h2; ?></span>
                            <div class="td"><input type="text" class="widefat" name="tag_h2[]" /></div>
						 </div>	
					  </div>
					   <div class="wrap-group">
					     <div class="wrap-elem">	
                             <span><?php echo $meta_titles; ?></span>
                            <div class="td"><input type="text" class="widefat" name="meta_title[]" /></div>
						  </div>
						  <div class="wrap-elem">
                            <span><?php echo $meta_descrs; ?></span>
                            <div class="td"><textarea class="widefat" name="meta_descr[]" rows="4" cols="90"></textarea></div>
						  </div>
					   </div>
						
                        <span><?php echo $faq_title; ?></span>
                        <div class="td"><input type="text" class="widefat" name="faq_title[]" /></div>
                        <div class="repeatable_q">
                            <div class="repeatable_q_value">
                                <span><?php echo $q1s; ?></span>
                                <div class="td"><textarea class="widefat" name="q1[]" rows="4" cols="90"></textarea></div>
                                <span><?php echo $a1s; ?></span>
                                <div class="td"><textarea class="widefat" name="a1[]" rows="4" cols="90"></textarea></div>
                            </div>
                            <div class="repeatable_q_value">
                                <span><?php echo $q2s; ?></span>
                                <div class="td"><textarea class="widefat" name="q2[]" rows="4" cols="90"></textarea></div>
                                <span><?php echo $a2s; ?></span>
                                <div class="td"><textarea class="widefat" name="a2[]" rows="4" cols="90"></textarea></div>
                            </div>
                            <div class="repeatable_q_value">
                                <span><?php echo $q3s; ?></span>
                                <div class="td"><textarea class="widefat" name="q3[]" rows="4" cols="90"></textarea></div>
                                <span><?php echo $a3s; ?></span>
                                <div class="td"><textarea class="widefat" name="a3[]" rows="4" cols="90"></textarea></div>
                            </div>
                            <div class="repeatable_q_value">
                                <span><?php echo $q4s; ?></span>
                                <div class="td"><textarea class="widefat" name="q4[]" rows="4" cols="90"></textarea></div>
                                <span><?php echo $a4s; ?></span>
                                <div class="td"><textarea class="widefat" name="a4[]" rows="4" cols="90"></textarea></div>
                            </div>
                            <div class="repeatable_q_value">
                                <span><?php echo $q5s; ?></span>
                                <div class="td"><textarea class="widefat" name="q5[]" rows="4" cols="90"></textarea></div>
                                <span><?php echo $a5s; ?></span>
                                <div class="td"><textarea class="widefat" name="a5[]" rows="4" cols="90"></textarea></div>
                            </div>
                        </div>
                    </div>
                    <div class="td"><a class="button remove-row" href="#">Remove</a></div>
                </div>
            <?php endif; ?>

            <!-- empty hidden one for jQuery -->

            <div class="tr empty-row screen-reader-text">
                <span>country/product</span>
                <div class="td">
                    <select name="select[]">
                        <option value="" selected><?php echo "Select"; ?></option>
                        <?php foreach ($options as $label => $value) : ?>
                            <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="repeatable_values">
						<div class="wrap-group">
					      <div class="wrap-elem">
                             <span><?php echo $tags_h1; ?></span>
                             <div class="td"><input type="text" class="widefat" name="tag_h1[]" /></div>
						  </div> 
						   <div class="wrap-elem">
                             <span><?php echo $tags_h2; ?></span>
                             <div class="td"><input type="text" class="widefat" name="tag_h2[]" /></div>
							 </div>
                        </div>	
						<div class="wrap-group">
                            <div class="wrap-elem">						   
                               <span><?php echo $meta_titles; ?></span>
                               <div class="td"><input type="text" class="widefat" name="meta_title[]" /></div>
							</div>
							<div class="wrap-elem">
                               <span><?php echo $meta_descrs; ?></span>
                               <div class="td"><textarea class="widefat" name="meta_descr[]" rows="4" cols="90"></textarea></div>
							 </div>
                        </div>							  
                    <span><?php echo $faq_title; ?></span>
                    <div class="td"><input type="text" class="widefat" name="faq_title[]" /></div>
                    <div class="repeatable_q">
                        <div class="repeatable_q_value">
                            <span><?php echo $q1s; ?></span>
                            <div class="td"><textarea class="widefat" name="q1[]" rows="4" cols="90"></textarea></div>
                            <span><?php echo $a1s; ?></span>
                            <div class="td"><textarea class="widefat" name="a1[]" rows="4" cols="90"></textarea></div>
                        </div>
                        <div class="repeatable_q_value">
                            <span><?php echo $q2s; ?></span>
                            <div class="td"><textarea class="widefat" name="q2[]" rows="4" cols="90"></textarea></div>
                            <span><?php echo $a2s; ?></span>
                            <div class="td"><textarea class="widefat" name="a2[]" rows="4" cols="90"></textarea></div>
                        </div>
                        <div class="repeatable_q_value">
                            <span><?php echo $q3s; ?></span>
                            <div class="td"><textarea class="widefat" name="q3[]" rows="4" cols="90"></textarea></div>
                            <span><?php echo $a3s; ?></span>
                            <div class="td"><textarea class="widefat" name="a3[]" rows="4" cols="90"></textarea></div>
                        </div>
                        <div class="repeatable_q_value">
                            <span><?php echo $q4s; ?></span>
                            <div class="td"><textarea class="widefat" name="q4[]" rows="4" cols="90"></textarea></div>
                            <span><?php echo $a4s; ?></span>
                            <div class="td"><textarea class="widefat" name="a4[]" rows="4" cols="90"></textarea></div>
                        </div>
                        <div class="repeatable_q_value">
                            <span><?php echo $q5s; ?></span>
                            <div class="td"><textarea class="widefat" name="q5[]" rows="4" cols="90"></textarea></div>
                            <span><?php echo $a5s; ?></span>
                            <div class="td"><textarea class="widefat" name="a5[]" rows="4" cols="90"></textarea></div>
                        </div>
                    </div>
                    <div class="td"><a class="button remove-row" href="#">Remove</a></div>
                </div>

        </section>

        <p><a id="add-row" class="button" href="#">Add another</a></p>
<?php
    }
}

public function hhs_repeatable_meta_box_save($post_id)
{
    if (
        !isset($_POST['hhs_repeatable_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['hhs_repeatable_meta_box_nonce'], 'hhs_repeatable_meta_box_nonce')
    )
        return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (!current_user_can('edit_post', $post_id))
        return;

    $old = get_post_meta($post_id, 'repeatable_fields', true);
    $old_sb_title = get_post_meta($post_id, 'subtitle', true);
    $old_page_class = get_post_meta($post_id, 'page_class', true);
    $new = array();
    $new_page_class = $new_sb_title = '';
    $options = $this->hhs_get_sample_options();

    $page_class = $_POST['page_class'];
    $sb_title = $_POST['subtitle'];
    $selects = $_POST['select'];
    $tags_h1 = $_POST['tag_h1'];
    $tags_h2 = $_POST['tag_h2'];
    $meta_titles = $_POST['meta_title'];
    $meta_descrs = $_POST['meta_descr'];
    $faq_title = $_POST['faq_title'];
    $q1s = $_POST['q1'];
    $a1s = $_POST['a1'];
    $q2s = $_POST['q2'];
    $a2s = $_POST['a2'];
    $q3s = $_POST['q3'];
    $a3s = $_POST['a3'];
    $q4s = $_POST['q4'];
    $a4s = $_POST['a4'];
    $q5s = $_POST['q5'];
    $a5s = $_POST['a5'];
	
	

    $count = (is_array($selects))?count($selects):0;

    for ($i = 0; $i < $count; $i++) {
        if (in_array($selects[$i], $options)) :
            $new[$i]['select'] = $selects[$i];
            if ($tags_h1[$i]) {
                $new[$i]['tag_h1'] = stripslashes(strip_tags($tags_h1[$i]));
            } else {
                $new[$i]['tag_h1'] = '';
            }
            if ($tags_h2[$i]) {
                $new[$i]['tag_h2'] = stripslashes(strip_tags($tags_h2[$i]));
            } else {
                $new[$i]['tag_h2'] = '';
            }
            if ($meta_titles[$i]) {
                $new[$i]['meta_title'] = stripslashes(strip_tags($meta_titles[$i]));
            } else {
                $new[$i]['meta_title'] = '';
            }
            if ($meta_descrs[$i]) {
                $new[$i]['meta_descr'] = stripslashes(strip_tags($meta_descrs[$i]));
            } else {
                $new[$i]['meta_descr'] = '';
            }
            if ($faq_title[$i]) {
                $new[$i]['faq_title'] = stripslashes(strip_tags($faq_title[$i]));
            } else {
                $new[$i]['faq_title'] = '';
            }
            if ($q1s[$i]) {
                $new[$i]['q1'] = stripslashes(strip_tags($q1s[$i]));
            } else {
                $new[$i]['q1'] = '';
            }
            if ($a1s[$i]) {
                $new[$i]['a1'] = stripslashes(strip_tags($a1s[$i]));
            } else {
                $new[$i]['a1'] = '';
            }
            if ($q2s[$i]) {
                $new[$i]['q2'] = stripslashes(strip_tags($q2s[$i]));
            } else {
                $new[$i]['q2'] = '';
            }
            if ($a2s[$i]) {
                $new[$i]['a2'] = stripslashes(strip_tags($a2s[$i]));
            } else {
                $new[$i]['a2'] = '';
            }
            if ($q3s[$i]) {
                $new[$i]['q3'] = stripslashes(strip_tags($q3s[$i]));
            } else {
                $new[$i]['q3'] = '';
            }
            if ($a3s[$i]) {
                $new[$i]['a3'] = stripslashes(strip_tags($a3s[$i]));
            } else {
                $new[$i]['a3'] = '';
            }
            if ($q4s[$i]) {
                $new[$i]['q4'] = stripslashes(strip_tags($q4s[$i]));
            } else {
                $new[$i]['q4'] = '';
            }
            if ($a4s[$i]) {
                $new[$i]['a4'] = stripslashes(strip_tags($a4s[$i]));
            } else {
                $new[$i]['a4'] = '';
            }
            if ($q5s[$i]) {
                $new[$i]['q5'] = stripslashes(strip_tags($q5s[$i]));
            } else {
                $new[$i]['q5'] = '';
            }
            if ($a5s[$i]) {
                $new[$i]['a5'] = stripslashes(strip_tags($a5s[$i]));
            } else {
                $new[$i]['a5'] = '';
            }
        endif;
    }
    if ( $sb_title  ) {
        $new_sb_title=sanitize_text_field( $sb_title ); }
    if ( $page_class ) {
        $new_page_class=sanitize_text_field( $page_class ); }
    if ( !empty( $new ) && $new != $old )
        update_post_meta( $post_id, 'repeatable_fields', $new );
    elseif ( empty($new) && $old )
        delete_post_meta( $post_id, 'repeatable_fields', $old );
    if ( $new_sb_title && $new_sb_title != $old_sb_title )
        update_post_meta( $post_id, 'subtitle', $new_sb_title );
    elseif ( !($new_sb_title) && $old_sb_title )
        delete_post_meta( $post_id, 'subtitle', $old_sb_title );
    if ( !empty($new_page_class) && $new_page_class != $old_page_class )
        update_post_meta( $post_id, 'page_class', $new_page_class );
    elseif ( empty($new_page_class) && $old_page_class )
        delete_post_meta( $post_id, 'page_class', $old_page_class);
}
}
new ThemeMetabox;
?>