<?php

namespace RefactoringGuru\Memento\Conceptual;

/**
 * EN: Memento Design Pattern
 *
 * Intent: Lets you save and restore the previous state of an object without
 * revealing the details of its implementation.
 *
 * RU: Паттерн Снимок
 *
 * Назначение: Фиксирует и восстанавливает внутреннее состояние объекта таким
 * образом, чтобы в дальнейшем объект можно было восстановить в этом состоянии
 * без нарушения инкапсуляции.
 */

/**
 * EN: The Originator holds some important state that may change over time. It
 * also defines a method for saving the state inside a memento and another
 * method for restoring the state from it.
 *
 * RU: Создатель содержит некоторое важное состояние, которое может со временем
 * меняться. Он также объявляет метод сохранения состояния внутри снимка и метод
 * восстановления состояния из него.
 */
class Originator
{
    /**
     * EN: @var string For the sake of simplicity, the originator's state is
     * stored inside a single variable.
     *
     * RU: @var string Для удобства состояние создателя хранится внутри одной
     * переменной.
     */
    private $state;

    public function __construct(string $state)
    {
        $this->state = $state;
        echo "Originator: My initial state is: {$this->state}\n";
    }

    /**
     * EN: The Originator's business logic may affect its internal state.
     * Therefore, the client should backup the state before launching methods of
     * the business logic via the save() method.
     *
     * RU: Бизнес-логика Создателя может повлиять на его внутреннее состояние.
     * Поэтому клиент должен выполнить резервное копирование состояния с помощью
     * метода save перед запуском методов бизнес-логики.
     */
    public function doSomething(): void
    {
        echo "Originator: I'm doing something important.\n";
        $this->state = $this->generateRandomString(30);
        echo "Originator: and my state has changed to: {$this->state}\n";
    }

    private function generateRandomString(int $length = 10): string
    {
        return substr(
            str_shuffle(
                str_repeat(
                    $x = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                    ceil($length / strlen($x))
                )
            ),
            1,
            $length,
        );
    }

    /**
     * EN: Saves the current state inside a memento.
     *
     * RU: Сохраняет текущее состояние внутри снимка.
     */
    public function save(): Memento
    {
        return new ConcreteMemento($this->state);
    }

    /**
     * EN: Restores the Originator's state from a memento object.
     *
     * RU: Восстанавливает состояние Создателя из объекта снимка.
     */
    public function restore(Memento $memento): void
    {
        $this->state = $memento->getState();
        echo "Originator: My state has changed to: {$this->state}\n";
    }
}

/**
 * EN: The Memento interface provides a way to retrieve the memento's metadata,
 * such as creation date or name. However, it doesn't expose the Originator's
 * state.
 *
 * RU: Интерфейс Снимка предоставляет способ извлечения метаданных снимка, таких
 * как дата создания или название. Однако он не раскрывает состояние Создателя.
 */
interface Memento
{
    public function getName(): string;

    public function getDate(): string;
}

/**
 * EN: The Concrete Memento contains the infrastructure for storing the
 * Originator's state.
 *
 * RU: Конкретный снимок содержит инфраструктуру для хранения состояния
 * Создателя.
 */
class ConcreteMemento implements Memento
{
    private $state;

    private $date;

    public function __construct(string $state)
    {
        $this->state = $state;
        $this->date = date('Y-m-d H:i:s');
    }

    /**
     * EN: The Originator uses this method when restoring its state.
     *
     * RU: Создатель использует этот метод, когда восстанавливает своё
     * состояние.
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * EN: The rest of the methods are used by the Caretaker to display
     * metadata.
     *
     * RU: Остальные методы используются Опекуном для отображения метаданных.
     */
    public function getName(): string
    {
        return $this->date . " / (" . substr($this->state, 0, 9) . "...)";
    }

    public function getDate(): string
    {
        return $this->date;
    }
}

/**
 * EN: The Caretaker doesn't depend on the Concrete Memento class. Therefore, it
 * doesn't have access to the originator's state, stored inside the memento. It
 * works with all mementos via the base Memento interface.
 *
 * RU: Опекун не зависит от класса Конкретного Снимка. Таким образом, он не
 * имеет доступа к состоянию создателя, хранящемуся внутри снимка. Он работает
 * со всеми снимками через базовый интерфейс Снимка.
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

    public function backup(): void
    {
        echo "\nCaretaker: Saving Originator's state...\n";
        $this->mementos[] = $this->originator->save();
    }

    public function undo(): void
    {
        if (!count($this->mementos)) {
            return;
        }
        $memento = array_pop($this->mementos);

        echo "Caretaker: Restoring state to: " . $memento->getName() . "\n";
        try {
            $this->originator->restore($memento);
        } catch (\Exception $e) {
            $this->undo();
        }
    }

    public function showHistory(): void
    {
        echo "Caretaker: Here's the list of mementos:\n";
        foreach ($this->mementos as $memento) {
            echo $memento->getName() . "\n";
        }
    }
}

/**
 * EN: Client code.
 *
 * RU: Клиентский код.
 */
$originator = new Originator("Super-duper-super-puper-super.");
$caretaker = new Caretaker($originator);

$caretaker->backup();
$originator->doSomething();

$caretaker->backup();
$originator->doSomething();

$caretaker->backup();
$originator->doSomething();

echo "\n";
$caretaker->showHistory();

echo "\nClient: Now, let's rollback!\n\n";
$caretaker->undo();

echo "\nClient: Once more!\n\n";
$caretaker->undo();
