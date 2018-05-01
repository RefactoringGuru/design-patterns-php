<?php

namespace RefactoringGuru\Observer\RealLife;

/**
 * Observer Design Pattern
 *
 * Intent: Define a one-to-many dependency between objects so that when one
 * object changes state, all of its dependents are notified and updated
 * automatically.
 *
 * Example: In this example the Observer pattern allows objects to observe the
 * events that happen inside a user repository of an app. The repository lets
 * observers listen for all events types, as well as only for the specific ones.
 */

/**
 * Subject.
 */
class UserRepository implements \SplSubject
{
    /**
     * @var array List of application's users.
     */
    private $users = [];

    // The Observer management infrastructure.

    /**
     * @var array
     */
    private $observers = [];

    public function __construct()
    {
        // The special event group for observers that want to listen all events.
        $this->observers["*"] = [];
    }

    private function initEventGroup(string &$event = null)
    {
        $event = $event ?? "*";
        if (! isset($this->observers[$event])) {
            $this->observers[$event] = [];
        }
    }

    private function getEventObservers(string $event = null)
    {
        $this->initEventGroup($event);
        $group = $this->observers[$event];
        $all = $this->observers["*"];

        return array_merge($group, $all);
    }

    public function attach(\SplObserver $observer, string $event = null)
    {
        $this->initEventGroup($event);

        $this->observers[$event][] = $observer;
    }

    public function detach(\SplObserver $observer, string $event = null)
    {
        foreach ($this->getEventObservers($event) as $key => $s) {
            if ($s === $observer) {
                unset($this->observers[$event][$key]);
            }
        }
    }

    public function notify(string $event = null, $data = null)
    {
        foreach ($this->getEventObservers($event) as $observer) {
            $observer->update($this, $event, $data);
        }
    }

    // These methods represent the business logic of the class.

    public function initialize($filename)
    {
        print("\nUserRepository: Loading user records from a file.\n");
        $this->notify("initialization", $filename);
    }

    public function createUser(array $data)
    {
        print("\nUserRepository: Creating a user...\n");

        $user = new User();
        $user->update($data);

        $id = bin2hex(openssl_random_pseudo_bytes(16));
        $user->update(["id" => $id]);
        $this->users[$id] = $user;

        $this->notify("user_created", func_get_args());

        return $user;
    }

    public function updateUser(User $user, array $data)
    {
        print("\nUserRepository: Updating a user...\n");

        $id = $user->attributes["id"];
        if (! isset($this->users[$id])) {
            return null;
        }

        $user = $this->users[$id];
        $user->update($data);

        $this->notify("user_updated", func_get_args());

        return $user;
    }

    public function deleteUser(User $user)
    {
        print("\nUserRepository: Deleting a user...\n");

        $id = $user->attributes["id"];
        if (! isset($this->users[$id])) {
            return;
        }

        unset($this->users[$id]);

        $this->notify("user_deleted", func_get_args());
    }
}

/**
 * The User class very simple since it's not the central part of the example.
 */
class User
{
    public $attributes = [];

    public function update($data)
    {
        $this->attributes = array_merge($this->attributes, $data);
    }
}

/**
 * Concrete Subscriber.
 */
class Logger implements \SplObserver
{
    private $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function update(\SplSubject $repository, string $event = null, $data = null)
    {
        if (! $repository instanceof UserRepository) {
            return;
        }

        $entry = date("Y-m-d H:i:s").": '$event' with data '".json_encode($data)."'\n";
        file_put_contents($this->filename, $entry, FILE_APPEND);

        print("Logger: I've written '$event' entry to the log.\n");
    }
}

/**
 * Concrete Subscriber.
 */
class OnboardingNotification implements \SplObserver
{
    private $adminEmail;

    public function __construct($adminEmail)
    {
        $this->adminEmail = $adminEmail;
    }

    public function update(\SplSubject $repository, string $event = null, $data = null)
    {
        if (! $repository instanceof UserRepository) {
            return;
        }

        //mail($this->adminEmail,
        //    "Onboarding required",
        //    "We have a new user. Here's his info: " .json_encode($data));

        print("OnboardingNotification: The notification has been emailed!\n");
    }
}

/**
 * Client code.
 */

$repository = new UserRepository();
$repository->attach(new Logger("log.txt"));
$repository->attach(new OnboardingNotification("1@example.com"), "user_created");

$repository->initialize("users.csv");

// ...

$user = $repository->createUser([
    "name" => "John Smith",
    "email" => "john99@example.com",
]);

// ...

$repository->deleteUser($user);