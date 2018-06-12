<?php
namespace RefactoringGuru\AbstractFactory\Structural;
/**
 * EN: Abstract Factory Design Pattern
 *
 * Intent: Provide an interface for creating families of related or dependent
 * objects without specifying their concrete classes.
 *
 * RU: Паттерн Абстрактная Фабрика
 *
 * Цель: Предоставить интерфейс для создания семейств связанных или зависимых
 * объектов без привязки их к конкретным классам.
 */

/**
 * EN: The Abstract Factory interface declares a set of methods that return
 * different abstract products. These products are called a family and are
 * related by a high-level theme or concept. Products of one family are usually
 * able to collaborate among themselves. A family of products may have several
 * variations, but the products of one variation are incompatible with products
 * of another.
 *
 * RU: Интерфейс Абстрактной Фабрики показывает набор методов, которые будут 
 * возвращать различные абстрактные продукты. Эти продукты называются семейством,
 * связанным с темой или концепцией высокого уровня. Обычно, продукты одного семейства 
 * могут сотрудничать между собой. Семейство продуктов может иметь несколько
 * вариации, но продукты одного варианта несовместимы с продуктами другого.
 */
interface AbstractFactory
{
    public function createProductA(): AbstractProductA;
    public function createProductB(): AbstractProductB;
}
/**
 * EN: Concrete Factories produce a family of products that belong to a single
 * variation. The factory guarantees that resulting products are compatible.
 * Note that signatures of the Concrete Factory's methods return an abstract
 * product, while inside the method a concrete product is instantiated.
 *
 * RU: Определенные фабрики производят семейство продуктов, которые относятся к одному 
 * варианту. Фабрика гарантирует совместимость полученных продуктов. 
 * Обратите внимание, что сигнатуры методов определенной фабрики возвращают
 * абстрактный продукт, а внутри метода создается конкретный продукт.
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
 * EN: Each Concrete Factory has a corresponding product variation.
 *
 * RU: Каждая конкретная фабрика имеет соответствующую вариацию продукта.
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
 * EN: Each distinct product of a product family should have a base interface. All
 * variations of the product must implement this interface.
 *
 * RU: Каждый отдельный продукт семейства должен иметь базовый интерфейс. 
 * Все варианты продукта должны реализовывать этот интерфейс.
 */
interface AbstractProductA
{
    public function usefulFunctionA(): string;
}
/**
 * EN: Concrete Products are created by corresponding Concrete Factories.
 * RU: Конкретные продукты создаются соответствующими фабриками.
 */
class ConcreteProductA1 implements AbstractProductA
{
    public function usefulFunctionA(): string
    {
        return "The result of the product A1.";
    }
}
class ConcreteProductA2 implements AbstractProductA
{
    public function usefulFunctionA(): string
    {
        return "The result of the product A2.";
    }
}
/**
 * EN: The base interface of another product. All products can interact with each
 * other, but proper interaction is possible only between products of the same
 * concrete variation.
 * RU: Базовый интерфейс другого продукта. Все продукты могут взаимодействовать 
 * друг с другом, но правильное взаимодействие возможно только между продуктами 
 * одного и того же конкретного варианта.
 */
interface AbstractProductB
{
    /**
     * EN: Product B is able to do its own thing...
     *
     * RU: Продукт B способен рабо самостоятельно...
     */
    public function usefulFunctionB(): string;
    /**
     * EN: ... but it can also collaborate with Product A.
     *
     * The Abstract Factory makes sure that all products it creates are of the
     * same variation and thus, compatible.
     *
     * RU: ... но он также может сотрудничать с Продуктом A. 
     *
     * Абстрактная Фабрика гарантирует, что все продукты, которые она создает, 
     * имеют одинаковую вариацию и, следовательно,являются совместимыми.
     */
    public function anotherUsefulFunctionB(AbstractProductA $collaborator): string;
}
/**
 * EN: Concrete Products are created by corresponding Concrete Factories.
 *
 * RU: Конкретные продукты создаются соответствующими фабриками.
 */
class ConcreteProductB1 implements AbstractProductB
{
    public function usefulFunctionB(): string
    {
        return "The result of the product B1.";
    }
    /**
     * EN: The variant, Product B1, is only able to work correctly with the variant,
     * Product A1. Nevertheless, it accepts any instance of AbstractProductA as
     * an argument.
     * 
     * RU: Существует вариант, что Продукт B1, может работать только с вариантом, Продукт A1. 
     * Тем не менее, он принимает любой экземпляр Абстрактного Продукта в качестве аргумента.
     */
    public function anotherUsefulFunctionB(AbstractProductA $collaborator): string
    {
        $result = $collaborator->usefulFunctionA();
        return "The result of the B1 collaborating with the ({$result})";
    }
}
class ConcreteProductB2 implements AbstractProductB
{
    public function usefulFunctionB(): string
    {
        return "The result of the product B2.";
    }
    /**
     * EN: The variant, Product B2, is only able to work correctly with the variant,
     * Product A2. Nevertheless, it accepts any instance of AbstractProductA as
     * an argument.
     *
     * RU: Существует вариант, что Продукт B2, может работать только с вариантом, Продукт A2. 
     * Тем не менее, он принимает любой экземпляр Абстрактного Продукта в качестве аргумента.
     */
    public function anotherUsefulFunctionB(AbstractProductA $collaborator): string
    {
        $result = $collaborator->usefulFunctionA();
        return "The result of the B2 collaborating with the ({$result})";
    }
}
/**
 * EN: The client code works with factories and products only through abstract
 * types: AbstractFactory and AbstractProduct. This lets you pass any factory or
 * product subclass to the client code without breaking it.
 *
 * RU: Код клиента работает с фабриками и продуктами только через абстрактные
 * типы: Абстрактную Фабрику и Абстрактный Продукт. Это позволяет передать любой фабричный
 * или товарный подкласс клиенту, не нарушая его.
 */
function clientCode(AbstractFactory $factory)
{
    $product_a = $factory->createProductA();
    $product_b = $factory->createProductB();
    print($product_b->usefulFunctionB() . "\n");
    print($product_b->anotherUsefulFunctionB($product_a) . "\n");
}
/**
 * EN: The client code can work with any concrete factory class.
 *
 * RU: Код клиента может работать с любым конкретным производственным классом.
 */
print("Client: Testing client code with the first factory type...\n");
clientCode(new ConcreteFactory1());
print("\n");
print("Client: Testing the same client code with the second factory type...\n");
clientCode(new ConcreteFactory2());
