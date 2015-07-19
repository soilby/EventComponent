<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 26.1.15
 * Time: 21.21
 */

namespace Soilby\EventComponent\Service;



use EasyRdf\Graph;
use EasyRdf\Http\Client;
use EasyRdf\Http\Response;

class HttpGearmanClient extends AbstractClient implements LogCarrierInterface {

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
    public function sendRaw($name, $message, $priority = 0)    {
        $this->client->setUri($this->endpointURL);
        $this->client->setMethod('POST');

        $this->client->setRawData($message);
        $response = $this->client->request();


        $this->lastResponse = $response;

        $data = json_decode($response->getBody(), true);
        $httpStatus = $response->getStatus();

        if ($httpStatus === 200 && is_array($data)) {

            return $data;

        }
        else    {
            if (is_array($data))    {
                throw new \Exception("Job sent request end up with HTTP$httpStatus. {$data['error']}");
            }
            else    {
                throw new \Exception("Job sent request end up with HTTP$httpStatus.");
            }
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