<?php

namespace Peridot\Core;

/**
 * Base class for Peridot Suites and Tests
 *
 * @package Peridot\Core
 */
abstract class AbstractTest implements TestInterface
{
    use HasEventEmitterTrait;
    use NodeTrait {
        setParent as setParentNode;
    }

    /**
     * The test definition as a callable.
     *
     * @var callable
     */
    protected $definition;

    /**
     * A collection of functions to run before tests execute.
     *
     * @var array
     */
    protected $setUpFns = [];

    /**
     * A collection of functions to run after tests execute.
     *
     * @var array
     */
    protected $tearDownFns = [];

    /**
     * @var string
     */
    protected $description;

    /**
     * @var bool|null
     */
    protected $pending = null;

    /**
     * @var Scope
     */
    protected $scope;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var array
     */
    protected $definitionArguments = [];

    /**
     * @param string   $description
     * @param callable $definition
     */
    public function __construct($description, callable $definition = null)
    {
        if ($definition === null) {
            $this->pending = true;
            $definition = function () {
                //noop
            };
        }

        $this->definition = $definition;
        $this->description = $description;
        $this->scope = new Scope();
    }

    /**
     * {@inheritdoc}
     *
     * @param callable $setupFn
     */
    public function addSetupFunction(callable $setupFn)
    {
        $fn = $this->getScope()->bindTo($setupFn);
        array_push($this->setUpFns, $fn);
    }

    /**
     * {@inheritdoc}
     *
     * @param callable $tearDownFn
     */
    public function addTearDownFunction(callable $tearDownFn)
    {
        $fn = $this->getScope()->bindTo($tearDownFn);
        array_push($this->tearDownFns, $fn);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     *
     * @return callable
     */
    public function getDefinition()
    {
        return $this->scope->bindTo($this->definition);
    }

    /**
     * {@inheritdoc}
     *
     * @param  TestInterface $parent
     * @return mixed|void
     */
    public function setParent(NodeInterface $parent)
    {
        $this->setParentNode($parent);
        if ($parent instanceof TestInterface) {
            $this->setScope($parent->getScope());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getTitle()
    {
        $parts = [];
        $node = $this;
        while ($node != null) {
            array_unshift($parts, $node->getDescription());
            $node = $node->getParent();
        }

        return implode(' ', $parts);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool|null
     */
    public function getPending()
    {
        return $this->pending;
    }

    /**
     * {@inheritdoc}
     *
     * @param bool $state
     */
    public function setPending($state)
    {
        $this->pending = (bool) $state;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getSetupFunctions()
    {
        return $this->setUpFns;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getTearDownFunctions()
    {
        return $this->tearDownFns;
    }

    /**
     * {@inheritdoc}
     *
     * @return Scope
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * {@inheritdoc}
     *
     * @param Scope $scope
     * @return mixed
     */
    public function setScope(Scope $scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * Get the file this test belongs to.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the file this test belongs to.
     *
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $args
     * @return $this
     */
    public function setDefinitionArguments(array $args)
    {
        $this->definitionArguments = $args;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getDefinitionArguments()
    {
        return $this->definitionArguments;
    }
}
