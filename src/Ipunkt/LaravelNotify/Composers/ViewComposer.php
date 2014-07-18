<?php
/**
 * laravel-notify
 *
 * @author rok
 * @since 18.07.14
 */

namespace Ipunkt\LaravelNotify\Composers;


use Ipunkt\LaravelNotify\Models\NotificationActivity;
use Ipunkt\LaravelNotify\NotificationManager;

class ViewComposer
{
	/**
	 * authentication manager
	 *
	 * @var \Illuminate\Auth\AuthManager
	 */
	private $authManager;

	/**
	 * notification manager
	 *
	 * @var \Ipunkt\LaravelNotify\NotificationManager
	 */
	private $notificationManager;

	/**
	 * inject the auth manager and notification manager
	 *
	 * @param \Illuminate\Auth\AuthManager $authManager
	 * @param NotificationManager $notificationManager
	 */
	public function __construct(\Illuminate\Auth\AuthManager $authManager, NotificationManager $notificationManager)
	{
		$this->authManager = $authManager;
		$this->notificationManager = $notificationManager;
	}

	/**
	 * composing the view
	 *
	 * @param \Illuminate\View\View $view
	 */
	public function compose(\Illuminate\View\View $view)
	{
		$notifications = [];

		if (null !== ($user = $this->authManager->user())) {
			$notifications = $this->notificationManager->getForUser($user, [
				NotificationActivity::CREATED,
				NotificationActivity::READ
			]);
		}

		$view->with('notifications', $notifications);
	}
}