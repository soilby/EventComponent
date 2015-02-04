<?php
namespace Events\Service;

/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 25.1.15
 * Time: 17.20
 */

use Api\Services\URInator;
use EasyRdf\Graph;
use  \EasyRdf\Literal\DateTime;
use Soilby\EventComponent\Entity\CommentEvent;
use Soilby\EventComponent\Entity\GenericEvent;

class EventLogger {

    protected $ontologyURI;
    protected $ontologyAbbr;

    const EVENT_CREATE = 'CREATE';
    const EVENT_REMOVE = 'REMOVE';
    const EVENT_CLAIM = 'CLAIM';
    const EVENT_DECLINE = 'DECLINE';
    const EVENT_SUBSCRIBE = 'SUBSCRIBE';
    const EVENT_COMPLETE = 'COMPLETE';
    const EVENT_COMMENT = 'COMMENT'; //derived from create

    protected $eventClasses = [];

    /**
     * @var Graph
     */
    protected $graph;

    /**
     * @var UrinatorInterface
     */
    protected $urinator;

    public function __construct($ontologyURI, $ontologyAbbr, $eventClassification = [])   {
        $this->ontologyURI = $ontologyURI;
        $this->ontologyAbbr = $ontologyAbbr;
        $this->eventClasses = $eventClassification;

        \EasyRdf\RdfNamespace::set($ontologyAbbr, $ontologyURI);

        $this->graph = new Graph();
    }

    /**
     * @param mixed $urinator
     */
    public function setUrinator($urinator)
    {
        $this->urinator = $urinator;
    }




    public function raiseCreate($target, $agent)    {
        $event = $this->getEvent(self::EVENT_CREATE);

        $event->addResource($this->ontologyAbbr . ':target', $this->urinator->generateURI($target));
        $event->addResource($this->ontologyAbbr . ':agent', $this->urinator->generateURI($agent));

    }

    public function raiseSubscribe($target, $agent) {
        $event = $this->getEvent(self::EVENT_SUBSCRIBE);

        $event->addResource($this->ontologyAbbr . ':target', $this->urinator->generateURI($target));
        $event->addResource($this->ontologyAbbr . ':agent', $this->urinator->generateURI($agent));

    }

    public function raiseComment($comment, $agent, $relatedObject) {
        $event = $this->getEvent(self::EVENT_COMMENT);

        $event->addResource($this->ontologyAbbr . ':target', $this->urinator->generateURI($comment));
        $event->addResource($this->ontologyAbbr . ':agent', $this->urinator->generateURI($agent));
        $event->addResource($this->ontologyAbbr . ':relatedObject', $this->urinator->generateURI($relatedObject));

    }


    /**
     * @param string $format
     * may be ntriples|rdfxml|turtle and more
     *
     * @return mixed
     */
    public function getRDFQueue($format = 'turtle')    {
        return $this->graph->serialise($format);
    }

    public function isEmpty()   {
        return $this->graph->isEmpty();
    }

    /**
     * @param $eventName
     * @param array $params
     * @return \EasyRdf\Resource
     * @throws \Exception
     */
    public function getEvent($eventName, $params = [])   {
        $eventUniqueCode = (string) new \MongoId();
        if (!array_key_exists($eventName, $this->eventClasses))  {
            throw new \Exception('Event type is not supported');
        }
        $eventClass = $this->eventClasses[$eventName];

        $event = $this->graph->resource(
            $this->ontologyAbbr . ':event_' . $eventUniqueCode,
            $this->ontologyAbbr . ':' . $eventClass
        );
        $event->set($this->ontologyAbbr . ':date', new DateTime());


        return $event;
    }

} 