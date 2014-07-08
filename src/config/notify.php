<?php

return [
    'enabled' => true,
    'routes' => [
        /**
         * Route to index-Page
         * creates a route named "notify.index"
         */
        'index' => 'notify/index',
        /**
         * Route to an action
         * creates a route named "notify.action"
         * Use {notification} for Notification-ID and {action} for the name of the action
         */
        'action' => 'notify/{notification}/{action}'
    ],
    'views' => [
        'index' => 'users.notifications',
    ],

    /**
     * Aktivitäten des Nutzers zu einer Notification (read, done, ...) werden automatisch beim Auslösen gesetzt
     * und müssen nicht innerhalb der Action erstellt werden
     */
    'auto_add_activities_for_actions' => true,
];