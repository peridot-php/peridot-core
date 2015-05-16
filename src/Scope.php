<?php
namespace Peridot\Core;

use BadMethodCallException;
use Closure;
use DomainException;

/**
 * Property bag for scoping "instance variables" and mixing in
 * behavior and state
 *
 * @package Peridot\Core
 */
class Scope
{
    /**
     * @var array
     */
    protected $peridotChildScopes = [];

    /**
     * @var Scope
     */
    protected $peridotParentScope;

    /**
     * @param Scope $scope
     * @param string $key - an optional key. defaults to the scope's object hash
     */
    public function peridotAddChildScope(Scope $scope, $key = "")
    {
        $scope->peridotSetParentScope($this);
        if (empty($key)) {
            $key = spl_object_hash($scope);
        }
        $this->peridotChildScopes[$key] = $scope;
    }

    /**
     * @return Scope
     */
    public function peridotGetParentScope()
    {
        return $this->peridotParentScope;
    }

    /**
     * @param Scope $peridotParentScope
     */
    public function peridotSetParentScope($peridotParentScope)
    {
        $this->peridotParentScope = $peridotParentScope;
        return $this;
    }

    /**
     * @return array
     */
    public function peridotGetChildScopes()
    {
        return $this->peridotChildScopes;
    }

    /**
     * See if a child scope identified by key exists
     *
     * @param string $key
     * @return bool
     */
    public function peridotHasChildScope($key)
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
    public function peridotGetChildScope($key)
    {
        if ($this->peridotHasChildScope($key)) {
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
    public function peridotRemoveChildScope($key)
    {
        if ($this->peridotHasChildScope($key)) {
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
    public function peridotBindTo(callable $callable)
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
        list($result, $found) = $this->peridotScanChildren($this, function ($childScope, &$accumulator) use ($name, $arguments) {
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
        list($result, $found) = $this->peridotScanChildren($this, function ($childScope, &$accumulator) use ($name) {
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
    protected function peridotScanChildren(Scope $scope, callable $fn, &$accumulator = [])
    {
        if (! empty($accumulator)) {
            return $accumulator;
        }

        $children = $scope->peridotGetChildScopes();
        foreach ($children as $childScope) {
            $fn($childScope, $accumulator);
            $this->peridotScanChildren($childScope, $fn, $accumulator);
        }
        return $accumulator;
    }
}
