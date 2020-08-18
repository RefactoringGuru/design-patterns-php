<?php

namespace RefactoringGuru\Adapter\Conceptual;

/**
 * EN: Adapter Design Pattern
 *
 * Intent: Provides a unified interface that allows objects with incompatible
 * interfaces to collaborate.
 *
 * RU: Паттерн Адаптер
 *
 * Назначение: Позволяет объектам с несовместимыми интерфейсами работать вместе.
 */

/**
 * EN: The Target defines the domain-specific interface used by the client code.
 *
 * RU: Целевой класс объявляет интерфейс, с которым может работать клиентский
 * код.
 */
class Target
{
    public function request(): string
    {
        return "Target: The default target's behavior.";
    }
}

/**
 * EN: The Adaptee contains some useful behavior, but its interface is
 * incompatible with the existing client code. The Adaptee needs some adaptation
 * before the client code can use it.
 *
 * RU: Адаптируемый класс содержит некоторое полезное поведение, но его
 * интерфейс несовместим с существующим клиентским кодом. Адаптируемый класс
 * нуждается в некоторой доработке, прежде чем клиентский код сможет его
 * использовать.
 */
class Adaptee
{
    public function specificRequest(): string
    {
        return ".eetpadA eht fo roivaheb laicepS";
    }
}

/**
 * EN: The Adapter makes the Adaptee's interface compatible with the Target's
 * interface.
 *
 * RU: Адаптер делает интерфейс Адаптируемого класса совместимым с целевым
 * интерфейсом.
 */
class Adapter extends Target
{
    private $adaptee;

    public function __construct(Adaptee $adaptee)
    {
        $this->adaptee = $adaptee;
    }

    public function request(): string
    {
        return "Adapter: (TRANSLATED) " . strrev($this->adaptee->specificRequest());
    }
}

/**
 * EN: The client code supports all classes that follow the Target interface.
 *
 * RU: Клиентский код поддерживает все классы, использующие целевой интерфейс.
 */
function clientCode(Target $target)
{
    echo $target->request();
}

echo "Client: I can work just fine with the Target objects:\n";
$target = new Target();
clientCode($target);
echo "\n\n";

$adaptee = new Adaptee();
echo "Client: The Adaptee class has a weird interface. See, I don't understand it:\n";
echo "Adaptee: " . $adaptee->specificRequest();
echo "\n\n";

echo "Client: But I can work with it via the Adapter:\n";
$adapter = new Adapter($adaptee);
clientCode($adapter);
