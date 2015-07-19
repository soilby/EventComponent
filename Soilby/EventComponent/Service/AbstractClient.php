<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 19.7.15
 * Time: 8.54
 */

namespace Soilby\EventComponent\Service;


use EasyRdf\Graph;

abstract class AbstractClient {


    protected $outputConfig = [
        'output_rdf_format' => 'ntriples',
        'queue_stream_name' => 'soil_event'
    ];

    /**
     * @return array
     */
    public function getOutputConfig()
    {
        return $this->outputConfig;
    }

    /**
     * @param array $outputConfig
     */
    public function setOutputConfig($outputConfig)
    {
        $this->outputConfig = $outputConfig;
    }



    protected function getGraphAsString(Graph $graph) {
        $outputConfig = $this->getOutputConfig();
        return $graph->serialise($outputConfig['output_rdf_format']);
    }



    public function send(Graph $graph, $priority = 0) {
        $s = $this->getGraphAsString($graph);

        return $this->sendRaw($this->getOutputConfig()['queue_stream_name'], $s, $priority);
    }


    abstract public function sendRaw($name, $message, $priority = 0);
} 