<?php
use Peridot\Core\Test;
use Peridot\Core\Behavior\StateBehavior;
use Peridot\Core\Exception\PendingException;

describe('StateBehavior', function () {
    beforeEach(function () {
        $this->test = new Test('test');
        $this->behavior = new StateBehavior($this->test);
    });

    describe('->pend()', function () {
        it('should throw a pending exception and set the test to pending', function () {
            $ex = null;
            try {
                $this->behavior->pend();
            } catch (PendingException $e) {
                $ex = $e;
            }
            assert($ex instanceof PendingException, 'exception should be a PendingException');
            assert($this->test->getPending(), 'test should be pending');
        });
    });

    describe('->fail()', function () {
        it('throws an exception with a given message', function () {
            $ex = null;
            try {
                $this->behavior->fail('failure!!');
            } catch (Exception $e) {
                $ex = $e;
            }
            assert($ex->getMessage() === 'failure!!');
        });
    });
});
