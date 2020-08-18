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
 * EN: The Singleton class defines the `GetInstance` method that serves as an
 * alternative to constructor and lets clients access the same instance of this
 * class over and over.
 *
 * RU: Класс Одиночка предоставляет метод `GetInstance`, который ведёт себя как
 * альтернативный конструктор и позволяет клиентам получать один и тот же
 * экземпляр класса при каждом вызове.
 */
class Singleton
{
    /**
     * EN: The Singleton's instance is stored in a static field. This field is
     * an array, because we'll allow our Singleton to have subclasses. Each item
     * in this array will be an instance of a specific Singleton's subclass.
     * You'll see how this works in a moment.
     *
     * RU: Объект одиночки храниться в статичном поле класса. Это поле — массив,
     * так как мы позволим нашему Одиночке иметь подклассы. Все элементы этого
     * массива будут экземплярами кокретных подклассов Одиночки. Не волнуйтесь,
     * мы вот-вот познакомимся с тем, как это работает.
     */
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
     * EN: This is the static method that controls the access to the singleton
     * instance. On the first run, it creates a singleton object and places it
     * into the static field. On subsequent runs, it returns the client existing
     * object stored in the static field.
     *
     * This implementation lets you subclass the Singleton class while keeping
     * just one instance of each subclass around.
     *
     * RU: Это статический метод, управляющий доступом к экземпляру одиночки.
     * При первом запуске, он создаёт экземпляр одиночки и помещает его в
     * статическое поле. При последующих запусках, он возвращает клиенту объект,
     * хранящийся в статическом поле.
     *
     * Эта реализация позволяет вам расширять класс Одиночки, сохраняя повсюду
     * только один экземпляр каждого подкласса.
     */
    public static function getInstance(): Singleton
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
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
