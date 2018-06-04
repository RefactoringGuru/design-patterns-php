<?php

namespace RefactoringGuru\FactoryMethod\RealWorld;

/**
 * Factory Method Design Pattern
 *
 * Intent: Define an interface for creating an object, but let subclasses decide
 * which class to instantiate. Factory Method lets a class defer instantiation
 * to subclasses.
 *
 * Example: In this example the Factory Method pattern provides an interface for
 * creating social network connectors, which can be used to log in to the
 * network, create posts and potentially perform other activities—and all of
 * this without coupling the client code to specific classes of the particular
 * social network.
 *
 * Instead of calling constructor of social network connectors directly, there
 * is a special method All social network connectors follow the same interface,
 * which lets the client treat any connector it gets from the factory method in
 * the same way.
 *
 * To add a new social network support to the program, you'll need implement a
 * new connector subclass to create a new Creator's subclass, which will be
 * creating a connector object  and after that create a different social
 * connector object in it.
 */

/**
 * Creator.
 */
abstract class SocialNetworkPoster
{
    /**
     * The actual factory method.
     */
    public abstract function getSocialNetwork(): SocialNetworkConnector;

    /**
     * The factory method is used inside the Creator's business logic.
     * Subclasses may alter this logic indirectly by returning different types
     * of the connector from the factory method.
     */
    public function post($content)
    {
        // Call the factory method to create a Product object...
        $network = $this->getSocialNetwork();
        // ...then use it as you will.
        $network->logIn();
        $network->createPost($content);
        $network->logout();
    }
}

/**
 * The Concrete Creator, which supports Facebook. Remember, that this class also
 * inherits the 'post' method from the parent class.
 */
class FacebookPoster extends SocialNetworkPoster
{
    private $login, $password;

    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function getSocialNetwork(): SocialNetworkConnector
    {
        return new FacebookConnector($this->login, $this->password);
    }
}

/**
 * The Concrete Creator, which supports LinkedIn.
 */
class LinkedInPoster extends SocialNetworkPoster
{
    private $email, $password;

    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function getSocialNetwork(): SocialNetworkConnector
    {
        return new LinkedInConnector($this->email, $this->password);
    }
}

/**
 * The Product interface.
 */
interface SocialNetworkConnector
{
    public function logIn();

    public function logOut();

    public function createPost($content);
}

/**
 * Concrete Product. The FacebookConnector implements Facebook API.
 */
class FacebookConnector implements SocialNetworkConnector
{
    private $login, $password;

    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function logIn()
    {
        print("Send HTTP API request to log in user $this->login with " .
            "password $this->password\n");
    }

    public function logOut()
    {
        print("Send HTTP API request to log out user $this->login\n");
    }

    public function createPost($content)
    {
        print("Send HTTP API requests to create a post in Facebook timeline.\n");
    }
}

/**
 * Concrete Product. The LinkedInConnector implements LinkedIn API.
 */
class LinkedInConnector implements SocialNetworkConnector
{
    private $email, $password;

    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function logIn()
    {
        print("Send HTTP API request to log in user $this->email with " .
            "password $this->password\n");
    }

    public function logOut()
    {
        print("Send HTTP API request to log out user $this->email\n");
    }

    public function createPost($content)
    {
        print("Send HTTP API requests to create a post in LinkedIn timeline.\n");
    }
}

/**
 * The client code can work with any type of social network poster since it
 * doesn't depend on concrete classes.
 */
function clientCode(SocialNetworkPoster $creator)
{
    // ...
    $creator->post("Hello world!");
    $creator->post("I had a large hamburger this morning!");
    // ...
}

/**
 * During the initialization phase, the app can decide which social network it
 * wants to work with, create it and pass it to the client code.
 */
print("Testing ConcreteCreator1:\n");
clientCode(new FacebookPoster("john_smith", "******"));
print("\n\n");

print("Testing ConcreteCreator2:\n");
clientCode(new LinkedInPoster("john_smith@example.com", "******"));
