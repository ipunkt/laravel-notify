<?php
/**
 * Created by PhpStorm.
 * User: bastian
 * Date: 27.03.14
 * Time: 12:36
 */

namespace Ipunkt\LaravelNotify\Types;


use Illuminate\Support\SerializableClosure;
use Notification;

class ClosureNotification extends AbstractNotification
{

    protected $closure;

    /**
     * Fire the Closure based queue job.
     *
     * @param \Notification $notification
     */
    public function __construct(Notification $notification)
    {
        parent::__construct($notification);
        /** @var SerializableClosure $closure */
        $this->closure = unserialize($this->data['closure']);
    }

    /**
     * Execute Closure
     * @return mixed
     */
    public function read()
    {
        return $this->closure($this->model);
    }

}