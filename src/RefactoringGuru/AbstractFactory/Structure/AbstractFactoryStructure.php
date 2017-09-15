<?php

namespace RefactoringGuru\AbstractFactory\Structure;

/**
 * Abstract Factory Design Pattern
 *
 * Intent: Provide an interface for creating families of related or dependent
 * objects without specifying their concrete classes.
 */

/**
 * Base AbstractFactory interface declares operations that create abstract
 * product objects.
 *
 * Abstract factory can produce a whole product family: several related
 * types of products. Products of one family can even collaborate between
 * themselves.
 */
interface AbstractFactory
{
    public function createProductA(): AbstractProductA;

    public function createProductB(): AbstractProductB;
}

/**
 * Concrete factories create product family of a single variation.
 * This guarantees that resulting products can collaborate.
 *
 * Note that concrete factory returns abstract products, even though it
 * creates concrete product objects. This will make factories interchangeable.
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
 * Concrete factories create product family of a single variation.
 * This guarantees that resulting products can collaborate.
 *
 * Note that concrete factory returns abstract products, even though it
 * creates concrete product objects. This will make factories interchangeable.
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
 * Each distinct product of a family should have a base interface.
 * Product variations must implement these common interfaces.
 */
interface AbstractProductA
{
    public function usefulFunctionA();
}

/**
 * Concrete products will be created by the corresponding concrete factory.
 */
class ConcreteProductA1 implements AbstractProductA
{
    public function usefulFunctionA()
    {
        return "Result of product A1.\n";
    }
}

/**
 * Concrete products will be created by the corresponding concrete factory.
 */
class ConcreteProductA2 implements AbstractProductA
{
    public function usefulFunctionA()
    {
        return "Result of product A2.\n";
    }
}

/**
 * Base interface for another product. Products can interact with each other,
 * however proper interaction is possible only between products of the same
 * concrete variation.
 */
interface AbstractProductB
{
    /**
     * ProductB does its own thing.
     */
    public function usefulFunctionB();

    /**
     * But also collaborates with ProductA.
     *
     * AbstractFactory handles production of compatible product variations that
     * can work together.
     */
    public function anotherUsefulFunctionB(AbstractProductA $collaborator);
}

/**
 * Concrete products will be created by the corresponding concrete factory.
 */
class ConcreteProductB1 implements AbstractProductB
{
    public function usefulFunctionB()
    {
        return "Result of product B1.\n";
    }

    /**
     * Product B1 correctly works only with product A1.
     * But it depends only on abstract type.
     */
    public function anotherUsefulFunctionB(AbstractProductA $collaborator)
    {
        $result = $collaborator->usefulFunctionA();
        return "Result of B1 collaborating with: {$result}";
    }
}

/**
 * Concrete products will be created by the corresponding concrete factory.
 */
class ConcreteProductB2 implements AbstractProductB
{
    public function usefulFunctionB()
    {
        return "Result of product B2.\n";
    }

    /**
     * Product B2 correctly works only with product A2.
     * But it only depends on abstract product type.
     */
    public function anotherUsefulFunctionB(AbstractProductA $collaborator)
    {
        $result = $collaborator->usefulFunctionA();
        return "Result of B2 collaborating with: {$result}";
    }
}

/**
 * Client code works only with abstract types: AbstractFactory and
 * AbstractProducts. This allows it to work with concrete factories and
 * products of any kind.
 */
function clientCode(AbstractFactory $factory)
{
    $product_a = $factory->createProductA();
    $product_b = $factory->createProductB();

    echo $product_b->usefulFunctionB();
    echo $product_b->anotherUsefulFunctionB($product_a);
}

/**
 * Client code can be launched with any factory type.
 */
echo "Testing client code with the first factory type:\n";
clientCode(new ConcreteFactory1());
echo "\n";

echo "Testing the same client code with the second factory type:\n";
clientCode(new ConcreteFactory2());
