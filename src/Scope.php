<?php
namespace Peridot\Core;

use BadMethodCallException;
use Closure;
use DomainException;

/**
 * Property bag for scoping "instance variables" and mixing in
 * behavior and state
 *
 * @method void use() use(Scope $scope, string $key = "") An alias for mixin
 *
 * @package Peridot\Core
 */
class Scope
{
    /**
     * @var Scope[]
     */
    protected $peridotChildScopes = [];

    /**
     * @var Scope
     */
    protected $peridotParentScope;

    /**
     * Mixin a scope to this scope. Scopes function in a similar fashion to PHP traits. Scopes
     * can access parent state, and also leverage child scopes of their own
     *
     * @param Scope $scope
     * @param string $key - an optional key. defaults to the scope's object hash
     * @return void
     */
    public function mixin(Scope $scope, $key = "")
    {
        if (empty($key)) {
            $key = get_class($scope);
        }

        if (isset($this->peridotChildScopes[$key])) {
            return;
        }

        $scope->setParentScope($this);
        $this->peridotChildScopes[$key] = $scope;
    }

    /**
     * @return Scope
     */
    public function getParentScope()
    {
        return $this->peridotParentScope;
    }

    /**
     * @param Scope $peridotParentScope
     */
    public function setParentScope(Scope $peridotParentScope)
    {
        $this->peridotParentScope = $peridotParentScope;
        return $this;
    }

    /**
     * @return array
     */
    public function getChildScopes()
    {
        return $this->peridotChildScopes;
    }

    /**
     * See if a child scope identified by key exists
     *
     * @param string $key
     * @return bool
     */
    public function hasChildScope($key)
    {
        if (isset($this->peridotChildScopes[$key])) {
            return true;
        }
        return false;
    }

    /**
     * Get a child scope by it's key
     *
     * @return Scope|null
     */
    public function getChildScope($key)
    {
        if ($this->hasChildScope($key)) {
            return $this->peridotChildScopes[$key];
        }
        return null;
    }

    /**
     * Remove a child scope by it's key
     *
     * @param string $key
     * @return bool
     */
    public function removeChildScope($key)
    {
        if ($this->hasChildScope($key)) {
            unset($this->peridotChildScopes[$key]);
            return true;
        }
        return false;
    }

    /**
     * Bind a callable to the scope.
     *
     * @param callable $callable
     * @return callable
     */
    public function bindTo(callable $callable)
    {
        if ($callable instanceof Closure) {
            return Closure::bind($callable, $this, $this);
        }
        return $callable;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throw BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if ($name === 'use') {
            return call_user_func_array([$this, 'mixin'], $arguments);
        }

        $parent = $this->getParentScope();

        while ($parent !== null) {
            if (method_exists($parent, $name)) {
                return call_user_func_array([$parent, $name], $arguments);
            }
            $parent = $parent->getParentScope();
        }

        list($result, $found) = $this->scanChildren($this, function ($childScope, &$accumulator) use ($name, $arguments) {
            if (method_exists($childScope, $name)) {
                $accumulator = [call_user_func_array([$childScope, $name], $arguments), true];
            }
        });

        if (!$found) {
            throw new BadMethodCallException("Scope method $name not found");
        }

        return $result;
    }

    /**
     * Lookup properties on child scopes.
     *
     * @param $name
     * @return mixed
     * @throws DomainException
     */
    public function &__get($name)
    {
        $parent = $this->getParentScope();

        while ($parent !== null) {
            if (property_exists($parent, $name)) {
                return $parent->$name;
            }
            $parent = $parent->getParentScope();
        }

        list($result, $found) = $this->scanChildren($this, function ($childScope, &$accumulator) use ($name) {
            if (property_exists($childScope, $name)) {
                $accumulator = [$childScope->$name, true, $childScope];
            }
        });
        if (!$found) {
            throw new DomainException("Scope property $name not found");
        }
        return $result;
    }


    /**
     * Scan child scopes and execute a function against each one passing an
     * accumulator reference along.
     *
     * @param Scope $scope
     * @param callable $fn
     * @param array $accumulator
     * @return array
     */
    protected function scanChildren(Scope $scope, callable $fn, &$accumulator = [])
    {
        if (! empty($accumulator)) {
            return $accumulator;
        }

        $children = $scope->getChildScopes();
        foreach ($children as $childScope) {
            $fn($childScope, $accumulator);
            $this->scanChildren($childScope, $fn, $accumulator);
        }
        return $accumulator;
    }
}
