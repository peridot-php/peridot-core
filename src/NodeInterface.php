<?php

namespace Peridot\Core;

interface NodeInterface
{
    /**
     * Execute a callback for each child node, starting
     * at the bottom of the tree.
     *
     * @param callable $fn
     */
    public function walkUp(callable $fn);

    /**
     * Execute a callback for each child node, starting
     * at the top of the tree.
     *
     * @param callable $fn
     */
    public function walkDown(callable $fn);

    /**
     * @return TestInterface
     */
    public function getParent();

    /**
     * @param  TestInterface $parent
     * @return mixed
     */
    public function setParent(TestInterface $parent);
}
