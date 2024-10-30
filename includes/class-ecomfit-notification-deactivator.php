<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://ecomfit.com
 * @since      1.0.0
 *
 * @package    Ecomfit_Notification
 * @subpackage Ecomfit_Notification/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Ecomfit_Notification
 * @subpackage Ecomfit_Notification/includes
 * @author     ecomfit
 */
class Ecomfit_Notification_Deactivator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate()
    {
        $webId = get_option('_ecomfit_web_id');
        $token = get_option('_ecomfit_auth_token');
        if ($webId && $token) {
            require_once 'class-ecomfit-api.php';
            $request = new Ecomfit_Api(ECOMFIT_NOTIFICATION_API_URL, $token);
            $request->post('/v3/wordpress/uninstall/' . ECOMFIT_NOTIFICATION_APP_TYPE, array("webId" => $webId));
        }
        delete_option('_ecomfit_notification_login');
    }

}
