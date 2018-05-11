<?php

namespace RefactoringGuru\Singleton\Structure;

/**
 * Singleton Design Pattern
 *
 * Intent: Ensure that class has a single instance, and provide a global
 * point of access to it.
 */

/**
 * The Singleton class defines the `getInstance` method that lets clients access
 * the unique singleton instance.
 */
class Singleton
{
    private static $instances = [];

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    protected function __construct() { }

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone() { }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * The static method that controls the access to the singleton instance.
     *
     * This implementation let you subclass the Singleton class while keeping
     * just one instance of each subclass around.
     */
    public static function getInstance(): Singleton
    {
        $cls = get_called_class();
        if (! isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
        }

        return self::$instances[$cls];
    }

    /**
     * Finally, any singleton should define some business logic, which can be
     * executed on its instance.
     */
    public function someBusinessLogic()
    {
        // ...
    }
}

/**
 * The client code.
 */
function clientCode()
{
    $s1 = Singleton::getInstance();
    $s2 = Singleton::getInstance();
    if ($s1 === $s2) {
        print("Singleton works, both variables contain the same instance.");
    } else {
        print("Singleton failed, variables contain different instances.");
    }
}

clientCode();