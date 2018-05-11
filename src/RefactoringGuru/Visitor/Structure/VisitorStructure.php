<?php

namespace RefactoringGuru\Visitor\Structure;

/**
 * Visitor Design Pattern
 *
 * Intent: Represent an operation to be performed over elements of an object
 * structure. The Visitor pattern lets you define a new operation without
 * changing the classes of the elements on which it operates.
 */

/**
 * EN: The Component interface declares an `accept` method that should take the
 * base visitor interface as an argument.
 *
 * RU: Интерфейс компонентов должен объявлять метод, принимающий базовый
 * интерфейс посетителей в качестве аргумента.
 */
interface Component
{
    function accept(Visitor $visitor);
}

/**
 * EN: Each Concrete Component must implement the `accept` method in a such a
 * way that it calls the visitor's method, corresponding to the component's
 * class.
 *
 * RU: Каждый конкретный компонент должен реализовать метод `accept` таким
 * образом, чтобы вызвать метод посетителя, соотвествующий текущему классу.
 */
class ConcreteComponentA implements Component
{
    /**
     * EN: Note that we're calling `visitConcreteComponentA`, which matches the
     * current class name. This way we will let the visitor know the class of
     * the component it works with.
     *
     * RU: Обратите внимание, что мы вызываем `visitConcreteComponentA`, что
     * совпадает с названием  текущего класса. Этим мы дадим посетителю знать с
     * каким классом компонентов он сейчас работает.
     */
    function accept(Visitor $visitor)
    {
        $visitor->visitConcreteComponentA($this);
    }

    /**
     * EN: Concrete Components may have special methods that don't exists in
     * their base class or interface. The Visitor will still be able to use
     * these methods, since it's aware of the component's concrete class.
     *
     * RU: Конкретные Компоненты могут иметь особые методы, не объявленные в их
     * базовом классе или интерфейсе. Несмотря на это, посетитель всё-равно
     * сможет их использовать, так как знает конкретный класс компонента.
     */
    function exclusiveMethodOfConcreteComponentA()
    {
        return "A";
    }
}

class ConcreteComponentB implements Component
{
    /**
     * EN: Same here: visitConcreteComponentB => ConcreteComponentB
     *
     * RU: То же самое: visitConcreteComponentB => ConcreteComponentB
     */
    function accept(Visitor $visitor)
    {
        $visitor->visitConcreteComponentB($this);
    }

    function specialMethodOfConcreteComponentB()
    {
        return "B";
    }
}

/**
 * EN: The Visitor Interface declares a set of visiting methods that correspond
 * to component classes. The signature of a visiting method allows the visitor
 * to identify the exact class of the component that it's dealing with.
 *
 * RU: Интерфейс Посетителя объявляет набор методов посещения для каждого класса
 * компонента. Сигнатуры этих методов позволяют посетителю определить конкретный
 * класс компонента, с которым он работает.
 */
interface Visitor
{
    public function visitConcreteComponentA(ConcreteComponentA $element);

    public function visitConcreteComponentB(ConcreteComponentB $element);
}

/**
 * EN: Concrete Visitors implement several versions of the same algorithm, which
 * are able to work with all concrete component classes.
 *
 * You can experience the biggest benefit of the Visitor pattern when using it
 * with a complex object structure, such as a Composite tree. In this case, it
 * might be helpful to store some intermediate state of the algorithm while
 * executing visitor's methods over various objects of the structure.
 *
 * RU: Конкретные посетители реализуют несколько версий какого-то одного
 * алгоритма, способные работать со всеми классами конкретных компонентов.
 *
 * Главная польза паттерна проявляется тогда, когда вы используете посетитель не
 * с отдельным объектами компонентов, а с целыми структурами разнорозных
 * компонентов, вроде дерева Компоновщика. В этом случае, посетитель может быть
 * реализован так, чтобы накоплять состояние при последовательном запуске своих
 * методов.
 */
class ConcreteVisitor1 implements Visitor
{
    public function visitConcreteComponentA(ConcreteComponentA $element)
    {
        print($element->exclusiveMethodOfConcreteComponentA()." + ConcreteVisitor1\n");
    }

    public function visitConcreteComponentB(ConcreteComponentB $element)
    {
        print($element->specialMethodOfConcreteComponentB()." + ConcreteVisitor1\n");
    }
}

class ConcreteVisitor2 implements Visitor
{
    public function visitConcreteComponentA(ConcreteComponentA $element)
    {
        print($element->exclusiveMethodOfConcreteComponentA()." + ConcreteVisitor1\n");
    }

    public function visitConcreteComponentB(ConcreteComponentB $element)
    {
        print($element->specialMethodOfConcreteComponentB()." + ConcreteVisitor2\n");
    }
}

/**
 * EN: The client code can run visitor operations over any set of elements without
 * figuring out their concrete classes. The accept operation directs call to the
 * appropriate operation in the visitor object.
 *
 * RU: Клиентский код может запускать операции посетителя над любым набором
 * компонентов, не привязываясь к их конкретным классам. Метод `accept`
 * позаботится о том, чтобы вызвать метод посетителя, соответствующий данному
 * классу компонента.
 */
function clientCode(array $components, Visitor $visitor)
{
    // ...
    foreach ($components as $component) {
        $component->accept($visitor);
    }
    // ...
}

$components = [
    new ConcreteComponentA(),
    new ConcreteComponentB(),
];

print("The client code works with all visitors via the base Visitor interface:\n");
$visitor1 = new ConcreteVisitor1();
clientCode($components, $visitor1);
print("\n");

print("It allows the same client code to work with different types of visitors:\n");
$visitor2 = new ConcreteVisitor2();
clientCode($components, $visitor2);
