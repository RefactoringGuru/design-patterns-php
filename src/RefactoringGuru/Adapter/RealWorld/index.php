<?php

namespace RefactoringGuru\Adapter\RealWorld;

/**
 * EN: Adapter Design Pattern
 *
 * Intent: Provides a unified interface that allows objects with incompatible
 * interfaces to collaborate.
 *
 * Example: The Adapter pattern allows you to use 3rd-party or legacy classes
 * even if they are incompatible with the bulk of your code. For example,
 * instead of rewriting the notification interface of your app to support each
 * 3rd-party service such as Slack, Facebook, SMS or (you-name-it), you can
 * create a set of special wrappers that adapt calls from your app to an
 * interface and format required by each 3rd-party class.
 *
 * RU: Паттерн Адаптер
 *
 * Назначение: Позволяет объектам с несовместимыми интерфейсами работать вместе.
 *
 * Пример: Паттерн Адаптер позволяет использовать сторонние или устаревшие
 * классы, даже если они несовместимы с основной частью кода. Например, вместо
 * того, чтобы переписывать интерфейс уведомлений вашего приложения для
 * поддержки каждого стороннего сервиса вроде Slack, Facebook, SMS и прочих, вы
 * создаёте под эти сервисы набор специальных обёрток, которые приводят вызовы
 * из приложения к требуемым сторонними классами интерфейсу и формату.
 */

/**
 * EN: The Target interface represents the interface that your application's
 * classes already follow.
 *
 * RU: Целевой интерфейс предоставляет интерфейс, которому следуют классы вашего
 * приложения.
 */
interface Notification
{
    public function send(string $title, string $message);
}

/**
 * EN: Here's an example of the existing class that follows the Target
 * interface.
 *
 * The truth is that many real apps may not have this interface clearly defined.
 * If you're in that boat, your best bet would be to extend the Adapter from one
 * of your application's existing classes. If that's awkward (for instance,
 * SlackNotification doesn't feel like a subclass of EmailNotification), then
 * extracting an interface should be your first step.
 *
 * RU: Вот пример существующего класса, который следует за целевым интерфейсом.
 *
 * Дело в том, что у большинства приложений нет чётко определённого интерфейса.
 * В этом случае лучше было бы расширить Адаптер за счёт существующего класса
 * приложения. Если это неудобно (например, SlackNotification не похож на
 * подкласс EmailNotification), тогда первым шагом должно быть извлечение
 * интерфейса.
 */
class EmailNotification implements Notification
{
    private $adminEmail;

    public function __construct(string $adminEmail)
    {
        $this->adminEmail = $adminEmail;
    }

    public function send(string $title, string $message): void
    {
        mail($this->adminEmail, $title, $message);
        echo "Sent email with title '$title' to '{$this->adminEmail}' that says '$message'.";
    }
}

/**
 * EN: The Adaptee is some useful class, incompatible with the Target interface.
 * You can't just go in and change the code of the class to follow the Target
 * interface, since the code might be provided by a 3rd-party library.
 *
 * RU: Адаптируемый класс – некий полезный класс, несовместимый с целевым
 * интерфейсом. Нельзя просто войти и изменить код класса так, чтобы следовать
 * целевому интерфейсу, так как код может предоставляться сторонней библиотекой.
 */
class SlackApi
{
    private $login;
    private $apiKey;

    public function __construct(string $login, string $apiKey)
    {
        $this->login = $login;
        $this->apiKey = $apiKey;
    }

    public function logIn(): void
    {
        // Send authentication request to Slack web service.
        echo "Logged in to a slack account '{$this->login}'.\n";
    }

    public function sendMessage(string $chatId, string $message): void
    {
        // Send message post request to Slack web service.
        echo "Posted following message into the '$chatId' chat: '$message'.\n";
    }
}

/**
 * EN: The Adapter is a class that links the Target interface and the Adaptee
 * class. In this case, it allows the application to send notifications using
 * Slack API.
 *
 * RU: Адаптер – класс, который связывает Целевой интерфейс и Адаптируемый
 * класс. Это позволяет приложению использовать Slack API для отправки
 * уведомлений.
 */
class SlackNotification implements Notification
{
    private $slack;
    private $chatId;

    public function __construct(SlackApi $slack, string $chatId)
    {
        $this->slack = $slack;
        $this->chatId = $chatId;
    }

    /**
     * EN: An Adapter is not only capable of adapting interfaces, but it can
     * also convert incoming data to the format required by the Adaptee.
     *
     * RU: Адаптер способен адаптировать интерфейсы и преобразовывать входные
     * данные в формат, необходимый Адаптируемому классу.
     */
    public function send(string $title, string $message): void
    {
        $slackMessage = "#" . $title . "# " . strip_tags($message);
        $this->slack->logIn();
        $this->slack->sendMessage($this->chatId, $slackMessage);
    }
}

/**
 * EN: The client code can work with any class that follows the Target
 * interface.
 *
 * RU: Клиентский код работает с классами, которые следуют Целевому интерфейсу.
 */
function clientCode(Notification $notification)
{
    // ...

    echo $notification->send("Website is down!",
        "<strong style='color:red;font-size: 50px;'>Alert!</strong> " .
        "Our website is not responding. Call admins and bring it up!");

    // ...
}

echo "Client code is designed correctly and works with email notifications:\n";
$notification = new EmailNotification("developers@example.com");
clientCode($notification);
echo "\n\n";


echo "The same client code can work with other classes via adapter:\n";
$slackApi = new SlackApi("example.com", "XXXXXXXX");
$notification = new SlackNotification($slackApi, "Example.com Developers");
clientCode($notification);
