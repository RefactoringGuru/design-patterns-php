<?php

namespace RefactoringGuru\Prototype\Conceptual;

/**
 * EN: Prototype Design Pattern
 *
 * Intent: Lets you copy existing objects without making your code dependent on
 * their classes.
 *
 * RU: Паттерн Прототип
 *
 * Назначение: Позволяет копировать объекты, не вдаваясь в подробности их
 * реализации.
 */

/**
 * EN: The example class that has cloning ability. We'll see how the values of
 * field with different types will be cloned.
 *
 * RU: Пример класса, имеющего возможность клонирования. Мы посмотрим как
 * происходит клонирование значений полей разных типов.
 */
class Prototype
{
    public $primitive;
    public $component;
    public $circularReference;

    /**
     * EN: PHP has built-in cloning support. You can `clone` an object without
     * defining any special methods as long as it has fields of primitive types.
     * Fields containing objects retain their references in a cloned object.
     * Therefore, in some cases, you might want to clone those referenced
     * objects as well. You can do this in a special `__clone()` method.
     *
     * RU: PHP имеет встроенную поддержку клонирования. Вы можете «клонировать»
     * объект без определения каких-либо специальных методов, при условии, что
     * его поля имеют примитивные типы. Поля, содержащие объекты, сохраняют свои
     * ссылки в клонированном объекте. Поэтому в некоторых случаях вам может
     * понадобиться клонировать также и вложенные объекты. Это можно сделать
     * специальным методом clone.
     */
    public function __clone()
    {
        $this->component = clone $this->component;

        // EN: Cloning an object that has a nested object with backreference
        // requires special treatment. After the cloning is completed, the
        // nested object should point to the cloned object, instead of the
        // original object.
        //
        // RU: Клонирование объекта, который имеет вложенный объект с обратной
        // ссылкой, требует специального подхода. После завершения клонирования
        // вложенный объект должен указывать на клонированный объект, а не на
        // исходный объект.
        $this->circularReference = clone $this->circularReference;
        $this->circularReference->prototype = $this;
    }
}

class ComponentWithBackReference
{
    public $prototype;

    /**
     * EN: Note that the constructor won't be executed during cloning. If you
     * have complex logic inside the constructor, you may need to execute it in
     * the `__clone` method as well.
     *
     * RU: Обратите внимание, что конструктор не будет выполнен во время
     * клонирования. Если у вас сложная логика внутри конструктора, вам может
     * потребоваться выполнить ее также и в методе clone.
     */
    public function __construct(Prototype $prototype)
    {
        $this->prototype = $prototype;
    }
}

/**
 * EN: The client code.
 *
 * RU: Клиентский код.
 */
function clientCode()
{
    $p1 = new Prototype();
    $p1->primitive = 245;
    $p1->component = new \DateTime();
    $p1->circularReference = new ComponentWithBackReference($p1);

    $p2 = clone $p1;
    if ($p1->primitive === $p2->primitive) {
        echo "Primitive field values have been carried over to a clone. Yay!\n";
    } else {
        echo "Primitive field values have not been copied. Booo!\n";
    }
    if ($p1->component === $p2->component) {
        echo "Simple component has not been cloned. Booo!\n";
    } else {
        echo "Simple component has been cloned. Yay!\n";
    }

    if ($p1->circularReference === $p2->circularReference) {
        echo "Component with back reference has not been cloned. Booo!\n";
    } else {
        echo "Component with back reference has been cloned. Yay!\n";
    }

    if ($p1->circularReference->prototype === $p2->circularReference->prototype) {
        echo "Component with back reference is linked to original object. Booo!\n";
    } else {
        echo "Component with back reference is linked to the clone. Yay!\n";
    }
}

clientCode();
