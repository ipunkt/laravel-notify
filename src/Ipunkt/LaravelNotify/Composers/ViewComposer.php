<?php
/**
 * laravel-notify
 *
 * @author rok
 * @since 18.07.14
 */

namespace Ipunkt\LaravelNotify\Composers;


use Ipunkt\LaravelNotify\Models\NotificationActivity;

class ViewComposer {

	/**
	 * authentication
	 *
	 * @var \Illuminate\Auth\Guard
	 */
	private $auth;

	/**
	 * inject the auth guard
	 *
	 * @param \Illuminate\Auth\Guard $auth
	 */
	public function __construct(\Illuminate\Auth\Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * composing the view
	 *
	 * @param \Illuminate\View\View $view
	 */
	public function compose($view) {

		$notifications = [];

		if (null !== $this->auth->user())
		{
			$notifications = Notify::getForUser($this->auth->user(), [NotificationActivity::CREATED, NotificationActivity::READ]);
		}


		$view->with('notifications', $notifications);

	}
}