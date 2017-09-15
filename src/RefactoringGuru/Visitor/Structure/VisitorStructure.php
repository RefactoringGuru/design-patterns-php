<?php

namespace RefactoringGuru\Visitor\Structure;

/**
 * Visitor Design Pattern
 *
 * Intent: Represent an operation to be performed on the elements of an object
 * structure. Visitor lets you define a new operation without changing the
 * classes of the elements on which it operates.
 */

/**
 * Accepting interface defines an `accept` operation that takes a base visitor
 * interface as an argument.
 */
interface AcceptsVisitors
{
    function accept(Visitor $visitor);
}

/**
 * Each concrete element accepting visitors, should implement the
 * accept operation by calling a method in visitor class that corresponds to
 * that element's class.
 */
class ConcreteElementA implements AcceptsVisitors
{
    /**
     * Note that we're calling `visitConcreteElementA` inside
     * ConcreteElementA. This way visitor will know the concrete type of
     * object it visits.
     */
    function accept(Visitor $visitor)
    {
        $visitor->visitConcreteElementA($this);
    }

    /**
     * Concrete elements could have special methods that does not exists in
     * their base class. Visitor has access to these methods, because it's
     * aware of the concrete element classes.
     */
    function exclusiveMethodOfConcreteElementA()
    {
        return "A";
    }
}

class ConcreteElementB implements AcceptsVisitors
{
    /**
     * Same here: visitConcreteElementB => ConcreteElementB
     */
    function accept(Visitor $visitor)
    {
        $visitor->visitConcreteElementB($this);
    }

    function specialMethodOfConcreteElementB()
    {
        return "B";
    }
}

/**
 * Visitor interface defines visiting operations for each ConcreteElement
 * class. The operation's name and signature identifies the class that sends
 * request to the visitor. That lets the visitor determine the concrete
 * class of the element being visited. Then the visitor can access the
 * element directly through its particular interface.
 */
interface Visitor
{
    public function visitConcreteElementA(ConcreteElementA $element);

    public function visitConcreteElementB(ConcreteElementB $element);
}

/**
 * Concrete Visitors implement specific algorithms and capable to execute
 * it over all types of elements. Concrete visitors implement all Visitor's
 * methods and thanks to them, know the concrete classes of elements they
 * work with.
 *
 * Concrete Visitor may not only work with separate element objects, but with
 * a complex element structure, for instance, composite tree. In this case,
 * Concrete Visitor can hold the local state of the algorithm. This state
 * often accumulates results during the traversal of the structure.
 */
class ConcreteVisitor1 implements Visitor
{
    public function visitConcreteElementA(ConcreteElementA $element)
    {
        echo $element->exclusiveMethodOfConcreteElementA() . " + ConcreteVisitor1\n";
    }

    public function visitConcreteElementB(ConcreteElementB $element)
    {
        echo $element->specialMethodOfConcreteElementB() . " + ConcreteVisitor1\n";
    }
}

class ConcreteVisitor2 implements Visitor
{
    public function visitConcreteElementA(ConcreteElementA $element)
    {
        echo $element->exclusiveMethodOfConcreteElementA() . " + ConcreteVisitor1\n";
    }

    public function visitConcreteElementB(ConcreteElementB $element)
    {
        echo $element->specialMethodOfConcreteElementB() . " + ConcreteVisitor2\n";
    }
}

/**
 * Client code can run visitor operations over any set of elements without
 * figuring out their concrete classes. The accept operation directs call to the
 * appropriate operation in the visitor object.
 */
function clientCode(array $elements, Visitor $visitor)
{
    // ...
    foreach ($elements as $element) {
        $element->accept($visitor);
    }
    // ...
}


$elements = [
    new ConcreteElementA(),
    new ConcreteElementB()
];

echo "Client code works with visitors through a common Visitor interface:\n";
$visitor1 = new ConcreteVisitor1();
clientCode($elements, $visitor1);
echo "\n";

echo "Same client code can work with different visitors:\n";
$visitor2 = new ConcreteVisitor2();
clientCode($elements, $visitor2);
