<?php

namespace RefactoringGuru\TemplateMethod\RealLife;

/**
 * Template Method Design Pattern
 *
 * Intent: Define the skeleton of an algorithm in an operation, deferring some
 * steps to subclasses. Template Method lets subclasses redefine certain steps
 * of an algorithm without changing the algorithm's structure.
 *
 * Example: In this example the Template Method defines an algorithm of posting
 * a message to a social network. Each subclass represents an actual social
 * network and implement all the steps used by the base class.
 */

/**
 * Abstract class.
 */
abstract class SocialNetwork
{
    protected $username;

    protected $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * The actual template method. It publishes the data to whatever network.
     */
    public function post(string $message): bool
    {
        // Authenticate before posting. Every network uses a different
        // authentication method.
        if ($this->logIn($this->username, $this->password)) {
            // Send the post data.
            $result = $this->sendData($message);
            $this->logOut();

            return $result;
        }

        return false;
    }

    public abstract function logIn(string $userName, string $password): bool;

    public abstract function sendData(string $message): bool;

    public abstract function logOut();
}

/**
 * ConcreteClass.
 */
class Facebook extends SocialNetwork
{
    public function logIn(string $userName, string $password): bool
    {
        print("\nChecking user's credentials...\n");
        print("Name: ".$this->username."\n");
        print("Password: ".str_repeat("*", strlen($this->password))."\n");

        simulateNetworkLatency();

        print("\n\nFacebook: '".$this->username."' has logged in successfully.\n");

        return true;
    }

    public function sendData(string $message): bool
    {
        print("Facebook: '".$this->username."' has posted '".$message."'.\n");

        return true;
    }

    public function logOut()
    {
        print("Facebook: '".$this->username."' has been logged out.\n");
    }
}

/**
 * ConcreteClass.
 */
class Twitter extends SocialNetwork
{
    public function logIn(string $userName, string $password): bool
    {
        print("\nChecking user's credentials...\n");
        print("Name: ".$this->username."\n");
        print("Password: ".str_repeat("*", strlen($this->password))."\n");

        simulateNetworkLatency();

        print("\n\nTwitter: '".$this->username."' has logged in successfully.\n");

        return true;
    }

    public function sendData(string $message): bool
    {
        print("Twitter: '".$this->username."' has posted '".$message."'.\n");

        return true;
    }

    public function logOut()
    {
        print("Twitter: '".$this->username."' has been logged out.\n");
    }
}

/**
 * A little helper function.
 */
function simulateNetworkLatency()
{
    $i = 0;
    while ($i < 5) {
        print(".");
        sleep(1);
        $i++;
    }
}

/**
 * Client code.
 */
print("Username: \n");
$username = readline();
print("Password: \n");
$password = readline();
print("Message: \n");
$message = readline();

print("\nChoose the social network to post the message:\n".
    "1 - Facebook\n".
    "2 - Twitter\n");
$choice = readline();

// Create proper network object and send the message.
if ($choice == 1) {
    $network = new Facebook($username, $password);
} elseif ($choice == 2) {
    $network = new Twitter($username, $password);
} else {
    die("Sorry, I'm not sure what you mean by that.\n");
}
$network->post($message);