<?php
/**
 * Created by PhpStorm.
 * User: bastian
 * Date: 02.10.14
 * Time: 19:58
 */
namespace Ipunkt\LaravelNotify\Contracts;

use Ipunkt\LaravelNotify\Models\Notification;
use Response;

interface NotifyControllerInterface
{
    /**
     * Display a listing of some resources.
     * GET /notify/index
     *
     * @return Response
     */
    public function index();

    /**
     * Display a listing of all resources.
     * GET /notify/all
     *
     * @return \Ipunkt\LaravelNotify\Contracts\NotificationTypeInterface[]
     */
    public function all();

    /**
     * Display the specified resource.
     * GET /notify/{notification}/{action}
     *
     * @param \Ipunkt\LaravelNotify\Models\Notification $notification
     * @param string $action
     * @return Response
     */
    public function action(Notification $notification, $action);
}