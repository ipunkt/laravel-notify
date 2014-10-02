<?php namespace Ipunkt\LaravelNotify;

use Illuminate\Foundation\AliasLoader;
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

		$this->registerRoutes();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerNotificationManager();

		$this->registerNotifyFacade();
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
	 * Register the Notify:: facade
	 */
	private function registerNotifyFacade()
	{
		$this->app->booting(function()
		{
			$loader = AliasLoader::getInstance();
			$loader->alias('Notify', 'Ipunkt\LaravelNotify\NotificationFacade');
		});
	}

	/**
	 * register all configured routes
	 */
	private function registerRoutes()
	{
		/** @var \Illuminate\Config\Repository $config */
		$config = $this->app['config'];

		/** @var \Illuminate\Routing\Router $router */
		$router = $this->app['router'];

		//  setting the model resolving
		$router->model('notification', 'Ipunkt\LaravelNotify\Models\Notification');

		//  setting the route to index
		$router->get($config->get('laravel-notify::notify.routes.index'), [
			'as' => 'notify.index',
			'uses' => $config->get('laravel-notify::notify.controller').'@index'
		]);

		//  setting the route to all
		$router->get($config->get('laravel-notify::notify.routes.all'), [
			'as' => 'notify.all',
			'uses' => $config->get('laravel-notify::notify.controller').'@all'
		]);

		//  setting the route to do an action
		$router->get($config->get('laravel-notify::notify.routes.action'), [
			'as' => 'notify.action',
			'uses' => $config->get('laravel-notify::notify.controller').'@action'
		]);
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