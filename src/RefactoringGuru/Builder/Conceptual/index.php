<?php

namespace RefactoringGuru\Builder\Conceptual;

/**
 * EN: Builder Design Pattern
 *
 * Intent: Lets you construct complex objects step by step. The pattern allows
 * you to produce different types and representations of an object using the
 * same construction code.
 *
 * RU: Паттерн Строитель
 *
 * Назначение: Позволяет создавать сложные объекты пошагово. Строитель даёт
 * возможность использовать один и тот же код строительства для получения разных
 * представлений объектов.
 */

/**
 * EN: The Builder interface specifies methods for creating the different parts
 * of the Product objects.
 *
 * RU: Интерфейс Строителя объявляет создающие методы для различных частей
 * объектов Продуктов.
 */
interface Builder
{
    public function producePartA(): void;

    public function producePartB(): void;

    public function producePartC(): void;
}

/**
 * EN: The Concrete Builder classes follow the Builder interface and provide
 * specific implementations of the building steps. Your program may have several
 * variations of Builders, implemented differently.
 *
 * RU: Классы Конкретного Строителя следуют интерфейсу Строителя и предоставляют
 * конкретные реализации шагов построения. Ваша программа может иметь несколько
 * вариантов Строителей, реализованных по-разному.
 */
class ConcreteBuilder1 implements Builder
{
    private $product;

    /**
     * EN: A fresh builder instance should contain a blank product object, which
     * is used in further assembly.
     *
     * RU: Новый экземпляр строителя должен содержать пустой объект продукта,
     * который используется в дальнейшей сборке.
     */
    public function __construct()
    {
        $this->reset();
    }

    public function reset(): void
    {
        $this->product = new Product1();
    }

    /**
     * EN: All production steps work with the same product instance.
     *
     * RU: Все этапы производства работают с одним и тем же экземпляром
     * продукта.
     */
    public function producePartA(): void
    {
        $this->product->parts[] = "PartA1";
    }

    public function producePartB(): void
    {
        $this->product->parts[] = "PartB1";
    }

    public function producePartC(): void
    {
        $this->product->parts[] = "PartC1";
    }

    /**
     * EN: Concrete Builders are supposed to provide their own methods for
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
     * RU: Конкретные Строители должны предоставить свои собственные методы
     * получения результатов. Это связано с тем, что различные типы строителей
     * могут создавать совершенно разные продукты с разными интерфейсами.
     * Поэтому такие методы не могут быть объявлены в базовом интерфейсе
     * Строителя (по крайней мере, в статически типизированном языке
     * программирования). Обратите внимание, что PHP является динамически
     * типизированным языком, и этот метод может быть в базовом интерфейсе.
     * Однако мы не будем объявлять его здесь для ясности.
     *
     * Как правило, после возвращения конечного результата клиенту, экземпляр
     * строителя должен быть готов к началу производства следующего продукта.
     * Поэтому обычной практикой является вызов метода сброса в конце тела
     * метода getProduct. Однако такое поведение не является обязательным, вы
     * можете заставить своих строителей ждать явного запроса на сброс из кода
     * клиента, прежде чем избавиться от предыдущего результата.
     */
    public function getProduct(): Product1
    {
        $result = $this->product;
        $this->reset();

        return $result;
    }
}

/**
 * EN: It makes sense to use the Builder pattern only when your products are
 * quite complex and require extensive configuration.
 *
 * Unlike in other creational patterns, different concrete builders can produce
 * unrelated products. In other words, results of various builders may not
 * always follow the same interface.
 *
 * RU: Имеет смысл использовать паттерн Строитель только тогда, когда ваши
 * продукты достаточно сложны и требуют обширной конфигурации.
 *
 * В отличие от других порождающих паттернов, различные конкретные строители
 * могут производить несвязанные продукты. Другими словами, результаты различных
 * строителей могут не всегда следовать одному и тому же интерфейсу.
 */
class Product1
{
    public $parts = [];

    public function listParts(): void
    {
        echo "Product parts: " . implode(', ', $this->parts) . "\n\n";
    }
}

/**
 * EN: The Director is only responsible for executing the building steps in a
 * particular sequence. It is helpful when producing products according to a
 * specific order or configuration. Strictly speaking, the Director class is
 * optional, since the client can control builders directly.
 *
 * RU: Директор отвечает только за выполнение шагов построения в определённой
 * последовательности. Это полезно при производстве продуктов в определённом
 * порядке или особой конфигурации. Строго говоря, класс Директор необязателен,
 * так как клиент может напрямую управлять строителями.
 */
class Director
{
    /**
     * @var Builder
     */
    private $builder;

    /**
     * EN: The Director works with any builder instance that the client code
     * passes to it. This way, the client code may alter the final type of the
     * newly assembled product.
     *
     * RU: Директор работает с любым экземпляром строителя, который передаётся
     * ему клиентским кодом. Таким образом, клиентский код может изменить
     * конечный тип вновь собираемого продукта.
     */
    public function setBuilder(Builder $builder): void
    {
        $this->builder = $builder;
    }

    /**
     * EN: The Director can construct several product variations using the same
     * building steps.
     *
     * RU: Директор может строить несколько вариаций продукта, используя
     * одинаковые шаги построения.
     */
    public function buildMinimalViableProduct(): void
    {
        $this->builder->producePartA();
    }

    public function buildFullFeaturedProduct(): void
    {
        $this->builder->producePartA();
        $this->builder->producePartB();
        $this->builder->producePartC();
    }
}

/**
 * EN: The client code creates a builder object, passes it to the director and
 * then initiates the construction process. The end result is retrieved from the
 * builder object.
 *
 * RU: Клиентский код создаёт объект-строитель, передаёт его директору, а затем
 * инициирует процесс построения. Конечный результат извлекается из
 * объекта-строителя.
 */
function clientCode(Director $director)
{
    $builder = new ConcreteBuilder1();
    $director->setBuilder($builder);

    echo "Standard basic product:\n";
    $director->buildMinimalViableProduct();
    $builder->getProduct()->listParts();

    echo "Standard full featured product:\n";
    $director->buildFullFeaturedProduct();
    $builder->getProduct()->listParts();

    // EN: Remember, the Builder pattern can be used without a Director class.
    //
    // RU: Помните, что паттерн Строитель можно использовать без класса
    // Директор.
    echo "Custom product:\n";
    $builder->producePartA();
    $builder->producePartC();
    $builder->getProduct()->listParts();
}

$director = new Director();
clientCode($director);
