<?php
/*
Plugin Name: Simply Multi addresses
Plugin URI:  simplyCT.co.il
Description: Enable multi addresses per user by ACF
Version:     1.0.0
Author:      Roy Ben Menachem
Author URI:  simplyCT.co.il
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
add_action('init','simply_init');
function simply_init()
{
    add_filter('woocommerce_checkout_fields', function ($fields) {
        $user = wp_get_current_user();
        $list = array_merge([null], get_field('field_5fd9d7c5bb008', 'user_' . $user->ID));
        if(empty($list)){
            return $fields;
        }
        $list_of_values = [];
        foreach ($list as $key => $value) {
            $list_of_values[$value->ID] = $value->post_title;
        }
        $fields['billing']['acf_addresses']['label'] = __('Shipping address', 'simplyCT');
        $fields['billing']['acf_addresses']['required'] = true;
        $fields['billing']['acf_addresses']['class'] = array( 'wps-drop' );
        $fields['billing']['acf_addresses']['type'] = 'select';
        $fields['billing']['acf_addresses']['options'] = $list_of_values;
        return $fields;
    });
    add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
        update_post_meta($order_id, acf_addresses, sanitize_text_field($_POST['acf_addresses']));
    });
    //add_action('woocommerce_checkout_process', 'simply_address_chosen');
    add_action('woocommerce_thankyou', 'simply_thankyou', 10, 2);
}
function simply_thankyou( $order_id ) {
    $order = wc_get_order( $order_id );
    $meta_key = 'acf_addresses';
    $branch_id = $order->get_meta( $meta_key,true );
    $address = get_the_title($branch_id);
    echo '<h2>'.__('Sonol Address','simply').'</h2>';
    echo '<h5>'.$address.'</h5>';
}
/*
function simply_address_chosen() {
    if(empty($_POST['acf_addresses'])){
      //  wc_add_notice( __( 'Unidress Address is missing.','simply'), 'error' );
    };
}
*/
