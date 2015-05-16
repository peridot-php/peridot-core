<?php

namespace Peridot\Core\Behavior;

use Peridot\EventEmitterInterface;
use Peridot\Core\TestInterface;

/**
 * An event listener that registers behaviors with relevant
 * events in the Peridot test life cycle
 */
class Listener
{
    /**
     * @var EventEmitterInterface;
     */
    protected $emitter;

    /**
     * @param EventEmitterInterface $emitter
     */
    public function __construct(EventEmitterInterface $emitter)
    {
        $this->emitter = $emitter;
    }

    /**
     * Listen for events and add behaviors
     *
     * @return void
     */
    public function listen()
    {
        $this->emitter->on('test.start', [$this, 'onTestStart']);
    }

    /**
     * Add behaviors relevant when a test is executed
     *
     * @param TestInterface $test
     * @return void
     */
    public function onTestStart(TestInterface $test)
    {
        $scope = $test->getScope();
        $behavior = new StateBehavior($test);
        $scope->peridotAddChildScope($behavior);
    }
}
