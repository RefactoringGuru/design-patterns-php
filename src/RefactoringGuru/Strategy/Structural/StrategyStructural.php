<?php

namespace RefactoringGuru\Strategy\Structural;

/**
 * EN: Strategy Design Pattern
 *
 * Intent: Define a family of algorithms, encapsulate each one, and make them
 * interchangeable. Strategy lets the algorithm vary independently from clients
 * that use it.
 *
 * RU: Паттерн Стратегия
 *
 * Назначение: Определяет семейство алгоритмов, инкапсулирует каждый из них
 * и делает взаимозаменяемыми. Стратегия позволяет изменять алгоритм независимо 
 * от клиентов, которые его используют.
 */

/**
 * EN:
 * The Context defines the interface of interest to clients.
 *
 * RU: 
 * Контекст определяет интерфейс, представляющий интерес для клиентов.
 */
class Context
{
    /**
     * EN:
     * @var Strategy The Context maintains a reference to one of the Strategy
     * objects. The Context does not know the concrete class of a strategy. It
     * should work with all strategies via the Strategy interface.
     *
     * RU:
     * @var Strategy Контекст хранит ссылку на один из объектов Стратегии. 
     * Контекст не знает конкретного класса стратегии. Он должен работать со всеми
     * стратегиями через интерфейс Стратегии.
     */
    private $strategy;

    /**
     * EN:
     * Usually, the Context accepts a strategy through the constructor, but also
     * provides a setter to change it at runtime.
     *
     * RU:
     * Обычно Контекст принимает стратегию через конструктор, а также предоставляет
     * сеттер для изменения её во время выполнения.
     *
     * @param Strategy $strategy
     */
    public function __constructor(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * EN:
     * Usually, the Context allows replacing a Strategy object at runtime.
     *
     * RU:
     * Обычно Контекст позволяет заменить объект Стратегии во время выполнения.
     *
     * @param Strategy $strategy
     */
    public function setStrategy(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * EN:
     * The Context delegates some work to the Strategy object instead of
     * implementing multiple versions of the algorithm on its own.
     *
     * RU:
     *
     */
    public function doSomeBusinessLogic()
    {
        // ...

        print("Context: Sorting data using the strategy (not sure how it'll do it)\n");
        $result = $this->strategy->doAlgorithm(["a", "b", "c", "d", "e"]);
        print(implode(",", $result)."\n");

        // ...
    }
}

/**
 * The Strategy interface declares operations common to all supported versions
 * of some algorithm.
 *
 * The Context uses this interface to call the algorithm defined by Concrete
 * Strategies.
 */
interface Strategy
{
    public function doAlgorithm($data);
}

/**
 * Concrete Strategies implement the algorithm while following the base Strategy
 * interface. The interface makes them interchangeable in the Context.
 */
class ConcreteStrategyA implements Strategy
{
    public function doAlgorithm($data)
    {
        sort($data);

        return $data;
    }
}

class ConcreteStrategyB implements Strategy
{
    public function doAlgorithm($data)
    {
        rsort($data);

        return $data;
    }
}

/**
 * The client code picks a concrete strategy and passes it to the context. The
 * client should be aware of the differences between strategies in order to make
 * the right choice.
 */
$context = new Context();

print("Client: Strategy is set to normal sorting.\n");
$context->setStrategy(new ConcreteStrategyA());
$context->doSomeBusinessLogic();
print("\n");

print("Client: Strategy is set to reverse sorting.\n");
$context->setStrategy(new ConcreteStrategyB());
$context->doSomeBusinessLogic();
