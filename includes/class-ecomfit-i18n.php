<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://ecomfit.com
 * @since      1.0.0
 *
 * @package    Ecomfit_Notification
 * @subpackage Ecomfit_Notification/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Ecomfit_Notification
 * @subpackage Ecomfit_Notification/includes
 * @author     ecomfit
 */
if (!class_exists('Ecomfit_i18n')) {
    class Ecomfit_i18n
    {


        /**
         * Load the plugin text domain for translation.
         *
         * @since    1.0.0
         */
        public function load_plugin_textdomain()
        {

            load_plugin_textdomain(
                'ecomfit',
                false,
                dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
            );

        }
    }
}
