<?php

namespace Peridot\Core\Behavior;

use Peridot\Core\Scope;
use Peridot\Core\Exception\PendingException;
use Peridot\Core\TestInterface;

/**
 * The StateBehavior adds behavior for controlling test state
 *
 * @package Peridot\Core\Behavior
 */
class StateBehavior extends Scope
{
    /**
     * @var \Peridot\Core\TestInterface
     */
    protected $test;

    /**
     * @param \Peridot\Core\TestInterface
     */
    public function __construct(TestInterface $test)
    {
        $this->test = $test;
    }

    /**
     * Put the test into a pending state. Halts test execution when
     * called
     *
     * @throws \Peridot\Core\Exception\PendingException
     */
    public function pend()
    {
        $this->test->setPending(true);
        throw new PendingException();
    }

    /**
     * Fail the test
     *
     * @param string $message
     * @throws \Exception
     */
    public function fail($message)
    {
        throw new \Exception($message);
    }
}
