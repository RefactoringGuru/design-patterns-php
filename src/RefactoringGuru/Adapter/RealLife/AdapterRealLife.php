<?php

namespace RefactoringGuru\Adater\RealLife;

/**
 * Adapter Design Pattern
 *
 * Intent: Convert the interface of a class into another interface clients
 * expect. Adapter lets classes work together that couldn't otherwise because of
 * incompatible interfaces.
 *
 * Example: Adapter allows using 3rd-party or legacy classes even if they are
 * incompatible with a bulk of your code. Instead of rewriting the
 * notification interface of your app for each 3rd-party service (Slack,
 * Facebook, SMS, you-name-it), you create a special wrapper, that adapts
 * calls of your app to the interface and format supported by 3rd-party classes.
 */

/**
 * Target interface.
 */
interface Notification
{
    public function send(string $title, string $message);
}

/**
 * Existing class, that successfully follows Target interface.
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
 * Adaptee. This is useful class incompatible with a Target interface. You can't
 * change its code directly since it is provided by a 3rd-party library.
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
 * Adapter. Allows sending notifications using the SlackApi.
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
     * Adapter not only adapts interfaces, but also converts incoming data to
     * the format, supported by adaptee class.
     */
    public function send(string $title, string $message)
    {
        $slackMessage = "#" . $title . "# " . strip_tags($message);
        $this->slack->logIn();
        $this->slack->sendMessage($this->chatId, $slackMessage);
    }
}

/**
 * Client code.
 */

function clientCode(Notification $notification)
{
    // ...

    print($notification->send("Website is down!",
        "<strong style='color:red;font-size: 50px;'>Alert!</strong> ".
        "Our website is not responding. Call the admins and bring it up!"));

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