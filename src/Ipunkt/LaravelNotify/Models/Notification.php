<?php
namespace Ipunkt\LaravelNotify\Models;

use DB;
use Eloquent;
use Illuminate\Auth\UserInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Ipunkt\LaravelNotify\Contracts\NotificationTypeContextInterface;
use Ipunkt\LaravelNotify\Contracts\NotificationTypeInterface;

/**
 * Created by PhpStorm.
 * User: bastian
 * Date: 26.03.14
 * Time: 15:52
 *
 *
 * @property integer $id
 * @property array $data
 * @property string $context
 * @property Collection|UserInterface[]|null $user All users which have this notification
 * @property Collection|NotificationActivity[]|null $activities
 * @method static Builder forUser(UserInterface $user)
 * @method static Builder withActivities(array $activities)
 * @method static Builder withContext($context)
 * @method static Builder reverse()
 */
class Notification extends Eloquent
{

    protected $table = 'notifications';

    protected $fillable = ['data','context'];

    protected $notdynamic = ['id', 'context', 'data', 'created_at', 'updated_at', 'deleted_at'];

    /** @var UserInterface $user set scope to default user */
    protected $user = null;

	/**
	 * Publish new Notification
	 *
	 * @param NotificationTypeInterface $notification
	 * @return \Illuminate\Database\Eloquent\Model|static
	 */
	public static function publish(NotificationTypeInterface $notification)
	{
		$attributes = ['data' => [serialize($notification)]];

		if ($notification instanceof NotificationTypeContextInterface) {
			$attributes['context'] = $notification->getContext();
			if ($notification->isSingleton()) {
				return static::updateSingleton($attributes);
			}
		}
		return static::create($attributes);
	}

	/**
	 * Update a singleton notification
	 *
	 * @param array $attributes
	 * @return \Illuminate\Database\Eloquent\Model|null|static
	 */
	protected static function updateSingleton(array $attributes) {
		$notification = static::firstOrCreate(['context' => $attributes['context']]);
		$notification->fill($attributes)->save();
		return $notification;
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function user()
    {
        return $this->hasManyThrough('Illuminate\Auth\UserInterface', 'Ipunkt\LaravelNotify\Models\NotificationActivity', 'notification_id', 'user_id');
    }

    /**
     * Get Default User-Scope
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set Default User-Scope
     * @param UserInterface $user
     * @return $this
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasUser()
    {
        return (!is_null($this->user));
    }

    /**
     * @param Builder $query
     * @param UserInterface $user
     * @return Builder
     */
    public function scopeForUser(Builder $query, UserInterface $user = null)
    {
        if ($user === null && $this->user !== null) {
            $user = $this->user;
        }
        if ($user === null) {
            return $query->has('activities');
        }

        return $query->whereHas('activities', function ($q) use ($user) {
            $q->where('user_id', '=', $user->getAuthIdentifier());
        });
    }

    /**
     * @param Builder $query
     * @param array $activities
     * @return Builder
     */
    public function scopeWithActivities(Builder $query, array $activities = [])
    {
	    if (empty($activities)) {
		    return $query;
	    }
        /**
         * TODO could be optimized for proper handling with Eloquent methods
         */
        return $query->whereHas('activities', function ($q) use ($activities) {
            /**  (SELECT s.activity FROM `notification_activities` s WHERE s.`notification_id` = a.`notification_id` ORDER BY s.`id` DESC LIMIT 1) in ('created', 'read') */
            $q->whereIn(DB::raw('(SELECT s.activity FROM `notification_activities` s WHERE s.`notification_id` = `notification_activities`.`notification_id` ORDER BY s.`id` DESC LIMIT 1)'), $activities);
        });
    }

    /**
     * Add Like / = Condition for Context. use * as wildcard
     *
     * @param Builder $query
     * @param $context
     * @return Builder|static
     */
    public function scopeWithContext(Builder $query, $context)
    {
        $operator = '=';
        if (str_contains($context, '*')) {
            $operator = 'LIKE';
            $context = str_replace('*', '%', $context);
        }

        return $query->where('context', $operator, $context);
    }

	/**
	 * @param Builder $query
	 */
	public function scopeReverse(Builder $query)
	{
		$query->orderBy('id', 'DESC');
	}

    /**
     * @param UserInterface $user
     * @return string|null
     */
    public function currentState(UserInterface $user = null)
    {
        $activity = $this->lastActivity($user);
        if ($activity !== null) {
            return $activity->activity;
        }
        return null;
    }

    /**
     * @param UserInterface $user
     * @return NotificationActivity
     */
    public function lastActivity(UserInterface $user = null)
    {
        if ($user === null && $this->user !== null) {
            $user = $this->user;
        }
        $activity = $this->activities()->orderBy('created_at', 'DESC');
        if ($user !== null) {
            $activity = $activity->where('user_id', '=', $user->getAuthIdentifier());
        }
        return $activity->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activities()
    {
        return $this->hasMany('Ipunkt\LaravelNotify\Models\NotificationActivity');
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    function __get($key)
    {
        $inAttributes = in_array($key, $this->notdynamic);

        if ($inAttributes) {
            return $this->getAttribute($key);
        }

        $data = $this->getDataAttribute();
        if (isset($data[$key])) {
            return $data[$key];
        }
        return null;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    function __set($key, $value)
    {
        $inAttributes = in_array($key, $this->notdynamic);

        if ($inAttributes) {
            $this->setAttribute($key, $value);
            return;
        }
        $data = $this->getDataAttribute();
        $data[$key] = $value;
        $this->setDataAttribute($data);
    }

    /**
     * Return Data
     * @return array
     */
    public function getDataAttribute()
    {
        return unserialize($this->attributes['data']);
    }

    /**
     * Set Data-Array
     * @param array $data
     */
    public function setDataAttribute(array $data)
    {
        $this->attributes['data'] = serialize($data);
    }


}