# laravel-notify

Provides an ready to use object oriented notification interface for laravel 4, including working example javascript and css for Twitter Bootstrap 3

## Installation

Add to your composer.json following lines

	"repositories": [
		{
			"type": "package",
			"package": {
				"name": "ipunkt/laravel-notify",
				"version": "master",
				"dist": {
					"url": "https://github.com/ipunkt/laravel-notify/archive/master.zip",
					"type": "zip"
				},
				"autoload": {
					"psr-0": {
						"Ipunkt\\LaravelNotify\\": "src/"
					}
				}
			}
		}
	]

	"require": {
		"ipunkt/laravel-notify": "*"
	}

Add `'Ipunkt\LaravelNotify\LaravelNotifyServiceProvider',` to `providers` in `app/config/app.php`.
The Service provider also registers an alias `Notify` to your application.

On command line publish the database migrations:

	$> php artisan migrate:publish ipunkt/laravel-notify

Please be sure the name of the name of the created migration file in your `app/database/migrations` folder has a name
 like `YYYY_mm_dd_hhiiss_notify_create_table_notifications.php`.

Then run `php artisan migrate` to get the necessary tables in your database.

## Usage

Add somewhere in your bootstrap the shipped view composer to auto-handle the given view script:

	View::composer('laravel-notify::notification', 'Ipunkt\LaravelNotify\Composers\ViewComposer');

The view composer injects a variable `$notifications` into the view. It is a collection of all notifications that
	were created or read.

Now you can use this template to display all notifications or you can use it with Bootstrap 3 in your navbar like
	this.

	$> php artisan asset:publish ipunkt/laravel-notify

And then include the following files in your layout: `/packages/ipunkt/laravel-notify/css/notify.css` and
	`/packages/ipunkt/laravel-notify/js/notify.js`. The latter needs jquery to be existent.

Then go to your layout and create an `<li id="notify"><a href="{{{ URL::route('notify.index') }}}"><i class="glyphicon glyphicon-warning-sign"></i></li>`
	in your navbar navigation list.

Whenever there are notifications to be listed the ViewComposer initially fills the `$notifications` variable in the
	shipped `notification` view and the javascript makes an ajax call to get these and displays as menu. Without
	javascript the link opens a page where all notifications will be listed.


### Important Notice

Do not forget to set filters for the routes build by the package.
For example:

	Route::when('notify/*', 'auth');

This example adds the `auth` filter to all package build routes.
