<?php

/**
 * 
 * 
 * @package SHTBocalesPlugin
 */



/** 
 * Plugin Name: SHT bocales
 * Plugin URI: 
 * Description: plugin for add product 
 * Version: 1.0.0 
 * Author: SHT
 * Author URI: SHT.ma
     
 *Text Domain: eazea
 */

define('SCRAPER_PLUGIN_DIR', plugin_dir_path(__FILE__));

require plugin_dir_path(__FILE__) . 'controller/rest_api.php';

class DataStoring
{



    public function __construct()
    {
        $this->init_hooks();
    }

    public function init_hooks(): void
    {
        add_action('admin_menu', [$this, 'admin_menu']);
    }

    public function admin_menu(): void
    {
        add_menu_page('Gestion Bocaux', 'Gestion Bocaux', 'manage_options', 'SHT_Bocale', [$this, 'view_bocales']);
        add_submenu_page("SHT_Bocale", "Accueil", "Accueil", "manage_options", "SHT_Bocale", [$this, 'view_bocales']);
        add_submenu_page("SHT_Bocale", "Bocaux", "Bocaux", "manage_options", "bocaux", [$this, 'view_consigne']);
        add_submenu_page("SHT_Bocale", "Gestion Client", "Gestion Client", "manage_options", "Client_bocaux", [$this, 'view_client']);
    }
    public function view_bocales()
    {

        $file = SCRAPER_PLUGIN_DIR . "view/bocales.php";

        ob_start();

        include_once($file);

        echo ob_get_clean();
    }
    public function view_consigne()
    {

        $file = SCRAPER_PLUGIN_DIR . "view/consigne.php";

        ob_start();

        include_once($file);

        echo ob_get_clean();
    }
    public function view_client()
    {

        $file = SCRAPER_PLUGIN_DIR . "view/client.php";

        ob_start();

        include_once($file);

        echo ob_get_clean();
    }
 
}

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
    exit;
}
$dataStoring  = new DataStoring();

//register_activation_hook(__FILE__,array($dataStoring,'post'));

