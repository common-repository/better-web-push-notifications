<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://ecomfit.com
 * @since      1.0.0
 *
 * @package    Ecomfit_Notification
 * @subpackage Ecomfit_Notification/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Ecomfit_Notification
 * @subpackage Ecomfit_Notification/includes
 * @author     ecomfit
 */

if (!class_exists('Ecomfit_Loader')) {
    class Ecomfit_Loader
    {

        /**
         * The array of actions registered with WordPress.
         *
         * @since    1.0.0
         * @access   protected
         * @var      array $actions The actions registered with WordPress to fire when the plugin loads.
         */
        protected $actions;

        /**
         * The array of filters registered with WordPress.
         *
         * @since    1.0.0
         * @access   protected
         * @var      array $filters The filters registered with WordPress to fire when the plugin loads.
         */
        protected $filters;

        /**
         * Initialize the collections used to maintain the actions and filters.
         *
         * @since    1.0.0
         */
        public function __construct()
        {
            $this->actions = array();
            $this->filters = array();
        }

        /**
         * Add a new action to the collection to be registered with WordPress.
         *
         * @param string $hook The name of the WordPress action that is being registered.
         * @param object $component A reference to the instance of the object on which the action is defined.
         * @param string $callback The name of the function definition on the $component.
         * @param boolean $unique Check hook and callback unique
         * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
         * @param int $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
         * @since    1.0.0
         */
        public function add_action($hook, $component, $callback, $unique = false, $priority = 10, $accepted_args = 1)
        {
            $this->actions = $this->add($this->actions, $hook, $component, $callback, $unique, $priority, $accepted_args);
        }

        /**
         * Add a new filter to the collection to be registered with WordPress.
         *
         * @param string $hook The name of the WordPress filter that is being registered.
         * @param object $component A reference to the instance of the object on which the filter is defined.
         * @param string $callback The name of the function definition on the $component.
         * @param boolean $unique Check hook and callback unique
         * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
         * @param int $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1
         * @since    1.0.0
         */
        public function add_filter($hook, $component, $callback, $unique = false, $priority = 10, $accepted_args = 1)
        {
            $this->filters = $this->add($this->filters, $hook, $component, $callback, $unique, $priority, $accepted_args);
        }

        /**
         * A utility function that is used to register the actions and hooks into a single
         * collection.
         *
         * @param array $hooks The collection of hooks that is being registered (that is, actions or filters).
         * @param string $hook The name of the WordPress filter that is being registered.
         * @param object $component A reference to the instance of the object on which the filter is defined.
         * @param string $callback The name of the function definition on the $component.
         * @param int $priority The priority at which the function should be fired.
         * @param int $accepted_args The number of arguments that should be passed to the $callback.
         * @param boolean $unique Check hook and callback unique
         * @return   array                                  The collection of actions and filters registered with WordPress.
         * @since    1.0.0
         * @access   private
         */
        private function add($hooks, $hook, $component, $callback, $unique, $priority, $accepted_args)
        {

            $hooks[] = array(
                'hook' => $hook,
                'component' => $component,
                'callback' => $callback,
                'priority' => $priority,
                'accepted_args' => $accepted_args,
                'unique' => $unique
            );

            return $hooks;

        }

        /**
         * Register the filters and actions with WordPress.
         *
         * @since    1.0.0
         */
        public function run()
        {
            $unique_filters = isset($GLOBALS['_ecomfit_filters']) ? $GLOBALS['_ecomfit_filters'] : array();
            foreach ($this->filters as $hook) {
                $key = $hook['hook'] . '_' . $hook['callback'];
                if (!(isset($hook['unique']) && $hook['unique']) || ($hook['unique'] && !isset($unique_filters[$key]))) {
                    add_filter($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
                    if ($hook['unique'] && !isset($unique_filters[$key])) {
                        $unique_filters[$key] = $hook;
                    }
                }
            }
            $GLOBALS['_ecomfit_filters'] = $unique_filters;

            $unique_actions = isset($GLOBALS['_ecomfit_actions']) ? $GLOBALS['_ecomfit_actions'] : array();
            foreach ($this->actions as $hook) {
                $key = $hook['hook'] . '_' . $hook['callback'];
                if (!(isset($hook['unique']) && $hook['unique']) || ($hook['unique'] && !isset($unique_actions[$key]))) {
                    add_action($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
                    if ($hook['unique'] && !isset($unique_actions[$key])) {
                        $unique_actions[$key] = $hook;
                    }
                }
            }
            $GLOBALS['_ecomfit_actions'] = $unique_actions;
        }

    }
}
