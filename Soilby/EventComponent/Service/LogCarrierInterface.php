<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 4.2.15
 * Time: 22.56
 */

namespace Soilby\EventComponent\Service;


interface LogCarrierInterface {

    public function send($name, $message, $priority = 0);

} 