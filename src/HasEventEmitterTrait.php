<?php
namespace Peridot\Core;

use Peridot\EventEmitterInterface;

/**
 * Trait indicating an object supports an EventEmitter via composition.
 *
 * @package Peridot\Core
 */
trait HasEventEmitterTrait
{
    /**
     * @var \Peridot\EventEmitterInterface
     */
    protected $eventEmitter;

    /**
     * @param \Peridot\EventEmitterInterface $eventEmitter
     */
    public function setEventEmitter(EventEmitterInterface $eventEmitter)
    {
        $this->eventEmitter = $eventEmitter;

        return $this;
    }

    /**
     * @return \Peridot\EventEmitterInterface
     */
    public function getEventEmitter()
    {
        return $this->eventEmitter;
    }
}
