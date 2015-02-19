<?php

/**
 * @class       MBJ_Product_Tab_For_WooCommerce_Admin
 * @version	1.0.0
 * @package	product-tab-for-woocommerce
 * @category	Class
 * @author      jigneshkailam <phpwebcreators@gmail.com>
 */
class MBJ_Product_Tab_For_WooCommerce_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @var      string    $plugin_name       The name of this plugin.
     * @var      string    $version    The version of this plugin.
     */
    private $tab_data;

    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->tab_data = FALSE;
    }

    public function product_tab_for_woocommerce_init() {

        add_action('woocommerce_product_write_panel_tabs', array($this, 'product_write_panel_tab'));
        add_action('woocommerce_product_write_panels', array($this, 'product_write_panel'));
        add_action('woocommerce_process_product_meta', array($this, 'product_save_data'), 10, 2);
        add_filter('woocommerce_product_tabs', array($this, 'add_custom_product_tabs'));
        add_filter('woocommerce_product_tab_content', 'do_shortcode');
    }

    public function add_custom_product_tabs($tabs) {
        global $product;

        if ($this->product_has_custom_tabs($product)) {
            foreach ($this->tab_data as $tab) {
                $tabs[$tab['id']] = array(
                    'title' => __($tab['title'], 'product_tab_for_woocommerce'),
                    'priority' => 25,
                    'callback' => array($this, 'custom_product_tabs_panel_content'),
                    'content' => $tab['content'], // custom field
                );
            }
        }

        return $tabs;
    }

    public function custom_product_tabs_panel_content($key, $tab) {


        $content = apply_filters('the_content', $tab['content']);
        $content = str_replace(']]>', ']]&gt;', $content);

        echo apply_filters('woocommerce_product_tab_content', $content, $tab);
    }

    public function product_write_panel_tab() {
        echo "<li class=\"product_tabs_lite_tab\"><a href=\"#product_tab_for_woocommerce\">" . __('Product Tab', 'product_tab_for_woocommerce') . "</a></li>";
    }

    public function product_write_panel() {
        global $post;

        $tab_data = maybe_unserialize(get_post_meta($post->ID, 'product_tab_for_woocommerce_woo_product_tabs', true));
        if (empty($tab_data)) {
            $tab_data[] = array('title' => '', 'content' => '');
        }

        foreach ($tab_data as $tab) {
            $tab['content'] = (isset($tab['content'])) ? $tab['content'] : '';
            $tab['title'] = (isset($tab['title']) && !empty($tab['title'])) ? $tab['title'] : 'Product Description';
            echo '<br><br><div id="product_tab_for_woocommerce" class="panel wc-metaboxes-wrapper woocommerce_options_panel">';
            woocommerce_wp_text_input(array('id' => 'product_tab_for_woocommerce_tab_title', 'label' => __('Product Tab Title', 'product_tab_for_woocommerce'), 'description' => __('Required field, It will display fronend', 'product_tab_for_woocommerce'), 'value' => $tab['title'], 'class' => 'regular-text'));
            wp_editor(htmlspecialchars_decode($tab['content']), 'mettaabox_ID_stylee', $settings = array('textarea_name' => 'MyInputNAME'));
            echo '</div>';
        }
    }

    public function product_save_data($post_id, $post) {

        $tab_title = stripslashes($_POST['product_tab_for_woocommerce_tab_title']);
        $tab_content = stripslashes($_POST['MyInputNAME']);

        if (empty($tab_title) && empty($tab_content) && get_post_meta($post_id, 'product_tab_for_woocommerce_woo_product_tabs', true)) {
            delete_post_meta($post_id, 'product_tab_for_woocommerce_woo_product_tabs');
        } elseif (!empty($tab_title) || !empty($tab_content)) {
            $tab_data = array();

            $tab_id = '';
            if ($tab_title) {
                if (strlen($tab_title) != strlen(utf8_encode($tab_title))) {
                    $tab_id = "tab-custom";
                } else {
                    $tab_id = strtolower($tab_title);
                    $tab_id = preg_replace("/[^\w\s]/", '', $tab_id);
                    $tab_id = preg_replace("/_+/", ' ', $tab_id);
                    $tab_id = preg_replace("/\s+/", '-', $tab_id);
                    $tab_id = 'tab-' . $tab_id;
                }
            }


            $tab_data[] = array('title' => $tab_title, 'id' => $tab_id, 'content' => $tab_content);
            update_post_meta($post_id, 'product_tab_for_woocommerce_woo_product_tabs', $tab_data);
        }
    }

    private function product_has_custom_tabs($product) {
        if (false === $this->tab_data) {
            $this->tab_data = maybe_unserialize(get_post_meta($product->id, 'product_tab_for_woocommerce_woo_product_tabs', true));
        }
        return !empty($this->tab_data) && !empty($this->tab_data[0]) && !empty($this->tab_data[0]['title']);
    }

}