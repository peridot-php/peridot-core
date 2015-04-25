<?php
use Peridot\Core\Exception;

describe('Exception', function () {
    describe('->getTraceString()', function () {
        it('returns a manually set trace string', function () {
            $exception = new Exception('message');
            $exception->setTraceString('this is a trace');
            assert($exception->getTraceString() === 'this is a trace');
        });

        it('uses ->getTraceAsString if nothing manually set', function () {
            $e = null;
            try {
                throw new Exception('message');
            } catch (Exception $ex) {
                $e = $ex;
            }
            assert($e->getTraceString() === $e->getTraceAsString());
        });

        it('prefers the manually set trace string', function () {
            $e = null;
            try {
                throw new Exception('message');
            } catch (Exception $ex) {
                $e = $ex;
            }
            $e->setTraceString('trace');
            assert($e->getTraceString() === 'trace');
        });
    });

    describe('type accessors', function () {
        it('can access a string type', function () {
            $e = new Exception('message');
            $e->setType('RuntimeException');
            $type = $e->getType();
            assert($type === 'RuntimeException');
        });
    });
});
