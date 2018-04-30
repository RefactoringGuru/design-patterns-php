<?php

namespace RefactoringGuru\Bridge\Structure;

/**
 * Bridge Design Pattern
 *
 * Intent: Decouple an abstraction from its implementation so that the two
 * can vary independently.
 *
 *             A
 *          /     \                        A         N
 *        Aa      Ab        ===>        /     \     / \
 *       / \     /  \                 Aa(N) Ab(N)  1   2
 *     Aa1 Aa2  Ab1 Ab2
 */

/**
 * Abstraction defines interface for the control part of two class hierarchies.
 * Abstraction maintains a reference to the Implementor object and delegates
 * any real work to it.
 */
class Abstraction
{
    /**
     * @var Implementor
     */
    protected $implementor;

    public function __construct(Implementor $implementor)
    {
        $this->implementor = $implementor;
    }

    public function operation()
    {
        return "Base operation with:\n" .
            $this->implementor->operationImplementation();
    }
}

/**
 * Abstraction can be easily extended without touching implementation classes.
 */
class ExtendedAbstraction extends Abstraction
{
    public function operation()
    {
        return "Extended operation with:\n" .
            $this->implementor->operationImplementation();
    }
}

/**
 * Implementor defines the interface for all implementation classes. This
 * interface doesn't have to correspond exactly to Abstraction's interface.
 * In fact the two interfaces can be quite different. Typically the Implementor
 * interface provides only primitive operations, and Abstraction defines
 * higher-level operations based on these primitives.
 */
interface Implementor
{
    public function operationImplementation();
}

/**
 * Each Concrete Implementor provides implementation for a specific platform.
 */
class ConcreteImplementorA implements Implementor
{
    public function operationImplementation()
    {
        return "Result of ConcreteImplementorA in platform A.\n";
    }
}

/**
 * Each Concrete Implementor provides implementation for a specific platform.
 */
class ConcreteImplementorB implements Implementor
{
    public function operationImplementation()
    {
        return "Result of ConcreteImplementorB in platform B.\n";
    }
}

/**
 * In most cases, Client code should only work with abstractions. It allows
 * supporting any abstraction-implementation combination as long as the
 * client code work with abstraction through a base class (though it is not
 * strictly required).
 *
 * The Client code may be aware of implementor classes only if abstractions
 * should be able to change implementations in run time.
 */
function clientCode(Abstraction $abstraction)
{
    // ...

    print($abstraction->operation());

    // ...
}

/**
 * Client code can be launched with any pre-configured
 * abstraction-implementation combination.
 */
$implementation = new ConcreteImplementorA();
$abstraction = new Abstraction($implementation);
clientCode($abstraction);

print("\n");

$implementation = new ConcreteImplementorB();
$abstraction = new ExtendedAbstraction($implementation);
clientCode($abstraction);