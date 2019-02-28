<?php

namespace RefactoringGuru\Singleton\Conceptual;

/**
 * EN: Singleton Design Pattern
 *
 * Intent: Lets you ensure that a class has only one instance, while providing a
 * global access point to this instance.
 *
 * RU: Паттерн Одиночка
 *
 * Назначение: Гарантирует, что у класса есть только один экземпляр, и
 * предоставляет к нему глобальную точку доступа.
 */

/**
 * EN: The Singleton class defines the `getInstance` method that lets clients
 * access the unique singleton instance.
 *
 * RU: Класс Одиночка предоставляет метод getInstance, который позволяет
 * клиентам получить доступ к уникальному экземпляру одиночки.
 */
class Singleton
{
    private static $instances = [];

    /**
     * EN: The Singleton's constructor should always be private to prevent
     * direct construction calls with the `new` operator.
     *
     * RU: Конструктор Одиночки всегда должен быть скрытым, чтобы предотвратить
     * создание объекта через оператор new.
     */
    protected function __construct() { }

    /**
     * EN: Singletons should not be cloneable.
     *
     * RU: Одиночки не должны быть клонируемыми.
     */
    protected function __clone() { }

    /**
     * EN: Singletons should not be restorable from strings.
     *
     * RU: Одиночки не должны быть восстанавливаемыми из строк.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * EN: The static method that controls the access to the singleton instance.
     *
     * This implementation let you subclass the Singleton class while keeping
     * just one instance of each subclass around.
     *
     * RU: Статический метод, управляющий доступом к экземпляру одиночки.
     *
     * Эта реализация позволяет вам расширять класс Одиночки, сохраняя повсюду
     * только один экземпляр каждого подкласса.
     */
    public static function getInstance(): Singleton
    {
        $cls = static::class;
        if (!isset(static::$instances[$cls])) {
            static::$instances[$cls] = new static;
        }

        return static::$instances[$cls];
    }

    /**
     * EN: Finally, any singleton should define some business logic, which can
     * be executed on its instance.
     *
     * RU: Наконец, любой одиночка должен содержать некоторую бизнес-логику,
     * которая может быть выполнена на его экземпляре.
     */
    public function someBusinessLogic()
    {
        // ...
    }
}

/**
 * EN: The client code.
 *
 * RU: Клиентский код.
 */
function clientCode()
{
    $s1 = Singleton::getInstance();
    $s2 = Singleton::getInstance();
    if ($s1 === $s2) {
        echo "Singleton works, both variables contain the same instance.";
    } else {
        echo "Singleton failed, variables contain different instances.";
    }
}

clientCode();
