<?php
namespace Ipunkt\LaravelNotify\Controllers;

use Illuminate\Auth\UserInterface;
use Ipunkt\LaravelNotify\Contracts\NotificationTypeInterface;
use Ipunkt\LaravelNotify\Contracts\NotifyControllerInterface;
use Ipunkt\LaravelNotify\Models\Notification;
use Ipunkt\LaravelNotify\Models\NotificationActivity;
use Auth;
use Notify;
use Request;
use View;
use Config;
use Response;

class NotifyController extends \Controller implements NotifyControllerInterface
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
        $notifications = Notify::withActivities([NotificationActivity::CREATED, NotificationActivity::READ])->get($this->user);
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
		$notificationModels = Notify::paginate($this->user);

        /** @var NotificationTypeInterface[] $notifications */
        $notifications = $notificationModels['items'];
        $links = $notificationModels['links'];
        if (Request::ajax()) {
            return $notifications;
        }

		return View::make(Config::get('laravel-notify::notify.views.all'), compact('notifications', 'links'));
	}

	/**
	 * Display the specified resource.
	 * GET /notify/{notification}/{action}
	 *
	 * @param \Ipunkt\LaravelNotify\Models\Notification $notification
	 * @param string $action
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
