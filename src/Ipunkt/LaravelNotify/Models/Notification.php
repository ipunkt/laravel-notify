<?php
namespace Ipunkt\LaravelNotify\Models;

use DB;
use Eloquent;
use Illuminate\Auth\UserInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Created by PhpStorm.
 * User: bastian
 * Date: 26.03.14
 * Time: 15:52
 *
 *
 * @property integer $id
 * @property array $data
 * @property string $job
 * @method static Builder forUser() forUser(UserInterface $user) forUser(UserInterface $user, array $activities)
 */
class Notification extends Eloquent
{

    protected $table = 'notifications';

    protected $fillable = ['job', 'data'];

    protected $notdynamic = ['id', 'job', 'data', 'created_at', 'updated_at', 'deleted_at'];

    /** @var UserInterface $user set scope to default user */
    protected $user = null;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activities()
    {
        return $this->hasMany('NotificationActivity');
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
     * Get Default User-Scope
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
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
     * @param array $activities
     * @return Builder|static
     */
    public function scopeForUser(Builder $query, UserInterface $user = null, array $activities = [])
    {
        if ($user === null && $this->user !== null) {
            $user = $this->user;
        }
        if ($user === null) {
            return $query->has('activities');
        }

        return $query->whereHas('activities', function ($q) use ($user, $activities) {
            $q->where('user_id', '=', $user->getAuthIdentifier());
            if (!empty($activities)) {
                /**  (SELECT s.activity FROM `notification_activities` s WHERE s.`notification_id` = a.`notification_id` ORDER BY s.`id` DESC LIMIT 1) in ('created', 'read') */
                $q->whereIn(DB::raw('(SELECT s.activity FROM `notification_activities` s WHERE s.`notification_id` = `notification_activities`.`notification_id` ORDER BY s.`id` DESC LIMIT 1)'), $activities);
            }
        });
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


} 