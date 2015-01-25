<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 23.1.15
 * Time: 17.53
 */

namespace Soil\EventComponent\Entity;


/**
 * Class CommentEvent
 * @package Soil\EventComponent\Entity
 */
class CommentEvent extends GenericEvent {


    /**
     * @var string
     */
    protected $comment;

}