<?php

namespace RefactoringGuru\Facade\Structure;

/**
 * Facade Design Pattern
 *
 * Intent: Provide a unified interface to a set of interfaces in a subsystem.
 * Facade defines a higher-level interface that makes the subsystem easier
 * to use.
 */

/**
 * Facade provides simple interface for the complex logic of one or
 * multiple subsystems. Facade delegate client requests to appropriate
 * subsystem objects.
 *
 * Usually, Facade knows which subsystem classes are responsible for a
 * request and able to manage their lifecycle. This way the client won't be
 * exposed to any subsystem classes.
 */
class Facade
{
    protected $subsystem1;
    protected $subsystem2;

    /**
     * Facade can either work with existing subsystems objects or create them
     * on its own.
     */
    public function __construct(Subsystem1 $subsystem1 = null,
                                Subsystem2 $subsystem2 = null)
    {
        $this->subsystem1 = $subsystem1 ?: new Subsystem1();
        $this->subsystem2 = $subsystem2 ?: new Subsystem2();
    }

    /**
     * Facade's methods are convenient shortcuts to the complex functionality of
     * subsystems. On the other hand, client gets access only to a fraction of
     * subsystem's capabilities.
     */
    public function operation()
    {
        $result = "Facade initializes subsystems:\n";
        $result .= $this->subsystem1->operation1();
        $result .= $this->subsystem2->operation1();
        $result .= "Facade orders subsystems to perform the action:\n";
        $result .= $this->subsystem1->operationN();
        $result .= $this->subsystem2->operationZ();
        return $result;
    }
}

/**
 * Subsystem handles the work assigned by a Facade object. Subsystems are not
 * aware about facade objects.
 */
class Subsystem1
{
    function operation1()
    {
        return "Sybsystem1: Ready!\n";
    }

    // ...

    function operationN()
    {
        return "Sybsystem1: Go!\n";
    }
}

/**
 * Single facade can work with multiple subsystems.
 */
class Subsystem2
{
    public function operation1()
    {
        return "Sybsystem2: Get ready!\n";
    }

    // ...

    public function operationZ()
    {
        return "Sybsystem2: Fire!\n";
    }
}

/**
 * Client code works with complex subsystems through a simple interface provided
 * by the Facade. Client might not even know that there's any subsystem, if
 * facade manages lifecycle of the subsystem on its own. That helps to keep
 * complexity under control.
 */
function clientCode(Facade $facade)
{
    // ...

    print($facade->operation());

    // ...
}

/**
 * Client code may already have the subsystem objects created that could be
 * passed to a facade object.
 */
$subsystem1 = new Subsystem1();
$subsystem2 = new Subsystem2();
$facade = new Facade($subsystem1, $subsystem2);
clientCode($facade);
