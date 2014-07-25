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
	 * returns a unique context identifier if necessary
	 *
	 * @return string
	 */
	public function getContext();

	/**
	 * Return true if there could be only one Instance per Context
	 *
	 * @return bool
	 */
	public function isSingleton();
}