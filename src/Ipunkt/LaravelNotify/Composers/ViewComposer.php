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
	 * @var \Illuminate\Auth\Manager
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
	 * @param \Illuminate\Auth\Manager $authManager
	 * @param NotificationManager $notificationManager
	 */
	public function __construct(\Illuminate\Auth\Manager $authManager, NotificationManager $notificationManager)
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

		if (null !== ($user = $this->auth->user())) {
			$notifications = $this->notificationManager->getForUser($this->auth->user(), [
				NotificationActivity::CREATED,
				NotificationActivity::READ
			]);
		}

		$view->with('notifications', $notifications);
	}
}