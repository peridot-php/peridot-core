<?php
use Peridot\Core\Scope;

describe('Scope', function() {
    beforeEach(function() {
        $this->scope = new Scope();
    });

    describe('->addChildScope()', function() {
        it('should mixin behavior via __call', function() {
            $this->scope->addChildScope(new TestScope());
            $number = $this->scope->getNumber();
            assert(5 === $number, 'getNumber() should return value');
        });

        it('should mixin properties via __get', function() {
            $this->scope->addChildScope(new TestScope());
            $name = $this->scope->name;
            assert($name == "brian", "property should return value");
        });

        it('should set the parent scope property on child', function() {
            $test = new TestScope();
            $this->scope->addChildScope($test);
            assert($test->getParentScope() === $this->scope, "should have set parent scope");
        });

        it('can supply a key for the child scope', function () {
            $test = new TestScope();
            $this->scope->addChildScope($test, 'test');
            $scope = $this->scope->getChildScope('test');
            assert($scope === $test);
        });
    });

    describe('->hasChildScope()', function () {
        it('returns true if a child scope with the given key exists', function () {
            $test = new TestScope();
            $this->scope->addChildScope($test, 'test');
            assert($this->scope->hasChildScope('test'));
        });

        it('returns false if a child scope with the given key does not exist', function () {
            assert($this->scope->hasChildScope('test') === false);
        });
    });

    describe('->removeChildScope()', function () {
        it('returns true if it removes a child scope successfully', function () {
            $test = new TestScope();
            $this->scope->addChildScope($test, 'test');
            $removed = $this->scope->removeChildScope('test');
            assert($removed);
        });

        it('returns false if it did not remove a scope', function () {
            assert($this->scope->removeChildScope('test') === false);
        });
    });

    describe('->bindTo()', function() {
        it('should bind a Closure to the scope', function() {
            $callable = function() {
                return $this->name;
            };
            $scope = new TestScope();
            $bound = $scope->bindTo($callable);
            $result = $bound();
            assert($result == "brian", "scope should have been bound to callable");
        });

        it('should return non closures', function () {
            $callable = 'strpos';
            $scope = new TestScope();

            $bound = $scope->bindTo($callable);

            assert($bound === 'strpos');
        });
    });

    describe('->inheritScope()', function () {
        it('should copy properties from another scope', function () {
            $src = new Scope();
            $src->name = 'brian';
            $target = new Scope();

            $target->inheritScope($src);

            assert($target->name === 'brian');
        });

        it('should not copy properties from another scope if they are already set', function () {
            $src = new Scope();
            $src->name = 'brian';
            $target = new Scope();
            $target->name = 'phil';

            $target->inheritScope($src);

            assert($target->name === 'phil');
        });
    });

    context("when calling a mixed in method", function() {
        it('should throw an exception when method not found', function() {
            $exception = null;
            try {
                $this->scope->nope();
            } catch (\Exception $e) {
                $exception = $e;
            }
            assert(!is_null($exception), 'exception should not be null');
        });

        context("and the desired method is on a child scope's child", function() {
            it ("should look up method on the child scope's child", function() {
                $testScope = new TestScope();
                $testScope->addChildScope(new TestChildScope());
                $this->scope->addChildScope($testScope);
                $evenNumber = $this->scope->getEvenNumber();
                assert($evenNumber === 4, "expected scope to look up child scope's child method");
            });

            context("and multiple scopes have been mixed in, one of which has a child", function() {
                it ("should look up the child scope on the sibling", function() {
                    $testScope = new TestScope();
                    $testSibling = new TestSiblingScope();
                    $testChild = new TestChildScope();
                    $testSibling->addChildScope($testChild);
                    $this->scope->addChildScope($testScope);
                    $this->scope->addChildScope($testSibling);

                    $number = $this->scope->getNumber();
                    $evenNumber = $this->scope->getEvenNumber();
                    $oddNumber = $this->scope->getOddNumber();

                    assert($number === 5, "expected result of TestScope::getNumber()");
                    assert($evenNumber === 4, "expected result of TestChildScope::getEvenNumber()");
                    assert($oddNumber === 3, "expected result of TestSiblingScope::getOddNumber()");
                });
            });
        });

        context("when mixing in multiple scopes", function() {
            it ("should look up methods for sibling scopes", function() {
                $this->scope->addChildScope(new TestScope());
                $this->scope->addChildScope(new TestChildScope());
                $evenNumber = $this->scope->getEvenNumber();
                $number = $this->scope->getNumber();
                assert($evenNumber === 4, "expected scope to look up child method getEvenNumber()");
                assert($number === 5, "expected scope to look up child method getNumber()");
            });
        });
    });

    context('when calling a mixed in property', function() {
        it('should throw an exception when property not found', function() {
            $exception = null;
            try {
                $this->scope->nope;
            } catch (\Exception $e) {
                $exception = $e;
            }
            assert(!is_null($exception), 'exception should not be null');
        });

        context('and the desired property is on a parent scope', function () {
            it('should look up the property on the scope parent', function () {
                $parent = new Scope();
                $child = new Scope();
                $parent->parentProperty = 'value';

                $parent->addChildScope($child);

                assert($child->parentProperty === 'value', 'expected child to be able to look at parent scope');
            });
        });

        context("and the desired property is on a child scope's child", function() {
            it ("should look up property on the child scope's child", function() {
                $testScope = new TestScope();
                $testScope->addChildScope(new TestChildScope());
                $this->scope->addChildScope($testScope);
                $surname = $this->scope->surname;
                assert($surname === "scaturro", "expected scope to look up child scope's child property");
            });

            context("when mixing in multiple scopes, one of which has a child", function() {
                it ("should look up the child scope on the sibling", function() {
                    $testScope = new TestScope();
                    $testSibling = new TestSiblingScope();
                    $testChild = new TestChildScope();
                    $testSibling->addChildScope($testChild);
                    $this->scope->addChildScope($testScope);
                    $this->scope->addChildScope($testSibling);

                    $name = $this->scope->name;
                    $middle = $this->scope->middleName;
                    $surname = $this->scope->surname;

                    assert($name === "brian", "expected result of TestScope::name");
                    assert($middle == "zooooom", "expected result of TestSiblingScope::middleName");
                    assert($surname === "scaturro", "expected result of TestChildScope::surname");
                });
            });
        });

        context("when mixing in multiple scopes", function() {
            it ("should look up properties for sibling scopes", function() {
                $this->scope->addChildScope(new TestScope());
                $this->scope->addChildScope(new TestChildScope());
                $name = $this->scope->name;
                $surname = $this->scope->surname;
                assert($name === "brian", "expected result of TestScope::name");
                assert($surname === "scaturro", "expected result of TestChildScope::surname");
            });
        });
    });
});

class TestScope extends Scope
{
    public $name = "brian";

    public $data;

    public function __construct()
    {
        $this->data = ['one' => 1, 'two' => 2];
    }

    public function getNumber()
    {
        return 5;
    }
}

class TestChildScope extends Scope
{
    public $surname = "scaturro";

    public function getEvenNumber()
    {
        return 4;
    }
}

class TestSiblingScope extends Scope
{
    public $middleName = "zooooom";

    public function getOddNumber()
    {
        return 3;
    }
}
