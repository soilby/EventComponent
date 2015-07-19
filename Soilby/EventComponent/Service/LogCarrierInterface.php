<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 4.2.15
 * Time: 22.56
 */

namespace Soilby\EventComponent\Service;


use EasyRdf\Graph;

interface LogCarrierInterface {

    public function send(Graph $graph, $priority = 0);
    public function sendRaw($name, $message, $priority = 0);

} 