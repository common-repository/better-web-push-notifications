<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ecomfit.com
 * @since      1.0.0
 *
 * @package    Ecomfit_Notification
 * @subpackage Ecomfit_Notification/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 *
 * @package    Ecomfit_Notification
 * @subpackage Ecomfit_Notification/public
 * @author     ecomfit
 */
class Ecomfit_Notification_Public
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
     * The website id in ecomfit.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $web_id The current website id of website.
     */
    private $web_id;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin The name of the plugin.
     * @param string $version The version of this plugin.
     * @param string $web_id The website id in ecomfit.
     * @since    1.0.0
     */
    public function __construct($plugin, $version, $web_id)
    {

        $this->plugin = $plugin;
        $this->version = $version;
        $this->web_id = $web_id;

        $this->load_dependencies();

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
        $token = '';
        $this->request = new Ecomfit_Api($api_url, $token);

        $this->product = new Ecomfit_Product();
        $this->order = new Ecomfit_Order();

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin, plugin_dir_url(__FILE__) . 'css/ecomfit-notification-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin, plugin_dir_url(__FILE__) . 'js/ecomfit-notification-public.js', array('jquery'), $this->version, false);
    }

    public function ecf_sw_rewrites()
    {
        global $wp_rewrite;
        $ecomfit_url_prefix = '';
        add_rewrite_rule('^' . $ecomfit_url_prefix . '/?$', 'index.php?ecomfit=/', 'top');
        add_rewrite_rule('^' . $ecomfit_url_prefix . '/(.*)?', 'index.php?ecomfit=/$matches[1]', 'top');
        add_rewrite_rule('^' . $wp_rewrite->index . '/' . $ecomfit_url_prefix . '/?$', 'index.php?ecomfit=/', 'top');
        add_rewrite_rule('^' . $wp_rewrite->index . '/' . $ecomfit_url_prefix . '/(.*)?', 'index.php?ecomfit=/$matches[1]', 'top');
    }

    public function ecf_sw_query_filter($query_vars)
    {
        $query_vars[] = 'ecomfit';
        $query_vars[] = 'webId';
        $query_vars[] = 'clientId';

        return $query_vars;
    }

    public function ecf_sw_output()
    {
        if (get_query_var('ecomfit') == 'workservice/get') {
            $content = $this->request->get('/workservice/get?webId=' . $this->web_id . '&clientId=' . get_query_var('clientId'));
            @header('Content-Type: application/javascript; charset=UTF-8');
            echo $content;
            exit();
        }
    }


    public function ecf_sdk_script()
    {
        echo '<script type="text/javascript">
				(function (w, d, s, id, src) {
					if (d.getElementById(id)) return;
					var js, fjs = d.getElementsByTagName(s)[0];
					js = d.createElement(s);
					js.id = id;
					js.src = src;
					fjs.parentNode.insertBefore(js, fjs);
				})(window, document, "script", "ecomfit-sdk", "' . ECOMFIT_NOTIFICATION_SDK_URL . '?v=' . ECOMFIT_NOTIFICATION_SDK_VERSION . '&webId=' . $this->web_id . '");
			</script>';
    }


    /**
     * Register the Router for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function register_router_api()
    {
        // get detail product
        register_rest_route('ecomfit', '/product/(?P<id>[\d]+)', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_product'),
        ));

        // get list product
        register_rest_route('ecomfit', '/products/(?P<limit>[\d]+)/(?P<offset>[\d]+)', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_products'),
        ));

        // get detail order
        register_rest_route('ecomfit', '/order/(?P<id>[\d]+)', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_order'),
        ));

        // get list order
        register_rest_route('ecomfit', '/orders/(?P<limit>\d+)/(?P<offset>\d+)/(?P<from>.+)/(?P<to>.+)', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_orders'),
        ));

        // get current cart
        register_rest_route('ecomfit', '/cart', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_cart'),
        ));
    }

    public function get_product($params)
    {
        if (isset($params['id']) && $params['id']) {
            $id = intval($params['id']);
            if ($id) {
                return rest_ensure_response($this->product->get($id));
            }
        }
        return rest_ensure_response(array());
    }

    public function get_products($params)
    {
        if (isset($params['limit']) && isset($params['offset'])) {
            $limit = intval($params['limit']);
            $offset = intval($params['offset']);
            if ($limit) {
                return rest_ensure_response($this->product->gets($limit, $offset));
            }
        }
        return rest_ensure_response(array());
    }

    public function get_order($params)
    {
        if (isset($params['id']) && $params['id']) {
            $id = intval($params['id']);
            if ($id) {
                return rest_ensure_response($this->order->get($id));
            }
        }
        return rest_ensure_response(array());
    }

    public function get_orders($params)
    {
        if (isset($params['limit']) && isset($params['offset']) && isset($params['from']) && isset($params['to'])) {
            $limit = intval($params['limit']);
            $offset = intval($params['offset']);
            if ($limit) {
                return $this->order->gets($params['from'], $params['to'], $limit, $offset);
            }
        }
        return rest_ensure_response(array());
    }

    public function get_cart()
    {
        $items = isset(WC()->cart) ? WC()->cart->get_cart() : [];
        $token = self::cart_token();
        if (!sizeof($items)) {
            return array(
                'token' => $token,
                'item_count' => 0,
                'items' => [],
                'session' => WC()->session->get_session_cookie(),
                'customer' => WC()->cart->get_customer(),
            );
        }
        $result = array_merge(array(
            'token' => $token,
            'item_count' => 0,
            'items' => [],
            'session' => WC()->session->get_session_cookie(),
            'customer' => WC()->cart->get_customer(),
        ), WC()->cart->get_totals());

        foreach ($items as $item) {
            $product = $item['data'];
            if ($product && is_a($product, 'WC_Product')) {
                $productId = $product->get_parent_id();
                $variantId = $product->get_variation_id();
                if (!$productId) {
                    $productId = $product->get_id();
                }
                if ($productId == $variantId) {
                    $variantId = 0;
                }
                array_push($result['items'], array(
                    "product_id" => $productId,
                    "title" => $product->get_title(),
                    "name" => $product->get_name(),
                    "price" => floatval($product->get_price()),
                    "total" => floatval($item['line_total']),
                    "quantity" => intval($item['quantity']),
                    "variant_id" => $variantId,
                    "variant_url" => $product->get_permalink(),
                    "url" => get_permalink($productId),
                    "data" => $item,
                ));
                $result['item_count']++;
            }
        }

        return $result;
    }

    public function check_prerequisites()
    {
        if (version_compare(WC_VERSION, '3.6.0', '>=')) {
            if (defined('WC_ABSPATH')) {
                // WC 3.6+ - Cart and notice functions are not included during a REST request.
                include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
                include_once WC_ABSPATH . 'includes/wc-notice-functions.php';
            }

            if (null === WC()->session) {
                $session_class = apply_filters('woocommerce_session_handler', 'WC_Session_Handler');

                // Prefix session class with global namespace if not already namespaced
                if (false === strpos($session_class, '\\')) {
                    $session_class = '\\' . $session_class;
                }

                WC()->session = new $session_class();
                WC()->session->init();
            }

            /**
             * For logged in customers, pull data from their account rather than the
             * session which may contain incomplete data.
             */
            if (is_null(WC()->customer)) {
                if (is_user_logged_in()) {
                    WC()->customer = new WC_Customer(get_current_user_id());
                } else {
                    WC()->customer = new WC_Customer(get_current_user_id(), true);
                }

                // Customer should be saved during shutdown.
                add_action('shutdown', array(WC()->customer, 'save'), 10);
            }

            // Load Cart.
            if (null === WC()->cart) {
                WC()->cart = new WC_Cart();
            }
        }
    }


    /**
     * Fired during client add product to cart.
     *
     * @since    1.0.0
     */
    public function add_to_cart()
    {
        self::cart_token();

    }

    /**
     * Fired during client remove product in cart.
     *
     * @since    1.0.0
     */
    public function cart_item_removed()
    {
        global $woocommerce;
        if ($woocommerce->cart->is_empty()) {
            self::destroy_cart_token();
        }
    }

    /**
     * Fired during client user create order.
     *
     * @since    1.0.0
     */
    public function order_update_cart_token($order_id)
    {
        session_start();
        $order = wc_get_order($order_id);
        $order->update_meta_data('_cart_token', self::cart_token());
        $order->save();
    }

    public function destroy_cart_token()
    {
        $host = parse_url($_SERVER['HTTP_HOST'], PHP_URL_HOST);
        $expiry = strtotime('+1 month');
        setcookie('ecomfit_cart_token', '', $expiry, '', $host);
    }

    public function cart_token($hasCart = true)
    {
        global $woocommerce;
        $value = isset($_COOKIE['ecomfit_cart_token']) ? sanitize_text_field($_COOKIE['ecomfit_cart_token']) : '';
        if (($value && $value != 'null') || !$hasCart) {
            return $value;
        }

        $value = sanitize_text_field(md5(json_encode($woocommerce->session->get_session_cookie()) . time()));
        $host = parse_url($_SERVER['HTTP_HOST'], PHP_URL_HOST);
        $expiry = strtotime('+1 month');
        setcookie('ecomfit_cart_token', $value, $expiry, '', $host);

        return $value;
    }
}
