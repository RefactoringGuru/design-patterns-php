<?php

namespace RefactoringGuru\Adater\Structure;

/**
 * Adapter Design Pattern
 *
 * Intent: Convert the interface of a class into the interface clients expect.
 * Adapter lets classes work together that couldn't otherwise because of
 * incompatible interfaces.
 */

/**
 * The Target defines the domain-specific interface used by the client code.
 */
class Target
{
    public function request()
    {
        return "Target: The default target's behavior.";
    }
}

/**
 * The Adaptee contains some useful behavior, but its interface is incompatible
 * with the existing client code. The Adaptee needs some adaptation before it
 * can be used by the client code.
 */
class Adaptee
{
    public function specificRequest()
    {
        return ".eetpadA eht fo roivaheb laicepS";
    }
}

/**
 * The Adapter makes the Adaptee's interface compatible with the Target's
 * interface.
 */
class Adapter extends Target
{
    private $adaptee;

    public function __construct(Adaptee $adaptee)
    {
        $this->adaptee = $adaptee;
    }

    public function request()
    {
        return "Adapter: (TRANSLATED) ".strrev($this->adaptee->specificRequest());
    }
}

/**
 * The client code supports all classes that follow the Target interface.
 */
function clientCode(Target $target)
{
    print($target->request());
}

print("Client: I can work just fine with the Target objects:\n");
$target = new Target();
clientCode($target);
print("\n\n");

$adaptee = new Adaptee();
print("Client: The Adaptee class has a weird interface. See, I don't understand it:\n");
print($adaptee->specificRequest());
print("\n\n");

print("Client: But I can work with it via the Adapter:\n");
$adapter = new Adapter($adaptee);
clientCode($adapter);