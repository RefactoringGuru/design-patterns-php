<?php

namespace RefactoringGuru\State\Conceptual;

/**
 * EN: State Design Pattern
 *
 * Intent: Lets an object alter its behavior when its internal state changes. It
 * appears as if the object changed its class.
 *
 * RU: Паттерн Состояние
 *
 * Назначение: Позволяет объектам менять поведение в зависимости от своего
 * состояния. Извне создаётся впечатление, что изменился класс объекта.
 */

/**
 * EN: The Context defines the interface of interest to clients. It also
 * maintains a reference to an instance of a State subclass, which represents
 * the current state of the Context.
 *
 * RU: Контекст определяет интерфейс, представляющий интерес для клиентов. Он
 * также хранит ссылку на экземпляр подкласса Состояния, который отображает
 * текущее состояние Контекста.
 */
class Context
{
    /**
     * EN: @var State A reference to the current state of the Context.
     *
     * RU: @var State Ссылка на текущее состояние Контекста.
     */
    private $state;

    public function __construct(State $state)
    {
        $this->transitionTo($state);
    }

    /**
     * EN: The Context allows changing the State object at runtime.
     *
     * RU: Контекст позволяет изменять объект Состояния во время выполнения.
     */
    public function transitionTo(State $state): void
    {
        echo "Context: Transition to " . get_class($state) . ".\n";
        $this->state = $state;
        $this->state->setContext($this);
    }

    /**
     * EN: The Context delegates part of its behavior to the current State
     * object.
     *
     * RU: Контекст делегирует часть своего поведения текущему объекту
     * Состояния.
     */
    public function request1(): void
    {
        $this->state->handle1();
    }

    public function request2(): void
    {
        $this->state->handle2();
    }
}

/**
 * EN: The base State class declares methods that all Concrete State should
 * implement and also provides a backreference to the Context object, associated
 * with the State. This backreference can be used by States to transition the
 * Context to another State.
 *
 * RU: Базовый класс Состояния объявляет методы, которые должны реализовать все
 * Конкретные Состояния, а также предоставляет обратную ссылку на объект
 * Контекст, связанный с Состоянием. Эта обратная ссылка может использоваться
 * Состояниями для передачи Контекста другому Состоянию.
 */
abstract class State
{
    /**
     * @var Context
     */
    protected $context;

    public function setContext(Context $context)
    {
        $this->context = $context;
    }

    abstract public function handle1(): void;

    abstract public function handle2(): void;
}

/**
 * EN: Concrete States implement various behaviors, associated with a state of
 * the Context.
 *
 * RU: Конкретные Состояния реализуют различные модели поведения, связанные с
 * состоянием Контекста.
 */
class ConcreteStateA extends State
{
    public function handle1(): void
    {
        echo "ConcreteStateA handles request1.\n";
        echo "ConcreteStateA wants to change the state of the context.\n";
        $this->context->transitionTo(new ConcreteStateB());
    }

    public function handle2(): void
    {
        echo "ConcreteStateA handles request2.\n";
    }
}

class ConcreteStateB extends State
{
    public function handle1(): void
    {
        echo "ConcreteStateB handles request1.\n";
    }

    public function handle2(): void
    {
        echo "ConcreteStateB handles request2.\n";
        echo "ConcreteStateB wants to change the state of the context.\n";
        $this->context->transitionTo(new ConcreteStateA());
    }
}

/**
 * EN: The client code.
 *
 * RU: Клиентский код.
 */
$context = new Context(new ConcreteStateA());
$context->request1();
$context->request2();
