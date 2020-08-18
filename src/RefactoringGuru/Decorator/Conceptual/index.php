<?php

namespace RefactoringGuru\Decorator\Conceptual;

/**
 * EN: Decorator Design Pattern
 *
 * Intent: Lets you attach new behaviors to objects by placing these objects
 * inside special wrapper objects that contain the behaviors.
 *
 * RU: Паттерн Декоратор
 *
 * Назначение: Позволяет динамически добавлять объектам новую функциональность,
 * оборачивая их в полезные «обёртки».
 */

/**
 * EN: The base Component interface defines operations that can be altered by
 * decorators.
 *
 * RU: Базовый интерфейс Компонента определяет поведение, которое изменяется
 * декораторами.
 */
interface Component
{
    public function operation(): string;
}

/**
 * EN: Concrete Components provide default implementations of the operations.
 * There might be several variations of these classes.
 *
 * RU: Конкретные Компоненты предоставляют реализации поведения по умолчанию.
 * Может быть несколько вариаций этих классов.
 */
class ConcreteComponent implements Component
{
    public function operation(): string
    {
        return "ConcreteComponent";
    }
}

/**
 * EN: The base Decorator class follows the same interface as the other
 * components. The primary purpose of this class is to define the wrapping
 * interface for all concrete decorators. The default implementation of the
 * wrapping code might include a field for storing a wrapped component and the
 * means to initialize it.
 *
 * RU: Базовый класс Декоратора следует тому же интерфейсу, что и другие
 * компоненты. Основная цель этого класса - определить интерфейс обёртки для
 * всех конкретных декораторов. Реализация кода обёртки по умолчанию может
 * включать в себя поле для хранения завёрнутого компонента и средства его
 * инициализации.
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
     * EN: The Decorator delegates all work to the wrapped component.
     *
     * RU: Декоратор делегирует всю работу обёрнутому компоненту.
     */
    public function operation(): string
    {
        return $this->component->operation();
    }
}

/**
 * EN: Concrete Decorators call the wrapped object and alter its result in some
 * way.
 *
 * RU: Конкретные Декораторы вызывают обёрнутый объект и изменяют его результат
 * некоторым образом.
 */
class ConcreteDecoratorA extends Decorator
{
    /**
     * EN: Decorators may call parent implementation of the operation, instead
     * of calling the wrapped object directly. This approach simplifies
     * extension of decorator classes.
     *
     * RU: Декораторы могут вызывать родительскую реализацию операции, вместо
     * того, чтобы вызвать обёрнутый объект напрямую. Такой подход упрощает
     * расширение классов декораторов.
     */
    public function operation(): string
    {
        return "ConcreteDecoratorA(" . parent::operation() . ")";
    }
}

/**
 * EN: Decorators can execute their behavior either before or after the call to
 * a wrapped object.
 *
 * RU: Декораторы могут выполнять своё поведение до или после вызова обёрнутого
 * объекта.
 */
class ConcreteDecoratorB extends Decorator
{
    public function operation(): string
    {
        return "ConcreteDecoratorB(" . parent::operation() . ")";
    }
}

/**
 * EN: The client code works with all objects using the Component interface.
 * This way it can stay independent of the concrete classes of components it
 * works with.
 *
 * RU: Клиентский код работает со всеми объектами, используя интерфейс
 * Компонента. Таким образом, он остаётся независимым от конкретных классов
 * компонентов, с которыми работает.
 */
function clientCode(Component $component)
{
    // ...

    echo "RESULT: " . $component->operation();

    // ...
}

/**
 * EN: This way the client code can support both simple components...
 *
 * RU: Таким образом, клиентский код может поддерживать как простые
 * компоненты...
 */
$simple = new ConcreteComponent();
echo "Client: I've got a simple component:\n";
clientCode($simple);
echo "\n\n";

/**
 * EN: ...as well as decorated ones.
 *
 * Note how decorators can wrap not only simple components but the other
 * decorators as well.
 *
 * RU: ...так и декорированные.
 *
 * Обратите внимание, что декораторы могут обёртывать не только простые
 * компоненты, но и другие декораторы.
 */
$decorator1 = new ConcreteDecoratorA($simple);
$decorator2 = new ConcreteDecoratorB($decorator1);
echo "Client: Now I've got a decorated component:\n";
clientCode($decorator2);