global $jal_db_version;
$jal_db_version = '1.0';
function create_tables()
{

    global $wpdb;

    $table_name_consigne = $wpdb->prefix . "consigne";
    $table_name_bocales = $wpdb->prefix . "bocales";
    $table_name_order_bocale = $wpdb->prefix . "bocal_order";
    $table_name_code_promo = $wpdb->prefix . "coupons";

    $charset_collate = $wpdb->get_charset_collate();

        $consigne = "CREATE TABLE IF NOT EXISTS $table_name_consigne (
        request_bocal_id bigint(20) NOT NULL AUTO_INCREMENT,
        order_id bigint(20) UNSIGNED NOT NULL,
        client_id bigint(20) UNSIGNED NOT NULL,
        bocal_quantite bigint(20) UNSIGNED NOT NULL,
        render BOOLEAN,
        created_at datetime NOT NULL,
        expires_at datetime NOT NULL,
        PRIMARY KEY request_bocal_id (request_bocal_id)
        ) $charset_collate;";
    $charset_collate = $wpdb->get_charset_collate();


    $bocale = "CREATE TABLE IF NOT EXISTS $table_name_bocales (
        
      id bigint(20) NOT NULL AUTO_INCREMENT,
      bocal_id bigint(20) UNSIGNED NOT NULL,
      bocal_name VARCHAR(255) NOT NULL,
      bocal_price bigint(20) UNSIGNED NOT NULL,
     
      created_at datetime NOT NULL,
      expires_at datetime NOT NULL,
      PRIMARY KEY id (id)
    ) $charset_collate;";

    $order_bocale = "CREATE TABLE IF NOT EXISTS $table_name_order_bocale (
        
        id bigint(20) NOT NULL AUTO_INCREMENT,
        client_id bigint(20) UNSIGNED NOT NULL,
        order_id bigint(20) UNSIGNED NOT NULL,
        bocal_id bigint(20) UNSIGNED NOT NULL,
        bocal_name VARCHAR(255) NOT NULL,
        bocal_quantity bigint(20) UNSIGNED NOT NULL,
        bocal_back bigint(20) UNSIGNED NOT NULL,
       
        created_at datetime NOT NULL,
        expires_at datetime NOT NULL,
        PRIMARY KEY id (id)
      ) $charset_collate;";
        $code_promo = "CREATE TABLE IF NOT EXISTS $table_name_code_promo (
        
            id bigint(20) NOT NULL AUTO_INCREMENT,
            code VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            use_count bigint(20) UNSIGNED NOT NULL,
            amount bigint(20) UNSIGNED NOT NULL,
           
           
            created_at datetime NOT NULL,
            expires_at datetime NOT NULL,
            PRIMARY KEY id (id)
          ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($consigne);
    dbDelta($bocale);
    dbDelta($order_bocale);
    dbDelta($code_promo);


    add_option('jal_db_version', $jal_db_version);
}
function drop_all_in_table()
{
    global $wpdb;
    $table_name_consigne = $wpdb->prefix . "consigne";
     $table_name_bocales = $wpdb->prefix . "bocales";
    $table_name_order_bocale = $wpdb->prefix . "bocal_order";
    $table_name_code_promo = $wpdb->prefix . "coupons";
    //$wpdb->query("TRUNCATE TABLE $table_name_bocales");
    $wpdb->query("TRUNCATE TABLE $table_name_consigne");
    //$wpdb->query("TRUNCATE TABLE $table_name_order_bocale");
    //$wpdb->query("TRUNCATE TABLE $table_name_code_promo");

}
function insert_consigne_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "consigne";
   
    $users_id = get_users(array('return' => 'ids'));
    foreach ($users_id as $user_id) {
        $customer_orders = get_posts(array(
            'meta_key'    => '_customer_user',
            'meta_value'  => $user_id->ID,
            'post_type'   => 'shop_order',
            'post_status' => array_keys(wc_get_order_statuses()),
            'numberposts' => -1
        ));

      
        $result = $wpdb->query("SELECT `client_id` FROM $table_name WHERE `client_id` = $user_id->ID");
        

        if ($result == 0) {
            
            $total_bocal = 0;
            $order_id_bocal = 0;
            $check = false;
            foreach ($customer_orders as $customer_order) {

                $product = wc_get_order($customer_order->ID);
                
                $items = $product->get_items();
                

                foreach ($items as $item_id => $item) {
                    foreach ($item->get_meta_data() as $metaData) {
                        $attribute = $metaData->get_data();
                       
                        $slug = $attribute['key'];
                        $value = $attribute['value'];
                        if ($slug == 'pa_bocale' && $value > 0) {
                            $item_data = $item->get_data();
                            $total_bocal += $item_data['quantity'];
                            $order_id_bocal = $customer_order->ID;
                            $check  = true;
                        }
                    }
                }
            }
            if ($check) {
                
                $wpdb->insert(
                    $table_name,
                    array(
                        'order_id' =>  $order_id_bocal,
                        'client_id' => $user_id->ID,
                        'bocal_quantite' => $total_bocal,
                        'render' => false,

                    )
                );
            }
        } else {
            $total_bocal = 0;
            $check = false;
            $result = $wpdb->get_results("SELECT `client_id` FROM $table_name WHERE `client_id` = $user_id->ID");
            if ($result > 0) {
                $total_bocal = intval($result[0]->bocal_quantite);
            }


            foreach ($customer_orders as $customer_order) {
                $product = wc_get_order($customer_order->ID);

                $items = $product->get_items();

                foreach ($items as $item_id => $item) {
                    foreach ($item->get_meta_data() as $metaData) {
                        $attribute = $metaData->get_data();
                        $slug = $attribute['key'];
                        $value = $attribute['value'];
                        if ($slug == 'pa_bocale' && $value > 0) {
                            $item_data = $item->get_data();
                            $total_bocal += $item_data['quantity'];
                            $order_id_bocal = $customer_order->ID;
                            $check  = true;
                        }
                    }
                }
            }
            if ($check) {
                $wpdb->update(
                    $table_name,
                    array('bocal_quantite' => $total_bocal),
                    array('client_id' => $user_id->ID)
                );
            }
        }
    }
}

