<?php

namespace RefactoringGuru\Observer\RealWorld;

/**
 * EN: Observer Design Pattern
 *
 * Intent: Lets you define a subscription mechanism to notify multiple objects
 * about any events that happen to the object they're observing.
 *
 * Example: In this example the Observer pattern allows various objects to
 * observe events that are happening inside a user repository of an app.
 *
 * The repository emits various types of events and allows observers to listen
 * to all of them, as well as only individual ones.
 *
 * RU: Паттерн Наблюдатель
 *
 * Назначение: Создаёт механизм подписки, позволяющий одним объектам следить и
 * реагировать на события, происходящие в других объектах.
 *
 * Пример: В этом примере паттерн Наблюдатель позволяет различным объектам
 * наблюдать за событиями, происходящими в пользовательском репозитории
 * приложения.
 *
 * Репозиторий генерирует различные типы событий и позволяет наблюдателям
 * прослушивать их все, а так же лишь отдельные из них.
 */

/**
 * EN: The UserRepository represents a Subject. Various objects are interested
 * in tracking its internal state, whether it's adding a new user or removing
 * one.
 *
 * RU: Пользовательский репозиторий представляет собой Издателя. Различные
 * объекты заинтересованы в отслеживании его внутреннего состояния, будь то
 * добавление нового пользователя или его удаление.
 */
class UserRepository implements \SplSubject
{
    /**
     * EN: @var array The list of users.
     *
     * RU: @var array Список пользователей.
     */
    private $users = [];

    // EN: Here goes the actual Observer management infrastructure. Note that
    // it's not everything that our class is responsible for. Its primary
    // business logic is listed below these methods.
    //
    // RU: Здесь находится реальная инфраструктура управления Наблюдателя.
    // Обратите внимание, что это не всё, за что отвечает наш класс. Его
    // основная бизнес-логика приведена ниже этих методов.

    /**
     * @var array
     */
    private $observers = [];

    public function __construct()
    {
        // EN: A special event group for observers that want to listen to all
        // events.
        //
        // RU: Специальная группа событий для наблюдателей, которые хотят
        // слушать все события.
        $this->observers["*"] = [];
    }

    private function initEventGroup(string $event = "*"): void
    {
        if (!isset($this->observers[$event])) {
            $this->observers[$event] = [];
        }
    }

    private function getEventObservers(string $event = "*"): array
    {
        $this->initEventGroup($event);
        $group = $this->observers[$event];
        $all = $this->observers["*"];

        return array_merge($group, $all);
    }

    public function attach(\SplObserver $observer, string $event = "*"): void
    {
        $this->initEventGroup($event);

        $this->observers[$event][] = $observer;
    }

    public function detach(\SplObserver $observer, string $event = "*"): void
    {
        foreach ($this->getEventObservers($event) as $key => $s) {
            if ($s === $observer) {
                unset($this->observers[$event][$key]);
            }
        }
    }

    public function notify(string $event = "*", $data = null): void
    {
        echo "UserRepository: Broadcasting the '$event' event.\n";
        foreach ($this->getEventObservers($event) as $observer) {
            $observer->update($this, $event, $data);
        }
    }

    // EN: Here are the methods representing the business logic of the class.
    //
    // RU: Вот методы, представляющие бизнес-логику класса.

    public function initialize($filename): void
    {
        echo "UserRepository: Loading user records from a file.\n";
        // ...
        $this->notify("users:init", $filename);
    }

    public function createUser(array $data): User
    {
        echo "UserRepository: Creating a user.\n";

        $user = new User();
        $user->update($data);

        $id = bin2hex(openssl_random_pseudo_bytes(16));
        $user->update(["id" => $id]);
        $this->users[$id] = $user;

        $this->notify("users:created", $user);

        return $user;
    }

    public function updateUser(User $user, array $data): User
    {
        echo "UserRepository: Updating a user.\n";

        $id = $user->attributes["id"];
        if (!isset($this->users[$id])) {
            return null;
        }

        $user = $this->users[$id];
        $user->update($data);

        $this->notify("users:updated", $user);

        return $user;
    }

    public function deleteUser(User $user): void
    {
        echo "UserRepository: Deleting a user.\n";

        $id = $user->attributes["id"];
        if (!isset($this->users[$id])) {
            return;
        }

        unset($this->users[$id]);

        $this->notify("users:deleted", $user);
    }
}

/**
 * EN: Let's keep the User class trivial since it's not the focus of our
 * example.
 *
 * RU: Давайте сохраним класс Пользователя тривиальным, так как он не является
 * главной темой нашего примера.
 */
class User
{
    public $attributes = [];

    public function update($data): void
    {
        $this->attributes = array_merge($this->attributes, $data);
    }
}

/**
 * EN: This Concrete Component logs any events it's subscribed to.
 *
 * RU: Этот Конкретный Компонент регистрирует все события, на которые он
 * подписан.
 */
class Logger implements \SplObserver
{
    private $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }

    public function update(\SplSubject $repository, string $event = null, $data = null): void
    {
        $entry = date("Y-m-d H:i:s") . ": '$event' with data '" . json_encode($data) . "'\n";
        file_put_contents($this->filename, $entry, FILE_APPEND);

        echo "Logger: I've written '$event' entry to the log.\n";
    }
}

/**
 * EN: This Concrete Component sends initial instructions to new users. The
 * client is responsible for attaching this component to a proper user creation
 * event.
 *
 * RU: Этот Конкретный Компонент отправляет начальные инструкции новым
 * пользователям. Клиент несёт ответственность за присоединение этого компонента
 * к соответствующему событию создания пользователя.
 */
class OnboardingNotification implements \SplObserver
{
    private $adminEmail;

    public function __construct($adminEmail)
    {
        $this->adminEmail = $adminEmail;
    }

    public function update(\SplSubject $repository, string $event = null, $data = null): void
    {
        // mail($this->adminEmail,
        //     "Onboarding required",
        //     "We have a new user. Here's his info: " .json_encode($data));

        echo "OnboardingNotification: The notification has been emailed!\n";
    }
}

/**
 * EN: The client code.
 *
 * RU: Клиентский код.
 */

$repository = new UserRepository();
$repository->attach(new Logger(__DIR__ . "/log.txt"), "*");
$repository->attach(new OnboardingNotification("1@example.com"), "users:created");

$repository->initialize(__DIR__ . "/users.csv");

// ...

$user = $repository->createUser([
    "name" => "John Smith",
    "email" => "john99@example.com",
]);

// ...

$repository->deleteUser($user);
