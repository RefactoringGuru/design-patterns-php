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
 * The Abstraction defines the interface for the "control" part of the two class hierarchies.
 * It maintains a reference to a Implementor object and delegates it
 * all of the real work.
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
        return "Abstraction: Base operation with:\n".
            $this->implementor->operationImplementation();
    }
}

/**
 * You can extend the Abstraction without changing the Implementor classes.
 */
class ExtendedAbstraction extends Abstraction
{
    public function operation()
    {
        return "ExtendedAbstraction: Extended operation with:\n".
            $this->implementor->operationImplementation();
    }
}

/**
 * The Implementor defines the interface for all implementation classes. This
 * interface doesn't have to match the methods of the Abstraction's interface.
 * In fact the two interfaces can be quite different. Typically the Implementor
 * interface provides only primitive operations, while the Abstraction defines
 * higher-level operations based on those primitives.
 */
interface Implementor
{
    public function operationImplementation();
}

/**
 * Each Concrete Implementor corresponds to the specific platform and implements the Implementor interface using that platform's API.
 */
class ConcreteImplementorA implements Implementor
{
    public function operationImplementation()
    {
        return "ConcreteImplementorA: The result in platform A.\n";
    }
}

class ConcreteImplementorB implements Implementor
{
    public function operationImplementation()
    {
        return "ConcreteImplementorB: The result in platform B.\n";
    }
}

/**
 * Except for an initialization phase, where an abstraction is linked with a
 * specific implementation object, the client code should only work directly
 * with the abstraction objects. This way the client code will be able to
 * support with any abstraction-implementation combination.
 */
function clientCode(Abstraction $abstraction)
{
    // ...

    print($abstraction->operation());

    // ...
}

/**
 * The client code should be able to run with any pre-configured
 * abstraction-implementation combination.
 */
$implementation = new ConcreteImplementorA();
$abstraction = new Abstraction($implementation);
clientCode($abstraction);

print("\n");

$implementation = new ConcreteImplementorB();
$abstraction = new ExtendedAbstraction($implementation);
clientCode($abstraction);