<?php

namespace RefactoringGuru\AbstractFactory\Structural;

/**
 * Abstract Factory Design Pattern
 *
 * Intent: Provide an interface for creating families of related or dependent
 * objects without specifying their concrete classes.
 */

/**
 * The Abstract Factory interface declares a set of methods that return
 * different abstract products. These products are related and called a family.
 * Products of one family are usually able to collaborate among themselves. A
 * family of products may have several variations, but the products of one
 * variation are incompatible with products of another.
 */
interface AbstractFactory
{
    public function createProductA(): AbstractProductA;

    public function createProductB(): AbstractProductB;
}

/**
 * Concrete Factories produce a family of products that belong to a single
 * variation. The factory guarantees that resulting products are compatible.
 * Note that signatures of the Concrete Factory's methods return an abstract
 * product, while inside the method a concrete product is instantiated.
 */
class ConcreteFactory1 implements AbstractFactory
{
    public function createProductA(): AbstractProductA
    {
        return new ConcreteProductA1();
    }

    public function createProductB(): AbstractProductB
    {
        return new ConcreteProductB1();
    }
}

/**
 * Each concrete factory has a corresponding product variation.
 */
class ConcreteFactory2 implements AbstractFactory
{
    public function createProductA(): AbstractProductA
    {
        return new ConcreteProductA2();
    }

    public function createProductB(): AbstractProductB
    {
        return new ConcreteProductB2();
    }
}

/**
 * Each distinct product of a product family should have a base interface. All
 * variations of the product must implement this interface.
 */
interface AbstractProductA
{
    public function usefulFunctionA();
}

/**
 * Concrete Products are created by corresponding Concrete Factories.
 */
class ConcreteProductA1 implements AbstractProductA
{
    public function usefulFunctionA()
    {
        return "The result of the product A1.";
    }
}

class ConcreteProductA2 implements AbstractProductA
{
    public function usefulFunctionA()
    {
        return "The result of the product A2.";
    }
}

/**
 * The base interface of another product. All products can interact with each
 * other, but proper interaction is possible only between products of the same
 * concrete variation.
 */
interface AbstractProductB
{
    /**
     * The ProductB is able to do its own thing...
     */
    public function usefulFunctionB();

    /**
     * ...but it also can collaborate with the ProductA.
     *
     * The Abstract Factory makes sure that all products it creates are of the
     * same variation and thus, compatible.
     */
    public function anotherUsefulFunctionB(AbstractProductA $collaborator);
}

/**
 * Concrete Products are created by corresponding Concrete Factories.
 */
class ConcreteProductB1 implements AbstractProductB
{
    public function usefulFunctionB()
    {
        return "The result of the product B1.";
    }

    /**
     * The product B1 is only able to work correctly with the product A1.
     * Nevertheless, it accepts any instance of Abstract Product A as an
     * argument.
     */
    public function anotherUsefulFunctionB(AbstractProductA $collaborator)
    {
        $result = $collaborator->usefulFunctionA();

        return "The result of the B1 collaborating with the ({$result})";
    }
}

class ConcreteProductB2 implements AbstractProductB
{
    public function usefulFunctionB()
    {
        return "The result of the product B2.";
    }

    /**
     * The product B2 is only able to work correctly with the product A2.
     * Nevertheless, it accepts any instance of Abstract Product A as an
     * argument.
     */
    public function anotherUsefulFunctionB(AbstractProductA $collaborator)
    {
        $result = $collaborator->usefulFunctionA();

        return "The result of the B2 collaborating with the ({$result})";
    }
}

/**
 * The client code works with factories and products only through abstract
 * types: AbstractFactory and AbstractProduct. It lets you pass any factory or
 * product subclass to the client code without breaking it.
 */
function clientCode(AbstractFactory $factory)
{
    $product_a = $factory->createProductA();
    $product_b = $factory->createProductB();

    print($product_b->usefulFunctionB() . "\n");
    print($product_b->anotherUsefulFunctionB($product_a) . "\n");
}

/**
 * The client code can work with any concrete factory class.
 */
print("Client: Testing client code with the first factory type...\n");
clientCode(new ConcreteFactory1());
print("\n\n");

print("Client: Testing the same client code with the second factory type...\n");
clientCode(new ConcreteFactory2());
