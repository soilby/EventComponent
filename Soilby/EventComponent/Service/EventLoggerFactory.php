<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 26.1.15
 * Time: 11.42
 */

namespace Events\Service;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventLoggerFactory implements FactoryInterface {


    public function createService(ServiceLocatorInterface $serviceLocator)  {
        $configuration = $serviceLocator->get('Configuration');
        $config = $configuration['events_config'];

        $eventLogger = new EventLogger(
            $config['ontology_uri'], $config['ontology_abbr'], $config['event_classification']
        );

        $urinator = $serviceLocator->get('URInator');
        $eventLogger->setUrinator($urinator);

        $module = $serviceLocator->get('Events\Module');
        $module->setEventLoggerInstance($eventLogger);

        return $eventLogger;
    }

} 