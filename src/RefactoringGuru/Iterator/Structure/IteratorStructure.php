<?php

namespace RefactoringGuru\Iterator\Structure;

/**
 * Iterator Design Pattern
 *
 * Intent: Provide a way to access the elements of an aggregate objects
 * without exposing its underlying representation.
 */

use Iterator;

/**
 * PHP has a built-in Iterator interface that provides a very
 * convenient integration with foreach loops.
 *
 * Here's how the interface looks like:
 * @link http://php.net/manual/en/class.iterator.php
 *
 *     interface Iterator extends Traversable {
 *         // Return the current element
 *         public function current();
 *    
 *         // Move forward to next element
 *         public function next();
 *    
 *         // Return the key of the current element
 *         public function key();
 *    
 *         // Checks if current position is valid
 *         public function valid();
 *    
 *         // Rewind the Iterator to the first element
 *         public function rewind();
 *     }
 *
 * There's also a built-in interface for collections:
 * @link http://php.net/manual/en/class.iteratoraggregate.php
 *
 *     interface IteratorAggregate extends Traversable {
 *         public getIterator(): Traversable;
 *     }
 */

/**
 * Concrete Iterator implements various traversal algorythms.
 * It stores the current traversal position at all times.
 */
class AlphabeticalOrderIterator implements \Iterator
{
    /**
     * @var WordsCollection
     */
    private $collection;

    /**
     * @var int Stores the current traversal position. An iterator may have a
     * lot of other fields for storing iteration state, especially when it is
     * supposed to works with a special kind of collection.
     */
    private $position = 0;

    /**
     * @var bool This variable indicates the traversal direction.
     */
    private $reverse = false;

    public function __construct($collection, $reverse = false)
    {
        $this->collection = $collection;
        $this->reverse = $reverse;
    }

    public function rewind()
    {
        $this->position = $this->reverse ?
            count($this->collection->getItems()) - 1 : 0;
    }

    public function current()
    {
        return $this->collection->getItems()[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        $this->position = $this->position + ($this->reverse ? -1 : 1);
    }

    public function valid()
    {
        return isset($this->collection->getItems()[$this->position]);
    }
}

/**
 * Concrete Collection provides one or several methods for retrieving a fresh
 * iterator instance, compatible with this collection.
 */
class WordsCollection implements \IteratorAggregate
{
    private $items = [];

    public function getItems()
    {
        return $this->items;
    }

    public function addItem($item)
    {
        $this->items[] = $item;
    }

    public function getIterator(): Iterator
    {
        return new AlphabeticalOrderIterator($this);
    }

    public function getReverseIterator(): Iterator
    {
        return new AlphabeticalOrderIterator($this, true);
    }
}

/**
 * Client code may or may not know about concrete iterator and collection
 * classes, depending on the level of indirection you want to keep in your
 * program.
 */
$collection = new WordsCollection();
$collection->addItem("First");
$collection->addItem("Second");
$collection->addItem("Third");

print("Strait traversal:\n");
foreach ($collection->getIterator() as $item) {
    print($item."\n");
}

print("\n");
print("Reverse traversal:\n");
foreach ($collection->getReverseIterator() as $item) {
    print($item."\n");
}
