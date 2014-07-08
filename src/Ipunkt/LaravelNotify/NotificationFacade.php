<?php
/**
 * Created by PhpStorm.
 * User: bastian
 * Date: 27.03.14
 * Time: 10:18
 */

namespace Ipunkt\LaravelNotify;


use Illuminate\Support\Facades\Facade;

class NotificationFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'notify';
    }
}