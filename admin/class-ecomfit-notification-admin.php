<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ecomfit.com
 * @since      1.0.0
 *
 * @package    Ecomfit_Notification
 * @subpackage Ecomfit_Notification/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 *
 * @package    Ecomfit_Notification
 * @subpackage Ecomfit_Notification/admin
 * @author     ecomfit
 */
class Ecomfit_Notification_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin The ID of this plugin.
     */
    private $plugin;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * The request api.
     *
     * @since    1.0.0
     * @access   private
     * @var      Ecomfit_Api $request
     */
    private $request;

    /**
     * The product.
     *
     * @since    1.0.0
     * @access   private
     * @var      Ecomfit_Product $product
     */
    private $product;

    /**
     * The order.
     *
     * @since    1.0.0
     * @access   private
     * @var      Ecomfit_Order $order
     */
    private $order;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin The name of this plugin.
     * @param string $version The version of this plugin.
     * @param string $web_id The website id in ecomfit.
     * @since    1.0.0
     */
    public function __construct($plugin, $version, $web_id)
    {

        $this->plugin = $plugin;
        $this->version = $version;
        $this->web_id = $web_id;

        // Check woocommerce active
        if ($this->woocommerce_active()) {
            $this->load_dependencies();
        } else {
            add_action('admin_notices', function () {
                include(plugin_dir_path(__FILE__) . '/partials/ecomfit-notification-admin-require-woocommerce.php');
            });
        }
    }

    /**
     * Load the required dependencies.
     *
     * Include the following files that make up the plugin:
     *
     * - Ecomfit_Api. Defines all API.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class api request
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ecomfit-api.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ecomfit-product.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ecomfit-order.php';

        $api_url = ECOMFIT_NOTIFICATION_API_URL;
        $token = get_option('_ecomfit_auth_token');
        $this->request = new Ecomfit_Api($api_url, $token);

        $this->product = new Ecomfit_Product();
        $this->order = new Ecomfit_Order();

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin, plugin_dir_url(__FILE__) . 'css/ecomfit-notification-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin, plugin_dir_url(__FILE__) . 'js/ecomfit-notification-admin.js', array('jquery'), $this->version, false);
    }


    public function app_menu()
    {
        global $admin_page_hooks;
        if (empty ($admin_page_hooks['ecomfit-app'])) {
            add_menu_page(
                'Dashboard',
                'Ecomfit',
                'manage_options',
                'ecomfit-app',
                array($this, 'app_page'),
                plugins_url('admin/images/ecomfit-logo.jpg', plugin_dir_path(__FILE__))
            );
        }
        add_submenu_page(
            'ecomfit-app',
            'Ecomfit - Better Web Push Notifications',
            'Notification',
            'manage_options',
            'ecomfit-notification-app',
            array($this, 'app_page')
        );
    }

    public function app_page()
    {
        if (!$this->woocommerce_active()) {
            return;
        }
        $isLogged = get_option('_ecomfit_notification_login');
        if ($isLogged) {
            include(plugin_dir_path(__FILE__) . '/partials/ecomfit-notification-admin-dashboard.php');
        } else {
            if (isset($_POST["webId"]) && $_POST["webId"] && isset($_POST["token"]) && $_POST["token"]
                && isset($_POST["meteorToken"]) && $_POST["meteorToken"]) {
                $webId = sanitize_text_field($_POST["webId"]);
                $result = $this->request->post('/v3/wordpress/getApiToken', array(
                    'webId' => $webId,
                    'token' => sanitize_text_field($_POST["token"]),
                    'meteorToken' => sanitize_text_field($_POST["meteorToken"])
                ));
                if ($result && $result->status) {
                    if ($this->web_id != $webId) {
                        update_option('_ecomfit_popup_login', 0, true);
                        update_option('_ecomfit_analytics_login', 0, true);
                    }
                    update_option('_ecomfit_notification_login', 1, true);
                    update_option('_ecomfit_web_id', $webId, true);
                    update_option('_ecomfit_auth_token', $result->data->token, true);
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit();
                } else {
                    update_option('_ecomfit_notification_login', 0, true);
                }
            }
            include(plugin_dir_path(__FILE__) . '/partials/ecomfit-notification-admin-login.php');
        }
    }

    public function save_post($post_id, $post, $update)
    {
        $post_id = intval($post_id);
        $web_id = $this->web_id;
        if ($web_id && $post_id) {
            if (($post->post_type == 'product_variation') || ($post->post_type == 'product')) {
                $product_id = wp_get_post_parent_id($post_id);
                $product = array();
                if ($product_id == 0) {
                    $product = $this->product->get($post_id);
                } else {
                    $product = $this->product->get($product_id);
                }
                if ($product) {
                    if ($product['status'] == 'publish') {
                        $this->request->post('/v3/wordpress/save_product', array(
                            'webId' => $this->web_id,
                            'product' => $product
                        ));
                    } else if ($product['status'] == 'trash') {
                        $this->request->post('/v3/wordpress/delete_product', array(
                            'webId' => $this->web_id,
                            'product' => $product
                        ));
                    }
                }
            } else if ($post->post_type == 'shop_order') {
                $order = $this->order->get($post_id);
                if ($order) {
                    // Order status starting with Pending and ending with Completed.
                    if ($post->post_status != 'trash' && $post->post_status != 'wc-pending') {
                        $this->request->post('/v3/wordpress/save_order', array(
                            'webId' => $this->web_id,
                            'order' => $order
                        ));
                    }
                }
            }
        }
    }

    public function save_product_variation($id, $i)
    {
        $id = intval($id);
        $web_id = $this->web_id;
        if ($web_id && $id) {
            $product_id = wp_get_post_parent_id($id);
            $product = array();
            if ($product_id == 0) {
                $product = $this->product->get($id);
            } else {
                $product = $this->product->get($product_id);
            }
            if ($product) {
                $this->request->post('/v3/wordpress/update_product', array(
                    'webId' => $web_id,
                    'product' => $product
                ));
            }
        }
    }

    public function delete_product_variation($id)
    {
        $id = intval($id);
        $web_id = $this->web_id;
        if ($web_id && $id) {
            $product = $this->product->get($id);
            if ($product) {
                $this->request->post('/v3/wordpress/delete_product', array(
                    'webId' =>$web_id,
                    'product' => $product
                ));
            }
        }
    }

    public function woocommerce_active()
    {
        if (!function_exists('is_plugin_active_for_network')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }

        if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            if (!is_plugin_active_for_network('woocommerce/woocommerce.php')) {
                return false;
            }
        }

        return true;
    }
}
