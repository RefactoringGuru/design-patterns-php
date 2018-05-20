<?php

namespace RefactoringGuru\Mediator\RealLife;

/**
 * Mediator Design Pattern
 *
 * Intent: Define an object that encapsulates how a set of objects interact.
 * Mediator promotes loose coupling by keeping objects from referring to each
 * other explicitly, and it lets you vary their interaction independently.
 *
 * Example: In this example the Mediator pattern expands the idea of the
 * Observer pattern by providing a central event dispatcher, which lets any
 * object track & trigger events in other objects without depending on their
 * classes.
 */

/**
 * The Event Dispatcher class acts as the Mediator and contains the subscription
 * and notification logic. While the classic Mediator may depend on the concrete
 * components, this one is tied only to the abstract interfaces. Such level of
 * indirection was possible to achieve because all connections between the
 * components are established by their own objects or by the client code via
 * subscription methods.
 */
class EventDispatcher
{
    /**
     * @var array
     */
    private $observers = [];

    public function __construct()
    {
        // The special event group for observers that want to listen all events.
        $this->observers["*"] = [];
    }

    private function initEventGroup(string &$event = "*")
    {
        if (! isset($this->observers[$event])) {
            $this->observers[$event] = [];
        }
    }

    private function getEventObservers(string $event = "*")
    {
        $this->initEventGroup($event);
        $group = $this->observers[$event];
        $all = $this->observers["*"];

        return array_merge($group, $all);
    }

    public function attach(Observer $observer, string $event = "*")
    {
        $this->initEventGroup($event);

        $this->observers[$event][] = $observer;
    }

    public function detach(Observer $observer, string $event = "*")
    {
        foreach ($this->getEventObservers($event) as $key => $s) {
            if ($s === $observer) {
                unset($this->observers[$event][$key]);
            }
        }
    }

    public function trigger(string $event, object $emitter, $data = null)
    {
        print("EventDispatcher: Broadcasting the '$event' event.\n");
        foreach ($this->getEventObservers($event) as $observer) {
            $observer->update($event, $emitter, $data);
        }
    }
}

/**
 * A simple helper function to provide global access to the event dispatcher.
 */
function events(): EventDispatcher
{
    static $eventDispatcher;
    if (! $eventDispatcher) {
        $eventDispatcher = new EventDispatcher();
    }

    return $eventDispatcher;
}

/**
 * The Observer interface defines how components will receive event
 * notifications.
 */
interface Observer
{
    public function update(string $event, object $emitter, $data = null);
}

/**
 * Unlike our example of the Observer pattern, this example makes the
 * UserRepository act as a regular component that doesn't have any special
 * event-related methods. Like any other component, this class relies on the
 * EventDispatcher to broadcast its own events and listen for the other ones.
 *
 * @see \RefactoringGuru\Observer\RealLife\UserRepository
 */
class UserRepository implements Observer
{
    /**
     * @var array List of application's users.
     */
    private $users = [];

    /**
     * Components can subscribe to events by themselves or by client code.
     */
    public function __construct()
    {
        events()->attach($this, "users:deleted");
    }

    /**
     * Components can decide whether they like to process an event using its
     * name, emitter or any contextual data passed along with the event.
     */
    public function update(string $event, object $emitter, $data = null)
    {
        switch ($event) {
            case "users:deleted":
                if ($emitter === $this) {
                    return;
                }
                $this->deleteUser($data, true);
                break;
        }
    }

    // These methods represent the business logic of the class.

    public function initialize($filename)
    {
        print("UserRepository: Loading user records from a file.\n");
        // ...
        events()->trigger("users:init", $this, $filename);
    }

    public function createUser(array $data, $silent = false)
    {
        print("UserRepository: Creating a user.\n");

        $user = new User();
        $user->update($data);

        $id = bin2hex(openssl_random_pseudo_bytes(16));
        $user->update(["id" => $id]);
        $this->users[$id] = $user;

        if (! $silent) {
            events()->trigger("users:created", $this, $user);
        }

        return $user;
    }

    public function updateUser(User $user, array $data, $silent = false)
    {
        print("UserRepository: Updating a user.\n");

        $id = $user->attributes["id"];
        if (! isset($this->users[$id])) {
            return null;
        }

        $user = $this->users[$id];
        $user->update($data);

        if (! $silent) {
            events()->trigger("users:updated", $this, $user);
        }

        return $user;
    }

    public function deleteUser(User $user, $silent = false)
    {
        print("UserRepository: Deleting a user.\n");

        $id = $user->attributes["id"];
        if (! isset($this->users[$id])) {
            return;
        }

        unset($this->users[$id]);

        if (! $silent) {
            events()->trigger("users:deleted", $this, $user);
        }
    }
}

/**
 * The User class is trivial since it's not the central part of the example.
 */
class User
{
    public $attributes = [];

    public function update($data)
    {
        $this->attributes = array_merge($this->attributes, $data);
    }

    /**
     * All objects can trigger events.
     */
    public function delete()
    {
        print("User: I can now delete myself without worrying about the repository.\n");
        events()->trigger("users:deleted", $this, $this);
    }
}

/**
 * Concrete Component.
 */
class Logger implements Observer
{
    private $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }

    public function update(string $event, object $emitter, $data = null)
    {
        $entry = date("Y-m-d H:i:s").": '$event' with data '".json_encode($data)."'\n";
        file_put_contents($this->filename, $entry, FILE_APPEND);

        print("Logger: I've written '$event' entry to the log.\n");
    }
}

/**
 * Concrete Component.
 */
class OnboardingNotification implements Observer
{
    private $adminEmail;

    public function __construct($adminEmail)
    {
        $this->adminEmail = $adminEmail;
    }

    public function update(string $event, object $emitter, $data = null)
    {
        // mail($this->adminEmail,
        //     "Onboarding required",
        //     "We have a new user. Here's his info: " .json_encode($data));

        print("OnboardingNotification: The notification has been emailed!\n");
    }
}

/**
 * Client code.
 */

$repository = new UserRepository();
events()->attach($repository, "facebook:update");

$logger = new Logger("log.txt");
events()->attach($logger, "*");

$onboarding = new OnboardingNotification("1@example.com");
events()->attach($onboarding, "users:created");

// ...

$repository->initialize("users.csv");

// ...

$user = $repository->createUser([
    "name" => "John Smith",
    "email" => "john99@example.com",
]);

// ...

$user->delete();