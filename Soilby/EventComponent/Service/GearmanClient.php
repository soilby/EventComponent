<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 26.1.15
 * Time: 21.21
 */

namespace Soilby\EventComponent\Service;


use Events\Service\LogCarrierInterface;

class GearmanClient implements LogCarrierInterface {

    protected $serverIP;
    protected $serverPort;

    protected $client;

    public function __construct($serverIP = '127.0.0.1', $serverPort = '4730')   {
        $this->serverIP = $serverIP;
        $this->serverPort = $serverPort;
    }

    public function getClient() {
        if (!$this->client) {
            $this->client = new \GearmanClient();
            $this->client->addServer($this->serverIP, $this->serverPort);
        }

        return $this->client;
    }

    /**
     * @param string $name
     * @param string $message
     * @param int    $priority
     *
     * @throws \Exception
     */
    public function send($name, $message, $priority = 0)  {
        switch ($priority)  {
            case 0:
                $this->getClient()->doBackground($name, $message);

                break;

            case 1:
                $this->getClient()->doHighBackground($name, $message);

                break;

            case -1:
                $this->getClient()->doLowBackground($name, $message);

                break;

            default:
                throw new \Exception('Priority can be 1, 0 or -1');
        }


    }
} 