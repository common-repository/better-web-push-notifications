<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://ecomfit.com
 * @since      1.0.0
 *
 * @package    Ecomfit_Notification
 * @subpackage Ecomfit_Notification/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ecomfit_Notification
 * @subpackage Ecomfit_Notification/includes
 * @author     ecomfit
 */
class Ecomfit_Notification
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Ecomfit_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $ecomfit_notification The string used to uniquely identify this plugin.
     */
    protected $ecomfit_notification;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * The website id in ecomfit.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $web_id The current website id of website.
     */
    private $web_id;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('ECOMFIT_NOTIFICATION_VERSION')) {
            $this->version = ECOMFIT_NOTIFICATION_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->ecomfit_notification = 'ecomfit-notification';

        $this->web_id = get_option('_ecomfit_web_id');

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Ecomfit_Loader. Orchestrates the hooks of the plugin.
     * - Ecomfit_i18n. Defines internationalization functionality.
     * - Ecomfit_Admin. Defines all hooks for the admin area.
     * - Ecomfit_Public. Defines all hooks for the public side of the site.
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
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ecomfit-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ecomfit-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ecomfit-notification-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-ecomfit-notification-public.php';

        $this->loader = new Ecomfit_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Ecomfit_Notification_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Ecomfit_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Ecomfit_Notification_Admin($this->get_ecomfit_notification(), $this->get_version(), $this->get_web_id());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        $this->loader->add_action('admin_menu', $plugin_admin, 'app_menu');


        $isLogged = get_option('_ecomfit_notification_login');
        if ($this->get_web_id() && $isLogged && $this->woocommerce_active()) {
            $this->loader->add_action('save_post', $plugin_admin, 'save_post', true, 10, 4);
            $this->loader->add_action('delete_post', $plugin_admin, 'delete_product_variation', true, 10, 6);
            $this->loader->add_action('woocommerce_save_product_variation', $plugin_admin, 'save_product_variation', true, 10, 5);
        }
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Ecomfit_Notification_Public($this->get_ecomfit_notification(), $this->get_version(), $this->get_web_id());

        $isLogged = get_option('_ecomfit_notification_login');
        if ($this->get_web_id() && $isLogged && $this->woocommerce_active()) {

            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

            $this->loader->add_action('wp_loaded', $plugin_public, 'check_prerequisites', true, 5);
            // add sdk script
            $this->loader->add_action('wp_footer', $plugin_public, 'ecf_sdk_script', true, 20);
            // add to cart
            $this->loader->add_action('woocommerce_add_to_cart', $plugin_public, 'add_to_cart', true);
            // removed item cart
            $this->loader->add_action('woocommerce_cart_item_removed', $plugin_public, 'cart_item_removed', true);
            // create order
            $this->loader->add_action('woocommerce_checkout_update_order_meta', $plugin_public, 'order_update_cart_token', true, 10);

            // notification
            $this->loader->add_action('init', $plugin_public, 'ecf_sw_rewrites', true);
            $this->loader->add_action('query_vars', $plugin_public, 'ecf_sw_query_filter', true);
            $this->loader->add_action('template_redirect', $plugin_public, 'ecf_sw_output', true);
        }

        $this->loader->add_action('rest_api_init', $plugin_public, 'register_router_api', true);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_ecomfit_notification()
    {
        return $this->ecomfit_notification;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Ecomfit_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     * Retrieve the website id of the ecomfit.
     *
     * @return    string    The website id of the ecomfit.
     * @since     1.0.0
     */
    public function get_web_id()
    {
        return $this->web_id;
    }

    /**
     * Retrieve plugin woocommerce active in shop.
     *
     * @return    boolean
     * @since     1.0.0
     */
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
