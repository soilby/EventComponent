<?php

namespace Soilby\EventComponent;

use Events\Service\EventLogger;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\LocatorRegisteredInterface;
use Zend\Mvc\MvcEvent;

class Module implements
    ConfigProviderInterface,
    AutoloaderProviderInterface,
    LocatorRegisteredInterface

{
    /**
     * @var EventLogger
     */
    protected $eventLogger;

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [];
        foreach (glob(__DIR__ . '/config/*.config.php') as $file) {
            /** @noinspection PhpIncludeInspection */
            $config = array_merge($config, include $file);
        }

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            )
        );
    }

    public function onBootstrap(\Zend\Mvc\MvcEvent $e){
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(\Zend\Mvc\MvcEvent::EVENT_FINISH, [$this, 'flushQueue']);

    }

    public function flushQueue(MvcEvent $e)    {
        //if was instantiated
        if ($this->eventLogger && !$this->eventLogger->isEmpty()) {
            $rdfQueue = $this->eventLogger->getRDFQueue('ntriples');
            $client = $e->getApplication()->getServiceManager()->get('GearmanClient');
            $client->addJob('talaka_events', $rdfQueue);
        }
    }

    public function setEventLoggerInstance($instance)   {
        $this->eventLogger = $instance;
    }

}