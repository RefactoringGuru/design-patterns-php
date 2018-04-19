<?php

namespace RefactoringGuru\ChainOfResponsibility\Structure;

/**
 * Chain of Responsibility Design Pattern
 *
 * Intent: Avoid coupling the sender of a request to its receiver by giving more
 * than one object a chance to handle the request. Chain the receiving objects
 * and pass the request along the chain until an object handles it.
 */

/**
 * Define an interface for handling requests.
 */
interface Handler
{
    public function setNext(Handler $handler);

    public function handle($request);
}

/**
 * Base handler class implements the standard linking behavior.
 */
abstract class AbstractHandler implements Handler
{
    /**
     * @var Handler
     */
    private $nextHandler;

    /**
     * @param Handler $handler
     * @return Handler
     */
    public function setNext(Handler $handler)
    {
        $this->nextHandler = $handler;

        // Returning a handler will let us link handlers in a convenient way.
        return $handler;
    }

    public function handle($request)
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($request);
        }
    }
}

/**
 * Handle request, otherwise forward it to the successor.
 */
class MonkeyHandler extends AbstractHandler
{
    public function handle($request)
    {
        if ($request == "Banana") {
            return "Monkey: I'll eat the " . $request . ".\n";
        } else {
            return parent::handle($request);
        }
    }
}

class SquirrelHandler extends AbstractHandler
{
    public function handle($request)
    {
        if ($request == "Nut") {
            return "Squirrel: I'll eat the " . $request . ".\n";
        } else {
            return parent::handle($request);
        }
    }
}

class DogHandler extends AbstractHandler
{
    public function handle($request)
    {
        if ($request == "MeatBall") {
            return "Dog: I'll eat the " . $request . ".\n";
        } else {
            parent::handle($request);
        }
    }
}

/**
 * Client code works only with abstract types: AbstractFactory and
 * AbstractProducts. This allows it to work with concrete factories and
 * products of any kind.
 */
function clientCode(Handler $handler)
{
    foreach (["Nut", "Banana", "Cup of coffee"] as $food) {
        print("Client: Who wants a " . $food . "?\n");
        $result = $handler->handle($food);
        if ($result) {
            print("  " . $result);
        } else {
            print("  " . $food . " was left untouched.\n");
        }
    }
}


/**
 * Client code. That's how a chain is constructed.
 */
$monkey = new MonkeyHandler();
$squirrel = new SquirrelHandler();
$dog = new DogHandler();

$monkey->setNext($squirrel)->setNext($dog);

/**
 * Client can accept any handler, not just the one that starts a chain.
 */
echo "Chain: Monkey > Squirrel > Dog\n\n";
clientCode($monkey);
echo "\n";

echo "Subchain: Squirrel > Dog\n\n";
clientCode($squirrel);
