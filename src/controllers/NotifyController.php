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
     * GET /notify
     *
     * @return Response
     */
    public function index()
    {
        /** @var NotificationTypeInterface $notifications */
        $notifications = Notify::getForUser($this->user, [NotificationActivity::CREATED, NotificationActivity::READ]);
        if (Request::ajax()) {
            return $notifications;
        }

        return View::make(Config::get('notification.views.index'), compact('notifications'));
    }

	/**
	 * Display the specified resource.
	 * GET /notify/{id}
	 *
	 * @param \Ipunkt\LaravelNotify\Models\Notification $notification
	 * @param $action
	 * @internal param int $id
	 * @return Response
	 */
    public function action(Notification $notification, $action)
    {
        return Notify::doAction($notification, $action, $this->user);
    }
}