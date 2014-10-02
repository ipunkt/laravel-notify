<?php

return [
	/**
	 * is the package enabled
	 */
	'enabled' => true,

    /**
     * Controller used for routes
     * @see Ipunkt\LaravelNotify\Contracts\NotifyControllerInterface
     */
    'controller' => 'Ipunkt\LaravelNotify\Controllers\NotifyController',

	/**
	 * route definitions
	 */
	'routes' => [
        /**
         * Route to index-Page
         * creates a route named "notify.index"
         */
        'index' => 'notify/index',

		/**
		 * Route to list all notifications
		 * creates a route named "notify.all"
		 */
		'all' => 'notify/all',

        /**
         * Route to an action
         * creates a route named "notify.action"
         * Use {notification} for Notification-ID and {action} for the name of the action
         */
        'action' => 'notify/{notification}/{action}'
    ],

	/**
	 * view definitions
	 */
	'views' => [
        'index' => 'laravel-notify::notifications',
    ],

    /**
	 * A users activity for a notification (read, done, ...) will be set automatically and do not have to be set
	 * in the action
     */
    'auto_add_activities_for_actions' => true,
];