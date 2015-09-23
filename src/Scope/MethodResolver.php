<?php

namespace Peridot\Core\Scope;

/**
 * MethodResolver is responsible for resolving methods from a scope chain
 *
 * @package Peridot\Core\Scope
 */
class MethodResolver extends AbstractResolver
{
    /**
     * Resolve a scope method
     *
     * @param $name
     * @param $arguments
     * @return ResolutionResult
     */
    public function resolve($name, $arguments)
    {
        list($result, $found) = $this->resolveParent($name, $arguments);

        if ($found) {
            return new ResolutionResult($result, $found);
        }

        list($result, $found) = $this->scanChildren($this->scope, function ($childScope, &$accumulator) use ($name, $arguments) {
            if (method_exists($childScope, $name)) {
                $accumulator = [call_user_func_array([$childScope, $name], $arguments), true];
            }
        });

        return new ResolutionResult($result, $found);
    }

    /**
     * Check the scope's parent for a method
     *
     * @param $name
     * @return mixed|null
     */
    protected function resolveParent($name, $arguments)
    {
        $parent = $this->scope->getParentScope();

        while ($parent !== null) {
            if (method_exists($parent, $name)) {
                return [call_user_func_array([$parent, $name], $arguments), true];
            }
            $parent = $parent->getParentScope();
        }

        return [null, false];
    }
}
