<?php

namespace RefactoringGuru\Observer\Structural;

/**
 * EN: Observer Design Pattern
 *
 * Intent: Define a one-to-many dependency between objects so that when one
 * object changes state, all of its dependents are notified and updated
 * automatically.
 *
 * Note that there's a lot of different terms with similar meaning associated
 * with this pattern. Just remember that the Subject is also called the
 * Publisher and the Observer is often called the Subscriber and vice versa.
 * Also the verbs "observe", "listen" or "track" usually mean the same thing.
 *
 * RU: Паттерн Наблюдатель
 * 
 * Назначение: Устанавливает между объектами зависимость «один ко многим» таким образом, 
 * что когда изменяется состояние одного объекта, все зависимые от него объекты оповещаются
 * и обновляются автоматически.
 *
 * Обратите внимание, что существует множество различных терминов с похожими значениемя,
 * связанных с этим паттерном. Просто помните, что Субъекта также называют Издателем, 
 * а Наблюдателя часто называют Подписчиком и наоборот. Также глаголы «наблюдать», 
 * «слушать» или «отслеживать» обычно означают одно и то же.
 */

/**
 * EN:
 * PHP has a couple of built-in interfaces related to the Observer pattern.
 *
 * Here's what the Subject interface looks like:
 *
 * RU:
 * PHP имеет несколько встроенных интерфейсов, связанных с паттерном Наблюдатель.
 *
 * Вот как выглядит интерфейс Издателя:
 *
 * @link http://php.net/manual/en/class.splsubject.php
 *
 *     interface SplSubject
 *     {
 *         // Attach an observer to the subject.
 *         // Присоединяет наблюдателя к издателю.
 *         public function attach(SplObserver $observer);
 *
 *         // Detach an observer from the subject.
 *         // Отсоединяет наблюдателя от издателя.
 *         public function detach(SplObserver $observer);
 *
 *         // Notify all observers about an event.
 *         // Уведомляет всех наблюдателей о событии.
 *         public function notify();
 *     }
 *
 * EN:
 * There's also a built-in interface for Observers:
 *
 * RU:
 * Также имеется встроенный интерфейс для Наблюдателей:
 *
 * @link http://php.net/manual/en/class.splobserver.php
 *
 *     interface SplObserver
 *     {
 *         public function update(SplSubject $subject);
 *     }
 */

/**
 * EN:
 * The Subject owns some important state and notifies observers when the state
 * changes.
 *
 * RU:
 * Издатель владеет некоторым важным состоянием и оповещает наблюдателей о его изменении.
 */
class Subject implements \SplSubject
{
    /**
     * EN:
     * @var int For the sake of simplicity, the Subject's state, essential to
     * all subscribers, is stored in this variable.
     *
     * RU:
     *  @var int Для удобства в этой переменной хранится состояние Издателя,
     * необходимое всем подписчикам.
     */
    public $state;

    /**
     * EN:
     * @var array List of subscribers. In real life, the list of subscribers can
     * be stored more comprehensively (categorized by event type, etc.).
     *
     * RU:
     * @var array Список подписчиков. В реальной жизни список подписчиков может храниться
     * в более подробном виде (классифицируется по типу события и т.д.)
     */
    private $observers = [];

    /**
     * EN:
     * The subscription management methods.
     *
     * RU:
     * Методы управления подпиской.
     */
    public function attach(\SplObserver $observer)
    {
        print("Subject: Attached an observer.\n");
        $this->observers[] = $observer;
    }

    public function detach(\SplObserver $observer)
    {
        foreach ($this->observers as $key => $s) {
            if ($s === $observer) {
                unset($this->observers[$key]);
                print("Subject: Detached an observer.\n");
            }
        }
    }

    /**
     * EN:
     * Trigger an update in each subscriber.
     *
     * RU:
     * Запуск обновления в каждом подписчике.
     */
    public function notify()
    {
        print("Subject: Notifying observers...\n");
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * EN:
     * Usually, the subscription logic is only a fraction of what a Subject can
     * really do. Subjects commonly hold some important business logic, that
     * triggers a notification method whenever something important is about to
     * happen (or after it).
     *
     * RU:
     * Обычно логика подписки – только часть того, что  делает Издатель.
     * Издатели часто содержат некоторую важную бизнес-логику, которая запускает
     * метод уведомления всякий раз, когда должно произойти что-то важное
     * (или после этого).
     */
    public function someBusinessLogic()
    {
        print("\nSubject: I'm doing something important.\n");
        $this->state = rand(0, 10);

        print("Subject: My state has just changed to: {$this->state}\n");
        $this->notify();
    }
}

/**
 * EN:
 * Concrete Observers react to the updates issued by the Subject they had been
 * attached to.
 *
 * RU:
 * Конкретные Наблюдатели реагируют на обновления, выпущенные Издателем, 
 * к которому они были прикреплены.
 */
class ConcreteObserverA implements \SplObserver
{
    public function update(\SplSubject $subject)
    {
        if (! $subject instanceof Subject) {
            return;
        }

        if ($subject->state < 3) {
            print("ConcreteObserverA: Reacted to the event.\n");
        }
    }
}

class ConcreteObserverB implements \SplObserver
{
    public function update(\SplSubject $subject)
    {
        if (! $subject instanceof Subject) {
            return;
        }

        if ($subject->state == 0 || $subject->state >= 2) {
            print("ConcreteObserverB: Reacted to the event.\n");
        }
    }
}

/**
 * EN: The client code.
 *
 * RU: Клиентский код.
 */

$subject = new Subject();

$o1 = new ConcreteObserverA();
$subject->attach($o1);

$o2 = new ConcreteObserverB();
$subject->attach($o2);

$subject->someBusinessLogic();
$subject->someBusinessLogic();

$subject->detach($o2);

$subject->someBusinessLogic();
