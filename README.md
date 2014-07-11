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

On command line publish the database migrations:

	$> php artisan migrate:publish ipunkt/laravel-notify

Please be sure the name of the name of the created migration file in your `app/database/migrations` folder has a name
 like `YYYY_mm_dd_hhiiss_notify_create_table_notifications.php`.

Then run `php artisan migrate` to get the necessary tables in your database.

