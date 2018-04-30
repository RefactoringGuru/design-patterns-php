<?php

namespace RefactoringGuru\Prototype\Structure;

/**
 * Prototype Design Pattern
 *
 * Intent: Specify the kinds of objects to create using a prototypical instance,
 * and create new objects by copying this prototype.
 */

/**
 * Example class to be copied.
 */
class Prototype
{
    public $primitive;
    public $component;
    public $circularReference;

    /**
     * PHP has built-in cloning support. You can `clone` an object without
     * defying any special methods as long as it have fields of primitive
     * types. Fields that contain objects, retain their references to old
     * sub-object in a cloned object. Therefore, in some cases you might
     * want to clone those referenced objects as well. You can do this in a
     * special `__clone()` method.
     */
    public function __clone()
    {
        $this->component = clone $this->component;

        // Components with back references require special treatment so that
        // their references point to cloned object, but not the original.
        $this->circularReference = clone $this->circularReference;
        $this->circularReference->prototype = $this;
    }
}

class ComponentWithBackReference
{
    public $prototype;

    /**
     * Note that the constructor won't be executed during cloning. If you
     * have a complex logic in constructor, you may need to execute it in
     * the `__clone` method as well.
     */
    public function __construct(Prototype $prototype)
    {
        $this->prototype = $prototype;
    }
}

/**
 * Client code.
 */
function clientCode()
{
    $p1 = new Prototype();
    $p1->primitive = 245;
    $p1->component = new \DateTime();
    $p1->circularReference = new ComponentWithBackReference($p1);

    $p2 = clone $p1;
    if ($p1->primitive === $p2->primitive) {
        print("Primitive field values have not been copied. Booo!\n");
    } else {
        print("Primitive field values have been carried over to a clone. Yay!\n");
    }
    if ($p1->component === $p2->component) {
        print("Simple component have not been cloned. Booo!\n");
    } else {
        print("Simple component have been cloned. Yay!\n");
    }

    if ($p1->circularReference === $p2->circularReference) {
        print("Component with back reference have not been cloned. Booo!\n");
    } else {
        print("Component with back reference have been cloned. Yay!\n");
    }

    if ($p1->circularReference->prototype === $p2->circularReference->prototype) {
        print("Component with back reference is linked to original object. Booo!\n");
    } else {
        print("Component with back reference is linked to the clone. Yay!\n");
    }
}

clientCode();
