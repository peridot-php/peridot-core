<?php
namespace Peridot\Core;

use Peridot\EventEmitterInterface;

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
     * @var string
     */
    protected $grep = '';

    /**
     * @var bool
     */
    protected $invert = false;

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
        $this->eventEmitter->on('test.failed', [$this, 'onTestFailure']);
        $this->runSuite($result);
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
     * {@inheritdoc}
     *
     * @param string $pattern
     * @return void
     */
    public function setGrepPattern($pattern, $invert = false)
    {
        $this->grep = '|' . preg_quote($pattern) . '|';
        $this->invert = $invert;
    }

    /**
     * Set an error handler to broadcast an error event.
     */
    protected function handleErrors()
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            $this->eventEmitter->emit('error', $errno, $errstr, $errfile, $errline);
        });
    }

    /**
     * @param TestResult $result
     */
    protected function runSuite(TestResult $result)
    {
        $suite = $this->getSuite();
        $this->eventEmitter->emit('runner.start');
        $suite->setEventEmitter($this->eventEmitter);
        $start = microtime(true);
        $suite->run($result);
        $this->eventEmitter->emit('runner.end', microtime(true) - $start);
    }

    /**
     * Get the suite being run. If a grep pattern has been supplied, it will be
     * used to filter the tests being run.
     *
     * @return Suite
     */
    protected function getSuite()
    {
        $suite = $this->suite;
        if (!empty($this->grep)) {
            $suite = $suite->filter([$this, 'filterNodes'], $this->invert);
            return $suite;
        }
        return $suite;
    }

    /**
     * A listener for test failure
     *
     * @return void
     */
    public function onTestFailure()
    {
        if ($this->shouldStopOnFailure()) {
            $this->eventEmitter->emit('suite.halt');
        }
    }

    /**
     * Filter nodes based on the set grep expression
     *
     * @param NodeInterface $node
     * @return bool
     */
    public function filterNodes(NodeInterface $node)
    {
        if (!$node instanceof TestInterface) {
            return true;
        }
        return (bool) preg_match($this->grep, $node->getTitle());
    }
}
