<?php

namespace RefactoringGuru\Bridge\Structure;

/**
 * Bridge Design Pattern
 *
 * Intent: Decouple an abstraction from its implementation so that the two can
 * vary independently.
 *
 *               A
 *            /     \                        A         N
 *          Aa      Ab        ===>        /     \     / \
 *         / \     /  \                 Aa(N) Ab(N)  1   2
 *         Aa1 Aa2  Ab1 Ab2
 */

/**
 * The Abstraction defines the interface for the "control" part of the two class
 * hierarchies. It maintains a reference to an object of the Implementation
 * hierarchy and delegates it all of the real work.
 */
class Abstraction
{
    /**
     * @var Implementation
     */
    protected $implementation;

    public function __construct(Implementation $implementation)
    {
        $this->implementation = $implementation;
    }

    public function operation()
    {
        return "Abstraction: Base operation with:\n".
            $this->implementation->operationImplementation();
    }
}

/**
 * You can extend the Abstraction without changing the Implementation classes.
 */
class ExtendedAbstraction extends Abstraction
{
    public function operation()
    {
        return "ExtendedAbstraction: Extended operation with:\n".
            $this->implementation->operationImplementation();
    }
}

/**
 * The Implementation defines the interface for all implementation classes. This
 * interface doesn't have to match the methods of the Abstraction's interface.
 * In fact the two interfaces can be quite different. Typically the
 * Implementation interface provides only primitive operations, while the
 * Abstraction defines higher-level operations based on those primitives.
 */
interface Implementation
{
    public function operationImplementation();
}

/**
 * Each Concrete Implementation corresponds to the specific platform and
 * implements the Implementation interface using that platform's API.
 */
class ConcreteImplementationA implements Implementation
{
    public function operationImplementation()
    {
        return "ConcreteImplementationA: The result in platform A.\n";
    }
}

class ConcreteImplementationB implements Implementation
{
    public function operationImplementation()
    {
        return "ConcreteImplementationB: The result in platform B.\n";
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
 * The client code should be able to run with any pre-configured abstraction-
 * implementation combination.
 */
$implementation = new ConcreteImplementationA();
$abstraction = new Abstraction($implementation);
clientCode($abstraction);

print("\n");

$implementation = new ConcreteImplementationB();
$abstraction = new ExtendedAbstraction($implementation);
clientCode($abstraction);