<?php

namespace RefactoringGuru\Memento\Structure;

/**
 * Memento Design Pattern
 *
 * Intent: Capture and externalize an object's internal state so that the object
 * can be restored to this state later, without violating encapsulation.
 */

/**
 * The Originator holds some important state that may change with time. It also
 * defines a method for saving the state inside a memento and another method for
 * restoring the state from a memento.
 */
class Originator
{
    /**
     * @var mixed For the sake of simplicity, originator's state will be stored
     * inside a single variable.
     */
    private $state;

    public function __construct($state)
    {
        $this->state = $state;
        print("Originator: My initial state is: {$this->state} \n");
    }

    /**
     * The Originator's business logic may affect its internal state. Therefore,
     * client should backup the state prior to launching methods of the business
     * logic via the save() method.
     */
    public function doSomething()
    {
        print("Originator: I'm doing something important.\n");
        $this->state = $this->generateRandomString(30);
        print("Originator: and my state has changed to: {$this->state}\n");
    }

    private function generateRandomString($length = 10)
    {
        return substr(
            str_shuffle(
                str_repeat(
                    $x = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                    ceil($length / strlen($x)))), 1, $length);
    }

    /**
     * Save current state inside a memento.
     *
     * @return Memento
     */
    public function save(): Memento
    {
        return new ConcreteMemento($this->state);
    }

    /**
     * Restore originator's state from a memento object.
     *
     * @param Memento $memento
     * @throws \Exception
     */
    public function restore(Memento $memento)
    {
        if (! $memento instanceof ConcreteMemento) {
            throw new \Exception("Unknown memento class ".get_class($memento));
        }

        $this->state = $memento->getState();
        print("Originator: My state has changed to: {$this->state}\n");
    }
}

/**
 * Memento interface provides a way to retrieve the memento's meta data, such as
 * a creation date. However, it doesn't expose the originator's state.
 */
interface Memento
{
    public function getName();

    public function getDate();
}

/**
 * Concrete Memento contains the infrastructure for storing Originator's state.
 */
class ConcreteMemento implements Memento
{
    private $state;

    private $date;

    public function __construct($state)
    {
        $this->state = $state;
        $this->date = date('Y-m-d H:i:s');
    }

    /**
     * The Originator uses this method when restoring its state.
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * The rest of the methods are used by the Caretaker to display meta data.
     */
    public function getName()
    {
        return $this->date." / (".substr($this->state, 0, 9)."...)";
    }

    public function getDate()
    {
        return $this->date;
    }
}

/**
 * Caretaker doesn't depend on the Concrete Memento class. Therefore, it doesn't
 * have access to originator's state, stored inside the memento. It works with
 * all mementos via the common Memento interface.
 */
class Caretaker
{
    /**
     * @var Memento[]
     */
    private $mementos = [];

    /**
     * @var Originator
     */
    private $originator;

    public function __construct(Originator $originator)
    {
        $this->originator = $originator;
    }

    public function backup()
    {
        print("\nCaretaker: Saving Originator's state...\n");
        $this->mementos[] = $this->originator->save();
    }

    public function undo()
    {
        if (! count($this->mementos)) {
            return;
        }
        $memento = array_pop($this->mementos);

        print("Caretaker: Restoring state to: ".$memento->getName()."\n");
        try {
            $this->originator->restore($memento);
        } catch (\Exception $e) {
            $this->undo();
        }
    }

    public function showHistory()
    {
        print("Caretaker: Here's the list of mementos:\n");
        foreach ($this->mementos as $memento) {
            print($memento->getName()."\n");
        }
    }
}

/**
 * Client code.
 */
$originator = new Originator("Super-duper-super-puper-super.");
$caretaker = new Caretaker($originator);

$caretaker->backup();
$originator->doSomething();

$caretaker->backup();
$originator->doSomething();

$caretaker->backup();
$originator->doSomething();

print("\n");
$caretaker->showHistory();

print("\nClient: Now, let's rollback!\n\n");
$caretaker->undo();

print("\nClient: Once more!\n\n");
$caretaker->undo();