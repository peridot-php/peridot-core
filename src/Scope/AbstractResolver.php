<?php

namespace Peridot\Core\Scope;

use Peridot\Core\Scope;

/**
 * Class AbstractResolver
 *
 * @package Peridot\Core\Scope
 */
class AbstractResolver
{
    /**
     * @var Scope
     */
    protected $scope;

    /**
     * @param Scope $scope
     */
    public function __construct(Scope $scope)
    {
        $this->scope = $scope;
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
