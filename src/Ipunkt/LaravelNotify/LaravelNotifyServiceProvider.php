<?php namespace Ipunkt\LaravelNotify;

use Illuminate\Support\ServiceProvider;

class LaravelNotifyServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('ipunkt/laravel-notify');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerNotificationManager();
	}

    /**
     * Register the Notify-Facade
     */
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
        return ['notify'];
    }

}
