<?php

namespace Peridot\Core;

/**
 * NodeInterface provides methods for setting node relationships and tree traversal
 *
 * @package Peridot\Core
 */
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
     * Get the parent node
     *
     * @return NodeInterface
     */
    public function getParent();

    /**
     * Set the parent node
     *
     * @param  TestInterface $parent
     * @return mixed
     */
    public function setParent(NodeInterface $parent);

    /**
     * Get all child nodes
     *
     * @return NodeInterface[]
     */
    public function getChildNodes();

    /**
     * Set child nodes
     *
     * @param array $nodes
     */
    public function setChildNodes(array $nodes);

    /**
     * @param NodeInterface $node
     */
    public function addChildNode(NodeInterface $node);

    /**
     * Remove the given child node and return it
     *
     * @param NodeInterface $node
     * @return NodeInterface|null
     */
    public function removeNode(NodeInterface $node);
}
