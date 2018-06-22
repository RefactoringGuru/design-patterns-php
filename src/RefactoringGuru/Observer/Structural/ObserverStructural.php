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
 * Назначение:
 *
 */

/**
 * PHP has a couple of built-in interfaces related to the Observer pattern.
 *
 * Here's what the Subject interface looks like:
 *
 * @link http://php.net/manual/en/class.splsubject.php
 *
 *     interface SplSubject
 *     {
 *         // Attach an observer to the subject.
 *         public function attach(SplObserver $observer);
 *
 *         // Detach an observer from the subject.
 *         public function detach(SplObserver $observer);
 *
 *         // Notify all observers about an event.
 *         public function notify();
 *     }
 *
 * There's also a built-in interface for Observers:
 *
 * @link http://php.net/manual/en/class.splobserver.php
 *
 *     interface SplObserver
 *     {
 *         public function update(SplSubject $subject);
 *     }
 */

/**
 * The Subject owns some important state and notifies observers when the state
 * changes.
 */
class Subject implements \SplSubject
{
    /**
     * @var int For the sake of simplicity, the Subject's state, essential to
     * all subscribers, is stored in this variable.
     */
    public $state;

    /**
     * @var array List of subscribers. In real life, the list of subscribers can
     * be stored more comprehensively (categorized by event type, etc.).
     */
    private $observers = [];

    /**
     * The subscription management methods.
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
     * Trigger an update in each subscriber.
     */
    public function notify()
    {
        print("Subject: Notifying observers...\n");
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * Usually, the subscription logic is only a fraction of what a Subject can
     * really do. Subjects commonly hold some important business logic, that
     * triggers a notification method whenever something important is about to
     * happen (or after it).
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
 * Concrete Observers react to the updates issued by the Subject they had been
 * attached to.
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
 * The client code.
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
