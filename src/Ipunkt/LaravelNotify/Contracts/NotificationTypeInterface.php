<?php
/**
 * Created by PhpStorm.
 * User: bastian
 * Date: 27.03.14
 * Time: 15:48
 */

namespace Ipunkt\LaravelNotify\Contracts;


use Ipunkt\LaravelNotify\Models\Notification;
use JsonSerializable;
use Symfony\Component\HttpFoundation\Response;

interface NotificationTypeInterface extends JsonSerializable
{

    /**
     * Set Notification Model
     * @param Notification $notification
     */
    public function setModel(Notification $notification);

    /**
     * @return Notification
     */
    public function getModel();

    /**
     * Show Message
     * @return string
     */
    public function show();

    /**
     * Do the read-Action
     * TODO could we return a Redirect?
     * TODO maybe register hooks
     * @return Response
     */
    public function read();

    /**
     * Do the done-Action
     * @return Response
     */
    public function done();

    /**
     * Do the delete-Action
     * @return Response
     */
    public function delete();

    /**
     * is notification in given state
     *
     * @param string $state
     * @return bool
     */
    public function is($state);

    /**
     * @param $activity
     * @return bool
     */
    public function isGrouped($activity);

    /**
     * @param $activity
     * @return bool
     */
    public function isDoAutoAddActivity($activity);

}