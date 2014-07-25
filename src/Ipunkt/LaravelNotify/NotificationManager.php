<?php
/**
 * Created by PhpStorm.
 * User: bastian
 * Date: 27.03.14
 * Time: 10:04
 */

namespace Ipunkt\LaravelNotify;


use Auth;
use Config;
use Ipunkt\LaravelNotify\Exceptions\ClassNotFoundException;
use Ipunkt\LaravelNotify\Types\MessageNotification;
use Redirect;
use Illuminate\Auth\UserInterface;
use Ipunkt\LaravelNotify\Contracts\NotificationTypeInterface;
use Ipunkt\LaravelNotify\Models\Notification;
use Ipunkt\LaravelNotify\Models\NotificationActivity;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NotificationManager
 * @package Ipunkt\LaravelNotify
 */
class NotificationManager
{
	/** ------ Publish Notifications ---- **/

	/**
	 * Create a new Notification for the user
	 *
	 * @param UserInterface|int $user
	 * @param string|Notification|NotificationTypeInterface $notification
	 */
    public function user($user, $notification)
    {
        $this->users([$user], $notification);
    }

	/**
	 * Create a new Notification for multiple users
	 * @param array $users of UserInterface
	 * @param string|Notification|NotificationTypeInterface $notification
	 */
    public function users($users, $notification)
    {
        /** @var Notification $notification */
        $notification = $this->createNotification($notification);

        foreach ($users as $user) {
            $this->addActivity($notification, NotificationActivity::CREATED, $user);
        }
    }

	/**
	 * Create a payload string from the given job and data.
	 *
	 * @param  NotificationTypeInterface|string|Notification $notification
	 * @return Notification
	 */
	protected function createNotification($notification)
	{
		if ($notification instanceof NotificationTypeInterface) {
			return Notification::publish($notification);
		}

		if ($notification instanceof Notification) {
			return $notification;
		}

		return $this->createNotification(new MessageNotification($notification));
	}


	/** ------ Query Notifications ---- **/

	/** @var string $context */
	private $context = null;

	/** @var array $activities */
	private $activities = [];

	/**
	 * Get all Notifications for the current authenticated user
	 * @param \Illuminate\Auth\UserInterface $user
	 * @return NotificationTypeInterface[]
	 */
    public function get(UserInterface $user)
    {
	    $query = $this->buildQuery($user);

	    $notificationModels = $query->get();
	    return $this->instantiateNotifications($notificationModels, $user);
    }

	/**
	 * returns paginated list of Notifications for the given user
	 *
	 * @param UserInterface $user
	 * @param int $itemsPerPage
	 * @return array
	 */
	public function paginate(UserInterface $user, $itemsPerPage = 15)
	{
		$query = $this->buildQuery($user);

		$paginatorModels = $query->paginate($itemsPerPage);

		return [
			'items' => $this->instantiateNotifications($paginatorModels, $user),
			'links' => $paginatorModels->links(),
		];
	}

	/**
	 * builds a query by given flags
	 *
	 * @param UserInterface $user
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	private function buildQuery(UserInterface $user)
	{
		$query = Notification::forUser($user);

		if (!empty($this->activities))
		{
			$query->withActivities($this->activities);
		}

		if (!empty($this->context))
		{
			$query->withContext($this->context);
		}

		$query->reverse();

		return $query;
	}

	/**
	 * set activities
	 *
	 * @param array $activities
	 * @return $this
	 */
	public function withActivities(array $activities)
	{
		$this->activities = $activities;
		return $this;
	}

	/**
	 * sets context
	 *
	 * @param string $context * for wildcard
	 * @return $this
	 */
	public function inContext($context)
	{
		$this->context = $context;
		return $this;
	}

	/** ------ Activate Actions ---- **/

    /**
     * @param Notification $notification
     * @param $action
     * @param UserInterface $user
     * @return Response
     */
    public function doAction(Notification $notification, $action, UserInterface $user = null)
    {
        $class = $this->instantiateNotification($notification, $user);
        if (method_exists($class, $action)) {
            if (Config::get('laravel-notify::notify.auto_add_activities_for_actions')) {
                $this->addActivity($notification, $action);
            }
            return $class->$action();
        }
        return Redirect::back();
    }

    /**
     * Add a new Activity to the Notfication for the user
     * @param Notification $notification
     * @param string $activity
     * @param UserInterface $user
     * @return bool
     */
    public function addActivity(Notification $notification, $activity, UserInterface $user = null)
    {
        if ($user === null && $notification->hasUser()) {
            $user = $notification->getUser();
        }

        if ($user === null && Auth::check()) {
            $user = Auth::user();
        }

        if ($user === null) {
            return false;
        }

        /**
         * Aktuellen Status nicht erneut setzen
         */
        if ($notification->currentState($user) === $activity) {
            return false;
        }

        /** @var NotificationActivity $user_activity */
        $user_activity = new NotificationActivity(['activity' => $activity, 'user_id' => $user->getAuthIdentifier()]);
        return ($notification->activities()->save($user_activity) !== false);
    }

	/**
	 * instantiates notification types
	 *
	 * @param Notification[] $notificationModels
	 * @param UserInterface $user
	 * @return array
	 */
	private function instantiateNotifications($notificationModels, UserInterface $user)
	{
		/**
		 * Create NotificationTypes
		 */
		$notifies = [];
		/** @var Notification $notificationModel */
		foreach ($notificationModels as $notificationModel) {
			$notifies[] = $this->instantiateNotification($notificationModel, $user);
		}
		return $notifies;
	}

	/**
	 * @param Notification $notification
	 * @param null|UserInterface $user
	 * @throws Exceptions\ClassNotFoundException
	 * @return NotificationTypeInterface
	 */
    protected function instantiateNotification(Notification $notification, UserInterface $user)
    {
        $notification->setUser($user);

        $notifytype = unserialize($notification->data[0]);
	    if($notifytype instanceof NotificationTypeInterface) {
		    return $notifytype->setModel($notification);
	    }

	    throw new ClassNotFoundException;
    }
}