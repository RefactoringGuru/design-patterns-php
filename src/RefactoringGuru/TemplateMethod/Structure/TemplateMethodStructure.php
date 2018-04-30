<?php

namespace RefactoringGuru\TemplateMethod\Structure;

/**
 * Template Method Design Pattern
 *
 * Intent: Define the skeleton of an algorithm in an operation, deferring some
 * steps to subclasses. Template Method lets subclasses redefine certain
 * steps of an algorithm without changing the algorithm's structure.
 */

/**
 * AbstractClass defines a template method that contains skeleton of some
 * algorithm, composed of calls to (usually) abstract primitive operations.
 *
 * Concrete subclasses should provide the implementations for steps but the
 * leave the template method intact.
 */
abstract class AbstractClass
{
    /**
     * Template method defines the skeleton of an algorithm.
     *
     * The template method calls primitive operations as well as operations
     * defined in AbstractClass or those of other objects.
     */
    final public function templateMethod()
    {
        $this->baseOperation1();
        $this->requiredOperations1();
        $this->baseOperation2();
        $this->hook1();
        $this->requiredOperation2();
        $this->baseOperation3();
        $this->hook2();
    }

    /**
     * These operations already do something in a base class.
     */
    protected function baseOperation1()
    {
        print("AbstractClass says: I am doing bulk of the work\n");
    }

    protected function baseOperation2()
    {
        print("AbstractClass says: But I let subclasses to override some operations\n");
    }

    protected function baseOperation3()
    {
        print("AbstractClass says: But I am doing bulk of the work anyway\n");
    }

    /**
     * These operations have to be implemented in subclasses.
     */
    protected abstract function requiredOperations1();

    protected abstract function requiredOperation2();

    /**
     * These are "hooks". They may be overridden by subclasses, but it's
     * not mandatory, since hooks already have default (but empty)
     * implementation. Hooks provide additional extension points in some crucial
     * places of the algorithm.
     */
    protected function hook1() { }

    protected function hook2() { }
}

/**
 * Implements the primitive operations to carry out subclass-specific steps of
 * the algorithm.
 */
class ConcreteClass1 extends AbstractClass
{
    protected function requiredOperations1()
    {
        print("ConcreteClass1 says: Implemented Operation1\n");
    }

    protected function requiredOperation2()
    {
        print("ConcreteClass1 says: Implemented Operation2\n");
    }
}

/**
 * Usually concrete classes override only fraction of base class' steps.
 */
class ConcreteClass2 extends AbstractClass
{
    protected function requiredOperations1()
    {
        print("ConcreteClass2 says: Implemented Operation1\n");
    }

    protected function requiredOperation2()
    {
        print("ConcreteClass2 says: Implemented Operation2\n");
    }

    protected function hook1()
    {
        print("ConcreteClass2 says: Overridden Hook1\n");
    }
}

/**
 * Client code calls the template method to execute the algorithm. Client
 * code does not have to know the concrete class of object it works with, as
 * long as it works with objects through the interface of their base class.
 */
function clientCode(AbstractClass $class)
{
    // ...
    $class->templateMethod();
    //...
}

print("Same client code can work with different subclasses:\n");
clientCode(new ConcreteClass1());
print("\n");

print("Same client code can work with different subclasses:\n");
clientCode(new ConcreteClass2());