<?php

namespace RefactoringGuru\Composite\Structure;

/**
 * Composite Design Pattern
 *
 * Intent: Compose objects into tree structures to represent part-whole
 * hierarchies. Composite lets clients treat individual objects and
 * compositions of objects uniformly.
 */

/**
 * Base Component declares common operations for simple and complex objects
 * in the composition.
 *
 * Declare an interface for accessing and managing its child
 * components.
 */
abstract class Component
{
    /**
     * @var Component
     */
    protected $parent;

    /**
     * Base Component may implement some default behavior or leave it to the
     * concrete classes while staying abstract.
     */
    public abstract function operation();

    /**
     * Optionally, the base Component can declare interface for setting and
     * accessing its parent in the recursive structure, and implement it if
     * that's appropriate.
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
     * In some cases it would be beneficial to define child-management
     * operations right in the base component class. This way, you won't need
     * to expose the composite classes to client even during the tree assembly.
     */
    public function add(Component $component) { }

    public function remove(Component $component) { }

    /**
     * In these cases, special method is required to figure out that the
     * component can bear children.
     */
    public function isComposite(): bool
    {
        return false;
    }
}

/**
 * Leaf class represents end objects in the composition. A leaf has no
 * children. Leaf classes define behavior for primitive objects in the
 * composition.
 */
class Leaf extends Component
{
    public function operation()
    {
        return "Leaf";
    }
}

/**
 * Composite defines behavior for components having children.
 * Store child components.
 * Implement child-related operations in the Component interface.
 */
class Composite extends Component
{
    /**
     * @var Component[]
     */
    protected $children = [];

    /**
     * Composite object can add or remove other components—simple and complex—to
     * or from its child list.
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
     * Composite executes the main component logic in a special way. It
     * traverses recursively through all its children, collects and sums-up
     * their results. The whole tree will be traversed this way, since
     * composite children pass these calls to their children and so forth.
     */
    public function operation()
    {
        $results = [];
        foreach ($this->children as $child) {
            $results[] = $child->operation();
        }
        return "Branch(" . implode("+", $results) . ")";
    }
}


/**
 * The Client code works with all components using the base interface.
 */
function clientCode(Component $component)
{
    //...

    echo "CLIENT SAYS: " . $component->operation();

    //...
}

/**
 * This way Client code can support both simple leaf components...
 */
$simple = new Leaf();
echo "Client code gets a simple component:\n";
clientCode($simple);
echo "\n\n";

/**
 * ...and complex composites.
 */
$tree = new Composite();
$branch1 = new Composite();
$branch1->add(new Leaf());
$branch1->add(new Leaf());
$branch2 = new Composite();
$branch2->add(new Leaf());
$tree->add($branch1);
$tree->add($branch2);
echo "Same client code gets a composite tree:\n";
clientCode($tree);
echo "\n\n";


/**
 * Thanks to operations in base class, client can work with composite
 * operations without depending on concrete composite classes.
 */
function clientCode2(Component $component1, Component $component2)
{
    // ...

    if ($component1->isComposite()) {
        $component1->add($component2);
    }
    echo $component1->operation();

    // ...
}

echo "Client merges two components without checking their classes:\n";
clientCode2($tree, $simple);