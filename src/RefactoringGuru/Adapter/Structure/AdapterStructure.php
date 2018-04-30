<?php

namespace RefactoringGuru\Adater\Structure;

/**
 * Adapter Design Pattern
 *
 * Intent: Convert the interface of a class into another interface clients
 * expect. Adapter lets classes work together that couldn't otherwise because of
 * incompatible interfaces.
 */

/**
 * Defines the domain-specific interface that Client uses.
 */
class Target
{
    public function request()
    {
        return "Default target behavior.";
    }
}

/**
 * Defines a useful, but incompatible interface that needs adapting.
 */
class Adaptee
{
    public function specificRequest()
    {
        return ".eetpadA fo roivaheb laicepS";
    }
}

/**
 * Converts the interface of Adaptee to the Target interface.
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
        return "TRANSLATED: " . strrev($this->adaptee->specificRequest());
    }
}

/**
 * Client code supports all classes, that follow the Target interface.
 */
function clientCode(Target $target)
{
    print($target->request());
}

print("Client code correctly works with normal targets:\n");
$target = new Target();
clientCode($target);
print("\n\n");

$adaptee = new Adaptee();
print("External adaptee has a weird interface, client code does not understand it:\n");
print($adaptee->specificRequest());
print("\n\n");


print("But the same client code can work fine with external adaptee via adapter:\n");
$adapter = new Adapter($adaptee);
clientCode($adapter);