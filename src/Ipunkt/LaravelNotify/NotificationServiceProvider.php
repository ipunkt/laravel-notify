<?php
/**
 * Created by PhpStorm.
 * User: bastian
 * Date: 25.03.14
 * Time: 17:28
 */

namespace Ipunkt\Notification;


use Config;
use Event;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        AliasLoader::getInstance()->alias(
            'Notify',
            'Ipunkt\Notification\NotificationFacade'
        );
    }

    /**
     * Register Listener
     */
    public function register()
    {
        $this->registerEventListeners();
        $this->registerNotificationManager();
    }

    /**
     * Register Eventlisteners for custom Person- and "Event"-Events
     */
    protected function registerEventListeners()
    {
        if (Config::get('notification.enabled') === true) {
            Event::listen('person.*', 'Ipunkt\Notification\PersonNotificationListener');
            Event::listen('event.*', 'Ipunkt\Notification\EventNotificationListener');
        }
    }

    protected function registerNotificationManager()
    {
        $this->app->bind('notify', function () {
            return new NotificationManager();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Notify'];
    }
}