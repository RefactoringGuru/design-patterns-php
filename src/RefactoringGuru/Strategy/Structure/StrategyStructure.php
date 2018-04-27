<?php

namespace RefactoringGuru\Strategy\Structure;

/**
 * Strategy Design Pattern
 *
 * Intent: Define a family of algorithms, encapsulate each one, and make them
 * interchangeable. Strategy lets the algorithm vary independently from
 * clients that use it.
 */

/**
 * Context defines the interface of interest to clients.
 */
class Context
{
    /**
     * Context maintains a reference to a Strategy object. Context does not
     * know the concrete class of a Strategy object. It works with Strategies
     * through a common interface.
     * @var Strategy
     */
    private $strategy;

    /**
     * Usually context accepts strategy in constructor, but provides a way
     * top change it in runtime.
     * @param Strategy $strategy
     */
    public function __constructor(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * Usually context allows changing strategy object in run time.
     * @param Strategy $strategy
     */
    public function setStrategy(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * Context delegates work to a strategy object instead of implementing
     * multiple versions of the algorithm on its own.
     */
    public function doSomeBusinessLogic()
    {
        //...

        echo "Context sort its data using strategy:\n";
        $result = $this->strategy->doAlgorithm(["a", "b", "c", "d", "e"]);
        echo implode(",", $result) . "\n";

        //...
    }
}

/**
 * Strategy interface declares operations common to all supported
 * versions of some algorithm.
 *
 * Context uses this interface to call the algorithm defined by
 * ConcreteStrategies.
 */
interface Strategy
{
    public function doAlgorithm($data);
}

/**
 * Concrete strategies implement the algorithm using the common Strategy
 * interface. This makes them interchangeable in the context.
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
 * Client code picks concrete strategy and passes it to the context. Client
 * should be aware of the differences between strategies in order to make the
 * right choice.
 */
$context = new Context();

echo "[Strategy is set to normal sorting]\n";
$context->setStrategy(new ConcreteStrategyA());
$context->doSomeBusinessLogic();
echo "\n";

echo "[Strategy is set to reverse sorting]\n";
$context->setStrategy(new ConcreteStrategyB());
$context->doSomeBusinessLogic();
