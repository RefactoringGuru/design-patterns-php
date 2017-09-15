<?php

namespace RefactoringGuru\State\Structure;

/**
 * State Design Pattern
 *
 * Intent: Allow an object to alter its behavior when its internal state changes.
 * The object will appear to change its class.
 */

/**
 * Define the interface of interest to clients.
 * Maintain an instance of a ConcreteState subclass that defines the
 * current state.
 */
class Context
{
    /**
     * @var State
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
     * Usually context allows changing strategy object in run time.
     * @param State $state
     */
    public function transitionTo(State $state)
    {
        $this->state = $state;
        $this->state->setContext($this);
    }

    /**
     *
     */
    public function request1()
    {
        $this->state->handle1();
    }

    /**
     *
     */
    public function request2()
    {
        $this->state->handle2();
    }

}

/**
 * Define an interface for encapsulating the behavior associated with a
 * particular state of the Context.
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
 * Implement a behavior associated with a state of the Context.
 */
class ConcreteStateA extends State
{
    public function handle1()
    {
        echo "ConcreteStateB handles request1.";
        $this->context->transitionTo(new ConcreteStateB());
    }

    public function handle2()
    {
        echo "ConcreteStateB handles request2.";
    }
}

/**
 * Implement a behavior associated with a state of the Context.
 */
class ConcreteStateB extends State
{
    public function handle1()
    {
        echo "ConcreteStateB handles request1.";
    }

    public function handle2()
    {
        echo "ConcreteStateAB handles request2.";
        $this->context->transitionTo(new ConcreteStateA());
    }
}

$context = new Context(new ConcreteStateA());
$context->request1();
$context->request2();