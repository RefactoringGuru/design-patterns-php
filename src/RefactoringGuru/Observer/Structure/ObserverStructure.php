<?php

namespace RefactoringGuru\Observer\Structure;

/**
 * Observer Design Pattern
 *
 * Intent: Define a one-to-many dependency between objects so that when one
 * object changes state, all its dependents are notified and updated
 * automatically.
 */

/**
 * Publisher.
 */
class Publisher
{
    /**
     * @var int For the sake of simplicity, the Publisher's
     * state important for all subscribers, will be stored in
     * this variable.
     */
    public $state;

    /**
     * @var array List of subscribers. In real life the list of subscribers can
     * be stored in more comprehensive manner, categorized by events types, etc.
     */
    private $subscribers = [];

    /**
     * @param Subscriber $subscriber Subscription management.
     */
    public function subscribe(Subscriber $subscriber)
    {
        print("Publisher: Subscribed an object.\n");
        $this->subscribers[] = $subscriber;
    }

    /**
     * @param Subscriber $subscriber Subscription management.
     */
    public function unsubscribe(Subscriber $subscriber)
    {
        foreach ($this->subscribers as $key => $s) {
            if ($s === $subscriber) {
                unset($this->subscribers[$key]);
                print("Publisher: Unsubscribed an object.\n");
            }
        }
    }

    /**
     * The method that fires update for all subscribers.
     */
    public function notifySubscribers()
    {
        print("Publisher: Notifying subscribers.\n");
        foreach ($this->subscribers as $subscriber) {
            $subscriber->update($this);
        }
    }

    /**
     * The subscription logic is only a fraction of the real behavior of the
     * Publisher. Usually it holds some important business logic, that uses
     * notification methods to notify all of the subscribers (or just the ones,
     * which subscribed for particular event) about some important event or
     * change of the Publisher's state.
     */
    public function someBusinessLogic()
    {
        print("\nPublisher: I'm doing something important.\n");
        $this->state = rand(0, 10);

        print("Publisher: My state has just changed to: {$this->state}\n");
        $this->notifySubscribers();
    }
}

/**
 * Subscriber interface defines the method for accepting updates from
 * publishers. Usually it describes just a single method.
 */
interface Subscriber
{
    /**
     * You can either pass an entire publisher object to the subscribers or just
     * a specific set of data, related to the event.
     */
    public function update(Publisher $publisher);
}

class ConcreteSubscriberA implements Subscriber
{
    public function update(Publisher $publisher)
    {
        if ($publisher->state < 3) {
            print("ConcreteSubscriberA: Reacted to the event.\n");
        }
    }
}

class ConcreteSubscriberB implements Subscriber
{
    public function update(Publisher $publisher)
    {
        if ($publisher->state == 0 || $publisher->state >= 2) {
            print("ConcreteSubscriberB: Reacted to the event.\n");
        }
    }
}

/**
 * Client code.
 */

$publisher = new Publisher();

$s1 = new ConcreteSubscriberA();
$publisher->subscribe($s1);

$s2 = new ConcreteSubscriberB();
$publisher->subscribe($s2);

$publisher->someBusinessLogic();
$publisher->someBusinessLogic();
$publisher->someBusinessLogic();