<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 23.1.15
 * Time: 15.49
 */

namespace Soilby\EventComponent\Entity;

class GenericEvent {

    /**
     * @var string
     */
    protected $id;

    /**
     * Object of the event
     * @var string
     */
    protected $target;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * For example an user
     * Subject of the event
     * @var string
     */
    protected $agent;





} 