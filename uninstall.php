<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://ecomfit.com
 * @since      1.0.0
 *
 * @package    Ecomfit_Notification
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$webId = get_option('_ecomfit_web_id');
$token = get_option('_ecomfit_auth_token');
if ($webId && $token) {
    require_once plugin_dir_path(__FILE__) . '/includes/class-ecomfit-api.php';
    $request = new Ecomfit_Api(ECOMFIT_NOTIFICATION_API_URL, $token);
    $request->post('/v3/wordpress/uninstall/' . ECOMFIT_NOTIFICATION_APP_TYPE, array("webId" => $webId));
}
delete_option('_ecomfit_notification_login');
