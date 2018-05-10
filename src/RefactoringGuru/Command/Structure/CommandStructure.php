<?php

namespace RefactoringGuru\Command\Structure;

/**
 * Command Design Pattern
 *
 * Intent: Encapsulate a request as an object, thereby letting you parameterize
 * clients with different requests, queue or log requests, and support undoable
 * operations.
 */

/**
 * The Command interface declares a method for executing the command.
 */
interface Command
{
    public function execute();
}

/**
 * Some commands can implement simple operations on their own.
 */
class SimpleCommand implements Command
{
    private $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function execute()
    {
        print("SimpleCommand: See, I can do simple things like printing (".$this->payload.")\n");
    }
}

/**
 * And some commands can delegate more complex operations to other objects, called "receivers".
 */
class ComplexCommand implements Command
{
    /**
     * @var Receiver
     */
    private $receiver;

    /**
     * Context data, required for launching the receiver's methods.
     */
    private $a;

    private $b;

    /**
     * Complex commands can accept one or several receiver objects along with
     * any context data via constructor.
     */
    public function __construct(Receiver $receiver, $a, $b)
    {
        $this->receiver = $receiver;
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * Commands can delegate to any methods of a receiver.
     */
    public function execute()
    {
        print("ComplexCommand: Complex stuff should be done by a receiver object.\n");
        $this->receiver->doSomething($this->a);
        $this->receiver->doSomethingElse($this->b);
    }
}

/**
 * The Receiver classes contain some important business logic. They know how to perform
 * all kinds of operations, associated with carrying out a request. In fact, any class may
 * serve as a Receiver.
 */
class Receiver
{
    public function doSomething($a)
    {
        print("Receiver: Working on (".$a.".)\n");
    }

    public function doSomethingElse($b)
    {
        print("Receiver: Also working on (".$b.".)\n");
    }
}

/**
 * The Invoker is associated with one or several commands. It sends a request to the command.
 */
class Invoker
{
    /**
     * @var Command
     */
    private $onStart;

    /**
     * @var Command
     */
    private $onFinish;

    /**
     * Initialize commands.
     *
     * @param Command $command
     */
    public function setOnStart(Command $command)
    {
        $this->onStart = $command;
    }

    public function setOnFinish(Command $command)
    {
        $this->onFinish = $command;
    }

    /**
     * The Invoker does not depend on concrete command or receiver classes. The Invoker passes a request to a receiver indirectly, by executing a command.
     */
    public function doSomethingImportant()
    {
        print("Invoker: Does anybody want something done before I begin?\n");
        if ($this->onStart instanceof Command) {
            $this->onStart->execute();
        }

        print("Invoker: ...doing something really important...\n");

        print("Invoker: Does anybody want something done after I finish?\n");
        if ($this->onFinish instanceof Command) {
            $this->onFinish->execute();
        }
    }
}

/**
 * The client code can parametrize an invoker with any commands.
 */
$invoker = new Invoker();
$invoker->setOnStart(new SimpleCommand("Say Hi!"));
$receiver = new Receiver();
$invoker->setOnFinish(new ComplexCommand($receiver, "Send email", "Save report"));

$invoker->doSomethingImportant();