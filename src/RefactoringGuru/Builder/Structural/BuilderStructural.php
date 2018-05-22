<?php

namespace RefactoringGuru\Builder\Structural;

/**
 * Builder Design Pattern
 *
 * Intent: Separate the construction of a complex object from its representation
 * so that the same construction process can create different representations.
 */

/**
 * The Builder interface specifies operations for creating parts of the Product
 * objects.
 */
interface Builder
{
    public function producePartA();

    public function producePartB();

    public function producePartC();
}

/**
 * The Concrete Builder classes follow the Builder interface and provide
 * specific implementations of the building steps. Your program may have several
 * variations of Builders, implemented differently.
 */
class ConcreteBuilder1 implements Builder
{
    private $product;

    /**
     * A fresh builder instance should contain a blank product object, which is
     * used in further assembly.
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
     * Concrete Builders are supposed to provide their own methods for
     * retrieving results. That's because various types of builders may create
     * entirely different products that don't follow the same interface.
     * Therefore, such method cannot be declared in the base Builder interface
     * (at least in a statically typed programming language). Note, that PHP is
     * a dynamically typed language and this method CAN be in the base
     * interface. However, we won't declare it there for the sake of clarity.
     *
     * Usually, after returning the end result to the client, a builder instance
     * is expected to be ready to start producing another product. That's why
     * it's a usual practice to call the reset method at the end of the
     * `getResult` method body. However, this behavior is not mandatory, and you
     * can make your builders wait for an explicit reset call from the client
     * code before disposing the previous result.
     */
    public function getProduct(): Product1
    {
        $result = $this->product;
        $this->reset();

        return $result;
    }
}

/**
 * It makes sense to use the Builder pattern only when your products are quite
 * complex and require extensive configuration.
 *
 * Unlike in other creational patterns, different concrete builders can produce
 * unrelated products. In other words, results of various builders may not
 * always follow the same interface.
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
 * The Director is only responsible for executing the building steps in a
 * particular sequence. It helps to produce products with a particular
 * configuration. Strictly speaking, the Director class is optional, since the
 * client can control builders directly.
 */
class Director
{
    /**
     * @var Builder
     */
    private $builder;

    /**
     * The Director works with any builder instance that the client code passes
     * to it. This way, the client code may alter the type of a product
     * assembled in the end.
     */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * The Director can construct several product variations using the same
     * building steps.
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
 * The client code creates a builder object, passes it to the director and then
 * initiates the construction process. The end result is retrieved from the
 * builder object.
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

    // Remember, the Builder pattern can be used without a Director class.
    print("Custom product:\n");
    $builder->producePartA();
    $builder->producePartC();
    print($builder->getProduct()->listParts());
}

$director = new Director();
clientCode($director);