<?php

namespace Peridot\Core;

use Peridot\Core\Exception\PendingException;
use Exception;

/**
 * The main test fixture for Peridot.
 *
 * @package Peridot\Core
 */
class Test extends AbstractTest
{
    /**
     * Execute the test along with any setup and tear down functions.
     *
     * @param  TestResult $result
     * @return void
     */
    public function run(TestResult $result)
    {
        $result->startTest($this);

        if ($this->getPending()) {
            $result->pendTest($this);
            return;
        }
        
        $this->executeTest($result);
        $result->endTest($this);
    }

    /**
     * Attempt to execute setup functions and run the test definition
     *
     * @param TestResult $result
     */
    protected function executeTest(TestResult $result)
    {
        $action = ['passTest', $this];
        try {
            $this->runSetup();
            call_user_func_array($this->getDefinition(), $this->getDefinitionArguments());
        } catch (PendingException $e) {
            $action = ['pendTest', $this];
        } catch (Exception $e) {
            $action = ['failTest', $this, $e];
        }
        $this->runTearDown($result, $action);
    }

    /**
     * Excecute the test's setup functions
     */
    protected function runSetup()
    {
        $this->walkDown(function (TestInterface $node) {
            $setups = $node->getSetupFunctions();
            foreach ($setups as $setup) {
                call_user_func($setup);
            }
        });
    }

    /**
     * Run the tests tear down methods and have the result
     * perform the method indicated by $action
     *
     * @param TestResult $result
     * @param array $action
     */
    protected function runTearDown(TestResult $result, $action)
    {
        $this->walkUp(function (TestInterface $test) use ($result, &$action) {
            $tearDowns = $test->getTearDownFunctions();
            foreach ($tearDowns as $tearDown) {
                try {
                    call_user_func($tearDown);
                } catch (Exception $e) {
                    $action = ['failTest', $this, $e];
                }
            }
        });
        call_user_func_array([$result, $action[0]], array_slice($action, 1));
    }
}
