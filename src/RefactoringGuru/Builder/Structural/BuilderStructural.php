<?php

namespace RefactoringGuru\Builder\Structural;

/**
 * EN: Builder Design Pattern
 *
 * Intent: Separate the construction of a complex object from its representation
 * so that the same construction process can create different representations.
 *
 * RU: Паттерн Строитель
 * Назначение: Отделяет построение сложного объекта от его представления так, 
 * что один и тот же процесс строительства создаёт разные представления объекта.
 */

/**
 * EN:
 * The Builder interface specifies methods for creating the different parts of
 * the Product objects.
 *
 * RU:
 * Интерфейс Строителя объявляет создающие методы для различных частей объектов Продуктов.
 */
interface Builder
{
    public function producePartA();

    public function producePartB();

    public function producePartC();
}

/**
 * EN:
 * The Concrete Builder classes follow the Builder interface and provide
 * specific implementations of the building steps. Your program may have several
 * variations of Builders, implemented differently.
 *
 * RU:
 * Классы Конкретного Строителя следуют интерфейсу Строителя и предоставляют 
 * конкретные реализации шагов построения. Ваша программа может иметь несколько вариантов Строителей,
 * реализованных по-разному.
 */
class ConcreteBuilder1 implements Builder
{
    private $product;

    /**
     * EN:
     * A fresh builder instance should contain a blank product object, which is
     * used in further assembly.
     *
     * RU:
     * Новый экземпляр строителя должен содержать пустой объект продукта, 
     * который используется в дальнейшей сборке.
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
     * EN:
     * All production steps work with the same product instance.
     *
     * RU:
     * Все этапы производства работают с одним и тем же экземпляром продукта.
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
     * EN:
     * Concrete Builders are supposed to provide their own methods for
     * retrieving results. That's because various types of builders may create
     * entirely different products that don't follow the same interface.
     * Therefore, such methods cannot be declared in the base Builder interface
     * (at least in a statically typed programming language). Note that PHP is a
     * dynamically typed language and this method CAN be in the base interface.
     * However, we won't declare it there for the sake of clarity.
     *
     * Usually, after returning the end result to the client, a builder instance
     * is expected to be ready to start producing another product. That's why
     * it's a usual practice to call the reset method at the end of the
     * `getProduct` method body. However, this behavior is not mandatory, and
     * you can make your builders wait for an explicit reset call from the
     * client code before disposing of the previous result.
     *
     * RU:
     * Конкретные Строители должны предоставить свои собственные методы 
     * получения результатов. Это связано с тем, что различные типы строителей 
     * могут создавать совершенно разные продукты с разными интерфейсами.
     * Поэтому такие методы не могут быть объявлены в базовом интерфейсе Строителя
     * (по крайней мере, в статически типизированном языке программирования).
     * Обратите внимание, что PHP является динамически типизированным языком,
     * и этот метод может быть в базовом интерфейсе. 
     * Однако мы не будем объявлять его здесь для ясности.
     *
     * Как правило, после возвращения конечного результата клиенту, экземпляр строителя
     * должен быть готов к началу производства другого продукта. Поэтому обычной практикой
     * является вызов метода сброса в конце тела метода getProduct.
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
        print("Product parts: ".implode(', ', $this->parts)."\n\n");
    }
}

/**
 * The Director is only responsible for executing the building steps in a
 * particular sequence. It is helpful when producing products according to a
 * specific order or configuration. Strictly speaking, the Director class is
 * optional, since the client can control builders directly.
 */
class Director
{
    /**
     * @var Builder
     */
    private $builder;

    /**
     * The Director works with any builder instance that the client code passes
     * to it. This way, the client code may alter the final type of the newly
     * assembled product.
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
    $builder->getProduct()->listParts();

    print("Standard full featured product:\n");
    $director->buildFullFeaturedProduct();
    $builder->getProduct()->listParts();

    // Remember, the Builder pattern can be used without a Director class.
    print("Custom product:\n");
    $builder->producePartA();
    $builder->producePartC();
    $builder->getProduct()->listParts();
}

$director = new Director();
clientCode($director);
