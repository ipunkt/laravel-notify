<?php
namespace Ipunkt\LaravelNotify\Models;

use App;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        return $this->belongsTo('Ipunkt\LaravelNotify\Models\Notification');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        /**
         * get fresh instance of current binding of UserInterface
         * @var UserInterface $modelInstance
         */
        $modelInstance = App::make('Illuminate\Auth\UserInterface');
        $modelClass = get_class($modelInstance);

        return $this->belongsTo($modelClass, 'user_id');
    }

}