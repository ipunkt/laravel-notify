<?php
namespace Ipunkt\LaravelNotify\Controllers;

use Illuminate\Auth\UserInterface;
use Ipunkt\LaravelNotify\Contracts\NotificationTypeInterface;
use Ipunkt\LaravelNotify\Models\Notification;
use Ipunkt\LaravelNotify\Models\NotificationActivity;
use Auth;
use Notify;
use Request;
use View;
use Config;
use Response;

class NotifyController extends \Controller
{
    /**
     * @var UserInterface
     */
    protected $user = null;

    /**
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user = null)
    {
        $this->user = $user ? : Auth::user();
    }

    /**
     * Display a listing of the resource.
     * GET /notify/index
     *
     * @return Response
     */
    public function index()
    {
        /** @var NotificationTypeInterface[] $notifications */
        $notifications = Notify::getForUser($this->user, [NotificationActivity::CREATED, NotificationActivity::READ]);
        if (Request::ajax()) {
            return $notifications;
        }

        return View::make(Config::get('laravel-notify::notify.views.index'), compact('notifications'));
    }

	/**
	 * Display a listing of all resources.
	 * GET /notify/all
	 *
	 * @return \Ipunkt\LaravelNotify\Contracts\NotificationTypeInterface[]
	 */
	public function all()
	{
		$notificationModels = Notify::getForUserPaginated($this->user, []);

        /** @var NotificationTypeInterface[] $notifications */
        $notifications = $notificationModels['items'];
        $links = $notificationModels['links'];
        if (Request::ajax()) {
            return $notifications;
        }

		return View::make(Config::get('laravel-notify::notify.views.index'), compact('notifications', 'links'));
	}

	/**
	 * Display the specified resource.
	 * GET /notify/{id}/{action}
	 *
	 * @param \Ipunkt\LaravelNotify\Models\Notification $notification
	 * @param $action
	 * @internal param int $id
	 * @return Response
	 */
    public function action(Notification $notification, $action)
    {
    	if (Request::ajax())
        {
            return [Notify::addActivity($notification, $action, $this->user)];
        }
        return Notify::doAction($notification, $action, $this->user);
    }
}
