<?php
use Peridot\Core\Suite;
use Peridot\Core\Test;
use Peridot\Core\TestInterface;

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

    describe('->walk()', function () {
        it('should walk a hierarchy', function () {
            $childSuite = new Suite('child suite');
            $grandChildTest = new Test(' grand child');
            $childSuite->addChildNode($grandChildTest);
            $this->node->addChildNode($childSuite);

            $joined = '';
            $this->node->walk(function (TestInterface $test) use (&$joined) {
                $joined .= $test->getDescription();
            });

            assert($joined === 'child suite grand child');
        });
    });

    describe('->filter()', function () {
        it('should return a filtered node based on the given predicate', function () {
            $fast = new Test('should run @fast');
            $slow1 = new Test('should run @slow');
            $slow2 = new Test('should also be @slow');
            $this->node->setChildNodes([$fast, $slow1, $slow2]);

            $filtered = $this->node->filter(function (TestInterface $test) {
                return (bool) preg_match('/@slow/', $test->getDescription());
            });
            $children = $filtered->getChildNodes();

            assert(count($children) === 2, 'should have filtered out 1 child');
            assert($children[0] === $slow1);
            assert($children[1] === $slow2);
        });
    });

});
