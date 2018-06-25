<?php

namespace RefactoringGuru\TemplateMethod\Structural;

/**
 * EN: Template Method Design Pattern
 *
 * Intent: Define the skeleton of an algorithm in operation, deferring some
 * steps to subclasses. Template Method lets subclasses redefine specific steps
 * of an algorithm without changing the algorithm's structure.
 * 
 * RU: Паттерн Шаблонный метод
 * 
 * Назначение: Определяет общую схему алгоритма в действии, перекладывая некоторые шаги
 * на подклассы. Шаблонный метод позволяет подклассам переопределять отдельные шаги алгоритма
 * без изменения структуры алгоритма.
 */

/**
 * EN:
 * The Abstract Class defines a template method that contains a skeleton of some
 * algorithm, composed of calls to (usually) abstract primitive operations.
 *
 * Concrete subclasses should implement these operations, but leave the template
 * method itself intact.
 *
 * RU:
 * Абстрактный Класс определяет шаблонный метод, содержащий скелет некоторого алгоритма,
 * состоящего из вызовов (обычно) абстрактных примитивных операций.
 *
 * Конкретные подклассы должны реализовать эти операции, но оставить сам метод шаблона
 * без изменений.
 */
abstract class AbstractClass
{
    /**
     * EN:
     * The template method defines the skeleton of an algorithm.
     *
     * RU:
     * Шаблонный метод определяет скелет алгоритма.
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
     * These operations already have implementations.
     */
    protected function baseOperation1()
    {
        print("AbstractClass says: I am doing the bulk of the work\n");
    }

    protected function baseOperation2()
    {
        print("AbstractClass says: But I let subclasses override some operations\n");
    }

    protected function baseOperation3()
    {
        print("AbstractClass says: But I am doing the bulk of the work anyway\n");
    }

    /**
     * These operations have to be implemented in subclasses.
     */
    protected abstract function requiredOperations1();

    protected abstract function requiredOperation2();

    /**
     * These are "hooks." Subclasses may override them, but it's not mandatory
     * since the hooks already have default (but empty) implementation. Hooks
     * provide additional extension points in some crucial places of the
     * algorithm.
     */
    protected function hook1() { }

    protected function hook2() { }
}

/**
 * Concrete classes have to implement all abstract operations of the base class.
 * They can also override some operations with a default implementation.
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
 * Usually, concrete classes override only a fraction of base class' operations.
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
 * The client code calls the template method to execute the algorithm. Client
 * code does not have to know the concrete class of an object it works with, as
 * long as it works with objects through the interface of their base class.
 */
function clientCode(AbstractClass $class)
{
    // ...
    $class->templateMethod();
    // ...
}

print("Same client code can work with different subclasses:\n");
clientCode(new ConcreteClass1());
print("\n");

print("Same client code can work with different subclasses:\n");
clientCode(new ConcreteClass2());
