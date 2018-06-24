<?php

namespace RefactoringGuru\Singleton\Structural;

/**
 * EN: Singleton Design Pattern
 *
 * Intent: Ensure that a class has a single instance, and provide a global point
 * of access to it.
 *
 * RU: Паттерн Одиночка
 * Назначение: Гарантирует существование единственного экземпляра класса и предоставляет
 * глобальную точку доступа к нему.
 */

/**
 * EN:
 * The Singleton class defines the `getInstance` method that lets clients access
 * the unique singleton instance.
 *
 * RU:
 * Класс Одиночка предоставляет метод getInstance, который позволяет клиентам получить доступ
 * к уникальному экземпляру одиночки.
 */
class Singleton
{
    private static $instances = [];

    /**
     * EN:
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     *
     * RU:
     * Конструктор Одиночки всегда должен быть скрытым, чтобы предотвратить 
     * прямое строительство, вызванное оператором new.
     */
    protected function __construct() { }

    /**
     * EN:
     * Singletons should not be cloneable.
     *
     * RU:
     * Одиночки не должны быть клонируемыми.
     */
    protected function __clone() { }

    /**
     * EN:
     * Singletons should not be restorable from strings.
     *
     * RU:
     * Одиночки не должны быть восстанавливаемыми из строк.
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
