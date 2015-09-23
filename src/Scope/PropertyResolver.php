<?php

namespace Peridot\Core\Scope;

use Peridot\Core\Scope;

/**
 * PropertyResolver is responsible for resolving property values from a scope chain
 *
 * @package Peridot\Core\Scope
 */
class PropertyResolver extends AbstractResolver
{
    /**
     * Resolve a scope property
     *
     * @param $name
     * @return ResolutionResult
     */
    public function resolve($name)
    {
        list($result, $found) = $this->resolveParent($name);

        if ($found) {
            return new ResolutionResult($result, $found);
        }

        list($result, $found) = $this->scanChildren($this->scope, function ($childScope, &$accumulator) use ($name) {
            if (property_exists($childScope, $name)) {
                $accumulator = [$childScope->$name, true, $childScope];
            }
        });

        return new ResolutionResult($result, $found);
    }

    /**
     * Check the scope's parent for a property value
     *
     * @param $name
     * @return mixed|null
     */
    protected function resolveParent($name)
    {
        $parent = $this->scope->getParentScope();

        while ($parent !== null) {
            if (property_exists($parent, $name)) {
                return [$parent->$name, true];
            }
            $parent = $parent->getParentScope();
        }

        return [null, false];
    }
}
