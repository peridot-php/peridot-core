<?php
namespace Peridot\Core;

use \Exception as BaseException;

/**
 * @package Peridot\Core
 */
class Exception extends BaseException
{
    /**
     * A manually set trace string
     *
     * @var string
     */
    protected $traceString = '';

    /**
     * A manually set exception type
     *
     * @var string
     */
    protected $type = '';

    /**
     * Set the trace string
     *
     * @param string $trace
     */
    public function setTraceString($trace)
    {
        $this->traceString = $trace;
    }

    /**
     * Get the set trace string
     *
     * @return string
     */
    public function getTraceString()
    {
        if (empty($this->traceString)) {
            return $this->getTraceAsString();
        }
        return $this->traceString;
    }

    /**
     * Set an exception type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Return the exception type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
