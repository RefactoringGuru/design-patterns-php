<?php

namespace RefactoringGuru\Command\Conceptual;

/**
 * EN: Command Design Pattern
 *
 * Intent: Turns a request into a stand-alone object that contains all
 * information about the request. This transformation lets you parameterize
 * methods with different requests, delay or queue a request's execution, and
 * support undoable operations.
 *
 * RU: Паттерн Команда
 *
 * Назначение: Превращает запросы в объекты, позволяя передавать их как
 * аргументы при вызове методов, ставить запросы в очередь, логировать их, а
 * также поддерживать отмену операций.
 */

/**
 * EN: The Command interface declares a method for executing a command.
 *
 * RU: Интерфейс Команды объявляет метод для выполнения команд.
 */
interface Command
{
    public function execute(): void;
}

/**
 * EN: Some commands can implement simple operations on their own.
 *
 * RU: Некоторые команды способны выполнять простые операции самостоятельно.
 */
class SimpleCommand implements Command
{
    private $payload;

    public function __construct(string $payload)
    {
        $this->payload = $payload;
    }

    public function execute(): void
    {
        echo "SimpleCommand: See, I can do simple things like printing (" . $this->payload . ")\n";
    }
}

/**
 * EN: However, some commands can delegate more complex operations to other
 * objects, called "receivers."
 *
 * RU: Но есть и команды, которые делегируют более сложные операции другим
 * объектам, называемым «получателями».
 */
class ComplexCommand implements Command
{
    /**
     * @var Receiver
     */
    private $receiver;

    /**
     * EN: Context data, required for launching the receiver's methods.
     *
     * RU: Данные о контексте, необходимые для запуска методов получателя.
     */
    private $a;

    private $b;

    /**
     * EN: Complex commands can accept one or several receiver objects along
     * with any context data via the constructor.
     *
     * RU: Сложные команды могут принимать один или несколько
     * объектов-получателей вместе с любыми данными о контексте через
     * конструктор.
     */
    public function __construct(Receiver $receiver, string $a, string $b)
    {
        $this->receiver = $receiver;
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * EN: Commands can delegate to any methods of a receiver.
     *
     * RU: Команды могут делегировать выполнение любым методам получателя.
     */
    public function execute(): void
    {
        echo "ComplexCommand: Complex stuff should be done by a receiver object.\n";
        $this->receiver->doSomething($this->a);
        $this->receiver->doSomethingElse($this->b);
    }
}

/**
 * EN: The Receiver classes contain some important business logic. They know how
 * to perform all kinds of operations, associated with carrying out a request.
 * In fact, any class may serve as a Receiver.
 *
 * RU: Классы Получателей содержат некую важную бизнес-логику. Они умеют
 * выполнять все виды операций, связанных с выполнением запроса. Фактически,
 * любой класс может выступать Получателем.
 */
class Receiver
{
    public function doSomething(string $a): void
    {
        echo "Receiver: Working on (" . $a . ".)\n";
    }

    public function doSomethingElse(string $b): void
    {
        echo "Receiver: Also working on (" . $b . ".)\n";
    }
}

/**
 * EN: The Invoker is associated with one or several commands. It sends a
 * request to the command.
 *
 * RU: Отправитель связан с одной или несколькими командами. Он отправляет
 * запрос команде.
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
     * EN: Initialize commands.
     *
     * RU: Инициализация команд.
     */
    public function setOnStart(Command $command): void
    {
        $this->onStart = $command;
    }

    public function setOnFinish(Command $command): void
    {
        $this->onFinish = $command;
    }

    /**
     * EN: The Invoker does not depend on concrete command or receiver classes.
     * The Invoker passes a request to a receiver indirectly, by executing a
     * command.
     *
     * RU: Отправитель не зависит от классов конкретных команд и получателей.
     * Отправитель передаёт запрос получателю косвенно, выполняя команду.
     */
    public function doSomethingImportant(): void
    {
        echo "Invoker: Does anybody want something done before I begin?\n";
        if ($this->onStart instanceof Command) {
            $this->onStart->execute();
        }

        echo "Invoker: ...doing something really important...\n";

        echo "Invoker: Does anybody want something done after I finish?\n";
        if ($this->onFinish instanceof Command) {
            $this->onFinish->execute();
        }
    }
}

/**
 * EN: The client code can parameterize an invoker with any commands.
 *
 * RU: Клиентский код может параметризовать отправителя любыми командами.
 */
$invoker = new Invoker();
$invoker->setOnStart(new SimpleCommand("Say Hi!"));
$receiver = new Receiver();
$invoker->setOnFinish(new ComplexCommand($receiver, "Send email", "Save report"));

$invoker->doSomethingImportant();
