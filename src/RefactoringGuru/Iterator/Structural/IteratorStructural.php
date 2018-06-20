<?php

namespace RefactoringGuru\Iterator\Structural;

/**
 * EN: Iterator Design Pattern
 *
 * Intent: Provide a way a way to access the elements of an aggregate object without
 * exposing its underlying representation.
 *
 * RU: Паттерн Итератор
 *
 * Назначение: Предоставляет возможность обращаться к элементам составного объекта,
 * не раскрывая его внутреннего представления.
 */

use Iterator;

/**
 * EN:
 * PHP has a built-in Iterator interface that provides a very convenient
 * integration with foreach loops.
 * 
 * RU:
 * PHP имеет встроенный интерфейс Итератора, который предоставляет 
 * очень удобную интеграцию с циклами foreach.
 * 
 * EN: Here's what the interface looks like:
 *
 * RU: Вот как выглядит интерфейс:
 *
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
 * EN: There's also a built-in interface for collections:
 *
 * RU: Также есть встроенный интерфейс для коллекций:
 *
 * @link http://php.net/manual/en/class.iteratoraggregate.php
 *
 *     interface IteratorAggregate extends Traversable {
 *         public getIterator(): Traversable;
 *     }
 */

/**
 * EN: 
 * Concrete Iterators implement various traversal algorithms. These classes
 * store the current traversal position at all times.
 *
 * RU:
 * Конкретные Итераторы реализуют различные алгоритмы обхода.
 * Эти классы постоянно хранят текущее положение обхода.
 */
class AlphabeticalOrderIterator implements \Iterator
{
    /**
     * @var WordsCollection
     */
    private $collection;

    /**
     * EN: 
     * @var int Stores the current traversal position. An iterator may have a
     * lot of other fields for storing iteration state, especially when it is
     * supposed to work with a particular kind of collection.
     *
     * RU:
     * @var int Хранит текущее положение обхода. У итератора может быть множество
     * других полей для хранения состояния итерации, особенно когда он должен работать
     * с определённым типом коллекции.
     */
    private $position = 0;

    /**
     * EN:
     * @var bool This variable indicates the traversal direction.
     *
     * RU:
     * @var bool Эта переменная указывает направление обхода.
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
 * EN:
 * Concrete Collections provide one or several methods for retrieving fresh
 * iterator instances, compatible with the collection class.
 *
 * RU:
 * Конкретные Коллекции предоставляют один или несколько методов для получения
 * новых экземпляров итератора, совместимых с классом коллекции.
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
 * EN:
 * The client code may or may not know about the Concrete Iterator or Collection
 * classes, depending on the level of indirection you want to keep in your
 * program.
 *
 * RU:
 * Клиентский код может знать или не знать о Конкретном Итераторе или классах Коллекций,
 * в зависимости от уровня косвенности, который вы хотите сохранить в своей программе.
 */
$collection = new WordsCollection();
$collection->addItem("First");
$collection->addItem("Second");
$collection->addItem("Third");

print("Straight traversal:\n");
foreach ($collection->getIterator() as $item) {
    print($item."\n");
}

print("\n");
print("Reverse traversal:\n");
foreach ($collection->getReverseIterator() as $item) {
    print($item."\n");
}
