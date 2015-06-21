<?php

namespace Peridot\Core;

/**
 * NodeTrait provides tree traversal and node based operations to tree-like structures - i.e the Peridot TestInterface
 *
 * @package Peridot\Core
 */
trait NodeTrait
{
    /**
     * @var array
     */
    protected $childNodes = [];

    /**
     * @var NodeInterface
     */
    protected $parent;

    /**
     * @return NodeInterface
     */
    public function getNode()
    {
        return $this;
    }

    /**
     * @return array
     */
    public function getChildNodes()
    {
        return $this->childNodes;
    }

    /**
     * {@inheritdoc}
     *
     * @return NodeInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     *
     * @param  NodeInterface $parent
     * @return mixed|void
     */
    public function setParent(TestInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * {@inheritdoc}
     *
     * @param callable $fn
     */
    public function walkUp(callable $fn)
    {
        $node = $this->getNode();
        while ($node !== null) {
            $fn($node);
            $node = $node->getParent();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param callable $fn
     */
    public function walkDown(callable $fn)
    {
        $node = $this->getNode();
        $nodes = [];
        while ($node !== null) {
            array_unshift($nodes, $node);
            $node = $node->getParent();
        }
        foreach ($nodes as $node) {
            $fn($node);
        }
    }
}
