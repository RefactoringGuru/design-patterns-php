<?php

namespace RefactoringGuru\TemplateMethod\RealWorld;

/**
 * EN: Template Method Design Pattern
 *
 * Intent: Define the skeleton of an algorithm in operation, deferring
 * implementation of some steps to subclasses. Template Method lets subclasses
 * redefine specific steps of an algorithm without changing the algorithm's
 * structure.
 *
 * Example: In this example, the Template Method defines a skeleton of the
 * algorithm of message posting to social networks. Each subclass represents a
 * separate social network and implements all the steps differently, but reuses
 * the base algorithm.
 *
 * RU: Паттерн Шаблонный метод
 *
 * Назначение: Определяет общую схему алгоритма, перекладывая реализацию
 * некоторых шагов на подклассы. Шаблонный метод позволяет подклассам
 * переопределять отдельные шаги алгоритма без изменения структуры алгоритма.
 *
 * Пример: В этом примере Шаблонный метод определяет общую схему алгоритма
 * отправки сообщений в социальных сетях. Каждый подкласс представляет отдельную
 * социальную сеть и реализует все шаги по-разному, но повторно использует
 * базовый алгоритм.
 */

/**
 * EN: The Abstract Class defines the template method and declares all its
 * steps.
 *
 * RU: Абстрактный Класс определяет метод шаблона и объявляет все его шаги.
 */
abstract class SocialNetwork
{
    protected $username;

    protected $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * EN: The actual template method calls abstract steps in a specific order.
     * A subclass may implement all of the steps, allowing this method to
     * actually post something to a social network.
     *
     * RU: Фактический метод шаблона вызывает абстрактные шаги в определённом
     * порядке. Подкласс может реализовать все шаги, позволяя этому методу
     * реально публиковать что-то в социальной сети.
     */
    public function post(string $message): bool
    {
        // EN: Authenticate before posting. Every network uses a different
        // authentication method.
        //
        // RU: Проверка подлинности перед публикацией. Каждая сеть использует
        // свой метод авторизации.
        if ($this->logIn($this->username, $this->password)) {
            // EN: Send the post data. All networks have different APIs.
            //
            // RU: Отправляем почтовые данные. Все сети имеют разные API.
            $result = $this->sendData($message);
            // ...
            $this->logOut();

            return $result;
        }

        return false;
    }

    /**
     * EN: The steps are declared abstract to force the subclasses to implement
     * them all.
     *
     * RU: Шаги объявлены абстрактными, чтобы заставить подклассы реализовать их
     * полностью.
     */
    abstract public function logIn(string $userName, string $password): bool;

    abstract public function sendData(string $message): bool;

    abstract public function logOut(): void;
}

/**
 * EN: This Concrete Class implements the Facebook API (all right, it pretends
 * to).
 *
 * RU: Этот Конкретный Класс реализует API Facebook (ладно, он пытается).
 */
class Facebook extends SocialNetwork
{
    public function logIn(string $userName, string $password): bool
    {
        echo "\nChecking user's credentials...\n";
        echo "Name: " . $this->username . "\n";
        echo "Password: " . str_repeat("*", strlen($this->password)) . "\n";

        simulateNetworkLatency();

        echo "\n\nFacebook: '" . $this->username . "' has logged in successfully.\n";

        return true;
    }

    public function sendData(string $message): bool
    {
        echo "Facebook: '" . $this->username . "' has posted '" . $message . "'.\n";

        return true;
    }

    public function logOut(): void
    {
        echo "Facebook: '" . $this->username . "' has been logged out.\n";
    }
}

/**
 * EN: This Concrete Class implements the Twitter API.
 *
 * RU: Этот Конкретный Класс реализует API Twitter.
 */
class Twitter extends SocialNetwork
{
    public function logIn(string $userName, string $password): bool
    {
        echo "\nChecking user's credentials...\n";
        echo "Name: " . $this->username . "\n";
        echo "Password: " . str_repeat("*", strlen($this->password)) . "\n";

        simulateNetworkLatency();

        echo "\n\nTwitter: '" . $this->username . "' has logged in successfully.\n";

        return true;
    }

    public function sendData(string $message): bool
    {
        echo "Twitter: '" . $this->username . "' has posted '" . $message . "'.\n";

        return true;
    }

    public function logOut(): void
    {
        echo "Twitter: '" . $this->username . "' has been logged out.\n";
    }
}

/**
 * EN: A little helper function that makes waiting times feel real.
 *
 * RU: Небольшая вспомогательная функция, которая делает время ожидания похожим
 * на реальность.
 */
function simulateNetworkLatency()
{
    $i = 0;
    while ($i < 5) {
        echo ".";
        sleep(1);
        $i++;
    }
}

/**
 * EN: The client code.
 *
 * RU: Клиентский код.
 */
echo "Username: \n";
$username = readline();
echo "Password: \n";
$password = readline();
echo "Message: \n";
$message = readline();

echo "\nChoose the social network to post the message:\n" .
    "1 - Facebook\n" .
    "2 - Twitter\n";
$choice = readline();

// EN: Now, let's create a proper social network object and send the message.
//
// RU: Теперь давайте создадим правильный объект социальной сети и отправим
// сообщение.
if ($choice == 1) {
    $network = new Facebook($username, $password);
} elseif ($choice == 2) {
    $network = new Twitter($username, $password);
} else {
    die("Sorry, I'm not sure what you mean by that.\n");
}
$network->post($message);
