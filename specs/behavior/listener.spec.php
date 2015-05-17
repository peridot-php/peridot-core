<?php
use Peridot\Core\Behavior\Listener;
use Peridot\EventEmitter;
use Peridot\Core\Test;
use Peridot\Core\TestResult;
use Peridot\Core\Exception\PendingException;

describe('Listener', function () {
    beforeEach(function () {
        $this->emitter = new EventEmitter();
        $this->listener = new Listener($this->emitter);
        $this->listener->listen();
    });

    context('when a test.start event is emitted', function () {
        it('should add the StateBehavior to the test', function () {
            $ex = null;
            $test = new Test('sample test', function () {
                $this->pend();
            });
            $result = new TestResult($this->emitter);
            $this->emitter->emit('test.start', $test);
            $test->run($result);
            
            assert($test->getPending(), 'test should be pending');
        });
    });
});
