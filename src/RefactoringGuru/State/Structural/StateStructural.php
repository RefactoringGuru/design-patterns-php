<?php

namespace RefactoringGuru\State\Structural;

/**
 * State Design Pattern
 *
 * Intent: Allow an object to alter its behavior when its internal state
 * changes. The object will appear to change its class.
 */

/**
 * The Context defines the interface of interest to clients. It also maintains a
 * reference to an instance of a State subclass, which represents the current
 * state of the Context.
 */
class Context
{
    /**
     * @var State A reference to the current state of the Context.
     */
    private $state;

    /**
     * @param State $state
     */
    public function __construct(State $state)
    {
        $this->transitionTo($state);
    }

    /**
     * The Context allows changing the State object in runtime.
     *
     * @param State $state
     */
    public function transitionTo(State $state)
    {
        print("Context: Transition to ".get_class($state).".\n");
        $this->state = $state;
        $this->state->setContext($this);
    }

    /**
     * The Context delegates part of its behavior to the current State object.
     */
    public function request1()
    {
        $this->state->handle1();
    }

    public function request2()
    {
        $this->state->handle2();
    }
}

/**
 * The base State class declares methods that all Concrete State should
 * implement and also provides a back-reference to the Context object,
 * associated with the State. This back-reference can be used by States to
 * transition the Context to another State.
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

    public abstract function handle1();

    public abstract function handle2();
}

/**
 * Concrete States implement various behaviors, associated with a state of the
 * Context.
 */
class ConcreteStateA extends State
{
    public function handle1()
    {
        print("ConcreteStateA handles request1.\n");
        print("ConcreteStateA wants to change the state of the context.\n");
        $this->context->transitionTo(new ConcreteStateB());
    }

    public function handle2()
    {
        print("ConcreteStateA handles request2.\n");
    }
}

class ConcreteStateB extends State
{
    public function handle1()
    {
        print("ConcreteStateB handles request1.\n");
    }

    public function handle2()
    {
        print("ConcreteStateB handles request2.\n");
        print("ConcreteStateB wants to change the state of the context.\n");
        $this->context->transitionTo(new ConcreteStateA());
    }
}

/**
 * The client code.
 */
$context = new Context(new ConcreteStateA());
$context->request1();
$context->request2();