<?php

namespace RefactoringGuru\Builder\Structure;

/**
 * Builder Design Pattern
 *
 * Intent: Separate the construction of a complex object from its representation so
 * that the same construction process can create different representations.
 */

/**
 * Base Builder interface specifies operations for creating parts of a
 * Product object.
 */
interface Builder
{
    public function producePartA();

    public function producePartB();

    public function producePartC();
}

/**
 * Concrete Builders implement the common Builder interface and provide specific
 * implementations of the building steps. Program may have several Builder
 * variations with different implementations.
 */
class ConcreteBuilder1 implements Builder
{
    private $product;

    /**
     * New builder already contains a blank product that will be used in
     * further assembly.
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
     * Provide an interface for retrieving the product. Builders may create
     * different product types. That's why this method is not defined in base
     * interface.
     *
     * Once product is completed, prepare a blank product object so that new
     * product could be built.
     */
    public function getProduct(): Product1
    {
        $result = $this->product;
        $this->reset();
        return $result;
    }
}

/**
 * Product class represents a complex object under construction.
 *
 * Unlike in other creational patterns, different builders can produce
 * unrelated products. In other words, products of different Builders don't need
 * to follow a common interface.
 */
class Product1
{
    public $parts = [];

    public function listParts()
    {
        return "Product parts: " . implode(', ', $this->parts) . "\n\n";
    }
}

/**
 * Director controls sequence of building steps, while delegating most of the
 * work to a Builder instance.
 */
class Director
{
    /**
     * @var Builder
     */
    private $builder;

    /**
     * Director works with any Builder instance client code passes to it.
     * This way, client code may vary the type of product that will be
     * produced in the end.
     */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Director can construct several product variations using the same building
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
 * Client code may reuse single instance of the Director. It creates builder
 * objects and passes them to director and then initiates the construction
 * process. The end result is returned by the builder.
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