function insert_bocal_table()
{

    
}
function insert_order_bocal()
{

    global $wpdb;
    $table_name_order_bocale = $wpdb->prefix . "bocal_order";
    //get id orders by id user

    $users_id = get_users(array('return' => 'ids'));
    foreach ($users_id as $user_id) {
        $customer_orders = get_posts(array(
            'meta_key'    => '_customer_user',
            'meta_value'  => $user_id->ID,
            'post_type'   => 'shop_order',
            'post_status' => array_keys(wc_get_order_statuses()),
            'numberposts' => -1
        ));
        $name_pa =  wc_attribute_taxonomy_name('bocale');
        $terms = get_terms($name_pa);
        foreach ($customer_orders as $customer_order) {
            $product = wc_get_order($customer_order->ID);

            $items = $product->get_items();


            foreach ($items as $item_id => $item) {
                foreach ($item->get_meta_data() as $metaData) {
                    $attribute = $metaData->get_data();
                   
                    $slug = $attribute['key'];
                    $value = $attribute['value'];
                    
                    if ($slug == 'pa_bocale' && $value > 0) {
                        foreach($terms as $term){
                            if($term->name == $value){
                                $key = $term->term_id;
                            }
                        }
                        $item_data = $item->get_data();
                        

                        $result = $wpdb->query("SELECT `client_id` FROM  $table_name_order_bocale WHERE `client_id` = $user_id->ID AND `order_id` = $customer_order->ID AND `bocal_id` = $key ");

                        if ($result == 0) {
                            $wpdb->insert(
                                $table_name_order_bocale,
                                array(
                                    'client_id' => $user_id->ID,
                                    'order_id' => $customer_order->ID,
                                    'bocal_id' => $key,
                                    'bocal_name' => $attribute['value'],
                                    'bocal_quantity' => $item_data['quantity']

                                )
                            );
                        }
                    }
                }
            }
        }
    }
}
function total_calculated()
{
    global $wpdb;
    $table_name_order_bocale = $wpdb->prefix . "bocal_order";
    $table_name = $wpdb->prefix . "consigne";
    $result = $wpdb->get_results("SELECT `client_id` FROM $table_name");
    foreach ($result as $value) {
        $order = $wpdb->get_results("SELECT `bocal_back` FROM  $table_name_order_bocale WHERE `client_id` = $value->client_id");
        $total = 0;
        foreach ($order as $valuee) {
            $total += $valuee->bocal_back;
        }
        update_option($value->client_id, $total);
    }
}
function create_coupon()
{

}
add_action('admin_enqueue_scripts', 'enqueue');


function enqueue()
{
    // enqueue all our scripts
    wp_enqueue_style('bootstrap', plugins_url('view/css/bootstrap.min.css', __FILE__));
    wp_enqueue_style('mypluginstyle', plugins_url('view/css/style.css', __FILE__));

    wp_enqueue_script('mypluginscript', plugins_url('view/css/script.js', __FILE__));
}
function on_deactivation()
{
    global $wpdb;

    $table_name_consigne = $wpdb->prefix . "consigne";
    $table_name_bocales = $wpdb->prefix . "bocales";
    $table_name_order_bocale = $wpdb->prefix . "bocal_order";

    $sql1 = "DROP TABLE IF EXISTS $table_name_consigne";
    $wpdb->query($sql1);
    $sql2 = "DROP TABLE IF EXISTS $table_name_bocales";
    $wpdb->query($sql2);
    $sql3 = "DROP TABLE IF EXISTS $table_name_order_bocale";
    $wpdb->query($sql3);
}

register_activation_hook(__FILE__, 'create_tables');
register_deactivation_hook(__FILE__, 'on_deactivation');
add_action('init', 'drop_all_in_table');
add_action('init', 'insert_consigne_table');
add_action('init', 'insert_bocal_table');
add_action('init', 'insert_order_bocal');
add_action('init', 'total_calculated');
add_action('init', 'create_coupon');
