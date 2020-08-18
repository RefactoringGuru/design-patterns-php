<?php

namespace RefactoringGuru\ChainOfResponsibility\Conceptual;

/**
 * EN: Chain of Responsibility Design Pattern
 *
 * Intent: Lets you pass requests along a chain of handlers. Upon receiving a
 * request, each handler decides either to process the request or to pass it to
 * the next handler in the chain.
 *
 * RU: Паттерн Цепочка обязанностей
 *
 * Назначение: Позволяет передавать запросы последовательно по цепочке
 * обработчиков. Каждый последующий обработчик решает, может ли он обработать
 * запрос сам и стоит ли передавать запрос дальше по цепи.
 */

/**
 * EN: The Handler interface declares a method for building the chain of
 * handlers. It also declares a method for executing a request.
 *
 * RU: Интерфейс Обработчика объявляет метод построения цепочки обработчиков. Он
 * также объявляет метод для выполнения запроса.
 */
interface Handler
{
    public function setNext(Handler $handler): Handler;

    public function handle(string $request): ?string;
}

/**
 * EN: The default chaining behavior can be implemented inside a base handler
 * class.
 *
 * RU: Поведение цепочки по умолчанию может быть реализовано внутри базового
 * класса обработчика.
 */
abstract class AbstractHandler implements Handler
{
    /**
     * @var Handler
     */
    private $nextHandler;

    public function setNext(Handler $handler): Handler
    {
        $this->nextHandler = $handler;
        // EN: Returning a handler from here will let us link handlers in a
        // convenient way like this:
        // $monkey->setNext($squirrel)->setNext($dog);
        //
        // RU: Возврат обработчика отсюда позволит связать обработчики простым
        // способом, вот так:
        // $monkey->setNext($squirrel)->setNext($dog);
        return $handler;
    }

    public function handle(string $request): ?string
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($request);
        }

        return null;
    }
}

/**
 * EN: All Concrete Handlers either handle a request or pass it to the next
 * handler in the chain.
 *
 * RU: Все Конкретные Обработчики либо обрабатывают запрос, либо передают его
 * следующему обработчику в цепочке.
 */
class MonkeyHandler extends AbstractHandler
{
    public function handle(string $request): ?string
    {
        if ($request === "Banana") {
            return "Monkey: I'll eat the " . $request . ".\n";
        } else {
            return parent::handle($request);
        }
    }
}

class SquirrelHandler extends AbstractHandler
{
    public function handle(string $request): ?string
    {
        if ($request === "Nut") {
            return "Squirrel: I'll eat the " . $request . ".\n";
        } else {
            return parent::handle($request);
        }
    }
}

class DogHandler extends AbstractHandler
{
    public function handle(string $request): ?string
    {
        if ($request === "MeatBall") {
            return "Dog: I'll eat the " . $request . ".\n";
        } else {
            return parent::handle($request);
        }
    }
}

/**
 * EN: The client code is usually suited to work with a single handler. In most
 * cases, it is not even aware that the handler is part of a chain.
 *
 * RU: Обычно клиентский код приспособлен для работы с единственным
 * обработчиком. В большинстве случаев клиенту даже неизвестно, что этот
 * обработчик является частью цепочки.
 */
function clientCode(Handler $handler)
{
    foreach (["Nut", "Banana", "Cup of coffee"] as $food) {
        echo "Client: Who wants a " . $food . "?\n";
        $result = $handler->handle($food);
        if ($result) {
            echo "  " . $result;
        } else {
            echo "  " . $food . " was left untouched.\n";
        }
    }
}

/**
 * EN: The other part of the client code constructs the actual chain.
 *
 * RU: Другая часть клиентского кода создает саму цепочку.
 */
$monkey = new MonkeyHandler();
$squirrel = new SquirrelHandler();
$dog = new DogHandler();

$monkey->setNext($squirrel)->setNext($dog);

/**
 * EN: The client should be able to send a request to any handler, not just the
 * first one in the chain.
 *
 * RU: Клиент должен иметь возможность отправлять запрос любому обработчику, а
 * не только первому в цепочке.
 */
echo "Chain: Monkey > Squirrel > Dog\n\n";
clientCode($monkey);
echo "\n";

echo "Subchain: Squirrel > Dog\n\n";
clientCode($squirrel);
