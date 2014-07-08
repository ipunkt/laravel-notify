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
     * @return array
     */
    public function getData();

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
     * @return string
     */
    public function __toString();

    /**
     * @return array
     */
    public function jsonSerialize();

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
}