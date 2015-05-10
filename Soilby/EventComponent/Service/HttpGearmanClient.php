<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 26.1.15
 * Time: 21.21
 */

namespace Soilby\EventComponent\Service;



use EasyRdf\Http\Client;
use EasyRdf\Http\Response;

class HttpGearmanClient implements LogCarrierInterface {

    protected $endpointURL;

    protected $lastResponse;

    /**
     * @var Client
     */
    protected $client;

    public function __construct($endpointURL, $httpClient)   {
        $this->endpointURL = $endpointURL;

        $this->client = $httpClient;
    }

    /**
     * @param string $name will not used in this implementation
     * @param string $message
     * @param int $priority will not used in this implementation
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function send($name, $message, $priority = 0)    {
        $this->client->setUri($this->endpointURL);
        $this->client->setMethod('POST');

        $this->client->setRawData($message);
        $response = $this->client->request();


        $this->lastResponse = $response;

        if ($response->getStatus() === 200) {
            $data = json_decode($response->getBody(), true);

            return $data['success'];

        }
        else    {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }



} 