<?php

/**
 * @class       MBJ_Product_Tab_For_WooCommerce_Activator
 * @version	1.0.0
 * @package	product-tab-for-woocommerce
 * @category	Class
 * @author      jigneshkailam <phpwebcreators@gmail.com>
 */
class MBJ_Product_Tab_For_WooCommerce_Activator {

    /**
     * @since    1.0.0
     */
    public static function activate() {

        $active_plugins = (array) get_option('active_plugins', array());

        if (is_multisite()) {
            $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
        }

        return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
    }

}
