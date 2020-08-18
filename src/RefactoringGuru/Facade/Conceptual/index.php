<?php

namespace RefactoringGuru\Facade\Conceptual;

/**
 * EN: Facade Design Pattern
 *
 * Intent: Provides a simplified interface to a library, a framework, or any
 * other complex set of classes.
 *
 * RU: Паттерн Фасад
 *
 * Назначение: Предоставляет простой интерфейс к сложной системе классов,
 * библиотеке или фреймворку.
 */

/**
 * EN: The Facade class provides a simple interface to the complex logic of one
 * or several subsystems. The Facade delegates the client requests to the
 * appropriate objects within the subsystem. The Facade is also responsible for
 * managing their lifecycle. All of this shields the client from the undesired
 * complexity of the subsystem.
 *
 * RU: Класс Фасада предоставляет простой интерфейс для сложной логики одной или
 * нескольких подсистем. Фасад делегирует запросы клиентов соответствующим
 * объектам внутри подсистемы. Фасад также отвечает за управление их жизненным
 * циклом. Все это защищает клиента от нежелательной сложности подсистемы.
 */
class Facade
{
    protected $subsystem1;

    protected $subsystem2;

    /**
     * EN: Depending on your application's needs, you can provide the Facade
     * with existing subsystem objects or force the Facade to create them on its
     * own.
     *
     * RU: В зависимости от потребностей вашего приложения вы можете
     * предоставить Фасаду существующие объекты подсистемы или заставить Фасад
     * создать их самостоятельно.
     */
    public function __construct(
        Subsystem1 $subsystem1 = null,
        Subsystem2 $subsystem2 = null
    ) {
        $this->subsystem1 = $subsystem1 ?: new Subsystem1();
        $this->subsystem2 = $subsystem2 ?: new Subsystem2();
    }

    /**
     * EN: The Facade's methods are convenient shortcuts to the sophisticated
     * functionality of the subsystems. However, clients get only to a fraction
     * of a subsystem's capabilities.
     *
     * RU: Методы Фасада удобны для быстрого доступа к сложной функциональности
     * подсистем. Однако клиенты получают только часть возможностей подсистемы.
     */
    public function operation(): string
    {
        $result = "Facade initializes subsystems:\n";
        $result .= $this->subsystem1->operation1();
        $result .= $this->subsystem2->operation1();
        $result .= "Facade orders subsystems to perform the action:\n";
        $result .= $this->subsystem1->operationN();
        $result .= $this->subsystem2->operationZ();

        return $result;
    }
}

/**
 * EN: The Subsystem can accept requests either from the facade or client
 * directly. In any case, to the Subsystem, the Facade is yet another client,
 * and it's not a part of the Subsystem.
 *
 * RU: Подсистема может принимать запросы либо от фасада, либо от клиента
 * напрямую. В любом случае, для Подсистемы Фасад – это еще один клиент, и он не
 * является частью Подсистемы.
 */
class Subsystem1
{
    public function operation1(): string
    {
        return "Subsystem1: Ready!\n";
    }

    // ...

    public function operationN(): string
    {
        return "Subsystem1: Go!\n";
    }
}

/**
 * EN: Some facades can work with multiple subsystems at the same time.
 *
 * RU: Некоторые фасады могут работать с разными подсистемами одновременно.
 */
class Subsystem2
{
    public function operation1(): string
    {
        return "Subsystem2: Get ready!\n";
    }

    // ...

    public function operationZ(): string
    {
        return "Subsystem2: Fire!\n";
    }
}

/**
 * EN: The client code works with complex subsystems through a simple interface
 * provided by the Facade. When a facade manages the lifecycle of the subsystem,
 * the client might not even know about the existence of the subsystem. This
 * approach lets you keep the complexity under control.
 *
 * RU: Клиентский код работает со сложными подсистемами через простой интерфейс,
 * предоставляемый Фасадом. Когда фасад управляет жизненным циклом подсистемы,
 * клиент может даже не знать о существовании подсистемы. Такой подход позволяет
 * держать сложность под контролем.
 */
function clientCode(Facade $facade)
{
    // ...

    echo $facade->operation();

    // ...
}

/**
 * EN: The client code may have some of the subsystem's objects already created.
 * In this case, it might be worthwhile to initialize the Facade with these
 * objects instead of letting the Facade create new instances.
 *
 * RU: В клиентском коде могут быть уже созданы некоторые объекты подсистемы. В
 * этом случае может оказаться целесообразным инициализировать Фасад с этими
 * объектами вместо того, чтобы позволить Фасаду создавать новые экземпляры.
 */
$subsystem1 = new Subsystem1();
$subsystem2 = new Subsystem2();
$facade = new Facade($subsystem1, $subsystem2);
clientCode($facade);
