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

    /**
     * Set whether or not the runner should stop on failure.
     *
     * @param bool $stop
     * @return void
     */
    public function setStopOnFailure($stop);

    /**
     * Returns whether or not the Runner should stop on failure.
     *
     * @return bool
     */
    public function shouldStopOnFailure();

    /**
     * Set a pattern used to grep test descriptions. Tests with titles
     * matching the pattern will be included in the test run
     *
     * @param string $pattern
     * @param bool $invert
     * @return void
     */
    public function setGrepPattern($pattern, $invert);
}
