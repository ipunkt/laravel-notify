<?php
namespace Ipunkt\LaravelNotify\Models;
use Eloquent;

/**
 * Created by PhpStorm.
 * User: bastian
 * Date: 26.03.14
 * Time: 15:53
 *
 * @property integer $user_id
 * @property string $activity
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class NotificationActivity extends Eloquent
{

    const CREATED = 'created';
    const READ = 'read';
    const DONE = 'done';
    const DELETED = 'deleted';
    /**
     * @var string
     */
    protected $table = 'notification_activities';

    protected $fillable = ['activity', 'user_id', 'notification_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function notification()
    {
        return $this->belongsTo('Notification');
    }

} 