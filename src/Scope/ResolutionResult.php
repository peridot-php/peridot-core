<?php

namespace Peridot\Core\Scope;

/**
 * ResolutionResult is a simple data structure for encapsulating
 * the results of resolving a value on a Scope
 *
 * @package Peridot\Core\Scope
 */
class ResolutionResult
{
    /**
     * @var mixed
     */
    public $value;

    /**
     * @var bool
     */
    public $found;

    /**
     * @param mixed $value
     * @param bool $found
     */
    public function __construct($value, $found = true)
    {
        $this->value = $value;
        $this->found = (bool) $found;
    }
}
