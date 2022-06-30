<?php

use Automattic\WooCommerce\Admin\API\Coupons;
use LDAP\Result;

    global $wpdb;
    $table_name_code_promo = $wpdb->prefix . "coupons";
    require plugin_dir_path(__DIR__) . 'controller/rest_api.php';
    $code_coupon = array();
    $all_coupons = $woocommerce->get('coupons');
    $results = $wpdb->get_results("SELECT * FROM  $table_name_code_promo ");
    
    foreach($results as $result){
        $code_coupon[$result->id] = $result->code;
    }
    
    foreach($all_coupons as $coupon){
       
    
        foreach($coupon->email_restrictions as $email){
            $email = $email;
        }
        if (!in_array($coupon->code,$code_coupon)) {
            
                            $wpdb->insert(
                                $table_name_code_promo,
                                array(
                                    'code' => $coupon->code,
                                    'email' => $email,
                                    'use_count' =>$coupon->usage_count ,
                                    'amount' => $coupon->amount
                                )
                            );
                        }
    }