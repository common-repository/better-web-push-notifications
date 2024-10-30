<?php

/**
 * Plugin Name:       Better Web Push Notifications
 * Plugin URI:        https://ecomfit.com/
 * Description:       Increase your revenue by reducing abandoned carts and convert guests to customers with the powerful popup tool.A simple and effective way to recover abandoned carts and
 * bring back customer to your sites.
 * Version:           1.0.0
 * Author:            ecomfit
 * Author URI:        https://ecomfit.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('ECOMFIT_NOTIFICATION_VERSION', '1.0.0');
/**
 * Currently url app.
 */
define('ECOMFIT_NOTIFICATION_APP_URL', 'https://apps.ecomfit.com');

/**
 * Currently url api.
 */
define('ECOMFIT_NOTIFICATION_API_URL', 'https://apps.ecomfit.com/api');

/**
 * Currently url sdk.
 */
define('ECOMFIT_NOTIFICATION_SDK_URL', 'https://apps.ecomfit.com/cdn/ecf.min.js');

/**
 * Currently version sdk.
 */
define('ECOMFIT_NOTIFICATION_SDK_VERSION', '190212');

/**
 * Currently app type.
 */
define('ECOMFIT_NOTIFICATION_APP_TYPE', 'notification');


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ecomfit-notification-activator.php
 */
function activate_ecomfit_notification()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-ecomfit-notification-activator.php';
    Ecomfit_Notification_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ecomfit-notification-deactivator.php
 */
function deactivate_ecomfit_notification()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-ecomfit-notification-deactivator.php';
    Ecomfit_Notification_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_ecomfit_notification');
register_deactivation_hook(__FILE__, 'deactivate_ecomfit_notification');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-ecomfit-notification.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ecomfit_notification()
{

    $plugin = new Ecomfit_Notification();
    $plugin->run();

}

run_ecomfit_notification();
