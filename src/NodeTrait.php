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
     * @param NodeInterface $node
     */
    public function addChildNode(NodeInterface $node)
    {
        $node->setParent($this->getNode());
        $this->childNodes[] = $node;
    }

    /**
     * @return array
     */
    public function getChildNodes()
    {
        return $this->childNodes;
    }

    /**
     * @param array $nodes
     */
    public function setChildNodes(array $nodes)
    {
        $this->childNodes = [];
        foreach ($nodes as $node) {
            $this->addChildNode($node);
        }
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

    /**
     * {@inheritdoc}
     *
     * @param callable $fn
     */
    public function walk(callable $fn)
    {
        $children = $this->getNode()->getChildNodes();

        foreach ($children as $child) {
            $fn($child);
            $child->walk($fn);
        }
    }

    /**
     * Remove the given node from the tree
     *
     * @param NodeInterface $node
     * @param NodeInterface|null
     */
    public function removeNode(NodeInterface $node)
    {
        $children = $this->getChildNodes();
        $filtered = [];

        foreach ($children as $child) {
            if ($child !== $node) {
                $filtered[] = $child;
            }
        }

        if (count($filtered) !== count($children)) {
            $this->setChildNodes($filtered);
            return $node;
        }

        return null;
    }

    /**
     * Return a new structure with nodes matching
     * the given predicate
     *
     * @param callable $predicate
     * @param bool $invert
     * @return NodeInterface
     */
    public function filter(callable $predicate, $invert = false)
    {
        $this->walk(function (NodeInterface $node) use ($predicate, $invert) {
            if ($predicate($node) === $invert) {
                $this->removeNode($node);
            }
        });
        return $this;
    }
}
