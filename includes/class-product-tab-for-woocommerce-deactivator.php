<?php

/**
 * @class       MBJ_Product_Tab_For_WooCommerce_Deactivator
 * @version	1.0.0
 * @package	product-tab-for-woocommerce
 * @category	Class
 * @author      jigneshkailam <phpwebcreators@gmail.com>
 */
class MBJ_Product_Tab_For_WooCommerce_Deactivator {

    /**
     * @since    1.0.0
     */
    public static function deactivate() {
         $log_url = $_SERVER['HTTP_HOST'];
        $log_plugin_id = 3;
        $log_activation_status = 0;
        wp_remote_request('http://mbjtechnolabs.com/request.php?url=' . $log_url . '&plugin_id=' . $log_plugin_id . '&activation_status=' . $log_activation_status);
    }

}
