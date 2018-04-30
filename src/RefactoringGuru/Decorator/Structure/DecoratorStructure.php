<?php

namespace RefactoringGuru\Decorator\Structure;

/**
 * Decorator Design Pattern
 *
 * Intent: Attach additional responsibilities to an object dynamically.
 * Decorators provide a flexible alternative to subclassing for extending
 * functionality.
 */

/**
 * The base Component interface Define the interface defines operations that
 * can be altered by decorators.
 */
interface Component
{
    public function operation();
}

/**
 * Concrete components provide default implementations for base component
 * operations.
 */
class ConcreteComponent implements Component
{
    public function operation()
    {
        return "ConcreteComponent";
    }
}

/**
 * The base decorator has the same interface as the other components. Its main
 * purpose is to define a wrapping interface for all concrete decorators.
 */
class Decorator implements Component
{
    /**
     * @var Component
     */
    protected $component;

    public function __construct(Component $component)
    {
        $this->component = $component;
    }

    /**
     * Decorator delegates all work to a wrapped component.
     */
    public function operation()
    {
        return $this->component->operation();
    }
}

/**
 * Concrete decorators fetch the execution result from a wrapped object
 * and alter it in some way.
 */
class ConcreteDecoratorA extends Decorator
{
    /**
     * Decorators may call parent implementation instead of direct call to a
     * wrapped objects. This allows extending decorator classes.
     */
    public function operation()
    {
        return "ConcreteDecoratorA(" . parent::operation() . ")";
    }
}

/**
 * Decorator can execute some code before or after the call to a wrapped object.
 */
class ConcreteDecoratorB extends Decorator
{
    public function operation()
    {
        return "ConcreteDecoratorB(" . parent::operation() . ")";
    }
}

/**
 * The Client code works with all components using the base interface. It's
 * not aware what type of component it works with.
 */
function clientCode(Component $component)
{
    //...

    print("CLIENT SAYS: " . $component->operation());

    //...
}

/**
 * This way Client code can support both simple components...
 */
$simple = new ConcreteComponent();
print("Client code gets a simple component:\n");
clientCode($simple);
print("\n\n");

/**
 * ...as well as the decorated ones.
 *
 * Note how decorators can wrap not only the simple components, but other
 * decorators as well.
 */
$decorator1 = new ConcreteDecoratorA($simple);
$decorator2 = new ConcreteDecoratorB($decorator1);
print("Same client code gets a decorated component:\n");
clientCode($decorator2);