<?php
use Peridot\Core\Suite;
use Peridot\Core\Test;

describe('NodeInterface', function () {

    beforeEach(function () {
        $this->node = new Suite('node');
    });

    describe('->removeNode()', function () {
        it('should remove the child node and return it', function () {
            $first = new Test('child 1');
            $second = new Test('child 2');
            $this->node->setChildNodes([$first, $second]);

            $removed = $this->node->removeNode($first);

            assert($removed === $first, 'should have returned removed node');
            assert(count($this->node->getChildNodes()) === 1, 'size should reflect removed node');
        });

        it('should return null if node not removed', function () {
            $first = new Test('child');

            $removed = $this->node->removeNode($first);

            assert($removed === null, 'cannot remove node that is not child');
        });

        it('should be possible through parent references', function () {
            $first = new Test('child 1');
            $second = new Test('child 2');
            $this->node->setChildNodes([$first, $second]);

            $removed = $first->getParent()->removeNode($first);

            assert($removed === $first, 'should have returned removed node');
            assert(count($this->node->getChildNodes()) === 1, 'size should reflect removed node');
        });
    });

});
