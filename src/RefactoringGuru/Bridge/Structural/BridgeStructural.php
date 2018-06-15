<?php

namespace RefactoringGuru\Bridge\Structural;

/**
 * EN: Bridge Design Pattern
 *
 * Intent: Decouple an abstraction from its implementation so that the two can
 * vary independently.
 *
 * RU: Паттерн Мост
 *
 * Назначение: Разделяет абстракцию и реализацию, что позволяет изменять их 
 * независимо друг от друга.
 *
 *               A
 *            /     \                        A         N
 *          Aa      Ab        ===>        /     \     / \
 *         / \     /  \                 Aa(N) Ab(N)  1   2
 *         Aa1 Aa2  Ab1 Ab2
 *
 */

/**
 * EN:
 * The Abstraction defines the interface for the "control" part of the two class
 * hierarchies. It maintains a reference to an object of the Implementation
 * hierarchy and delegates all of the real work to this object.
 *
 * RU:
 * Абстракция устанавливает интерфейс для «управляющей» части двух иерархий классов.
 * Она содержит ссылку на объект иерархии Реализации и делегирует всю настоящую работу этому объекту.
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
 * EN:
 * You can extend the Abstraction without changing the Implementation classes.
 *
 * RU:
 * Можно расширить Абстракцию без изменения классов Реализации.
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
 * EN:
 * The Implementation defines the interface for all implementation classes. It
 * doesn't have to match the Abstraction's interface. In fact, the two
 * interfaces can be entirely different. Typically the Implementation interface
 * provides only primitive operations, while the Abstraction defines higher-
 * level operations based on those primitives.
 *
 * RU:
 * Реализация устанавливает интерфейс для всех классов реализации.
 * Он не должен соответствовать интерфейсу Абстракции.
 * На практике оба интерфейса могут быть совершенно разными.
 * Как правило, интерфейс Реализации предоставляет только примитивные операции, 
 * в то время как Абстракция определяет операции более высокого уровня,
 * основанные на этих примитивах.
 */
interface Implementation
{
    public function operationImplementation();
}

/**
 * EN:
 * Each Concrete Implementation corresponds to a specific platform and
 * implements the Implementation interface using that platform's API.
 * 
 * RU:
 * Каждая Конкретная Реализация соответствует определенной платформе 
 * и реализует интерфейс Реализации с использованием API этой платформы.
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
 * EN:
 * Except for the initialization phase, where an Abstraction object gets linked
 * with a specific Implementation object, the client code should only depend on
 * the Abstraction class. This way the client code can support any abstraction-
 * implementation combination.
 *
 * RU:
 * За исключением этапа инициализации, когда объект Абстракции связывается
 * с определенным объектом Реализации, клиентский код должен зависеть 
 * только от класса Абстракции. Таким образом, клиентский код может поддерживать 
 * любую комбинацию абстракции и реализации.
 */
function clientCode(Abstraction $abstraction)
{
    // ...

    print($abstraction->operation());

    // ...
}

/**
 * EN:
 * The client code should be able to run with any pre-configured abstraction-
 * implementation combination.
 *
 * RU:
 * Клиентский код должен работать с любой предварительно сконфигурированной
 * комбинацией абстракции и реализации
 */
$implementation = new ConcreteImplementationA();
$abstraction = new Abstraction($implementation);
clientCode($abstraction);

print("\n");

$implementation = new ConcreteImplementationB();
$abstraction = new ExtendedAbstraction($implementation);
clientCode($abstraction);
