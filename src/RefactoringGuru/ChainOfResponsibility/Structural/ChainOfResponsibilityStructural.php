<?php

namespace RefactoringGuru\ChainOfResponsibility\Structural;

/**
 * EN: Chain of Responsibility Design Pattern
 *
 * Intent: Avoid coupling a sender of a request to its receiver by giving more
 * than one object a chance to handle the request. Chain the receiving objects
 * and then pass the request through the chain until some receiver handles it.
 *
 * RU: Паттерн Цепочка обязанностей
 *
 * Назначение: Позволяет избежать привязки отправителя запроса к его получателю,
 * предоставляя возможность обработать запрос нескольким объектам.  Связывает в
 * цепочку объекты-получатели, а затем передаёт запрос по цепочке, пока некий
 * получатель не обработает его.
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
    public function setNext(Handler $handler);

    public function handle($request);
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

    /**
     * @param Handler $handler
     * @return Handler
     */
    public function setNext(Handler $handler)
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

    public function handle($request)
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($request);
        }
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
    public function handle($request)
    {
        if ($request == "Banana") {
            return "Monkey: I'll eat the ".$request.".\n";
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
            return "Squirrel: I'll eat the ".$request.".\n";
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
            return "Dog: I'll eat the ".$request.".\n";
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
        echo "Client: Who wants a ".$food."?\n";
        $result = $handler->handle($food);
        if ($result) {
            echo "  ".$result;
        } else {
            echo "  ".$food." was left untouched.\n";
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
