<?php

namespace RefactoringGuru\Mediator\Structural;

/**
 * EN: Mediator Design Pattern
 *
 * Intent: Define an object that encapsulates how a set of objects interact.
 * Mediator promotes loose coupling by keeping objects from referring to each
 * other explicitly, and it lets you vary their interaction independently.
 *
 * RU: Паттерн Посредник
 *
 * Назначение: Определяет объект, который инкапсулирует взаимодействие набора
 * объектов. Посредник способствует слабой связанности, удерживая объекты от
 * обращения друг к другу напрямую, и это позволяет вам менять их взаимодействие
 * независимо.
 */

/**
 * EN: The Mediator interface declares a method used by components to notify the
 * mediator about various events. The Mediator may react to these events and
 * pass the execution to other components.
 *
 * RU: Интерфейс Посредника предоставляет метод, используемый компонентами для
 * уведомления посредника о различных событиях. Посредник может реагировать на
 * эти события  и передавать исполнение другим компонентам.
 */
interface Mediator
{
    public function notify($sender, $event);
}

/**
 * EN: Concrete Mediators implement cooperative behavior by coordinating several
 * components.
 *
 * RU: Конкретные Посредники реализуют совместное поведение, координируя
 * отдельные компоненты.
 */
class ConcreteMediator implements Mediator
{
    private $component1;

    private $component2;

    public function __construct(Component1 $c1, Component2 $c2)
    {
        $this->component1 = $c1;
        $this->component1->setMediator($this);
        $this->component2 = $c2;
        $this->component2->setMediator($this);
    }

    public function notify($sender, $event)
    {
        if ($event == "A") {
            print("Mediator reacts on A and triggers following operations:\n");
            $this->component2->doC();
        }

        if ($event == "D") {
            print("Mediator reacts on D and triggers following operations:\n");
            $this->component1->doB();
            $this->component2->doC();
        }
    }
}

/**
 * EN: The Base Component provides the basic functionality of storing a
 * mediator's instance inside component objects.
 *
 * RU: Базовый Компонент обеспечивает базовую функциональность хранения
 * экземпляра посредника внутри объектов компонентов.
 */
class BaseComponent
{
    protected $mediator;

    public function __construct(Mediator $mediator = null)
    {
        $this->mediator = $mediator;
    }

    function setMediator(Mediator $mediator)
    {
        $this->mediator = $mediator;
    }
}

/**
 * EN: Concrete Components implement various functionality. They don't depend on
 * other components. They also don't depend on any concrete mediator classes.
 *
 * RU: Конкретные Компоненты реализуют различную функциональность. Они не
 * зависят от других компонентов. Они также не зависят от каких-либо конкретных
 * классов посредников.
 */
class Component1 extends BaseComponent
{
    public function doA()
    {
        print("Component 1 does A.\n");
        $this->mediator->notify($this, "A");
    }

    public function doB()
    {
        print("Component 1 does B.\n");
        $this->mediator->notify($this, "B");
    }
}

class Component2 extends BaseComponent
{
    public function doC()
    {
        print("Component 2 does C.\n");
        $this->mediator->notify($this, "C");
    }

    public function doD()
    {
        print("Component 2 does D.\n");
        $this->mediator->notify($this, "D");
    }
}

/**
 * EN: The client code.
 *
 * RU: Клиентский код.
 */
$c1 = new Component1();
$c2 = new Component2();
$mediator = new ConcreteMediator($c1, $c2);

print("Client triggers operation A.\n");
$c1->doA();

print("\n");
print("Client triggers operation D.\n");
$c2->doD();
