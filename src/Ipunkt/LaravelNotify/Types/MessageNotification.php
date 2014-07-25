<?php
/**
 * Created by PhpStorm.
 * User: bastian
 * Date: 27.03.14
 * Time: 14:51
 */

namespace Ipunkt\LaravelNotify\Types;


class MessageNotification extends AbstractNotification
{

	/**
	 * @param string $message
	 */
	public function __construct($message) {
		$this->message = $message;
	}

}