<?php

/**
 * Fired during plugin activation
 *
 * @link       https://ecomfit.com
 * @since      1.0.0
 *
 * @package    Ecomfit_Notification
 * @subpackage Ecomfit_Notification/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ecomfit_Notification
 * @subpackage Ecomfit_Notification/includes
 * @author     ecomfit
 */
class Ecomfit_Notification_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        add_option('_ecomfit_notification_login', 0, true);
    }

}
