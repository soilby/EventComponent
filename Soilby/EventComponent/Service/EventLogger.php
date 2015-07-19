<?php
namespace Soilby\EventComponent\Service;

/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 25.1.15
 * Time: 17.20
 */

use EasyRdf\Graph;
use  \EasyRdf\Literal\DateTime;
use EasyRdf\RdfNamespace;
use Soilby\EventComponent\Entity\CommentEvent;
use Soilby\EventComponent\Entity\GenericEvent;

class EventLogger {

    protected $ontologyAbbr;

    const EVENT_CREATE = 'CREATE';
    const EVENT_REMOVE = 'REMOVE';
    const EVENT_CLAIM = 'CLAIM';
    const EVENT_DECLINE = 'DECLINE';
    const EVENT_SUBSCRIBE = 'SUBSCRIBE';
    const EVENT_COMPLETE = 'COMPLETE';
    const EVENT_REMIND = 'REMIND';
    const EVENT_COMMENT = 'COMMENT'; //derived from create
    const EVENT_VOTE = 'VOTE'; //derived from create
    const EVENT_PAID = 'PAID';

    /**
     * @var LogCarrierInterface
     */
    protected $logCarrier;


    protected $ontologyConfig = [];
    protected $protocolSettings = [];

    /**
     * @var Graph
     */
    protected $graph;

    /**
     * @var UrinatorInterface
     */
    protected $urinator;

    public function __construct($ontologyConfig, $protocolSettings)   {
        $this->ontologyConfig = $ontologyConfig;
        $this->protocolSettings = $protocolSettings;

        $this->ontologyAbbr = $ontologyConfig['ontology_abbr'];

        RdfNamespace::set($ontologyConfig['ontology_abbr'], $ontologyConfig['ontology_uri']);

        $this->graph = new Graph();
    }


    public function getOntologyAbbr()   {
        return $this->ontologyAbbr;
    }

    public function getUrinator()   {
        return $this->urinator;
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

    public function raiseVote($vote, $voterAgent, $agent, $voteValue, $relatedObject) {
        $event = $this->getEvent(self::EVENT_VOTE);

        $event->addResource($this->ontologyAbbr . ':target', $this->urinator->generateURI($vote));
        $event->addResource($this->ontologyAbbr . ':voterAgent', $this->urinator->generateURI($voterAgent));
        $event->addResource($this->ontologyAbbr . ':agent', $this->urinator->generateURI($agent));
        $event->addLiteral($this->ontologyAbbr . ':voteValue', $voteValue);
        $event->addResource($this->ontologyAbbr . ':relatedObject', $this->urinator->generateURI($relatedObject));

    }

    public function raiseCampaignComplete($projectOrCampaign) {
        $event = $this->getEvent(self::EVENT_COMPLETE);

        $event->addResource($this->ontologyAbbr . ':target', $this->urinator->generateURI($projectOrCampaign));
    }


    /**
     * @param string $format
     * may be ntriples|rdfxml|turtle and more
     *
     * @return mixed
     */
    public function getRDFQueue($format = 'turtle')    {
//        echo '[' . date('Y-m-d H:i:s') . ']';
//        echo $this->graph->dump('text') . PHP_EOL . PHP_EOL;
        return $this->graph->serialise($format);
    }

    public function isEmpty()   {
        return $this->graph->isEmpty();
    }

    /**
     * @param $eventName string Use constant from this class
     * @param $uniqueId string Unique identificator for event, can be provided or generated randomly
     *
     * @return \EasyRdf\Resource
     *
     * @throws \Exception
     */
    public function getEvent($eventName, $uniqueId = null)   {
        if (!$uniqueId) $uniqueId = 'event_' . $eventName . '_' . (string) new \MongoId();

        $classes = $this->ontologyConfig['event_classification'];

        if (!array_key_exists($eventName, $classes))  {
            throw new \Exception('Event type is not supported');
        }

        $eventClass = $classes[$eventName];

        $event = $this->graph->resource(
            $this->ontologyAbbr . ':' . $uniqueId,
            $this->ontologyAbbr . ':' . $eventClass
        );
        $event->set($this->ontologyAbbr . ':date', new DateTime());


        return $event;
    }



    /**
     * @param LogCarrierInterface $logCarrier
     */
    public function setLogCarrier(LogCarrierInterface $logCarrier)
    {
        $this->logCarrier = $logCarrier;
    }

    public function flush() {
        if (!$this->isEmpty()) {
            $rdfQueue = $this->getRDFQueue($this->protocolSettings['output_rdf_format']);
            $sendStatus = $this->logCarrier->sendRaw($this->protocolSettings['queue_stream_name'], $rdfQueue);
            if ($sendStatus['success'])    {
                $this->graph = new Graph(); //clear graph
            }
            else    {
                $message = $sendStatus['error'];
                throw new \Exception("Graph cannot be saved. $message");
            }
        }
    }

} 