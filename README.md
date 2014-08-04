# laravel-notify
[![Latest Stable Version](https://poser.pugx.org/ipunkt/laravel-notify/v/stable.svg)](https://packagist.org/packages/ipunkt/laravel-notify) [![Latest Unstable Version](https://poser.pugx.org/ipunkt/laravel-notify/v/unstable.svg)](https://packagist.org/packages/ipunkt/laravel-notify) [![License](https://poser.pugx.org/ipunkt/laravel-notify/license.svg)](https://packagist.org/packages/ipunkt/laravel-notify) [![Total Downloads](https://poser.pugx.org/ipunkt/laravel-notify/downloads.svg)](https://packagist.org/packages/ipunkt/laravel-notify)

Provides an ready to use object oriented notification interface for laravel 4, including working example javascript and css for Twitter Bootstrap 3

## Installation

Add to your composer.json following lines

	"require": {
		"ipunkt/laravel-notify": "1.*"
	}

Add `'Ipunkt\LaravelNotify\LaravelNotifyServiceProvider',` to `providers` in `app/config/app.php`.
The Service provider also registers an alias `Notify` to your application.

On command line publish the database migrations:

	$> php artisan migrate --package="ipunkt/laravel-notify"

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

## Use-Cases
### 1. Message Notification

If you want to publish a simple message notification just do the following to notify users:

	$user = Auth::user();
	Notify::user($user, new \Ipunkt\LaravelNotify\Types\MessageNotification('Welcome on board!'));
	// or
	// Notify::users([$user, ...], new \Ipunkt\LaravelNotify\Types\MessageNotification('Welcome on board!'));

This sends a simple shipped message notification to the current logged in user.

### 2. Specific Notification

For sending application specific notifications simply create your own NotificationType.

	class MyNotification extends Ipunkt\LaravelNotify\Types\AbstractNotification {
	}

Then you can modify the read and done action for your needs. And you can add your own custom actions. You do not have to
 use only these 3 actions: `created`, `read`, `done`. You can build your own custom workflow. Try it out!

The AbstractNotification sends a user to the done action if he reads the notification. Override this behaviour to have
 your own workflow. Ending with `done` might be a good one, starting with `created` is fix by the current implementation.
