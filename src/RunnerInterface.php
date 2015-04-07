<?php
namespace Peridot\Core;

/**
 * The RunnerInterface defines how a runner should run tests
 * and populate results.
 *
 * @package Peridot\Core
 */
interface RunnerInterface
{
    /**
     * Run the Suite
     *
     * @param TestResult $result
     */
    public function run(TestResult $result);
}
