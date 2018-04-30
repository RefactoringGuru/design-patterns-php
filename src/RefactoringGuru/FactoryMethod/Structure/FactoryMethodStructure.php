<?php

namespace RefactoringGuru\FactoryMethod\Structure;

/**
 * Factory Method Design Pattern
 *
 * Intent: Define an interface for creating an object, but let subclasses decide
 * which class to instantiate. Factory Method lets a class defer
 * instantiation to subclasses.
 */

/**
 * Declare the factory method, which returns an object of type Product.
 */
abstract class Creator
{
    /**
     * Creator may also define a default implementation of the factory
     * method that returns a default ConcreteProduct object.
     */
    public abstract function factoryMethod(): Product;

    /**
     * Creator class should have some primary business logic. Factory method
     * acts just as a helper in such code.
     */
    public function someOperation(): string
    {
        // Call the factory method to create a Product object.
        $product = $this->factoryMethod();
        // Now, use product.
        $result = "Same creator's code worked with: " . $product->operation();
        return $result;
    }
}

/**
 * Override the factory method to return an instance of a ConcreteProduct1.
 */
class ConcreteCreator1 extends Creator
{
    public function factoryMethod(): Product
    {
        return new ConcreteProduct1();
    }
}

/**
 * Override the factory method to return an instance of a ConcreteProduct2.
 */
class ConcreteCreator2 extends Creator
{
    public function factoryMethod(): Product
    {
        return new ConcreteProduct2();
    }
}

/**
 * Define the interface of objects the factory method creates.
 */
interface Product
{
    public function operation();
}

/**
 * Implement the Product interface.
 */
class ConcreteProduct1 implements Product
{
    public function operation()
    {
        return "Result of ConcreteProduct1";
    }
}

/**
 * Implement the Product interface.
 */
class ConcreteProduct2 implements Product
{
    public function operation()
    {
        return "Result of ConcreteProduct2";
    }
}

/**
 * Client code produces a concrete creator object of certain kind instead of
 * base creator's class. As long as client works with creators using
 * base interface, you can make it work with any creator subclass.
 */
function clientCode(Creator $creator)
{
    //...
    print($creator->someOperation());
    //...
}

/**
 * Application picks a creator's type depending on configuration or
 * environment.
 */
print("Testing ConcreteCreator1:\n");
clientCode(new ConcreteCreator1());
print("\n\n");

print("Testing ConcreteCreator2:\n");
clientCode(new ConcreteCreator2());
