<?php

namespace RefactoringGuru\Composite\Structural;

/**
 * EN: Composite Design Pattern
 *
 * Intent: Compose objects into tree structures to represent part-whole
 * hierarchies. Composite lets clients treat individual objects and compositions
 * of objects uniformly.
 *
 * RU: Паттерн Компоновщик
 *
 * Назначение: Объединяет объекты в древовидные структуры для представления
 * иерархий часть-целое. Компоновщик позволяет клиентам обрабатывать отдельные объекты
 * и композиции объектов одинаковым образом.
 */

/**
 * EN:
 * The base Component class declares common operations for both simple and
 * complex objects of a composition.
 *
 * RU:
 * Базовый класс Компонент объявляет общие операции как для простых,
 * так и для сложных объектов композиции.
 */
abstract class Component
{
    /**
     * @var Component
     */
    protected $parent;

    /**
     * EN:
     * The base Component may implement some default behavior or leave it to
     * concrete classes (by declaring the method containing the behavior as
     * "abstract").
     *
     * RU:
     * Базовый Компонент может сам реализовать некоторое поведение по умолчанию
     * или поручить это конкретным классам, объявив метод, содержащий поведение как «абстракцию».
     */
    public abstract function operation();

    /**
     * Optionally, the base Component can declare an interface for setting and
     * accessing a parent of the component in a tree structure. It can also
     * provide some default implementation for these methods.
     */
    public function setParent(Component $parent)
    {
        $this->parent = $parent;
    }

    public function getParent(): Component
    {
        return $this->parent;
    }

    /**
     * In some cases, it would be beneficial to define the child-management
     * operations right in the base Component class. This way, you won't need to
     * expose any concrete component classes to the client code, even during the
     * object tree assembly. The downside is that these methods will be empty
     * for the leaf-level components.
     */
    public function add(Component $component) { }

    public function remove(Component $component) { }

    /**
     * You can provide a method that lets the client code figure out whether a
     * component can bear children.
     */
    public function isComposite(): bool
    {
        return false;
    }
}

/**
 * The Leaf class represents the end objects of a composition. A leaf can't have
 * any children.
 *
 * Usually, it's the Leaf objects that do the actual work, whereas Composite
 * objects only delegate to their sub-components.
 */
class Leaf extends Component
{
    public function operation()
    {
        return "Leaf";
    }
}

/**
 * The Composite class represents the complex components that may have children.
 * Usually, the Composite objects delegate the actual work to their children and
 * then "sum-up" the result.
 */
class Composite extends Component
{
    /**
     * @var Component[]
     */
    protected $children = [];

    /**
     * A composite object can add or remove other components (both simple or
     * complex) to or from its child list.
     */
    public function add(Component $component)
    {
        $this->children[] = $component;
        $component->setParent($this);
    }

    public function remove(Component $component)
    {
        $this->children = array_filter($this->children, function ($child) use ($component) {
            return $child == $component;
        });
        $component->setParent(null);
    }

    public function isComposite(): bool
    {
        return true;
    }

    /**
     * The Composite executes the primary component's logic in a particular way.
     * It traverses recursively through all its children, collecting and summing
     * their results. Since the composite's children pass these calls to their
     * children and so forth, the whole object tree is traversed as a result.
     */
    public function operation()
    {
        $results = [];
        foreach ($this->children as $child) {
            $results[] = $child->operation();
        }

        return "Branch(".implode("+", $results).")";
    }
}

/**
 * The client code works with all of the components via the base interface.
 */
function clientCode(Component $component)
{
    // ...

    print("RESULT: ".$component->operation());

    // ...
}

/**
 * This way the client code can support the simple leaf components...
 */
$simple = new Leaf();
print("Client: I get a simple component:\n");
clientCode($simple);
print("\n\n");

/**
 * ... as well as the complex composites.
 */
$tree = new Composite();
$branch1 = new Composite();
$branch1->add(new Leaf());
$branch1->add(new Leaf());
$branch2 = new Composite();
$branch2->add(new Leaf());
$tree->add($branch1);
$tree->add($branch2);
print("Client: Now I get a composite tree:\n");
clientCode($tree);
print("\n\n");

/**
 * Thanks to the fact that the child-management operations are declared in the
 * base Component class, the client code can work with any component, simple or
 * complex, without depending on their concrete classes.
 */
function clientCode2(Component $component1, Component $component2)
{
    // ...

    if ($component1->isComposite()) {
        $component1->add($component2);
    }
    print("RESULT: ".$component1->operation());

    // ...
}

print("Client: I can merge two components without checking their classes:\n");
clientCode2($tree, $simple);
