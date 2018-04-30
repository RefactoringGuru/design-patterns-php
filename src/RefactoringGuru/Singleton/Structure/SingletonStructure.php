<?php

namespace RefactoringGuru\Singleton\Structure;

/**
 * Singleton Design Pattern
 *
 * Intent: Ensure that class has a single instance, and provide a global
 * point of access to it.
 */

/**
 * Defines a `getInstance` operation that lets clients access unique singleton
 * instance.
 */
class Singleton
{
    private static $instances = array();

    /**
     * Singleton constructor should always be private to block direct
     * construction call with `new` operator.
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
        throw new \Exception("Cannot unserialize singleton");
    }

    /**
     * Static method, which controls access to singleton instance.
     *
     * This implementation allows creating singleton subclasses and having
     * just one instance of each subclcass.
     */
    public static function getInstance(): Singleton
    {
        $cls = get_called_class();
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
        }
        return self::$instances[$cls];
    }

    /**
     * Finally, singleton has some business logic, which can be executed on
     * its instance.
     */
    public function someBusinessLogic()
    {
        // ...
    }
}

/**
 * Client code.
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