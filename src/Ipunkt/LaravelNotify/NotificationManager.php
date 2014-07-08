<?php
/**
 * Created by PhpStorm.
 * User: bastian
 * Date: 27.03.14
 * Time: 10:04
 */

namespace Ipunkt\LaravelNotify;


use Auth;
use Closure;
use Config;
use Illuminate\Auth\UserInterface;
use Illuminate\Support\SerializableClosure;
use Ipunkt\Notification\Contracts\NotificationTypeInterface;
use Notification;
use NotificationActivity;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NotificationManager
 * @package Ipunkt\Notification
 */
class NotificationManager
{
    /** @var null Collection */
    protected $notifications = null;

    /**
     * Create a new Notification for the user
     *
     * @param UserInterface|int $user
     * @param string|Notification|callable $job
     * @param array $data
     */
    public function user($user, $job, array $data = [])
    {
        $this->users([$user], $job, $data);
    }

    /**
     * Create a new Notification for multiple users
     * @param array $users of UserInterface
     * @param string|Notification|callable $job
     * @param array $data
     */
    public function users($users, $job, array $data = [])
    {
        /** @var Notification $notification */
        $notification = $this->createNotification($job, $data);

        foreach ($users as $user) {
            $this->addActivity($notification, NotificationActivity::CREATED, $user);
        }
    }

    /**
     * Get all Notifications for the current authenticated user
     * @return array of NotificationTypeInterface
     */
    public function get()
    {
        return $this->getForUser();
    }

    /**
     * get all Notifications for the given user
     * @param UserInterface $user
     * @param array $activities
     * @return array of NotificationTypeInterface
     */
    public function getForUser(UserInterface $user = null, $activities = [])
    {
        if ($user === null && Auth::check()) {
            $user = Auth::user();
        }

        if ($user === null) {
            return [];
        }

        /** @var Notification $notificationModels */
        $notificationModels = Notification::forUser($user, $activities)->get();

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
     * @param $action
     * @param UserInterface $user
     * @return Response
     */
    public function doAction(Notification $notification, $action, UserInterface $user = null)
    {
        $class = $this->instantiateNotification($notification, $user);
        if (method_exists($class, $action)) {
            if (Config::get('notification.auto_add_activities_for_actions')) {
                $this->addActivity($notification, $action);
            }
            return $class->$action();
        }
        return \Redirect::back();
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
     * @param Notification $notification
     * @param null|UserInterface $user
     * @return null|NotificationTypeInterface
     */
    protected function instantiateNotification(Notification $notification, UserInterface $user = null)
    {
        if ($user === null && Auth::check()) {
            $user = Auth::user();
        }

        /**
         * Userscope im Model setzen
         */
        $notification->setUser($user);

        if ($notification->job === 'serialize') {
            $notifytype = unserialize($notification->data[0]);
            return $notifytype->setModel($notification);
        }

        if (class_exists($notification->job)) {
            $jobclass = $notification->job;
            $notifytype = new $jobclass();
            return $notifytype->setModel($notification)->setData($notification->data);
        }

        /**
         * TODO throw NotificationClassNotFoundException
         */
        return null;
    }

    /**
     * Create a payload string for the given Closure job.
     *
     * @param  \Closure $job
     * @param  array $data
     * @return string
     */
    protected function createClosurePayload($job, array $data = [])
    {
        $data['closure'] = serialize(new SerializableClosure($job));

        return array('job' => 'Ipunkt\Notification\Types\ClosureNotification', 'data' => $data);
    }


    /**
     * Create a payload string from the given job and data.
     *
     * @param  string $job
     * @param  array $data
     * @return Notification
     */
    protected function createNotification($job, array $data = [])
    {
        if ($job instanceof Closure) {
            $payload = $this->createClosurePayload($job, $data);
            return Notification::create($payload);
        } elseif ($job instanceof NotificationTypeInterface) {
            return Notification::create(['job' => 'serialize', 'data' => [serialize($job)]]);
        } elseif (class_exists($job)) {
            return Notification::create(['job' => $job, 'data' => $data]);
        } elseif ($job instanceof Notification) {
            return $job;
        } else {
            $data['message'] = $job;
            return $this->createNotification('Ipunkt\Notification\Types\MessageNotification', $data);
        }
    }
}