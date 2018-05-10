<?php

namespace RefactoringGuru\Builder\Structure;

/**
 * Builder Design Pattern
 *
 * Intent: Separate the construction of a complex object from its representation so
 * that the same construction process can create different representations.
 */

/**
 * The Builder interface specifies operations for creating parts of the
 * Product objects.
 */
interface Builder
{
    public function producePartA();

    public function producePartB();

    public function producePartC();
}

/**
 * The Concrete Builder classes follow the Builder interface and provide specific
 * implementations of the building steps. Your program may have several variations of Builders,
 *  implemented differently.
 */
class ConcreteBuilder1 implements Builder
{
    private $product;

    /**
     * A new builder instance should contain a blank product object that will
     * be used in further assembly.
     */
    public function __construct()
    {
        $this->reset();
    }

    public function reset()
    {
        $this->product = new Product1();
    }

    /**
     * All production steps work with the same product instance.
     */
    public function producePartA()
    {
        $this->product->parts[] = "PartA1";
    }

    public function producePartB()
    {
        $this->product->parts[] = "PartB1";
    }

    public function producePartC()
    {
        $this->product->parts[] = "PartC1";
    }

    /**
     * Concrete builders are supposed to provide an interface for retrieving the final product. Various types of builders may create
     * different product types. That's why in a statically typed language this method can not be declared in the base Builder
     * interface. Note, that PHP is a dynamic typed language and this method CAN be declared in the base class. However, we won't do that for the sake of clarity.
     *
     * Usually, after returning the end result to the client, the builder instance is expected to be ready to start producing another product. That's why inside this method we're calling the reset method. But this behavior is not mandatory and you can make your builders wait for a explicit reset call from the client code before disposing previous result.
     */
    public function getProduct(): Product1
    {
        $result = $this->product;
        $this->reset();

        return $result;
    }
}

/**
 * It makes sense to use the Builder pattern only when your products are quite complex and require extensive configuration.
 *
 * Unlike in other creational patterns, different concrete builders can produce
 * unrelated products. In other words, results of various builders may not always follow the same interface.
 */
class Product1
{
    public $parts = [];

    public function listParts()
    {
        return "Product parts: ".implode(', ', $this->parts)."\n\n";
    }
}

/**
 * The Director is only responsible for executing the building steps in a particular sequence in order to produce a product with the particular configuration. Strictly speaking, the Director class is optional, since the client can control builders directly.
 */
class Director
{
    /**
     * @var Builder
     */
    private $builder;

    /**
     * The Director works with any builder instance that the client code passes to it.
     * This way, the client code may alter the type of a product that will be
     * produced in the end.
     */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * The Director can construct several product variations using the same building
     * steps.
     */
    public function buildMinimalViableProduct()
    {
        $this->builder->producePartA();
    }

    public function buildFullFeaturedProduct()
    {
        $this->builder->producePartA();
        $this->builder->producePartB();
        $this->builder->producePartC();
    }
}

/**
 * The client code creates a builder object, passes it to the director and then initiates the construction
 * process. The end result is retrieved from the builder object.
 */
function clientCode(Director $director)
{
    $builder = new ConcreteBuilder1();
    $director->setBuilder($builder);

    print("Standard basic product:\n");
    $director->buildMinimalViableProduct();
    print($builder->getProduct()->listParts());

    print("Standard full featured product:\n");
    $director->buildFullFeaturedProduct();
    print($builder->getProduct()->listParts());

    // By the way, builder can be used without a director.
    print("Custom product:\n");
    $builder->producePartA();
    $builder->producePartC();
    print($builder->getProduct()->listParts());
}

$director = new Director();
clientCode($director);