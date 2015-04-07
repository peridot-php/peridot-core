<?php
namespace Peridot\Core;

use Evenement\EventEmitterInterface;

/**
 * The Runner is responsible for running a given Suite.
 *
 * @package Peridot\Core
 */
class Runner implements RunnerInterface
{
    use HasEventEmitterTrait;

    /**
     * @var \Peridot\Core\Suite
     */
    protected $suite;

    /**
     * @var bool
     */
    protected $stopOnFailure;

    /**
     * @param Suite $suite
     * @param EventEmitterInterface $eventEmitter
     */
    public function __construct(Suite $suite, EventEmitterInterface $eventEmitter)
    {
        $this->suite = $suite;
        $this->eventEmitter = $eventEmitter;
    }

    /**
     * {@inheritdoc}
     *
     * @param TestResult $result
     */
    public function run(TestResult $result)
    {
        $this->handleErrors();

        $this->eventEmitter->on('test.failed', function () {
            if ($this->shouldStopOnFailure()) {
                $this->eventEmitter->emit('suite.halt');
            }
        });

        $this->eventEmitter->emit('runner.start');
        $this->suite->setEventEmitter($this->eventEmitter);
        $start = microtime(true);
        $this->suite->run($result);
        $this->eventEmitter->emit('runner.end', [microtime(true) - $start]);

        restore_error_handler();
    }

    /**
     * {@inheritdoc}
     */
    public function setStopOnFailure($stop)
    {
        $this->stopOnFailure = (bool) $stop;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldStopOnFailure()
    {
        return $this->stopOnFailure;
    }

    /**
     * Set an error handler to broadcast an error event.
     */
    protected function handleErrors()
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            $this->eventEmitter->emit('error', [$errno, $errstr, $errfile, $errline]);
        });
    }
}
