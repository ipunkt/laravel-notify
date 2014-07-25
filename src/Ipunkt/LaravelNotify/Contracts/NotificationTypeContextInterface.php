<?php
/**
 * Created by PhpStorm.
 * User: rok
 * Date: 25.07.14
 * Time: 14:53
 */

namespace Ipunkt\LaravelNotify\Contracts;


interface NotificationTypeContextInterface extends NotificationTypeInterface{

	/**
	 *
	 *
	 * @return mixed
	 */
	public function getContext();
}