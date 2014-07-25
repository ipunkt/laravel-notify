<?php
/**
 * Created by PhpStorm.
 * User: bastian
 * Date: 27.03.14
 * Time: 14:17
 */

namespace Ipunkt\LaravelNotify\Types;


use Ipunkt\LaravelNotify\Contracts\NotificationTypeInterface;
use Ipunkt\LaravelNotify\Models\Notification;
use Ipunkt\LaravelNotify\Models\NotificationActivity;
use Lang;
use URL;
use Notify;
use Redirect;
use Response;

abstract class AbstractNotification implements NotificationTypeInterface
{
	/** @var array */
	protected $data = [];

	/** @var Notification */
	protected $model = null;

	/** @var string $message */
	protected $message = '';

	/**
	 * Set Notification Model
	 * @param Notification $notification
	 * @return $this
	 */
	public function setModel(Notification $notification)
	{
		$this->model = $notification;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @param array $data
	 * @return AbstractNotification
	 */
	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}


	/**
	 * returns message as translatable string
	 * @return string
	 */
	public function show()
	{
		return Lang::get($this->message, $this->data);
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->show();
	}

	/**
	 * @return array|mixed
	 */
	public function jsonSerialize()
	{
		$latestActivity = $this->model->lastActivity();
		return [
			'id' => $this->model->id,
			'show' => $this->show(),
			'link' => $this->getActionLink('read'),
			'message' => $this->message,
			'data' => $this->data,
			'state' => $latestActivity->activity,
			'updated_at' => $latestActivity->created_at,
			'created_at' => $this->model->created_at,
		];
	}

	/**
	 * @return Notification
	 */
	public function getModel()
	{
		return $this->model;
	}


	/**
	 * Do the read-Action
	 * By default, forwards to Done-Action
	 * @return Response
	 */
	public function read()
	{
		return Notify::doAction($this->model, NotificationActivity::DONE);
	}

	/**
	 * Do the done-Action
	 * @return Response
	 */
	public function done()
	{
		return Redirect::back();
	}


	/**
	 * Do the delete-Action
	 * @return Response
	 */
	public function delete()
	{
		$this->model->delete();
	}

	/**
	 * returns action link
	 *
	 * @param string $action
	 * @return string
	 */
	public function getActionLink($action = 'read')
	{
		return URL::route('notify.action', array('notification' => $this->model->id, 'action' => $action));
	}

	/**
	 * is notification in given state
	 *
	 * @param string $state
	 * @return bool
	 */
	public function is($state)
	{
		return $this->getModel()->currentState() === $state;
	}

	/**
	 * returns data content
	 *
	 * @param string $key
	 * @return mixed|null
	 */
	public function __get($key)
	{
		if (array_key_exists($key, $this->data))
			return $this->data[$key];

		return null;
	}
}
