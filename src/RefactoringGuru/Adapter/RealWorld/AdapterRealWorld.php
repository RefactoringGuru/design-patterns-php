<?php

namespace RefactoringGuru\Adapter\RealWorld;

/**
 * Adapter Design Pattern
 *
 * Intent: Convert the interface of a class into the interface clients expect.
 * Adapter lets classes work together where they otherwise couldn't, due to
 * incompatible interfaces.
 *
 * Example: The Adapter pattern allows you to use 3rd-party or legacy classes
 * even if they are incompatible with the bulk of your code. For example,
 * instead of rewriting the notification interface of your to support each 3rd-
 * party service such as Slack, Facebook, SMS or {you-name-it}, you can create a
 * set of special wrappers that adapt calls from your app to an interface and
 * format required by each 3rd-party class.
 */

/**
 * The Target interface represents the interface that your application's classes
 * already follow.
 */
interface Notification
{
    public function send(string $title, string $message);
}

/**
 * Here's an example of the existing class that follows the Target interface.
 *
 * The truth is that many real apps may not have this interface clearly defined.
 * If you're in that boat, your first bet would be to extend the Adapter from
 * one of your existing application's classes. If that's awkward (for instance,
 * SlackNotification doesn't feel like a subclass of EmailNotification), then
 * extracting an interface should be your first step.
 */
class EmailNotification implements Notification
{
    private $adminEmail;

    public function __construct(string $adminEmail)
    {
        $this->adminEmail = $adminEmail;
    }

    public function send(string $title, string $message)
    {
        mail($this->adminEmail, $title, $message);
        print("Sent email with title '$title' to '{$this->adminEmail}' that says '$message'.");
    }
}

/**
 * The Adaptee is some useful class, incompatible with the Target interface. You
 * can't just go in and change the code of the class to follow the Target
 * interface. The code might be provided by a 3rd-party library.
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

    public function logIn()
    {
        // Send authentication request to Slack web service.
        print("Logged in to a slack account '{$this->login}'.\n");
    }

    public function sendMessage($chatId, $message)
    {
        // Send message post request to Slack web service.
        print("Posted following message into the '$chatId' chat: '$message'.\n");
    }
}

/**
 * The Adapter is a class that links the Target interface and the Adaptee class.
 * In this case, it allows the application to send notifications using Slack
 * API.
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
     * An Adapter is not only able of adapting interfaces, but it also can
     * convert incoming data to the format, required by the Adaptee.
     */
    public function send(string $title, string $message)
    {
        $slackMessage = "#" . $title . "# " . strip_tags($message);
        $this->slack->logIn();
        $this->slack->sendMessage($this->chatId, $slackMessage);
    }
}

/**
 * The client code can work with any class that follows the Target interface.
 */
function clientCode(Notification $notification)
{
    // ...

    print($notification->send("Website is down!",
        "<strong style='color:red;font-size: 50px;'>Alert!</strong> " .
        "Our website is not responding. Call admins and bring it up!"));

    // ...
}

print("Client code is designed correctly works with email notifications:\n");
$notification = new EmailNotification("developers@example.com");
clientCode($notification);
print("\n\n");


print("The same client code can work with other classes via adapter:\n");
$slackApi = new SlackApi("example.com", "XXXXXXXX");
$notification = new SlackNotification($slackApi, "Examples.com Developers");
clientCode($notification);